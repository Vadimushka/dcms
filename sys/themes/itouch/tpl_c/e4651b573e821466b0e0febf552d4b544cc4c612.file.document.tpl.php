<?php /* Smarty version Smarty-3.0.6, created on 2012-09-16 21:14:58
         compiled from "/var/www/dcmssu2462/data/www/dcms.su/sys/themes/itouch/tpl/document.tpl" */ ?>
<?php /*%%SmartyHeaderCode:13746214350560912399fa3-25985837%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'e4651b573e821466b0e0febf552d4b544cc4c612' => 
    array (
      0 => '/var/www/dcmssu2462/data/www/dcms.su/sys/themes/itouch/tpl/document.tpl',
      1 => 1339315062,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '13746214350560912399fa3-25985837',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<!DOCTYPE html>
<html>
    <head>
        <title><?php echo for_value($_smarty_tpl->getVariable('title')->value);?>
</title>
        <link rel="shortcut icon" href="/favicon.ico" />
        <link rel="stylesheet" href="/sys/themes/style.css" type="text/css" />
        <link rel="stylesheet" href="<?php echo $_smarty_tpl->getVariable('path')->value;?>
/style.css" type="text/css" />
        <link rel="stylesheet" href="<?php echo $_smarty_tpl->getVariable('path')->value;?>
/jquery.mobile-1.1.0.css" type="text/css" />
        <script charset="utf-8" src="<?php echo $_smarty_tpl->getVariable('path')->value;?>
/jquery-1.7.1.min.js" type="text/javascript"></script>
        <script charset="utf-8" src="<?php echo $_smarty_tpl->getVariable('path')->value;?>
/jquery.mobile-1.1.0.js" type="text/javascript"></script>
        <meta name="viewport" content="width=device-width, initial-scale=1" /> 
    <?php if ($_smarty_tpl->getVariable('description')->value){?><meta name="description" content="<?php echo for_value($_smarty_tpl->getVariable('description')->value);?>
" /><?php }?>
<?php if ($_smarty_tpl->getVariable('keywords')->value){?><meta name="keywords" content="<?php echo for_value($_smarty_tpl->getVariable('keywords')->value);?>
" /><?php }?>
</head>
<body>
    <div data-role="page">
        <?php $_template = new Smarty_Internal_Template("inc.title.tpl", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php $_template->updateParentVariables(0);?><?php unset($_template);?>        
        <?php $_template = new Smarty_Internal_Template("inc.user.tpl", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php $_template->updateParentVariables(0);?><?php unset($_template);?>
        <?php $_template = new Smarty_Internal_Template("inc.adt.top.tpl", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php $_template->updateParentVariables(0);?><?php unset($_template);?>
        <div data-role="content">
            <?php echo $_smarty_tpl->getVariable('content')->value;?>

        </div>

        <?php $_template = new Smarty_Internal_Template("inc.foot.tpl", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php $_template->updateParentVariables(0);?><?php unset($_template);?>   
        
<?php $_template = new Smarty_Internal_Template("inc.adt.bottom.tpl", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php $_template->updateParentVariables(0);?><?php unset($_template);?>
    </div>
</body>
</html>