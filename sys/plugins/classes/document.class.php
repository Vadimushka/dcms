<?php

/**
 * Класс для формирования HTML документа.
 */
class document extends design
{
    public $title = 'Заголовок';
    public $description = '';
    public $keywords = array();
    public $last_modified = null;

    protected $err = array();
    protected $msg = array();
    protected $outputed = false;
    protected $actions = array();
    protected $returns = array();
    protected $tabs = array();

    function __construct($group = 0)
    {
        parent::__construct();
        global $user, $dcms;
        $this->title = __($dcms->title); // локализированое название сайта
        if ($group > $user->group) {
            $this->access_denied(__('Доступ к данной странице запрещен'));
        }
        ob_start();
    }

    /**
     * @param $name
     * @param $url
     * @param bool $selected
     * @return document_link
     */
    function tab($name, $url, $selected = false)
    {
        return $this->tabs[] = new document_link(text::toValue($name), $url, $selected);
    }

    /**
     * @param $name
     * @param $url
     * @return document_link
     */
    function ret($name, $url)
    {
        return $this->returns[] = new document_link(text::toValue($name), $url);
    }

    /**
     * @param $name
     * @param $url
     * @return document_link
     */
    function act($name, $url)
    {
        return $this->actions[] = new document_link(text::toValue($name), $url);
    }

    /**
     * @param $text
     * @return document_message
     */
    function err($text)
    {
        return $this->err[] = new document_message($text, true);
    }

    /**
     * @param $text
     * @return document_message
     */
    function msg($text)
    {
        return $this->msg[] = new document_message($text);
    }

    /**
     * Отображение страницы с ошибкой
     * @param string $err Текст ошибки
     */
    function access_denied($err)
    {
        if (isset($_GET['return'])) {
            header('Refresh: 2; url=' . $_GET['return']);
        }
        $this->err($err);
        $this->output();
        exit;
    }

    /**
     * Формирование HTML документа и отправка данных браузеру
     * @global dcms $dcms
     */
    private function output()
    {
        global $dcms;
        if ($this->outputed) {
            // повторная отправка html кода вызовет нарушение синтаксиса документа, да и вообще нам этого нафиг не надо
            return;
        }
        $this->outputed = true;
        header('Cache-Control: no-store, no-cache, must-revalidate', true);
        header('Expires: ' . date('r'), true);
        if ($this->last_modified)
            header("Last-Modified: " . gmdate("D, d M Y H:i:s", (int)$this->last_modified) . " GMT", true);

        header('X-UA-Compatible: IE=edge', true); // отключение режима совместимости в осле
        header('Content-Type: text/html; charset=utf-8', true);

        $this->assign('adt', new adt()); // реклама
        $this->assign('description', $this->description ? $this->description : $this->title, 1); // описание страницы (meta)
        $this->assign('keywords', $this->keywords ? implode(',', $this->keywords) : $this->title, 1); // ключевые слова (meta)

        $this->assign('actions', $this->actions); // ссылки к действию
        $this->assign('returns', $this->returns); // ссылки для возврата
        $this->assign('tabs', $this->tabs); // вкладки

        $this->assign('err', $this->err); // сообщения об ошибке
        $this->assign('msg', $this->msg); // сообщения
        $this->assign('title', $this->title, 1); // заголовок страницы
        $this->assign('content', ob_get_clean()); // то, что попало в буфер обмена при помощи echo (display())
        $this->assign('document_generation_time', round(microtime(true) - TIME_START, 3)); // время генерации страницы

        if ($dcms->align_html) {
            // форматирование HTML кода
            $document_content = $this->fetch('document.tpl');
            $align = new alignedxhtml();
            echo $align->parse($document_content);
        } else {
            $this->display('document.tpl');
        }

    }

    /**
     * Очистка вывода
     * Тема оформления применяться не будет
     */
    function clean()
    {
        $this->outputed = true;
        ob_clean();
    }

    /**
     * То что срабатывает при exit
     */
    function __destruct()
    {
        $this->output();
    }

}