<?php
/**
 * Created by PhpStorm.
 * User: DES
 * Date: 26.12.2015
 * Time: 2:41
 */

namespace Dcms\Helpers;

/**
 * Своя реализация работы по HTTP протоколу (аналог cUrl)
 * Умеет отправлять GET, POST, COOKIE, FILES
 * Также можно задать Referer, User-Agent
 * Можно работать через прокси (HTTP, SOCKS4, SOCKS5)
 */
class HttpClient
{
    public $ua = 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/535.11 (KHTML, like Gecko) Chrome/17.0.963.83 Safari/535.11';
    public $timeout = 20; // таймаут запроса (сек)
    public $referer = '';
    public $accept = '*/*';
    public $accept_encoding = 'identity';
    public $accept_charset = 'windows-1251,utf-8;q=0.7,*;q=0.3';
    public $accept_language = 'ru-RU,ru;q=0.8,en-US;q=0.6,en;q=0.4';
    protected $_url;
    protected $_post = array(); // массив с POST
    protected $_cookie = array(); // массив с COOKIE
    protected $_files = array(); //
    protected $_sock = null;
    protected $_http_proxy = false; // HTTP прокси
    protected $_socks_proxy = false; // SOCKS прокси
    protected $_boundary = false; // разделитель для отправляемых данных
    public $errn;
    public $errs;

    /**
     * @param string|Url $url
     */
    function __construct($url)
    {
        if ($url instanceof Url) {
            $url = $url->getUrl();
        }
        $this->_url = $url;
        $this->dataReset();
    }

    /**
     * Сброс всех данных для запроса
     */
    public function dataReset()
    {
        $this->_boundary = Text::generateRandomString(32);
        $this->_post = array();
        $this->_cookie = array();
        $this->_sock = null;
        $this->errn = '';
        $this->errs = '';
    }

    /**
     * Подключение к сокету хоста или прокси сервера
     * @return bool
     * @throws \Exception
     */
    protected function _connect()
    {

        if ($this->_http_proxy) {
            $purl = @parse_url($this->_http_proxy);
        } else {
            $purl = @parse_url($this->_url);
        }

        if (!empty($purl['host'])) {
            $port = empty($purl['port']) ? 80 : $purl['port'];

            if (empty($purl['scheme'])) {
                $scheme = '';
            } elseif ($purl['scheme'] == 'https') {
                $scheme = 'ssl://';
                $port = '443';
            } else {
                $scheme = '';
            }


            if ($this->_socks_proxy) {
                $spurl = @parse_url($this->_socks_proxy);
                $sport = empty($spurl['port']) ? 9050 : $spurl['port'];
                if (!$this->_sock = @fsockopen('tcp://' . $spurl['host'], $sport, $this->errn, $this->errs,
                    $this->timeout)
                ) {
                    throw new \Exception($spurl['host'] . ' (Прокси-сервер) - Не удалось подключиться');
                } else {

                    $packet = "\x05\x01\x00";
                    fwrite($this->_sock, $packet, strlen($packet));
                    $response = fread($this->_sock, 3);

                    fwrite($this->_sock,
                        "\x05\x01\x00\x03" . chr(strlen($purl['host'])) . $purl['host'] . pack("n", $port));

                    $response = fread($this->_sock, 2048);

                    if (ord($response[0]) == 5) {
                        $status = ord($response[1]);
                        if ($status != 0) {
                            throw new \Exception($spurl['host'] . ' (Прокси-сервер) - Не удалось подключиться');
                        }
                    } else {
                        throw new \Exception($spurl['host'] . ' (Прокси-сервер) - Не удалось подключиться');
                    }
                }
            }

            if (!$this->_sock = @fsockopen($scheme . $purl['host'], $port, $this->errn, $this->errs, $this->timeout)) {
                throw new \Exception($scheme . $purl['host'] . ($this->_http_proxy ? ' (Прокси-сервер)' : '') . ' - Не удалось подключиться');
            } else {
                stream_set_timeout($this->_sock, $this->timeout);
            }
        }
        return true;
    }

    /**
     * Закрытие подключения
     */
    protected function _disconnect()
    {
        fclose($this->_sock);
    }

    /**
     * Установка прокси-сервера
     * @param string $proxy Адрес прокси
     * @param boolean $socks используется socks
     */
    public function setProxy($proxy, $socks = true){
        if ($proxy === false) {
            $this->_http_proxy = false;
            $this->_socks_proxy = false;
        }
        if ($socks) {
            $this->_socks_proxy = $proxy;
        } else {
            $this->_http_proxy = $proxy;
        }
    }

    /**
     * Установка прокси-сервера
     * @param string $proxy Адрес прокси
     * @param boolean $socks используется socks
     * @deprecated
     */
    public function set_proxy($proxy, $socks = true)
    {
        $this->setProxy($proxy, $socks);
    }

    /**
     * Установка POST переменной
     * @param string $name Имя
     * @param string $value Значение
     * @deprecated
     */
    public function set_post($name, $value = '')
    {
        $this->setPost($name, $value);
    }

    /**
     * Установка POST переменной
     * @param string $name Имя
     * @param string $value Значение
     */
    public function setPost($name, $value = '')
    {
        $this->_post[$name] = $value;
    }

    /**
     * Установка COOKIE
     * @param string $name Имя
     * @param string $value Значение
     * @deprecated
     */
    public function set_cookie($name, $value = null)
    {
        $this->setCookie($name, $value);
    }
    /**
     * Установка COOKIE
     * @param string $name Имя
     * @param string $value Значение
     */
    public function setCookie($name, $value = null)
    {
        $this->_cookie[] = urlencode($name) . '=' . urlencode($value);
    }

    /**
     * Прикрепление файла для отправки
     * @param string $name Имя поля, в котором будет передаваться файл
     * @param string $path Путь к файлу на сервере
     * @param boolean $filename Имя файла
     * @return boolean
     * @deprecated
     */
    public function set_file($name, $path, $filename = false)
    {
        return $this->setFile($name, $path, $filename);
    }
    /**
     * Прикрепление файла для отправки
     * @param string $name Имя поля, в котором будет передаваться файл
     * @param string $path Путь к файлу на сервере
     * @param boolean $filename Имя файла
     * @return boolean
     */
    public function setFile($name, $path, $filename = false)
    {

        if (!$content = @file_get_contents($path)) {
            return false;
        }

        if ($filename === false) {
            $filename = basename($path);
        }


        $this->_files[$name] = array('name' => $filename, 'content' => $content);
        return true;
    }

    /**
     * Сохранение полученных данных в файл
     * @param string $file_path путь к файлу на сервере
     * @param int $max_size максимальный размер принимаемых данных
     * @return bool
     * @throws \Exception
     * @deprecated
     */
    public function save_content($file_path, $max_size = 0)
    {
        return $this->saveContent($file_path, $max_size);
    }

    /**
     * Сохранение полученных данных в файл
     * @param string $file_path путь к файлу на сервере
     * @param int $max_size максимальный размер принимаемых данных
     * @return bool
     * @throws \Exception
     */
    public function saveContent($file_path, $max_size = 0)
    {
        if (!$fo = @fopen($file_path, 'wb')) {
            @unlink($file_path);
            throw new \Exception('Не удалось открыть указатель ресурса');
        }
        if (!$this->_connect()) {
            @unlink($file_path);
            throw new \Exception('Соединение разорвано');
        }

        fputs($this->_sock, $this->getOutputHeaders());

        $headers = '';
        while (!feof($this->_sock)) {
            $data = fgets($this->_sock, 2048);
            if ($data == "\r\n") {
                break;
            }
            $headers .= $data;
        }

        $saved = 0;
        while (!feof($this->_sock)) {
            if ($data = fgets($this->_sock, 4096)) {
                $saved += strlen($data);
                if ($max_size && $saved > $max_size) {
                    @unlink($file_path);
                    throw new \Exception('Превышено ограничение на длину скачиваемого файла');
                }

                if (!@fwrite($fo, $data)) {
                    @unlink($file_path);
                    throw new \Exception('Не удалось сохранить файл');
                }
            }
        }

        $size = filesize($file_path);
        if (!$size) {
            throw new \Exception('Получен файл с нулевым размером');
        }

        fclose($fo);
        $this->_disconnect();
        return (bool)$size;
    }

    /**
     * Получение имени файла из ответа
     * @return string
     */
    public function getFileName()
    {
        $headers = $this->getHeaders();
        if ($headers) {
            if (preg_match('/filename=(.+?);/', $headers, $m)) {
                return $m[1];
            }
        }

        $path = @parse_url($this->_url);
        return basename($path['path']);
    }

    /**
     * Получение заголовков ответа
     * @return string
     * @throws \Exception
     */
    public function getHeaders()
    {
        if (!$this->_connect()) {
            throw new \Exception('Соединение разорвано');
        }

        $output_headers = $this->getOutputHeaders();
        fputs($this->_sock, $output_headers, strlen($output_headers));
        $headers = '';
        while (!feof($this->_sock)) {
            $data = fgets($this->_sock, 2048);
            if ($data == "\r\n") {
                break;
            }
            $headers .= $data;
        }

        $this->_disconnect();
        return $headers;
    }

    /**
     * Получение содержимого ответа
     * @param boolean $with_headers включать заголовки
     * @return string
     * @throws \Exception
     */
    public function getContent($with_headers = false)
    {
        if (!$this->_connect()) {
            throw new \Exception('Соединение разорвано');
        }
        $output_headers = $this->getOutputHeaders();
        fputs($this->_sock, $output_headers, strlen($output_headers));

        $headers = '';
        while (!feof($this->_sock)) {
            $data = fgets($this->_sock, 2048);
            if ($data == "\r\n") {
                break;
            }
            $headers .= $data;
        }

        if ($with_headers) {
            $content = $headers . "\r\n";
        } else {
            $content = '';
        }

        while (!feof($this->_sock)) {
            if ($data = fgets($this->_sock, 4096)) {
                $content .= $data;
            }
        }

        $this->_disconnect();
        return $content;
    }

    /**
     * Получение содержимого ответа
     * @param boolean $with_headers включать заголовки
     * @return string
     * @throws \Exception
     * @deprecated
     */
    public function get_content($with_headers = false)
    {
        return $this->getContent($with_headers);
    }

    /**
     * Получение заголовков ответа
     * @return string
     * @throws \Exception
     * @deprecated
     */
    public function get_headers()
    {
        return $this->getHeaders();
    }

    /**
     * формирование данных для multipart/form-data
     * @return string
     */
    protected function _multipart()
    {
        $data = array();

        if ($this->_post) {
            foreach ($this->_post as $key => $value) {
                $data[] = "--{$this->_boundary}\r\nContent-Disposition: form-data; name='" . urlencode($key) . "'\r\n\r\n$value";
            }
        }

        if ($this->_files) {
            foreach ($this->_files as $name => $value) {
                $data[] = "--{$this->_boundary}\r\nContent-Disposition: form-data; name=\"" . urlencode($name) . "\"; filename=\"" . urlencode($value['name']) . "\"\r\nContent-Type: application/octet-stream\r\nContent-Transfer-Encoding: binary\r\n\r\n{$value['content']}";
            }
        }

        return implode("\r\n", $data) . "\r\n--{$this->_boundary}--\r\n";
    }

    /**
     * Получение заголовков запроса
     * @return string
     */
    public function getOutputHeaders()
    {
        $headers = array();
        $purl = @parse_url($this->_url);
        $scheme = empty($purl['scheme']) ? 'http' : $purl['scheme'];
        $host = empty($purl['host']) ? '' : $purl['host'];

        if ($this->_http_proxy) $path = $scheme . '://' . $host . (empty($purl['path']) ? '/' : $purl['path']);
        else $path = empty($purl['path']) ? '/' : $purl['path'];

        $query = empty($purl['query']) ? '' : '?' . $purl['query'];
        $headers[] = ($this->_post ? 'POST' : 'GET') . ' ' . $path . $query . ' HTTP/1.0';
        $headers[] = 'Host: ' . $host;

        if ($this->accept) $headers[] = 'Accept: ' . $this->accept;
        if ($this->accept_charset) $headers[] = 'Accept-Charset: ' . $this->accept_charset;
        if ($this->accept_encoding) $headers[] = 'Accept-Encoding: ' . $this->accept_encoding;
        if ($this->accept_language) $headers[] = 'Accept-Language: ' . $this->accept_language;
        if ($this->referer) $headers[] = 'Referer: ' . $this->referer;
        if ($this->_cookie) $headers[] = 'Cookie: ' . implode(';', $this->_cookie);
        if ($this->ua) $headers[] = 'User-Agent: ' . $this->ua;

        if ($this->_files || $this->_post) {
            $post_data = $this->_multipart();
            $headers[] = 'Content-Type: multipart/form-data; boundary=' . $this->_boundary;
            $headers[] = 'Content-Length: ' . strlen($post_data);
        }

        if (isset($purl['user']) && isset($purl['pass']))
            $headers[] = 'Authorization: Basic ' . base64_encode($purl['user'] . ':' . $purl['pass']);

        $headers[] = 'Connection: Close';

        $header = implode("\r\n", $headers) . "\r\n\r\n";

        if (!empty($post_data)) $header .= $post_data;

        return $header;
    }
}