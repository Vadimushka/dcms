#!/usr/bin/env bash
# Возврат на предыдущий релиз. Запускается по ssh под www-root:
#
#   ssh "$REMOTE" "APP=... bash -s" < deploy/rollback.sh
#
# Откатывает только код. База к этому моменту уже могла измениться — дампы,
# снятые перед каждым выкатом, лежат в $APP/backups.
set -euo pipefail

: "${APP:?}"
PREV_FILE="$APP/.previous-release"

say() { echo "==> $*"; }

[ -f "$PREV_FILE" ] || { echo "нечего откатывать: нет $PREV_FILE" >&2; exit 1; }
PREV="$(cat "$PREV_FILE")"
[ -d "$PREV" ] || { echo "предыдущий релиз уже удалён: $PREV" >&2; exit 1; }
[ -f "$PREV/index.php" ] || { echo "в предыдущем релизе нет index.php: $PREV" >&2; exit 1; }

# Текущий релиз становится «предыдущим»: откат можно отменить тем же скриптом
if [ -L "$APP/current" ]; then
    readlink -f "$APP/current" > "$PREV_FILE.new"
fi

rm -f "$APP/current.new"
ln -sfn "$PREV" "$APP/current.new"
mv -T "$APP/current.new" "$APP/current"
[ -f "$PREV_FILE.new" ] && mv -f "$PREV_FILE.new" "$PREV_FILE"
say "current -> $(readlink -f "$APP/current")"

# Без сброса OPcache воркеры продолжат исполнять код, на который мы только что
# перестали ссылаться: путь current/index.php не изменился
SITE="$(basename "$APP")"
TOKEN="$(head -c 12 /dev/urandom | od -An -tx1 | tr -d ' \n')"
RESET="$APP/current/__opcache-$TOKEN.php"
trap 'rm -f "$RESET"' EXIT

printf '%s' '<?php if (function_exists("opcache_reset")) { opcache_reset(); } clearstatcache(true); echo "ok";' > "$RESET"
RESET_OUT="$(curl -sS --max-time 20 "https://$SITE/__opcache-$TOKEN.php" 2>&1 || true)"
rm -f "$RESET"
trap - EXIT

[ "$RESET_OUT" = "ok" ] || { echo "не удалось сбросить OPcache (ответ: $RESET_OUT)" >&2; exit 1; }
say "OPcache сброшен"

echo "откат выполнен: $(basename "$PREV")"
