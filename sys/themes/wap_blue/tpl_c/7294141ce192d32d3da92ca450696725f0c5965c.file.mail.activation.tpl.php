<?php /* Smarty version Smarty-3.0.6, created on 2012-09-17 02:13:31
         compiled from "/var/www/dcmssu2462/data/www/dcms.su/sys/templates/mail.activation.tpl" */ ?>
<?php /*%%SmartyHeaderCode:120236969550564f0bc54fa4-38571191%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '7294141ce192d32d3da92ca450696725f0c5965c' => 
    array (
      0 => '/var/www/dcmssu2462/data/www/dcms.su/sys/templates/mail.activation.tpl',
      1 => 1339312834,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '120236969550564f0bc54fa4-38571191',
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
</b>, Вы успешно зарегистрированы на сайте <b><?php echo $_smarty_tpl->getVariable('site')->value;?>
</b>.<br />
  Пароль для входа: <b><?php echo $_smarty_tpl->getVariable('password')->value;?>
</b><br />
  Для активации аккаунта необходимо перейти по ссылке: <b><a href="<?php echo $_smarty_tpl->getVariable('url')->value;?>
"><?php echo $_smarty_tpl->getVariable('url')->value;?>
</a></b><br />
 </body>
 
</html>