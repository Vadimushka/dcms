#!/usr/bin/env bash
# Smoke-тесты DCMS: HTTP-код, обязательный фрагмент, отсутствие PHP-ошибок.
# Использование: tests/smoke/run.sh [базовый URL]
#
# Без `set -e`: определение USE_LOGS ниже опирается на код возврата
# `docker compose ps`, который ненулевой, когда compose-проект не резолвится.
# С `set -e` скрипт умирал бы на этой строке вместо того, чтобы продолжить
# работу без чтения лога.
set -u

BASE="${1:-http://127.0.0.1:8090}"
# readlink -f разыменовывает симлинк: без него запуск через ссылку из другого
# каталога дал бы SCRIPT_DIR каталога ссылки, urls.txt бы не нашёлся, и набор
# завершился бы «успешно» с нулём проверок.
SCRIPT_PATH="$(readlink -f "$0" 2>/dev/null || echo "$0")"
SCRIPT_DIR="$(cd "$(dirname "$SCRIPT_PATH")" && pwd)"
PROJECT_DIR="$(cd "$SCRIPT_DIR/../.." && pwd)"
LIST="$SCRIPT_DIR/urls.txt"
if [ ! -r "$LIST" ]; then
    echo "Не найден список URL: $LIST" >&2
    exit 1
fi

COOKIE_JAR="$(mktemp)"
trap 'rm -f "$COOKIE_JAR"' EXIT

# Следы ошибок PHP — ищем и в теле ответа, и в логе контейнера
ERROR_PATTERN='Fatal error|Parse error|Warning:|Notice:|Deprecated:|Uncaught|Call to undefined|syntax error'

# Стенд должен отвечать хоть чем-то, иначе цикл ниже даст 17 одинаковых
# провалов с кодом 000 — сразу и понятно говорим, что curl не достучался.
if ! curl -s -o /dev/null --max-time 10 "$BASE/" 2>/dev/null; then
    echo "Стенд недоступен: $BASE (нет соединения). Проверьте, что сервис поднят: docker compose up -d" >&2
    exit 1
fi

# Лог контейнера читаем только для локального стенда: у боевого сайта его нет.
# --project-directory привязывает docker compose к каталогу проекта явно —
# без этого `docker compose ps/logs` ищут docker-compose.yml в текущем рабочем
# каталоге процесса, а не рядом со скриптом, и при запуске не из корня
# репозитория (например, из CI) детектор ошибок в логе молча отключается.
IS_LOCAL=0
case "$BASE" in
    http://127.0.0.1:*|http://localhost:*) IS_LOCAL=1 ;;
esac

# `ps php` возвращает 0 и при остановленном контейнере (просто пустой список),
# поэтому проверяем не код возврата, а наличие идентификатора контейнера.
USE_LOGS=0
if [ "$IS_LOCAL" = 1 ]; then
    if [ -n "$(docker compose --project-directory "$PROJECT_DIR" ps -q php 2>/dev/null)" ]; then
        USE_LOGS=1
    else
        echo "ВНИМАНИЕ: контейнер php недоступен (project-directory: $PROJECT_DIR) —" >&2
        echo "детектор PHP-ошибок в логе отключён, проверяется только тело ответа." >&2
    fi
fi

pass=0
fail=0
skipped=0
failed_urls=()

# Сценарии ниже меняют состояние сайта (журнал авторизаций, счётчик неудач),
# поэтому против боевого адреса не выполняются. Прогон без них проверяет
# заметно меньше — засчитываем их пропущенными, чтобы итог отличался числом,
# а не только отсутствующим разделом.
SCENARIOS_TOTAL=3
if [ "$IS_LOCAL" != 1 ]; then
    skipped=$((skipped + SCENARIOS_TOTAL))
fi

while IFS=$'\t' read -r path expected_code min_size must_contain comment; do
    [ -z "${path:-}" ] && continue
    case "$path" in \#*) continue ;; esac

    log_before=0
    if [ "$USE_LOGS" = 1 ]; then
        log_before="$(docker compose --project-directory "$PROJECT_DIR" logs php --no-log-prefix 2>/dev/null | wc -l)"
    fi

    body_file="$(mktemp)"
    code="$(curl -s -o "$body_file" -w '%{http_code}' \
        -b "$COOKIE_JAR" -c "$COOKIE_JAR" \
        --max-time 30 "$BASE$path")"
    size="$(wc -c < "$body_file")"

    problems=""

    # Ожидание может перечислять несколько кодов через «|»: служебные каталоги
    # на боевой сервер не заливаются вовсе, и там, где стенд отдаёт 403 по
    # запрету, боевой отдаёт 404 по отсутствию файла — верны оба ответа.
    if ! printf '%s' "$expected_code" | tr '|' '\n' | grep -qx "$code"; then
        problems="код $code, ожидался $expected_code"
    fi

    if [ "$must_contain" != "-" ] && ! grep -qF -- "$must_contain" "$body_file"; then
        problems="$problems; нет фрагмента «$must_contain»"
    fi

    # Ошибки и размер проверяем только у успешных ответов: тело 403 формирует nginx
    if [ "$expected_code" = "200" ]; then
        if [ "$min_size" != "-" ] && [ "$size" -lt "$min_size" ]; then
            problems="$problems; ответ оборван ($size б, ожидалось от $min_size)"
        fi

        found="$(grep -ohE "$ERROR_PATTERN" "$body_file" | sort -u | tr '\n' ' ')"
        if [ -n "$found" ]; then
            problems="$problems; PHP-ошибки в теле: $found"
        fi

        if [ "$USE_LOGS" = 1 ]; then
            # PHP-FPM пишет в stderr асинхронно относительно ответа curl.
            # В этом окружении строка успевает попасть в лог контейнера
            # раньше, чем сюда доходит выполнение (проверено многократными
            # прогонами и с sleep 0 — пропусков не было), но это не гарантия
            # на другом железе или загруженном хосте, поэтому пауза оставлена
            # как подстраховка.
            sleep 0.3
            log_found="$(docker compose --project-directory "$PROJECT_DIR" logs php --no-log-prefix 2>/dev/null \
                | tail -n "+$((log_before + 1))" \
                | grep -ohE "$ERROR_PATTERN" | sort -u | tr '\n' ' ')"
            if [ -n "$log_found" ]; then
                problems="$problems; PHP-ошибки в логе: $log_found"
            fi
        fi
    fi

    if [ -z "$problems" ]; then
        pass=$((pass + 1))
        printf '  OK   %s\n' "$path"
    else
        fail=$((fail + 1))
        failed_urls+=("$path — ${problems#; }")
        printf '  FAIL %s — %s\n' "$path" "${problems#; }"
    fi

    rm -f "$body_file"
done < "$LIST"


# --- Сценарии: то, что не проверяется одним GET ---------------------------
# Только для локального стенда: неудачный вход пишет в журнал авторизаций и
# включает капчу для IP на 10 минут — на боевом сайте набор остаётся
# read-only проверкой обычных страниц.
if [ "$IS_LOCAL" = 1 ]; then
    echo
    echo "Сценарии:"
    SC_JAR="$(mktemp)"

    # Счётчик неудачных входов живёт десять минут и переживает прогон. Если его
    # не снять заранее, форма входа покажет капчу из-за прошлого запуска — и
    # набор проверит разный объём в зависимости от того, что было до него.
    if [ "$USE_LOGS" = 1 ]; then
        docker compose --project-directory "$PROJECT_DIR" exec -T php \
            sh -c 'rm -f /var/www/html/sys/tmp/cache.aut_failture*' >/dev/null 2>&1 || true
    fi

    check_scenario() {
        # $1 — название, $2 — «ок» или текст ошибки
        if [ "$2" = "ок" ]; then
            pass=$((pass + 1)); printf '  OK   %s\n' "$1"
        else
            fail=$((fail + 1)); failed_urls+=("$1 — $2"); printf '  FAIL %s — %s\n' "$1" "$2"
        fi
    }

    # 1. POST на форму входа с заведомо неверным паролем: прогоняет весь
    #    путь авторизации (хэш пароля, журнал, счётчик неудач), который
    #    обычные GET-проверки не задевают. Учётная запись не нужна.
    log_before=0
    [ "$USE_LOGS" = 1 ] && log_before="$(docker compose --project-directory "$PROJECT_DIR" logs php --no-log-prefix 2>/dev/null | wc -l)"
    body_file="$(mktemp)"
    curl -s -o "$body_file" -c "$SC_JAR" -b "$SC_JAR" --max-time 30 \
        -d "login=smoke_no_such_user&password=заведомо_неверный&ok=1" "$BASE/login.php" >/dev/null
    problems=""
    grep -qE 'не зарегистрирован|ошиблись при вводе пароля|Проверочное число' "$body_file" \
        || problems="нет ожидаемого сообщения об ошибке входа"
    found="$(grep -ohE "$ERROR_PATTERN" "$body_file" | sort -u | tr '\n' ' ')"
    [ -n "$found" ] && problems="$problems; PHP-ошибки в теле: $found"
    if [ "$USE_LOGS" = 1 ]; then
        sleep 0.3
        log_found="$(docker compose --project-directory "$PROJECT_DIR" logs php --no-log-prefix 2>/dev/null \
            | tail -n "+$((log_before + 1))" | grep -ohE "$ERROR_PATTERN" | sort -u | tr '\n' ' ')"
        [ -n "$log_found" ] && problems="$problems; PHP-ошибки в логе: $log_found"
    fi
    check_scenario "POST /login.php с неверным паролем" "${problems:-ок}"

    # 2. Отрисовка капчи. Без captcha_session скрипт выходит сразу, поэтому
    #    ключ берётся из формы, показанной после неудачного входа выше, —
    #    иначе код генерации изображения не выполняется вообще.
    CAP="$(grep -oE 'name="captcha_session" value="[^"]*"' "$body_file" | sed 's/.*value="//;s/"//' | head -1)"
    if [ -n "$CAP" ]; then
        img_file="$(mktemp)"
        ctype="$(curl -s -o "$img_file" -b "$SC_JAR" -w '%{content_type}' --max-time 30 \
            "$BASE/pages/captcha.php?captcha_session=$CAP")"
        img_size="$(wc -c < "$img_file")"
        problems=""
        case "$ctype" in image/*) ;; *) problems="тип ответа «$ctype», ожидалось image/*" ;; esac
        [ "$img_size" -lt 300 ] && problems="$problems; изображение $img_size б, ожидалось от 300"
        check_scenario "GET /pages/captcha.php с ключом сессии" "${problems:-ок}"
        rm -f "$img_file"
    else
        skipped=$((skipped + 1))
        # капча появляется в форме входа только после неудачной попытки
        # существующего пользователя — подбирать чужой логин набор не должен
        printf '  --   %s — пропущено: форма входа не показала капчу\n' \
            "GET /pages/captcha.php с ключом сессии"
    fi

    # 3. Подделанная COOKIE «запомнить меня»: проверяет, что таблица
    #    users_tokens на месте и что чужой токен не пускает на сайт.
    log_before=0
    [ "$USE_LOGS" = 1 ] && log_before="$(docker compose --project-directory "$PROJECT_DIR" logs php --no-log-prefix 2>/dev/null | wc -l)"
    fake_file="$(mktemp)"
    curl -sL -o "$fake_file" --max-time 30 \
        -b "DCMS_COOKIE_USER_TOKEN=00000000000000000000000000000000000000000000000000000000deadbeef" \
        "$BASE/" >/dev/null
    problems=""
    grep -qE 'name="password"|Авторизация' "$fake_file" || problems="подделанный токен не привёл к форме входа"
    if [ "$USE_LOGS" = 1 ]; then
        sleep 0.3
        log_found="$(docker compose --project-directory "$PROJECT_DIR" logs php --no-log-prefix 2>/dev/null \
            | tail -n "+$((log_before + 1))" | grep -ohE "$ERROR_PATTERN|user_token:" | sort -u | tr '\n' ' ')"
        [ -n "$log_found" ] && problems="$problems; в логе: $log_found"
    fi
    check_scenario "подделанная COOKIE «запомнить меня»" "${problems:-ок}"

    # Неудачный вход выше включил капчу для этого адреса на десять минут.
    # Снимаем счётчик, иначе после каждого прогона нельзя войти на стенд.
    if [ "$USE_LOGS" = 1 ]; then
        docker compose --project-directory "$PROJECT_DIR" exec -T php \
            sh -c 'rm -f /var/www/html/sys/tmp/cache.aut_failture*' >/dev/null 2>&1 || true
    fi

    rm -f "$body_file" "$fake_file" "$SC_JAR"
fi

echo
if [ "$skipped" -gt 0 ]; then
    echo "Успешно: $pass, провалено: $fail, пропущено: $skipped"
else
    echo "Успешно: $pass, провалено: $fail"
fi

if [ "$IS_LOCAL" = 1 ] && [ "$USE_LOGS" = 1 ]; then
    echo "Режим: локальный стенд — проверены страницы, сценарии входа и лог PHP."
elif [ "$IS_LOCAL" = 1 ]; then
    echo "Режим: локальный стенд без контейнера php — лог PHP не читался," >&2
    echo "предупреждения и фатальные ошибки с пустым телом ответа не видны." >&2
else
    echo "Режим: внешний адрес — проверены только страницы." >&2
    echo "Сценарии входа не выполнялись, лог PHP недоступен: тихая ошибка на" >&2
    echo "странице, вернувшей ожидаемый текст, таким прогоном не ловится." >&2
fi

if [ "$((pass + fail))" -eq 0 ]; then
    echo "Ни одной проверки не выполнено — список $LIST пуст или нечитаем." >&2
    exit 1
fi

if [ "$fail" -gt 0 ]; then
    echo
    echo "Провалившиеся:"
    for u in "${failed_urls[@]}"; do
        echo "  - $u"
    done
    exit 1
fi
