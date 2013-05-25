<?php

class native_templating {

    public $cache_template = true; // кэширование шаблона в памяти. Используется eval вместо include
    protected $_dir_template = ''; // папка с файлами шаблонов
    protected $_assigned = array(); // переменные, которые будут переданы в шаблон

    function __construct() {
        
    }

    /**
     * Передача переменной в шаблон
     * @param type $name
     * @param type $value
     * @return type
     */
    public function assign($name, $value = null, $filter = 0) {
        if (is_array($name)) {
            foreach ($name as $key => $value) {
                $this->assign($key, $value);
            }
            return;
        }
        if (is_scalar($name))
            $this->_assigned[$name] = text::filter($value, $filter);
    }

    /**
     * Получение обработанного шаблона
     * @param type $tpl_file
     * @return null
     */
    public function fetch($tpl_file) {
        if (($tpl_path = $this->_getTemplatePath($tpl_file)) === false) {
            return null;
        }
        extract($this->_assigned);
        ob_start();
        if ($this->cache_template)
            @eval('?>' . $this->_getTemplate($tpl_path));
        else
            @include $tpl_path;

        $content = ob_get_clean();
        //ob_end_clean();
        return $content;
    }

    /**
     * выводим обработанный шаблон
     * @param type $tpl_file
     */
    public function display($tpl_file) {
        echo $this->fetch($tpl_file);
    }

    /**
     * получение пути к файлу шаблона
     * @param type $tpl_name
     * @return boolean
     */
    protected function _getTemplatePath($tpl_name) {
        if (strpos($tpl_name, 'file:') === 0) {
            $abs_path = text::substr($tpl_name, 256, 5, '');
            $tpl_path = dirname($abs_path) . '/' . basename($abs_path, '.tpl') . '.tpl.php';
        } elseif ($this->_dir_template) {
            $tpl_path = $this->_dir_template . '/' . basename($tpl_name, '.tpl') . '.tpl.php';
        } else {
            $tpl_path = $tpl_name;
        }

        if (!file_exists($tpl_path)) {
            return false;
        }

        return $tpl_path;
    }

    /**
     * Получение содержимого шаблона (по возможности из кэша)
     * @staticvar array $templates
     * @param type $tpl_path
     * @return type
     */
    protected function _getTemplate($tpl_path) {
        static $templates = array();
        if (!array_key_exists($tpl_path, $templates)) {
            $templates[$tpl_path] = file_get_contents($tpl_path);
        }
        return $templates[$tpl_path];
    }

    /**
     * Перебирает массив, вставляя значения ключей в шаблон
     * @param array $array входной массив. первый уровень перебирается, ключи второго используются для вставки в шаблон значений
     * @param string $tpl шаблон вида <a href="{url}">{name}</a>
     * @param boolean $reverse
     * @return string
     */
    protected function section($array, $tpl, $reverse = false) {
        $return = '';
        if ($reverse)
            $array = array_reverse($array);
        foreach ($array AS $data) {
            $return.=$this->replace($data, $tpl);
        }
        return $return;
    }

    /**
     * Вставка произвольных данных из массива в шаблон
     * @param array $data ассоциативный массив вида $data = array('url' => 'http://...', 'name' => 'Название ссылки')
     * @param string $tpl шаблон вида <a href="{url}">{name}</a>
     * @return string
     */
    protected function replace($data, $tpl) {
        $keys = array();
        $values = array();
        foreach ($data AS $key => $value) {
            $keys[] = '{' . $key . '}';
            $values[] = $value;
        }
        return str_replace($keys, $values, $tpl);
    }

}

?>
