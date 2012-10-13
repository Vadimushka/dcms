<?php /* Smarty version Smarty-3.0.6, created on 2012-09-20 18:52:40
         compiled from "/var/www/dcmssu2462/data/www/dcms.su/sys/templates/mail.news.tpl" */ ?>
<?php /*%%SmartyHeaderCode:815472636505b2db801f5c9-03779228%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '98d2b8101ca29206bad43752ac1bd95f773da703' => 
    array (
      0 => '/var/www/dcmssu2462/data/www/dcms.su/sys/templates/mail.news.tpl',
      1 => 1339312834,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '815472636505b2db801f5c9-03779228',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<html lang="ru" xml:lang="ru" xmlns="http://www.w3.org/1999/xhtml">
 <head>
 <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title><?php echo for_value($_smarty_tpl->getVariable('title')->value);?>
 - <?php echo for_value($_smarty_tpl->getVariable('site')->value);?>
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
  Уведомляем Вас о новостях:<br />
  <?php echo $_smarty_tpl->getVariable('content')->value;?>

 </body>
 
</html>