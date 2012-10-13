<?php /* Smarty version Smarty-3.0.6, created on 2012-10-13 13:39:36
         compiled from "E:/DESURE/Dropbox/domains/dcms7dev/sys/themes/web_blue/tpl/document.tpl" */ ?>
<?php /*%%SmartyHeaderCode:7444507936d8878a55-60782515%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '2ca8ec478323ebd5eedc7cc990dc8303d6469b5f' => 
    array (
      0 => 'E:/DESURE/Dropbox/domains/dcms7dev/sys/themes/web_blue/tpl/document.tpl',
      1 => 1347808400,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '7444507936d8878a55-60782515',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php if (!is_callable('smarty_block_php')) include 'E:\DESURE\Dropbox\domains\dcms7dev\sys\plugins\smarty\plugins\block.php.php';
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $_smarty_tpl->getVariable('lang')->value->xml_lang;?>
">
    <head>
        <title><?php echo for_value($_smarty_tpl->getVariable('title')->value);?>
</title>
        <link rel="shortcut icon" href="/favicon.ico" />
        <link rel="stylesheet" href="/sys/themes/system.css" type="text/css" />        
        <link rel="stylesheet" type="text/css" href="<?php echo $_smarty_tpl->getVariable('path')->value;?>
/style.css" />
        <meta http-equiv="Сontent-Type" content="application/xhtml+xml; charset=utf-8" />
    <?php if ($_smarty_tpl->getVariable('description')->value){?><meta name="description" content="<?php echo for_value($_smarty_tpl->getVariable('description')->value);?>
" /><?php }?>

<?php if ($_smarty_tpl->getVariable('keywords')->value){?><meta name="keywords" content="<?php echo for_value($_smarty_tpl->getVariable('keywords')->value);?>
" /><?php }?>
<script charset="utf-8" src="<?php echo $_smarty_tpl->getVariable('path')->value;?>
/jquery-1.7.2.min.js" type="text/javascript"></script>
<script charset="utf-8" src="<?php echo $_smarty_tpl->getVariable('path')->value;?>
/jquery-ui-1.8.20.custom.min.js" type="text/javascript"></script>
<script charset="utf-8" src="<?php echo $_smarty_tpl->getVariable('path')->value;?>
/jquery.sound.js" type="text/javascript"></script>
<script charset="utf-8" src="<?php echo $_smarty_tpl->getVariable('path')->value;?>
/custom.js" type="text/javascript"></script>        
<script>
    
    page_time = <?php echo time();?>
;
    // фразы, используемые в JavaScript
    lang_smiles = '<?php echo $_smarty_tpl->getVariable('lang')->value->getString("Смайлы");?>
';
    lang_smiles_hide = '<?php echo $_smarty_tpl->getVariable('lang')->value->getString("Скрыть смайлы");?>
';
    lang_smiles_show = '<?php echo $_smarty_tpl->getVariable('lang')->value->getString("Показать смайлы");?>
';
    lang_smiles_load = '<?php echo $_smarty_tpl->getVariable('lang')->value->getString("Загрузка смайлов");?>
';
    lang_smiles_load_err = '<?php echo $_smarty_tpl->getVariable('lang')->value->getString("Не удалось загрузить смайлы");?>
';
    lang_mail = '<?php echo $_smarty_tpl->getVariable('lang')->value->getString("Почта");?>
';
    lang_friends = '<?php echo $_smarty_tpl->getVariable('lang')->value->getString("Друзья");?>
';
    lang_server_return_ok = '<?php echo $_smarty_tpl->getVariable('lang')->value->getString("Данные успешно сохранены");?>
';
    lang_server_return_err = '<?php echo $_smarty_tpl->getVariable('lang')->value->getString("Сервер вернул ошибку");?>
';    
</script>    
<script>
    // спойлеры скрываем с помощью JavaScript, чтобы в случае его отключения они оказались открытыми
        <![CDATA[
            $('head').append('<style type="text/css">div.spoiler div.spoiler_content{ display: none; }</style>');
        ]]>
</script>
</head>
<body> 
    <div class="head">
        <div class="body">
            <div class="title">
                <h1>
                    <?php echo for_value($_smarty_tpl->getVariable('title')->value);?>

                </h1>
                <div class="user">

                    <?php unset($_smarty_tpl->tpl_vars['smarty']->value['section']['i']);
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['name'] = 'i';
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['loop'] = is_array($_loop=$_smarty_tpl->getVariable('actions')->value) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['show'] = true;
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['max'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['loop'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['step'] = 1;
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['start'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['step'] > 0 ? 0 : $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['loop']-1;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['i']['show']) {
    $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['total'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['loop'];
    if ($_smarty_tpl->tpl_vars['smarty']->value['section']['i']['total'] == 0)
        $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['show'] = false;
} else
    $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['total'] = 0;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['i']['show']):

            for ($_smarty_tpl->tpl_vars['smarty']->value['section']['i']['index'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['start'], $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['iteration'] = 1;
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['iteration'] <= $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['total'];
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['index'] += $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['step'], $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['iteration']++):
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['rownum'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['iteration'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['index_prev'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['index'] - $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['index_next'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['index'] + $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['first']      = ($_smarty_tpl->tpl_vars['smarty']->value['section']['i']['iteration'] == 1);
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['last']       = ($_smarty_tpl->tpl_vars['smarty']->value['section']['i']['iteration'] == $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['total']);
?><a href="<?php echo $_smarty_tpl->getVariable('actions')->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']][1];?>
"><?php echo $_smarty_tpl->getVariable('actions')->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']][0];?>
</a>
                    <?php endfor; endif; ?>    

                    <?php if ($_smarty_tpl->getVariable('user')->value->id){?>
                        <script>
                            USER.message_file_path = '<?php echo $_smarty_tpl->getVariable('path')->value;?>
/sound.swf';
                            USER.id = <?php echo $_smarty_tpl->getVariable('user')->value->id;?>
;
                            USER.count_mail = <?php echo $_smarty_tpl->getVariable('user')->value->mail_new_count;?>
;
                            USER.count_friends = <?php echo $_smarty_tpl->getVariable('user')->value->friend_new_count;?>
;
                            USER.updateBar(false);
                            USER.getData();
                        </script>
                        <a id='friends' style='font-weight: bold; display: none' href='/my.friends.php'><?php echo $_smarty_tpl->getVariable('lang')->value->getString("Друзья");?>
</a>
                        <a id='mail' style='font-weight: bold; display: none' href='/my.mail.php?only_unreaded'><?php echo $_smarty_tpl->getVariable('lang')->value->getString("Почта");?>
</a>
                        <a id='menu_user' style='font-weight: bold;' href="/menu.user.php"><?php echo $_smarty_tpl->getVariable('user')->value->login;?>
</a>
                    <?php }else{ ?>
                        <a href="/login.php?return=<?php echo $_smarty_tpl->getVariable('URL')->value;?>
"><?php echo $_smarty_tpl->getVariable('lang')->value->getString("Авторизация");?>
</a>
                        <a href="/reg.php?return=<?php echo $_smarty_tpl->getVariable('URL')->value;?>
"><?php echo $_smarty_tpl->getVariable('lang')->value->getString("Регистрация");?>
</a>
                    <?php }?>
                </div>
            </div>
            <?php if ($_SERVER['SCRIPT_NAME']!='/index.php'){?>
                <div class="returns"> 
                    <span><span><a href='/'><?php echo $_smarty_tpl->getVariable('lang')->value->getString("На главную");?>
</a></span></span>    
                    <?php unset($_smarty_tpl->tpl_vars['smarty']->value['section']['i']);
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['name'] = 'i';
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['loop'] = is_array($_loop=$_smarty_tpl->getVariable('returns')->value) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['step'] = ((int)-1) == 0 ? 1 : (int)-1;
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['show'] = true;
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['max'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['loop'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['start'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['step'] > 0 ? 0 : $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['loop']-1;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['i']['show']) {
    $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['total'] = min(ceil(($_smarty_tpl->tpl_vars['smarty']->value['section']['i']['step'] > 0 ? $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['loop'] - $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['start'] : $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['start']+1)/abs($_smarty_tpl->tpl_vars['smarty']->value['section']['i']['step'])), $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['max']);
    if ($_smarty_tpl->tpl_vars['smarty']->value['section']['i']['total'] == 0)
        $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['show'] = false;
} else
    $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['total'] = 0;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['i']['show']):

            for ($_smarty_tpl->tpl_vars['smarty']->value['section']['i']['index'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['start'], $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['iteration'] = 1;
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['iteration'] <= $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['total'];
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['index'] += $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['step'], $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['iteration']++):
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['rownum'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['iteration'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['index_prev'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['index'] - $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['index_next'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['index'] + $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['first']      = ($_smarty_tpl->tpl_vars['smarty']->value['section']['i']['iteration'] == 1);
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['last']       = ($_smarty_tpl->tpl_vars['smarty']->value['section']['i']['iteration'] == $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['total']);
?>
                        <span><span><a href="<?php echo $_smarty_tpl->getVariable('returns')->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']][1];?>
"><?php echo $_smarty_tpl->getVariable('returns')->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']][0];?>
</a></span></span>
                    <?php endfor; endif; ?>
                </div>
            <?php }?>
        </div>
    </div>

    <div class="body">
        <div class="messages">
            <?php unset($_smarty_tpl->tpl_vars['smarty']->value['section']['i']);
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['name'] = 'i';
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['loop'] = is_array($_loop=$_smarty_tpl->getVariable('err')->value) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['show'] = true;
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['max'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['loop'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['step'] = 1;
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['start'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['step'] > 0 ? 0 : $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['loop']-1;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['i']['show']) {
    $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['total'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['loop'];
    if ($_smarty_tpl->tpl_vars['smarty']->value['section']['i']['total'] == 0)
        $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['show'] = false;
} else
    $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['total'] = 0;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['i']['show']):

            for ($_smarty_tpl->tpl_vars['smarty']->value['section']['i']['index'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['start'], $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['iteration'] = 1;
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['iteration'] <= $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['total'];
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['index'] += $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['step'], $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['iteration']++):
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['rownum'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['iteration'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['index_prev'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['index'] - $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['index_next'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['index'] + $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['first']      = ($_smarty_tpl->tpl_vars['smarty']->value['section']['i']['iteration'] == 1);
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['last']       = ($_smarty_tpl->tpl_vars['smarty']->value['section']['i']['iteration'] == $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['total']);
?>
                <div class="err"><?php echo output_text($_smarty_tpl->getVariable('err')->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['text']);?>
<?php if ($_smarty_tpl->getVariable('err')->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['help']){?> [<a title="Помощь" href="/faq.php?info=<?php echo $_smarty_tpl->getVariable('err')->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['help'];?>
&amp;return=<?php echo urlencode($_smarty_tpl->getVariable('return')->value);?>
">?</a>]<?php }?></div>
            <?php endfor; endif; ?>
            <?php unset($_smarty_tpl->tpl_vars['smarty']->value['section']['i']);
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['name'] = 'i';
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['loop'] = is_array($_loop=$_smarty_tpl->getVariable('msg')->value) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['show'] = true;
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['max'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['loop'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['step'] = 1;
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['start'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['step'] > 0 ? 0 : $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['loop']-1;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['i']['show']) {
    $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['total'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['loop'];
    if ($_smarty_tpl->tpl_vars['smarty']->value['section']['i']['total'] == 0)
        $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['show'] = false;
} else
    $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['total'] = 0;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['i']['show']):

            for ($_smarty_tpl->tpl_vars['smarty']->value['section']['i']['index'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['start'], $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['iteration'] = 1;
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['iteration'] <= $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['total'];
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['index'] += $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['step'], $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['iteration']++):
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['rownum'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['iteration'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['index_prev'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['index'] - $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['index_next'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['index'] + $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['first']      = ($_smarty_tpl->tpl_vars['smarty']->value['section']['i']['iteration'] == 1);
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['last']       = ($_smarty_tpl->tpl_vars['smarty']->value['section']['i']['iteration'] == $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['total']);
?>
                <div class="msg"><?php echo output_text($_smarty_tpl->getVariable('msg')->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['text']);?>
<?php if ($_smarty_tpl->getVariable('msg')->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['help']){?> [<a title="Помощь" href="/faq.php?info=<?php echo $_smarty_tpl->getVariable('msg')->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['help'];?>
&amp;return=<?php echo urlencode($_smarty_tpl->getVariable('return')->value);?>
">?</a>]<?php }?></div>
            <?php endfor; endif; ?>
        </div>

        <table>
            <tr>
                <td class="menu">
                    <div class="menu"> 
                        <?php if ($_smarty_tpl->getVariable('adt')->value->top){?>
                            <div class="adt">
                                <div class="post_hightlight"><?php echo $_smarty_tpl->getVariable('lang')->value->getString("Партнеры");?>
</div>
                                <?php unset($_smarty_tpl->tpl_vars['smarty']->value['section']['i']);
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['name'] = 'i';
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['loop'] = is_array($_loop=$_smarty_tpl->getVariable('adt')->value->top) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['show'] = true;
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['max'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['loop'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['step'] = 1;
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['start'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['step'] > 0 ? 0 : $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['loop']-1;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['i']['show']) {
    $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['total'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['loop'];
    if ($_smarty_tpl->tpl_vars['smarty']->value['section']['i']['total'] == 0)
        $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['show'] = false;
} else
    $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['total'] = 0;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['i']['show']):

            for ($_smarty_tpl->tpl_vars['smarty']->value['section']['i']['index'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['start'], $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['iteration'] = 1;
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['iteration'] <= $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['total'];
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['index'] += $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['step'], $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['iteration']++):
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['rownum'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['iteration'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['index_prev'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['index'] - $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['index_next'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['index'] + $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['first']      = ($_smarty_tpl->tpl_vars['smarty']->value['section']['i']['iteration'] == 1);
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['last']       = ($_smarty_tpl->tpl_vars['smarty']->value['section']['i']['iteration'] == $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['total']);
?>
                                    <?php echo $_smarty_tpl->getVariable('adt')->value->top[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']];?>

                                <?php endfor; endif; ?>
                            </div>
                        <?php }?>


                        <?php $_smarty_tpl->smarty->_tag_stack[] = array('php', array()); $_block_repeat=true; smarty_block_php(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

$menu = new menu('main');
$menu -> display();
                        <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_php(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>


                        <?php if ($_smarty_tpl->getVariable('adt')->value->bottom){?>
                            <div class="post_hightlight"><?php echo $_smarty_tpl->getVariable('lang')->value->getString("Разное");?>
</div>
                            <?php unset($_smarty_tpl->tpl_vars['smarty']->value['section']['i']);
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['name'] = 'i';
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['loop'] = is_array($_loop=$_smarty_tpl->getVariable('adt')->value->bottom) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['show'] = true;
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['max'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['loop'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['step'] = 1;
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['start'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['step'] > 0 ? 0 : $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['loop']-1;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['i']['show']) {
    $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['total'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['loop'];
    if ($_smarty_tpl->tpl_vars['smarty']->value['section']['i']['total'] == 0)
        $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['show'] = false;
} else
    $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['total'] = 0;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['i']['show']):

            for ($_smarty_tpl->tpl_vars['smarty']->value['section']['i']['index'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['start'], $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['iteration'] = 1;
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['iteration'] <= $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['total'];
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['index'] += $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['step'], $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['iteration']++):
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['rownum'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['iteration'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['index_prev'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['index'] - $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['index_next'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['index'] + $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['first']      = ($_smarty_tpl->tpl_vars['smarty']->value['section']['i']['iteration'] == 1);
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['last']       = ($_smarty_tpl->tpl_vars['smarty']->value['section']['i']['iteration'] == $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['total']);
?>
                                <?php echo $_smarty_tpl->getVariable('adt')->value->bottom[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']];?>

                            <?php endfor; endif; ?>
                        <?php }?>
                    </div>
                </td>
                <td class="content">
                    <div class="content">
                        <?php echo $_smarty_tpl->getVariable('content')->value;?>

                    </div>
                </td>
            </tr>
        </table>



        <div class="foot">
            <span class="copyright">
                <?php echo output_text($_smarty_tpl->getVariable('dcms')->value->copyright);?>

            </span>
            <span class="generation">
                <?php echo $_smarty_tpl->getVariable('lang')->value->getString("Время генерации страницы");?>
: <?php echo $_smarty_tpl->getVariable('document_generation_time')->value;?>

                <a href='/language.php?return=<?php echo $_smarty_tpl->getVariable('URL')->value;?>
' style='background-image: url(<?php echo $_smarty_tpl->getVariable('lang')->value->icon;?>
); background-repeat: no-repeat; background-position: 5px 2px; padding-left: 23px;'><?php echo $_smarty_tpl->getVariable('lang')->value->name;?>
</a>
            </span>

        </div>

    </div>

</body>
</html>