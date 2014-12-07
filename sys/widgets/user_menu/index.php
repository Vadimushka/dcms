<?php
if (current_user::getInstance()->group){
    $menu = new menu('user');
    $menu->display();
}
