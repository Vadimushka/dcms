<?php

class document extends design {

    public $title = 'Заголовок';
    public $description = '';
    public $keywords = array();
    protected $err = array();
    protected $msg = array();
    protected $outputed = false;
    protected $actions = array();
    protected $returns = array();

    function __construct($group = 0) {
        parent::__construct();
        global $user, $dcms;
        $this->title = $dcms->title;
        if ($group > $user->group) {
            $this->access_denied(__('Доступ к данной странице запрещен'));
        }
        ob_start();
    }

    // добавление в список ошибок
    function err($err) {
        $this->err[] = array('text' => text::filter($err, 1));
    }

    // добавление в список сообщений
    function msg($msg) {
        $this->msg[] = array('text' => text::filter($msg, 1));
    }

    function ret($name, $link) {
        $this->returns[] = array($name, $link);
    }

    function act($name, $link) {
        $this->actions[] = array($name, $link);
    }

    function access_denied($err) {
        if (isset($_GET['return'])) {
            header('Refresh: 2; url=' . $_GET['return']);
        }
        $this->err($err);
        $this->output();
        exit;
    }

    private function output() {
        if ($this->outputed) {
            return false;
        }
        $this->outputed = true;

        global $dcms, $user;


        header('Cache-Control: no-store, no-cache, must-revalidate', true);
        header('Expires: ' . date('r'), true);
        // для осла (IE) как обычно отдельное условие
        if ($dcms->browser == 'Microsoft Internet Explorer') {
            header('Content-Type: text/html; charset=utf-8', true);
            header('X-UA-Compatible: IE=edge', true);
        } else {
            switch ($this->theme['content']) {
                case 'wml':header('Content-Type: text/vnd.wap.wml; charset=utf-8', true);
                    break;
                case 'xhtml':header('Content-Type: application/xhtml+xml; charset=utf-8', true);
                    break;
                default:header('Content-Type: text/html; charset=utf-8', true);
                    break;
            }
        }

        $this->assign('adt', new adt()); // реклама
        $this->assign('description', $this->description, 1); // описание страницы (meta)
        $this->assign('keywords', implode(', ', $this->keywords), 1); // ключевые слова (meta)
        $this->assign('actions', $this->actions); // ссылки к действию
        $this->assign('returns', $this->returns); // ссылки для возврата
        $this->assign('err', $this->err); // сообщения об ошибке
        $this->assign('msg', $this->msg); // сообщения
        $this->assign('title', $this->title, 1); // заголовок страницы
        $this->assign('content', @ob_get_clean());
        $this->assign('document_generation_time', round(microtime(true) - TIME_START, 3));
        debug::step('Перед формированием страницы');

        if ($dcms->align_html) {
            $document_content = $this->fetch('document.tpl');
            debug::step('Формирование страницы');
            $align = new alignedxhtml();
            echo $align->parse($document_content);
            debug::step('Форматирование HTML');
        } else {
            $this->display('document.tpl');
            debug::step('Формирование страницы');
        }


        if ($dcms->debug && $user->group == groups::max()) {
            debug::display();
        }
    }

    function clean() {
        $this->outputed = true;
        @ob_clean();
    }

    function __destruct() {

        $this->output();
    }

}

?>