<?php

class url
{
    protected
        $_params = array(),
        $_path = "";

    /**
     * @param string $url
     * @param array $params
     */
    function __construct($url = null, $params = array())
    {
        if (is_null($url)){
            $url = $_SERVER['REQUEST_URI'];
        }
        $this->_parseUrl($url);
        $this->_params = array_merge($this->_params, $params);
    }

    private function _parseUrl($url)
    {
        $parsed = parse_url($url);
        $this->_path = $parsed['path'];
        if (array_key_exists('query', $parsed)) {
            $query_parts = preg_split('/&(amp;)?/', $parsed['query']);
            for ($i = 0; $i < count($query_parts); $i++) {
                $query_part = explode('=', $query_parts[$i], 2);
                $this->setParam(urldecode($query_part[0]), isset($query_part[1]) ? urldecode($query_part[1]) : null);
            }
        }
    }

    public function __toString()
    {
        return $this->getUrl();
    }

    public function getParam($name, $default = null)
    {
        if (!array_key_exists($name, $this->_params)) return $default;
        return $this->_params[$name];
    }

    public function setParam($name, $value)
    {
        $this->_params[$name] = $value;
        return $this;
    }

    public function removeParam($name)
    {
        unset($this->_params[$name]);
        return $this;
    }

    public function setPath($path)
    {
        $this->_path = $path;
        return $this;
    }

    public function getUrl()
    {
        $url = $this->_path;
        if ($this->_params) {
            $params_query = array();
            foreach ($this->_params AS $key => $value) {
                $params_query[] = urlencode($key).(is_null($value) ? : '='.urlencode($value));
            }
            $url .= '?'.implode('&', $params_query);
        }
        return $url;
    }
}