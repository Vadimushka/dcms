<?php /* Smarty version Smarty-3.0.6, created on 2012-09-19 21:21:23
         compiled from "/var/www/dcmssu2462/data/www/dcms.su/sys/templates/mail.pass.tpl" */ ?>
<?php /*%%SmartyHeaderCode:17010562825059ff13050639-50683179%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '8feb56edbc2ca2481cd7400b239eb4f7ff9f7225' => 
    array (
      0 => '/var/www/dcmssu2462/data/www/dcms.su/sys/templates/mail.pass.tpl',
      1 => 1346077954,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '17010562825059ff13050639-50683179',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<html lang="ru" xml:lang="ru" xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title><?php echo $_smarty_tpl->getVariable('title')->value;?>
</title>
        <style type="text/css">
            /* <![CDATA[ */
            body{
                font-family: tahoma, arial, verdana, sans-serif, Lucida Sans;
                font-size: 14px;
            }
            /* ]]> */
        </style>
    </head>
    <body>
        <b><?php echo $_smarty_tpl->getVariable('login')->value;?>
</b>, Вы запросили восстановление пароля на сайте <b><?php echo $_smarty_tpl->getVariable('site')->value;?>
</b>.<br />
        Для ввода нового пароля перейдите по ссылке: <b><a href="<?php echo $_smarty_tpl->getVariable('url')->value;?>
"><?php echo $_smarty_tpl->getVariable('url')->value;?>
</a></b><br />
        <br />
        Браузер: <?php echo $_smarty_tpl->getVariable('dcms')->value->browser;?>
<br />
        IP: <?php echo long2ip($_smarty_tpl->getVariable('dcms')->value->ip_long);?>
<br />
    </body> 
</html>