#!/bin/bash
#
# Проверка установщика DCMS на локальном стенде.
#
#   bash tests/install/run.sh
#
# Проходит мастер установки от выбора языка до сохранения настроек, ставит
# движок в отдельную базу и следит, чтобы ни один шаг не записал в лог PHP
# предупреждение. Установщик прячет ошибки от посетителя (install/index.php
# очищает буфер вывода на завершении), поэтому единственный честный источник —
# лог контейнера.
#
# Рабочий стенд не страдает: настройки сохраняются и возвращаются, временная
# база удаляется — в том числе если прогон прервать.
#
set -uo pipefail

PROJECT_DIR="${PROJECT_DIR:-$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)}"
BASE="${BASE:-http://127.0.0.1:8090}"
TEST_DB="${TEST_DB:-dcms_install_check}"
ADMIN_LOGIN="installcheck"
ADMIN_PASS="install12345"

SETTINGS="$PROJECT_DIR/sys/ini/settings.ini"
STASH="$(mktemp)"
JAR="$(mktemp)"
BODY="$(mktemp)"

dc() { docker compose --project-directory "$PROJECT_DIR" "$@"; }
mysql_root() { dc exec -T mysql sh -c "mysql -u root -p\"\$MYSQL_ROOT_PASSWORD\" $*" 2>/dev/null; }

restore() {
    [ -s "$STASH" ] && cp -f "$STASH" "$SETTINGS"
    mysql_root "-e 'DROP DATABASE IF EXISTS \`$TEST_DB\`;'" >/dev/null
    rm -f "$STASH" "$JAR" "$BODY"
}
trap restore EXIT

[ -f "$SETTINGS" ] || { echo "нет $SETTINGS — стенд не настроен" >&2; exit 1; }
dc ps -q php >/dev/null 2>&1 || { echo "стенд не поднят: docker compose up -d" >&2; exit 1; }

# Если настройки уже указывают на временную базу, значит прошлый прогон
# прервали до восстановления: сохранять такой файл как эталон нельзя — он
# затрёт рабочие настройки стенда безвозвратно.
if grep -qE "^mysql_base\s*=\s*\"?$TEST_DB\"?" "$SETTINGS"; then
    echo "в $SETTINGS прописана временная база «$TEST_DB» — восстановите настройки стенда" >&2
    exit 1
fi

cp -f "$SETTINGS" "$STASH"

fail=0
log_pos() { dc logs php --no-log-prefix 2>/dev/null | wc -l; }

current_step() {
    curl -s -b "$JAR" -c "$JAR" "$BASE/install/" -o "$BODY"
    grep -oE '<h1>[^<]*</h1>' "$BODY" | head -1 | sed 's/<[^>]*>//g'
}

# $1 — название шага, дальше поля формы
step() {
    local name="$1"; shift
    local before after problems
    before="$(log_pos)"

    local args=()
    local f
    for f in "$@"; do args+=(-d "$f"); done
    args+=(-d "next_step=1")

    curl -s -b "$JAR" -c "$JAR" -X POST "${args[@]}" "$BASE/install/" -o /dev/null
    after="$(current_step)"

    problems="$(dc logs php --no-log-prefix 2>/dev/null | tail -n "+$((before + 1))" \
        | grep -oE 'PHP (Warning|Deprecated|Fatal|Parse|Notice)[^\\]*' | sort -u | head -3 | tr '\n' ' ')"

    if [ -n "$problems" ]; then
        fail=$((fail + 1))
        printf '  FAIL %-24s %s\n' "$name" "$problems"
    else
        printf '  OK   %-24s → %s\n' "$name" "${after:-переход на сайт}"
    fi
}

echo "Установка в базу «$TEST_DB»"
mysql_root "-e 'DROP DATABASE IF EXISTS \`$TEST_DB\`; CREATE DATABASE \`$TEST_DB\` CHARACTER SET utf8mb4;'" >/dev/null

# Пока файл настроек на месте, установщик уводит на главную
rm -f "$SETTINGS"

step "выбор языка"            "language=russian"
step "приветствие"
step "лицензия"               "license_accept=1"
step "проверка платформы"
step "права на запись"
step "загрузка предустановок"
step "подключение к базе"     "mysql_host=mysql" "mysql_user=root" "mysql_pass=dcms" "mysql_base=$TEST_DB"
step "поиск старой версии"
step "загрузка таблиц"
step "регистрация админа"     "login=$ADMIN_LOGIN" "password=$ADMIN_PASS" "password_retry=$ADMIN_PASS"
step "сохранение настроек"

echo
echo "Результат установки:"

if [ -f "$SETTINGS" ]; then
    printf '  OK   %-24s\n' "settings.ini создан"
else
    fail=$((fail + 1))
    printf '  FAIL %-24s установка не дошла до конца\n' "settings.ini"
fi

tables="$(mysql_root "-N -e 'SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = \"$TEST_DB\";'" | tr -d '[:space:]')"
if [ "${tables:-0}" -ge 30 ]; then
    printf '  OK   %-24s %s\n' "таблицы созданы" "$tables"
else
    fail=$((fail + 1))
    printf '  FAIL %-24s создано %s, ожидалось не меньше 30\n' "таблицы" "${tables:-0}"
fi

# Свежепоставленный сайт должен открываться и пускать заведённого администратора
codes_ok=1
for path in / /login.php /forum/ /files/; do
    code="$(curl -s -o /dev/null -w '%{http_code}' "$BASE$path")"
    [ "$code" = "200" ] || { codes_ok=0; echo "      $path → $code"; }
done
if [ "$codes_ok" = 1 ]; then
    printf '  OK   %-24s\n' "страницы отвечают"
else
    fail=$((fail + 1))
    printf '  FAIL %-24s\n' "страницы отвечают"
fi

# Счётчик неудачных входов лежит в sys/tmp и не зависит от базы: он переживает
# установку и заставляет форму требовать капчу, из-за чего проверка входа
# провалилась бы на ровном месте.
dc exec -T php sh -c 'rm -f /var/www/html/sys/tmp/cache.aut_failture*' >/dev/null 2>&1

rm -f "$JAR"; JAR="$(mktemp)"
curl -s -c "$JAR" -b "$JAR" "$BASE/login.php" -o /dev/null
login_code="$(curl -s -c "$JAR" -b "$JAR" -X POST \
    -d "login=$ADMIN_LOGIN&password=$ADMIN_PASS" "$BASE/login.php" \
    -o "$BODY" -w '%{http_code}')"

# Признак входа берём не с главной: она отдаётся из кэша движка и сразу после
# входа ещё показывает страницу гостя. Форма входа вошедшему недоступна —
# login.php уводит его переадресацией, и это состояние сессии, а не кэша.
after_login="$(curl -s -o /dev/null -b "$JAR" -c "$JAR" -w '%{http_code}' "$BASE/login.php")"

if [ "$login_code" = "302" ] && [ "$after_login" = "302" ]; then
    printf '  OK   %-24s\n' "вход администратора"
else
    fail=$((fail + 1))
    reason="$(grep -oE 'не зарегистрирован|ошиблись при вводе пароля|Проверочное число введено неверно|Аккаунт не активирован|Ошибка при получении профиля' "$BODY" | head -1)"
    printf '  FAIL %-24s вход %s, повторный заход на форму %s%s\n' \
        "вход администратора" "$login_code" "$after_login" "${reason:+, «$reason»}"
fi

echo
if [ "$fail" -gt 0 ]; then
    echo "Провалено проверок: $fail"
    exit 1
fi
echo "Установщик отработал без единого предупреждения"
