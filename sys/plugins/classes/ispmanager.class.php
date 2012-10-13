<?php

class ispmanager {

    protected $_url = 'https://dcms.su/manager/ispmgr';
    protected $_session = false;

    function __construct($url = null) {
        if ($url)
            $this->_url = $url;
        $this->_session = &$_SESSION['ispmanager_session'];
    }

    function isAutorized() {
        if (!$this->_session)
            return false;
        $data = $this->getData('diskusage', array('auth' => $this->_session));
        if ($data->error)
            return false;
        return true;
    }

    function sessionReset() {
        $this->_session = null;
    }

    function setSession($session) {
        $this->sessionReset();
        $this->_session = $session;

        if (!$this->isAutorized()) {
            $this->sessionReset();
            return false;
        }

        return true;
    }

    function login($login, $pass) {
        $this->sessionReset();
        $data = $this->getData('auth', array('username' => $login, 'password' => $pass));
        if (!is_a($data, 'SimpleXMLElement'))
            return false;

        if ($data->authfail)
            return false;

        if ($data->auth) {
            $session = (string) $data->auth;
            return $this->_session = $session;
        }
        return false;
    }

    function getData($func, $params = array()) {
        $params = (array) $params;
        if (!array_key_exists('out', $params))
            $params['out'] = 'xml';
        $params['func'] = $func;
        if ($this->_session)
            $params['auth'] = $this->_session;

        $get_query = array();
        foreach ($params AS $key => $value) {
            $get_query[] = urlencode($key) . '=' . urlencode($value);
        }

        $http = new http_client($this->_url . '?' . implode('&', $get_query));
        $content = $http->getContent();

        switch ($params['out']) {
            case 'xml': return simplexml_load_string($content);
                break;
            case 'json': return json_decode($content);
                break;
            default :return $content;
                break;
        }
    }
    

}

?>
