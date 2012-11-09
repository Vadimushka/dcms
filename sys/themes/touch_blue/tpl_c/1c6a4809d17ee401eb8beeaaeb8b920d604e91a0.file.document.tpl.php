<?php /* Smarty version Smarty-3.0.6, created on 2012-11-02 20:04:41
         compiled from "E:/DESURE/Dropbox/domains/dcms7dev/sys/themes/pda_blue/tpl/document.tpl" */ ?>
<?php /*%%SmartyHeaderCode:226565093ef19ba20e0-35483432%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '1c6a4809d17ee401eb8beeaaeb8b920d604e91a0' => 
    array (
      0 => 'E:/DESURE/Dropbox/domains/dcms7dev/sys/themes/pda_blue/tpl/document.tpl',
      1 => 1339315072,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '226565093ef19ba20e0-35483432',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $_smarty_tpl->getVariable('lang')->value->xml_lang;?>
">
<head>
<title><?php echo for_value($_smarty_tpl->getVariable('title')->value);?>
</title>
<link rel="shortcut icon" href="/favicon.ico" />
<link rel="stylesheet" href="/sys/themes/style.css" type="text/css" />
<link rel="stylesheet" href="<?php echo $_smarty_tpl->getVariable('path')->value;?>
/style.css" type="text/css" />
<meta http-equiv="content-Type" content="application/xhtml+xml; charset=utf-8" />
<?php if ($_smarty_tpl->getVariable('description')->value){?><meta name="description" content="<?php echo for_value($_smarty_tpl->getVariable('description')->value);?>
" /><?php }?>
<?php if ($_smarty_tpl->getVariable('keywords')->value){?><meta name="keywords" content="<?php echo for_value($_smarty_tpl->getVariable('keywords')->value);?>
" /><?php }?>
</head>
<body>
<div>
<?php $_template = new Smarty_Internal_Template("inc.title.tpl", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php $_template->updateParentVariables(0);?><?php unset($_template);?>
<?php $_template = new Smarty_Internal_Template("inc.adt.top.tpl", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php $_template->updateParentVariables(0);?><?php unset($_template);?>
<?php $_template = new Smarty_Internal_Template("inc.user.tpl", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php $_template->updateParentVariables(0);?><?php unset($_template);?>
<?php echo $_smarty_tpl->getVariable('content')->value;?>

<?php $_template = new Smarty_Internal_Template("inc.foot.tpl", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php $_template->updateParentVariables(0);?><?php unset($_template);?>
<?php $_template = new Smarty_Internal_Template("inc.adt.bottom.tpl", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php $_template->updateParentVariables(0);?><?php unset($_template);?>

<?php echo $_smarty_tpl->getVariable('lang')->value->getString("Время генерации страницы");?>
: <?php echo $_smarty_tpl->getVariable('document_generation_time')->value;?>
 сек<br />
<?php echo output_text($_smarty_tpl->getVariable('dcms')->value->copyright);?>

</div>
</body>
</html>