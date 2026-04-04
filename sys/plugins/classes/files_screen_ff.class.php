<?php

/**
 * Получение скриншота при помощи ffmpeg (CLI)
 */
class files_screen_ff {

    protected $_path_abs;

    function __construct($path_abs) {
        $this->_path_abs = $path_abs;
    }

    /**
     * Проверка доступности ffmpeg
     * @return bool
     */
    public static function isAvailable() {
        exec('ffmpeg -version 2>&1', $output, $code);
        return $code === 0;
    }

    /**
     * Получение длительности видео в секундах
     * @return float|false
     */
    protected function _getDuration() {
        $path = escapeshellarg($this->_path_abs);
        $cmd = "ffprobe -v error -show_entries format=duration -of default=noprint_wrappers=1:nokey=1 $path 2>/dev/null";
        $duration = trim(shell_exec($cmd));
        return is_numeric($duration) ? (float)$duration : false;
    }

    /**
     * Получение массива скриншотов из видео
     * @return boolean|array
     */
    public function getScreen() {
        if (!self::isAvailable())
            return false;

        $duration = $this->_getDuration();
        if (!$duration || $duration <= 0)
            return false;

        $screens = array();
        $k_kadr = 6;
        $path = escapeshellarg($this->_path_abs);

        for ($i = 1; $i <= $k_kadr; $i++) {
            $time = ($duration / ($k_kadr + 1)) * $i;
            $tmp = tempnam(sys_get_temp_dir(), 'ffscreen_') . '.png';
            $tmp_escaped = escapeshellarg($tmp);

            $cmd = "ffmpeg -ss $time -i $path -frames:v 1 -y $tmp_escaped 2>/dev/null";
            exec($cmd, $output, $code);

            if ($code === 0 && is_file($tmp)) {
                $gd_image = @imagecreatefrompng($tmp);
                unlink($tmp);
                if ($gd_image) {
                    $screens[] = $gd_image;
                }
            } elseif (is_file($tmp)) {
                unlink($tmp);
            }
        }

        return !empty($screens) ? $screens : false;
    }

}