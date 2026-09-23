<?php

namespace Dcms\Helpers;

/**
 * Проверка наличия новой версии движка.
 *
 * Спрашивает у GitHub последний выпущенный релиз и сравнивает его номер с
 * версией установленной сборки. Ничего не скачивает и не переписывает:
 * архив забирает и заливает администратор сайта вручную. Прежний механизм
 * умел ставить обновление сам, но требовал права записи в каталог с кодом
 * и сборки пакетов на сайте разработчика, который больше не отвечает.
 */
class UpdateCheck
{
    /**
     * Откуда берутся сведения о релизах. Форку движка этот адрес нужно
     * сменить в настройках на свой репозиторий, иначе он будет получать
     * уведомления о чужих выпусках.
     */
    const DEFAULT_URL = 'https://api.github.com/repos/Vadimushka/dcms/releases/latest';

    /** Сколько ждём ответа, секунд */
    const TIMEOUT = 10;

    protected $_url;
    protected $_error = '';

    /**
     * @param string $url адрес с описанием последнего релиза
     */
    public function __construct($url = '')
    {
        $this->_url = self::normalizeUrl($url);
    }

    /**
     * Приведение сохранённого адреса к рабочему.
     *
     * Установки, пережившие переход с оригинального DCMS, хранят в
     * настройках адрес на dcms.su: сайт не отвечает с 2019 года, а его
     * пакеты несовместимы с PHP 8. Такой адрес заменяется на текущий.
     *
     * @param string $url
     * @return string
     */
    public static function normalizeUrl($url)
    {
        $url = trim((string) $url);

        if ($url === '' || stripos($url, 'dcms.su') !== false) {
            return self::DEFAULT_URL;
        }

        return $url;
    }

    /**
     * @return string адрес, к которому обращается проверка
     */
    public function getUrl()
    {
        return $this->_url;
    }

    /**
     * Последний выпущенный релиз.
     *
     * @return array|false ['version' => '8.1.0.0', 'url' => 'https://…'] либо
     *                     false, если сведения получить не удалось
     */
    public function getLatest()
    {
        $this->_error = '';

        if (!$content = $this->_request()) {
            return false;
        }

        try {
            $release = Json::parse($content);
        } catch (\Exception $e) {
            $this->_error = 'Ответ сервера обновлений не разобран: ' . $e->getMessage();
            return false;
        }

        if (!is_array($release) || empty($release['tag_name'])) {
            $this->_error = 'В ответе сервера обновлений нет номера версии';
            return false;
        }

        $version = ltrim((string) $release['tag_name'], 'vV');

        if (!preg_match('/^\d+(\.\d+)*/', $version)) {
            $this->_error = 'Номер версии не распознан: ' . $version;
            return false;
        }

        return array(
            'version' => $version,
            'url' => empty($release['html_url']) ? $this->_url : (string) $release['html_url'],
        );
    }

    /**
     * Новее ли выпущенный релиз, чем установленная сборка.
     *
     * @param string $current текущая версия движка
     * @return array|false описание релиза либо false
     */
    public function getNewer($current)
    {
        if (!$release = $this->getLatest()) {
            return false;
        }

        return version_compare($release['version'], $current, '>') ? $release : false;
    }

    /**
     * @return string причина, по которой проверка не удалась
     */
    public function getError()
    {
        return $this->_error;
    }

    /**
     * Запрос к серверу обновлений.
     *
     * Собственный HttpClient здесь не годится: он не понимает
     * ни chunked-ответов, ни переадресации, а GitHub отдаёт и то, и другое.
     *
     * @return string|false тело ответа
     */
    protected function _request()
    {
        $headers = array(
            'Accept: application/vnd.github+json',
            'X-GitHub-Api-Version: 2022-11-28',
            // без User-Agent GitHub отвечает 403
            'User-Agent: DCMS update check',
        );

        if (function_exists('curl_init')) {
            return $this->_requestCurl($headers);
        }

        return $this->_requestStream($headers);
    }

    /**
     * @param array $headers
     * @return string|false
     */
    protected function _requestCurl($headers)
    {
        $curl = curl_init($this->_url);
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 3,
            CURLOPT_CONNECTTIMEOUT => self::TIMEOUT,
            CURLOPT_TIMEOUT => self::TIMEOUT,
            CURLOPT_HTTPHEADER => $headers,
        ));

        $content = curl_exec($curl);
        $code = (int) curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
        $error = curl_error($curl);

        if ($content === false) {
            $this->_error = 'Сервер обновлений недоступен: ' . $error;
            return false;
        }

        if ($code !== 200) {
            $this->_error = 'Сервер обновлений ответил кодом ' . $code;
            return false;
        }

        return $content;
    }

    /**
     * @param array $headers
     * @return string|false
     */
    protected function _requestStream($headers)
    {
        $context = stream_context_create(array(
            'http' => array(
                'method' => 'GET',
                'header' => implode("\r\n", $headers),
                'timeout' => self::TIMEOUT,
                'follow_location' => 1,
                'max_redirects' => 3,
                'ignore_errors' => true,
            ),
        ));

        // читаем потоком: заголовки ответа берутся из метаданных, а не из
        // переменной $http_response_header, объявленной устаревшей в PHP 8.5
        $handle = @fopen($this->_url, 'rb', false, $context);

        if (!$handle) {
            $this->_error = 'Сервер обновлений недоступен';
            return false;
        }

        $meta = stream_get_meta_data($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        $code = 0;
        foreach ((array) ($meta['wrapper_data'] ?? array()) as $header) {
            if (preg_match('~^HTTP/\S+\s+(\d{3})~', $header, $match)) {
                $code = (int) $match[1];
            }
        }

        if ($code !== 200) {
            $this->_error = 'Сервер обновлений ответил кодом ' . $code;
            return false;
        }

        if ($content === false) {
            $this->_error = 'Ответ сервера обновлений не прочитан';
            return false;
        }

        return $content;
    }
}
