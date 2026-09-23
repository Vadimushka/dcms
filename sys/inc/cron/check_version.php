<?php
/**
 * Проверка наличия новой версии движка.
 *
 * Движок только сообщает администраторам о выпуске: файлы заливаются вручную.
 * Автоматическая установка обновлений убрана вместе с сервером сборок dcms.su.
 */
if ($dcms->update_auto && $dcms->update_auto_time && !cache_events::get('system.update.check')) {
    cache_events::set('system.update.check', true, $dcms->update_auto_time);

    $check = new \Dcms\Helpers\UpdateCheck($dcms->update_url);

    if (!$release = $check->getNewer($dcms->version)) {
        if ($error = $check->getError()) {
            misc::log($error, 'system.update');
        }
    } elseif ($dcms->update_auto_notified != $release['version']) {
        $dcms->update_auto_notified = $release['version'];
        $dcms->save_settings();

        $mess = __('Вышла новая версия DCMS: %s. [url=%s]Что изменилось[/url]', $release['version'], $release['url']);

        /** @var $admin user */
        foreach (groups::getAdmins() AS $admin) {
            $admin->mess($mess);
        }

        unset($mess, $admin);
    }

    unset($check, $release, $error);
}
