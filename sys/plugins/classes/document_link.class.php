<?php

class document_link
{
    public
        $url,
        $name,
        $selected;

    function __construct($name, $url, $selected = false)
    {
        $this->name = $name;
        $this->url = $url;
        $this->selected = $selected;
    }
}
