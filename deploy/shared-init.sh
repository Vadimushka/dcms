#!/bin/bash
#
# Перенос РАБОТАЮЩЕГО сайта на схему releases/current: разовая операция.
#
# Для установки с нуля скрипт не нужен — выкат сам создаёт каталоги, а
# настройки появляются, когда владелец проходит мастер в /install/.
#
# Запускается на сервере под www-root:
#
#   deploy/shared-init.sh <каталог сайта>
#
# Сейчас сайт лежит прямо в каталоге сайта. Скрипт создаёт рядом releases,
# shared и backups и КОПИРУЕТ в shared всё, что движок пишет во время работы.
# Копирует, а не переносит: до переключения docroot сайт продолжает работать
# со старого места, и ломать его нельзя. Повторный запуск (до переключения)
# просто освежает копию.
#
set -euo pipefail

APP_DIR="${1:-}"
SRC="${2:-$APP_DIR}"   # откуда брать данные; по умолчанию — сам каталог сайта

die() { echo "ОШИБКА: $*" >&2; exit 1; }
say() { echo "==> $*"; }

[ -n "$APP_DIR" ] || die "укажите каталог сайта, например /var/www/www-root/data/www/dcms.vadimushka-d.ru"
[ -d "$APP_DIR" ] || die "каталог не найден: $APP_DIR"
[ -f "$SRC/index.php" ] || die "в $SRC нет index.php — это не каталог работающего сайта"

SHARED="$APP_DIR/shared"

mkdir -p "$APP_DIR/releases" "$SHARED" "$APP_DIR/backups"

# Каталоги с данными: загрузки, кэш, логи, настройки
for d in sys/files sys/tmp sys/logs sys/ini; do
    [ -d "$SRC/$d" ] || continue
    mkdir -p "$SHARED/$d"
    say "копирую $d"
    rsync -a "$SRC/$d/" "$SHARED/$d/"
done

# Отдельные файлы, которые создаёт сам движок
for f in sys/languages/for_translate.lng sys/themes/.common/icons.css sys/themes/.common/icons.png; do
    [ -f "$SRC/$f" ] || continue
    mkdir -p "$SHARED/$(dirname "$f")"
    say "копирую $f"
    cp -a "$SRC/$f" "$SHARED/$f"
done

# Подтверждения прав на домен лежат в корне сайта и в репозиторий не входят
shopt -s nullglob
for f in "$SRC"/yandex_*.html "$SRC"/google*.html; do
    say "копирую $(basename "$f")"
    cp -a "$f" "$SHARED/"
done
shopt -u nullglob

[ -f "$SHARED/sys/ini/settings.ini" ] || die "в shared не оказалось sys/ini/settings.ini — проверьте $SRC"

chmod 600 "$SHARED/sys/ini/settings.ini"
[ -f "$SHARED/sys/ini/iv.dat" ] && chmod 600 "$SHARED/sys/ini/iv.dat"

say "готово. Содержимое shared:"
du -sh "$SHARED"/* 2>/dev/null | sed 's/^/    /'

cat <<EOF

Дальше:
  1. Выкатить релиз (GitHub Actions → Deploy), он ляжет в $APP_DIR/releases/
     и подключит к себе эти данные симлинками.
  2. Переключить docroot сайта в ISPmanager на $APP_DIR/current
     и версию PHP на 8.1+ (на сервере есть 8.5).
  3. Убедиться, что сайт работает, и только потом убирать старые файлы
     из корня $APP_DIR — до этого они остаются рабочей копией на откат.
EOF
