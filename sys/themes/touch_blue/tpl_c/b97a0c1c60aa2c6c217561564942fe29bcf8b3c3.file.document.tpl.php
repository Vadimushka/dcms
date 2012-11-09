<?php /* Smarty version Smarty-3.0.6, created on 2012-11-09 20:49:28
         compiled from "E:/DESURE/Dropbox/domains/dcms7dev/sys/themes/touch_blue/tpl/document.tpl" */ ?>
<?php /*%%SmartyHeaderCode:28796509d34184edfa1-49468659%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'b97a0c1c60aa2c6c217561564942fe29bcf8b3c3' => 
    array (
      0 => 'E:/DESURE/Dropbox/domains/dcms7dev/sys/themes/touch_blue/tpl/document.tpl',
      1 => 1352479766,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '28796509d34184edfa1-49468659',
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
        <script charset="utf-8" src="/sys/themes/system.js" type="text/javascript"></script>
        <script charset="utf-8" src="<?php echo $_smarty_tpl->getVariable('path')->value;?>
/user.js" type="text/javascript"></script>
        <link rel="stylesheet" href="/sys/themes/system.css" type="text/css" />
        <link rel="stylesheet" href="<?php echo $_smarty_tpl->getVariable('path')->value;?>
/style.css" type="text/css" />
        <meta http-equiv="content-Type" content="application/xhtml+xml; charset=utf-8" />
    <?php if ($_smarty_tpl->getVariable('description')->value){?><meta name="description" content="<?php echo for_value($_smarty_tpl->getVariable('description')->value);?>
" /><?php }?>
<?php if ($_smarty_tpl->getVariable('keywords')->value){?><meta name="keywords" content="<?php echo for_value($_smarty_tpl->getVariable('keywords')->value);?>
" /><?php }?>

<style>
    .hide {
        display: none !important;
    }
</style>
</head>
<body>
    <div>
        <?php $_template = new Smarty_Internal_Template("inc.title.tpl", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php $_template->updateParentVariables(0);?><?php unset($_template);?>   
        <?php $_template = new Smarty_Internal_Template("inc.user.tpl", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php $_template->updateParentVariables(0);?><?php unset($_template);?>

        <div class="content">
            <?php $_template = new Smarty_Internal_Template("inc.adt.top.tpl", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php $_template->updateParentVariables(0);?><?php unset($_template);?> 
            <?php echo $_smarty_tpl->getVariable('content')->value;?>


        </div>
        <?php $_template = new Smarty_Internal_Template("inc.foot.tpl", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php $_template->updateParentVariables(0);?><?php unset($_template);?>   
        <?php $_template = new Smarty_Internal_Template("inc.adt.bottom.tpl", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php $_template->updateParentVariables(0);?><?php unset($_template);?>
        <div class="foot">
            <?php echo $_smarty_tpl->getVariable('lang')->value->getString("Время генерации страницы");?>
: <?php echo $_smarty_tpl->getVariable('document_generation_time')->value;?>
 сек<br />
            <?php echo output_text($_smarty_tpl->getVariable('dcms')->value->copyright);?>

        </div>

    </div>
</body>
</html>