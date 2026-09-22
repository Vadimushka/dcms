#!/usr/bin/env bash
# Активация релиза на сервере. Запускается из CI по ssh под www-root:
#
#   ssh "$REMOTE" "APP=... SHA=... bash -s" < deploy/activate.sh
#
# Код релиза к этому моменту уже лежит в $APP/releases/$SHA. Скрипт подключает
# к нему изменяемые данные из shared, снимает дамп базы, атомарно переключает
# симлинк current, сбрасывает OPcache и убирает устаревшие релизы.
set -euo pipefail

: "${APP:?}" "${SHA:?}"
KEEP_RELEASES="${KEEP_RELEASES:-5}"
KEEP_DUMPS="${KEEP_DUMPS:-20}"

REL="$APP/releases/$SHA"
SHARED="$APP/shared"

say() { echo "==> $*"; }

[ -d "$REL" ] || { echo "нет каталога релиза: $REL" >&2; exit 1; }
[ -f "$REL/index.php" ] || { echo "в релизе нет index.php — выкат приехал неполным" >&2; exit 1; }
[ -f "$SHARED/sys/ini/settings.ini" ] || { echo "нет $SHARED/sys/ini/settings.ini — сначала deploy/shared-init.sh" >&2; exit 1; }

# ---------------------------------------------------------------------------
# Данные, которые переживают выкат: всё это движок пишет во время работы.
# ---------------------------------------------------------------------------

# Загрузки пользователей, кэш и логи
for d in sys/files sys/tmp sys/logs; do
    mkdir -p "$SHARED/$d"
    rm -rf "${REL:?}/$d"
    ln -sfn "$SHARED/$d" "$REL/$d"
done

# Настройки — каталогом целиком: движок переписывает не только settings.ini,
# но и меню, смайлы и набор виджетов, которые правятся из админки. Файлы,
# появившиеся в релизе впервые, досыпаем в shared — иначе новый ini никогда
# не доедет до сайта; существующие не трогаем, там правки владельца.
cp -n "$REL/sys/ini"/* "$SHARED/sys/ini/" 2>/dev/null || true
rm -rf "${REL:?}/sys/ini"
ln -sfn "$SHARED/sys/ini" "$REL/sys/ini"

# Файлы, которые создаёт сам сайт: словарь непереведённых строк и спрайты иконок
for f in sys/languages/for_translate.lng sys/themes/.common/icons.css sys/themes/.common/icons.png; do
    [ -e "$SHARED/$f" ] || continue
    mkdir -p "$(dirname "$REL/$f")"
    rm -f "$REL/$f"
    ln -sfn "$SHARED/$f" "$REL/$f"
done

# Подтверждения прав на домен: в репозитории их нет, а потерять нельзя
shopt -s nullglob
for f in "$SHARED"/yandex_*.html "$SHARED"/google*.html; do
    ln -sfn "$f" "$REL/$(basename "$f")"
done
shopt -u nullglob

say "изменяемые данные подключены из shared"

# ---------------------------------------------------------------------------
# Дамп базы до переключения: откат кода мгновенный, откат данных — только отсюда
# ---------------------------------------------------------------------------
BACKUPS="$APP/backups"
mkdir -p "$BACKUPS"

# Параметры подключения читаем тем же парсером, что и движок: своя регулярка
# по ini-файлу рано или поздно разойдётся с ним.
creds="$(php -r '
    $i = parse_ini_file($argv[1]);
    foreach (array("mysql_host", "mysql_base", "mysql_user", "mysql_pass") as $k)
        printf("%s\n", isset($i[$k]) ? $i[$k] : "");
' "$SHARED/sys/ini/settings.ini")"
{ read -r DB_HOST; read -r DB_BASE; read -r DB_USER; read -r DB_PASS; } <<< "$creds"
[ -n "$DB_BASE" ] || { echo "в settings.ini не задана база" >&2; exit 1; }

DUMP="$BACKUPS/db-$(date +%Y%m%d-%H%M%S)-before-$SHA.sql.gz"
say "снимаю дамп базы «$DB_BASE»"
# Пароль уходит переменной окружения: в списке процессов MYSQL_PWD не виден
MYSQL_PWD="$DB_PASS" mysqldump \
    --single-transaction --routines --events --default-character-set=utf8mb4 \
    -h "$DB_HOST" -u "$DB_USER" "$DB_BASE" | gzip > "$DUMP"

# gzip в конвейере молчит об оборванном дампе, поэтому проверяем явно
gzip -t "$DUMP"
tables="$(zcat "$DUMP" | grep -c '^CREATE TABLE' || true)"
[ "$tables" -gt 0 ] || { echo "в дампе нет ни одной таблицы: $DUMP" >&2; exit 1; }
say "таблиц в дампе: $tables, размер: $(du -h "$DUMP" | cut -f1)"

# shellcheck disable=SC2012
ls -1t "$BACKUPS"/db-*.sql.gz 2>/dev/null | tail -n "+$((KEEP_DUMPS + 1))" | xargs -r rm -f

# ---------------------------------------------------------------------------
# Переключение
# ---------------------------------------------------------------------------

# Прежний релиз запоминаем до переключения — по нему работает deploy/rollback.sh
if [ -L "$APP/current" ]; then
    readlink -f "$APP/current" > "$APP/.previous-release"
fi

# Панель ispmanager проверяет docroot при сохранении настроек сайта и создаёт
# отсутствующие каталоги. Если current оказался реальным каталогом, mv -T не
# сможет положить на его место симлинк — убираем.
if [ -d "$APP/current" ] && [ ! -L "$APP/current" ]; then
    rm -rf "$APP/current"
fi

rm -f "$APP/current.new"
ln -sfn "$REL" "$APP/current.new"
mv -T "$APP/current.new" "$APP/current"
say "current -> $(readlink -f "$APP/current")"

# ---------------------------------------------------------------------------
# Сброс OPcache. Путь current/index.php между релизами не меняется, поэтому
# воркеры php-fpm продолжают исполнять код прошлого релиза из кеша. Под www-root
# перезапустить php-fpm нельзя, а до сокета пула не достать по правам — остаётся
# одноразовый запрос через веб.
# ---------------------------------------------------------------------------
SITE="$(basename "$APP")"
TOKEN="$(head -c 12 /dev/urandom | od -An -tx1 | tr -d ' \n')"
RESET="$APP/current/__opcache-$TOKEN.php"
trap 'rm -f "$RESET"' EXIT

printf '%s' '<?php if (function_exists("opcache_reset")) { opcache_reset(); } clearstatcache(true); echo "ok";' > "$RESET"

RESET_OUT="$(curl -sS --max-time 20 "https://$SITE/__opcache-$TOKEN.php" 2>&1 || true)"
rm -f "$RESET"
trap - EXIT

if [ "$RESET_OUT" != "ok" ]; then
    echo "не удалось сбросить OPcache (ответ: $RESET_OUT)" >&2
    echo "сайт будет отдавать прошлый релиз, пока кеш не сброшен" >&2
    exit 1
fi
say "OPcache сброшен"

# Кэш движка от прошлого релиза остаётся в shared/sys/tmp и может содержать
# структуры, которых в новом коде уже нет. Файлы вида public.* не трогаем:
# на них ссылаются отданные наружу адреса.
find "$SHARED/sys/tmp" -maxdepth 1 -type f ! -name 'public.*' -delete 2>/dev/null || true
say "кэш движка очищен"

# Оставляем последние релизы; текущий и предыдущий не трогаем в любом случае
KEEP_PATHS=("$(readlink -f "$APP/current")")
[ -f "$APP/.previous-release" ] && KEEP_PATHS+=("$(cat "$APP/.previous-release")")
# shellcheck disable=SC2012
for old in $(ls -1dt "$APP"/releases/*/ 2>/dev/null | tail -n "+$((KEEP_RELEASES + 1))"); do
    old="${old%/}"
    skip=0
    for k in "${KEEP_PATHS[@]}"; do
        [ "$(readlink -f "$old")" = "$k" ] && skip=1
    done
    [ "$skip" = 1 ] && continue
    say "убираю старый релиз $(basename "$old")"
    rm -rf "$old"
done

echo "активирован релиз $SHA"
