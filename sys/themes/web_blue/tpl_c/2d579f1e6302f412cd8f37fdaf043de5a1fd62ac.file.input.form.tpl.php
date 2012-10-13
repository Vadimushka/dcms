<?php /* Smarty version Smarty-3.0.6, created on 2012-10-13 13:39:39
         compiled from "E:/DESURE/Dropbox/domains/dcms7dev/sys/themes/web_blue/tpl/input.form.tpl" */ ?>
<?php /*%%SmartyHeaderCode:21443507936db4f62f3-73715064%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '2d579f1e6302f412cd8f37fdaf043de5a1fd62ac' => 
    array (
      0 => 'E:/DESURE/Dropbox/domains/dcms7dev/sys/themes/web_blue/tpl/input.form.tpl',
      1 => 1347785724,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '21443507936db4f62f3-73715064',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php if (!is_callable('smarty_function_html_radios')) include 'E:\DESURE\Dropbox\domains\dcms7dev\sys\plugins\smarty\plugins\function.html_radios.php';
?><div class="form">
<form<?php if ($_smarty_tpl->getVariable('method')->value){?> method="<?php echo $_smarty_tpl->getVariable('method')->value;?>
"<?php }?><?php if ($_smarty_tpl->getVariable('action')->value){?> action="<?php echo $_smarty_tpl->getVariable('action')->value;?>
"<?php }?><?php if ($_smarty_tpl->getVariable('files')->value){?> enctype="multipart/form-data"<?php }?>>
<?php unset($_smarty_tpl->tpl_vars['smarty']->value['section']['sect']);
$_smarty_tpl->tpl_vars['smarty']->value['section']['sect']['name'] = 'sect';
$_smarty_tpl->tpl_vars['smarty']->value['section']['sect']['loop'] = is_array($_loop=$_smarty_tpl->getVariable('el')->value) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$_smarty_tpl->tpl_vars['smarty']->value['section']['sect']['show'] = true;
$_smarty_tpl->tpl_vars['smarty']->value['section']['sect']['max'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['sect']['loop'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['sect']['step'] = 1;
$_smarty_tpl->tpl_vars['smarty']->value['section']['sect']['start'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['sect']['step'] > 0 ? 0 : $_smarty_tpl->tpl_vars['smarty']->value['section']['sect']['loop']-1;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['sect']['show']) {
    $_smarty_tpl->tpl_vars['smarty']->value['section']['sect']['total'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['sect']['loop'];
    if ($_smarty_tpl->tpl_vars['smarty']->value['section']['sect']['total'] == 0)
        $_smarty_tpl->tpl_vars['smarty']->value['section']['sect']['show'] = false;
} else
    $_smarty_tpl->tpl_vars['smarty']->value['section']['sect']['total'] = 0;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['sect']['show']):

            for ($_smarty_tpl->tpl_vars['smarty']->value['section']['sect']['index'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['sect']['start'], $_smarty_tpl->tpl_vars['smarty']->value['section']['sect']['iteration'] = 1;
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['sect']['iteration'] <= $_smarty_tpl->tpl_vars['smarty']->value['section']['sect']['total'];
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['sect']['index'] += $_smarty_tpl->tpl_vars['smarty']->value['section']['sect']['step'], $_smarty_tpl->tpl_vars['smarty']->value['section']['sect']['iteration']++):
$_smarty_tpl->tpl_vars['smarty']->value['section']['sect']['rownum'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['sect']['iteration'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['sect']['index_prev'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['sect']['index'] - $_smarty_tpl->tpl_vars['smarty']->value['section']['sect']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['sect']['index_next'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['sect']['index'] + $_smarty_tpl->tpl_vars['smarty']->value['section']['sect']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['sect']['first']      = ($_smarty_tpl->tpl_vars['smarty']->value['section']['sect']['iteration'] == 1);
$_smarty_tpl->tpl_vars['smarty']->value['section']['sect']['last']       = ($_smarty_tpl->tpl_vars['smarty']->value['section']['sect']['iteration'] == $_smarty_tpl->tpl_vars['smarty']->value['section']['sect']['total']);
?>
<?php if ($_smarty_tpl->getVariable('el')->value[$_smarty_tpl->getVariable('smarty')->value['section']['sect']['index']]['title']){?><?php echo $_smarty_tpl->getVariable('el')->value[$_smarty_tpl->getVariable('smarty')->value['section']['sect']['index']]['title'];?>
:<br /><?php }?>
<?php if ($_smarty_tpl->getVariable('el')->value[$_smarty_tpl->getVariable('smarty')->value['section']['sect']['index']]['type']=='text'){?>
<?php echo $_smarty_tpl->getVariable('el')->value[$_smarty_tpl->getVariable('smarty')->value['section']['sect']['index']]['value'];?>

<?php }elseif($_smarty_tpl->getVariable('el')->value[$_smarty_tpl->getVariable('smarty')->value['section']['sect']['index']]['type']=='captcha'){?>
<input type="hidden" name="captcha_session" value="<?php echo $_smarty_tpl->getVariable('el')->value[$_smarty_tpl->getVariable('smarty')->value['section']['sect']['index']]['session'];?>
" />
<img id="captcha" src="/captcha.php?captcha_session=<?php echo $_smarty_tpl->getVariable('el')->value[$_smarty_tpl->getVariable('smarty')->value['section']['sect']['index']]['session'];?>
&amp;<?php echo @SID;?>
" alt="captcha" /><br />
<script>function captcha_reload(){document.getElementById('captcha').src = "/captcha.php?captcha_session=<?php echo $_smarty_tpl->getVariable('el')->value[$_smarty_tpl->getVariable('smarty')->value['section']['sect']['index']]['session'];?>
&amp;" + Math.random()+"&amp;<?php echo @SID;?>
";}</script>
<a href="javascript:captcha_reload();"><?php echo $_smarty_tpl->getVariable('lang')->value->getString("Обновить картинку");?>
</a><br />
<?php echo $_smarty_tpl->getVariable('lang')->value->getString("Введите число с картинки");?>
:<br /><input type="text" name="captcha" size="5" maxlength="5" />
<?php }elseif($_smarty_tpl->getVariable('el')->value[$_smarty_tpl->getVariable('smarty')->value['section']['sect']['index']]['type']=='input_text'){?>
<input type="text"<?php if ($_smarty_tpl->getVariable('el')->value[$_smarty_tpl->getVariable('smarty')->value['section']['sect']['index']]['info']['size']){?> size="<?php echo $_smarty_tpl->getVariable('el')->value[$_smarty_tpl->getVariable('smarty')->value['section']['sect']['index']]['info']['size'];?>
"<?php }?><?php if ($_smarty_tpl->getVariable('el')->value[$_smarty_tpl->getVariable('smarty')->value['section']['sect']['index']]['info']['disabled']){?> disabled="disabled"<?php }?><?php if ($_smarty_tpl->getVariable('el')->value[$_smarty_tpl->getVariable('smarty')->value['section']['sect']['index']]['info']['name']){?> name="<?php echo $_smarty_tpl->getVariable('el')->value[$_smarty_tpl->getVariable('smarty')->value['section']['sect']['index']]['info']['name'];?>
"<?php }?><?php if ($_smarty_tpl->getVariable('el')->value[$_smarty_tpl->getVariable('smarty')->value['section']['sect']['index']]['info']['value']){?> value="<?php echo for_value($_smarty_tpl->getVariable('el')->value[$_smarty_tpl->getVariable('smarty')->value['section']['sect']['index']]['info']['value']);?>
"<?php }?><?php if ($_smarty_tpl->getVariable('el')->value[$_smarty_tpl->getVariable('smarty')->value['section']['sect']['index']]['info']['maxlength']){?> maxlength="<?php echo $_smarty_tpl->getVariable('el')->value[$_smarty_tpl->getVariable('smarty')->value['section']['sect']['index']]['info']['maxlength'];?>
"<?php }?> />
<?php }elseif($_smarty_tpl->getVariable('el')->value[$_smarty_tpl->getVariable('smarty')->value['section']['sect']['index']]['type']=='radio'){?>
<?php echo smarty_function_html_radios(array('name'=>$_smarty_tpl->getVariable('el')->value[$_smarty_tpl->getVariable('smarty')->value['section']['sect']['index']]['info']['name'],'values'=>$_smarty_tpl->getVariable('el')->value[$_smarty_tpl->getVariable('smarty')->value['section']['sect']['index']]['info']['values'],'output'=>$_smarty_tpl->getVariable('el')->value[$_smarty_tpl->getVariable('smarty')->value['section']['sect']['index']]['info']['output'],'selected'=>$_smarty_tpl->getVariable('el')->value[$_smarty_tpl->getVariable('smarty')->value['section']['sect']['index']]['info']['selected'],'separator'=>"<br />"),$_smarty_tpl);?>

<?php }elseif($_smarty_tpl->getVariable('el')->value[$_smarty_tpl->getVariable('smarty')->value['section']['sect']['index']]['type']=='hidden'){?>
<input type="hidden"<?php if ($_smarty_tpl->getVariable('el')->value[$_smarty_tpl->getVariable('smarty')->value['section']['sect']['index']]['info']['name']){?> name="<?php echo $_smarty_tpl->getVariable('el')->value[$_smarty_tpl->getVariable('smarty')->value['section']['sect']['index']]['info']['name'];?>
"<?php }?><?php if ($_smarty_tpl->getVariable('el')->value[$_smarty_tpl->getVariable('smarty')->value['section']['sect']['index']]['info']['value']){?> value="<?php echo for_value($_smarty_tpl->getVariable('el')->value[$_smarty_tpl->getVariable('smarty')->value['section']['sect']['index']]['info']['value']);?>
"<?php }?> />
<?php }elseif($_smarty_tpl->getVariable('el')->value[$_smarty_tpl->getVariable('smarty')->value['section']['sect']['index']]['type']=='password'){?>
<input type="password"<?php if ($_smarty_tpl->getVariable('el')->value[$_smarty_tpl->getVariable('smarty')->value['section']['sect']['index']]['info']['size']){?> size="<?php echo $_smarty_tpl->getVariable('el')->value[$_smarty_tpl->getVariable('smarty')->value['section']['sect']['index']]['info']['size'];?>
"<?php }?><?php if ($_smarty_tpl->getVariable('el')->value[$_smarty_tpl->getVariable('smarty')->value['section']['sect']['index']]['info']['name']){?> name="<?php echo $_smarty_tpl->getVariable('el')->value[$_smarty_tpl->getVariable('smarty')->value['section']['sect']['index']]['info']['name'];?>
"<?php }?><?php if ($_smarty_tpl->getVariable('el')->value[$_smarty_tpl->getVariable('smarty')->value['section']['sect']['index']]['info']['value']){?> value="<?php echo for_value($_smarty_tpl->getVariable('el')->value[$_smarty_tpl->getVariable('smarty')->value['section']['sect']['index']]['info']['value']);?>
"<?php }?><?php if ($_smarty_tpl->getVariable('el')->value[$_smarty_tpl->getVariable('smarty')->value['section']['sect']['index']]['info']['maxlength']){?> maxlength="<?php echo $_smarty_tpl->getVariable('el')->value[$_smarty_tpl->getVariable('smarty')->value['section']['sect']['index']]['info']['maxlength'];?>
"<?php }?> />
<?php }elseif($_smarty_tpl->getVariable('el')->value[$_smarty_tpl->getVariable('smarty')->value['section']['sect']['index']]['type']=='textarea'){?>
<?php $_smarty_tpl->tpl_vars["uniq"] = new Smarty_variable(passgen(5), null, null);?>
<div class="textarea">
<div class="bb_menu">
<span class="u" title="<?php echo $_smarty_tpl->getVariable('lang')->value->getString("Подчеркнутый текст");?>
">U</span>
<span class="i" title="<?php echo $_smarty_tpl->getVariable('lang')->value->getString("Текст курсивом");?>
">I</span>
<span class="b" title="<?php echo $_smarty_tpl->getVariable('lang')->value->getString("Текст жирным шрифтом");?>
">B</span>
<span class="big" title="<?php echo $_smarty_tpl->getVariable('lang')->value->getString("Увеличенный размер шрифта");?>
">Big</span>
<span class="small" title="<?php echo $_smarty_tpl->getVariable('lang')->value->getString("Уменьшенный размер шрифта");?>
">Small</span>
<span class="php" title="<?php echo $_smarty_tpl->getVariable('lang')->value->getString("Подсветка PHP кода");?>
">PHP</span>
<span class="spoiler" title="<?php echo $_smarty_tpl->getVariable('lang')->value->getString("Свернуть текст");?>
"><?php echo $_smarty_tpl->getVariable('lang')->value->getString("Свернуть текст");?>
</span>
<span class="hide" title="<?php echo $_smarty_tpl->getVariable('lang')->value->getString("Скрыть текст от гостей");?>
"><?php echo $_smarty_tpl->getVariable('lang')->value->getString("Скрыть текст");?>
</span>
<span class="smiles" title="<?php echo $_smarty_tpl->getVariable('lang')->value->getString("Открыть панель со смайлами");?>
"><?php echo $_smarty_tpl->getVariable('lang')->value->getString("Смайлы");?>
</span>
</div>
<textarea id="<?php echo $_smarty_tpl->getVariable('uniq')->value;?>
" <?php if ($_smarty_tpl->getVariable('el')->value[$_smarty_tpl->getVariable('smarty')->value['section']['sect']['index']]['info']['disabled']){?> disabled="disabled"<?php }?><?php if ($_smarty_tpl->getVariable('el')->value[$_smarty_tpl->getVariable('smarty')->value['section']['sect']['index']]['info']['name']){?> name="<?php echo $_smarty_tpl->getVariable('el')->value[$_smarty_tpl->getVariable('smarty')->value['section']['sect']['index']]['info']['name'];?>
"<?php }?>><?php if ($_smarty_tpl->getVariable('el')->value[$_smarty_tpl->getVariable('smarty')->value['section']['sect']['index']]['info']['value']){?><?php echo for_value($_smarty_tpl->getVariable('el')->value[$_smarty_tpl->getVariable('smarty')->value['section']['sect']['index']]['info']['value']);?>
<?php }?></textarea>
</div>
<?php }elseif($_smarty_tpl->getVariable('el')->value[$_smarty_tpl->getVariable('smarty')->value['section']['sect']['index']]['type']=='checkbox'){?>
<label><input type="checkbox"<?php if ($_smarty_tpl->getVariable('el')->value[$_smarty_tpl->getVariable('smarty')->value['section']['sect']['index']]['info']['name']){?> name="<?php echo $_smarty_tpl->getVariable('el')->value[$_smarty_tpl->getVariable('smarty')->value['section']['sect']['index']]['info']['name'];?>
"<?php }?><?php if ($_smarty_tpl->getVariable('el')->value[$_smarty_tpl->getVariable('smarty')->value['section']['sect']['index']]['info']['value']){?> value="<?php echo $_smarty_tpl->getVariable('el')->value[$_smarty_tpl->getVariable('smarty')->value['section']['sect']['index']]['info']['value'];?>
"<?php }?><?php if ($_smarty_tpl->getVariable('el')->value[$_smarty_tpl->getVariable('smarty')->value['section']['sect']['index']]['info']['checked']){?> checked="checked"<?php }?> /><?php if ($_smarty_tpl->getVariable('el')->value[$_smarty_tpl->getVariable('smarty')->value['section']['sect']['index']]['info']['text']){?> <?php echo $_smarty_tpl->getVariable('el')->value[$_smarty_tpl->getVariable('smarty')->value['section']['sect']['index']]['info']['text'];?>
<?php }?></label>
<?php }elseif($_smarty_tpl->getVariable('el')->value[$_smarty_tpl->getVariable('smarty')->value['section']['sect']['index']]['type']=='submit'){?>
<input type="submit"<?php if ($_smarty_tpl->getVariable('el')->value[$_smarty_tpl->getVariable('smarty')->value['section']['sect']['index']]['info']['name']){?> name="<?php echo $_smarty_tpl->getVariable('el')->value[$_smarty_tpl->getVariable('smarty')->value['section']['sect']['index']]['info']['name'];?>
"<?php }?><?php if ($_smarty_tpl->getVariable('el')->value[$_smarty_tpl->getVariable('smarty')->value['section']['sect']['index']]['info']['value']){?> value="<?php echo $_smarty_tpl->getVariable('el')->value[$_smarty_tpl->getVariable('smarty')->value['section']['sect']['index']]['info']['value'];?>
"<?php }?> />
<?php }elseif($_smarty_tpl->getVariable('el')->value[$_smarty_tpl->getVariable('smarty')->value['section']['sect']['index']]['type']=='file'){?>
<input type="file"<?php if ($_smarty_tpl->getVariable('el')->value[$_smarty_tpl->getVariable('smarty')->value['section']['sect']['index']]['info']['name']){?> name="<?php echo $_smarty_tpl->getVariable('el')->value[$_smarty_tpl->getVariable('smarty')->value['section']['sect']['index']]['info']['name'];?>
"<?php }?> />
<?php }elseif($_smarty_tpl->getVariable('el')->value[$_smarty_tpl->getVariable('smarty')->value['section']['sect']['index']]['type']=='select'){?>
<select name="<?php echo $_smarty_tpl->getVariable('el')->value[$_smarty_tpl->getVariable('smarty')->value['section']['sect']['index']]['info']['name'];?>
">
<?php unset($_smarty_tpl->tpl_vars['smarty']->value['section']['select']);
$_smarty_tpl->tpl_vars['smarty']->value['section']['select']['name'] = 'select';
$_smarty_tpl->tpl_vars['smarty']->value['section']['select']['loop'] = is_array($_loop=$_smarty_tpl->getVariable('el')->value[$_smarty_tpl->getVariable('smarty')->value['section']['sect']['index']]['info']['options']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$_smarty_tpl->tpl_vars['smarty']->value['section']['select']['show'] = true;
$_smarty_tpl->tpl_vars['smarty']->value['section']['select']['max'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['select']['loop'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['select']['step'] = 1;
$_smarty_tpl->tpl_vars['smarty']->value['section']['select']['start'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['select']['step'] > 0 ? 0 : $_smarty_tpl->tpl_vars['smarty']->value['section']['select']['loop']-1;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['select']['show']) {
    $_smarty_tpl->tpl_vars['smarty']->value['section']['select']['total'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['select']['loop'];
    if ($_smarty_tpl->tpl_vars['smarty']->value['section']['select']['total'] == 0)
        $_smarty_tpl->tpl_vars['smarty']->value['section']['select']['show'] = false;
} else
    $_smarty_tpl->tpl_vars['smarty']->value['section']['select']['total'] = 0;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['select']['show']):

            for ($_smarty_tpl->tpl_vars['smarty']->value['section']['select']['index'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['select']['start'], $_smarty_tpl->tpl_vars['smarty']->value['section']['select']['iteration'] = 1;
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['select']['iteration'] <= $_smarty_tpl->tpl_vars['smarty']->value['section']['select']['total'];
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['select']['index'] += $_smarty_tpl->tpl_vars['smarty']->value['section']['select']['step'], $_smarty_tpl->tpl_vars['smarty']->value['section']['select']['iteration']++):
$_smarty_tpl->tpl_vars['smarty']->value['section']['select']['rownum'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['select']['iteration'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['select']['index_prev'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['select']['index'] - $_smarty_tpl->tpl_vars['smarty']->value['section']['select']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['select']['index_next'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['select']['index'] + $_smarty_tpl->tpl_vars['smarty']->value['section']['select']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['select']['first']      = ($_smarty_tpl->tpl_vars['smarty']->value['section']['select']['iteration'] == 1);
$_smarty_tpl->tpl_vars['smarty']->value['section']['select']['last']       = ($_smarty_tpl->tpl_vars['smarty']->value['section']['select']['iteration'] == $_smarty_tpl->tpl_vars['smarty']->value['section']['select']['total']);
?>
<?php if ($_smarty_tpl->getVariable('el')->value[$_smarty_tpl->getVariable('smarty')->value['section']['sect']['index']]['info']['options'][$_smarty_tpl->getVariable('smarty')->value['section']['select']['index']]['groupstart']){?>
<optgroup label="<?php echo $_smarty_tpl->getVariable('el')->value[$_smarty_tpl->getVariable('smarty')->value['section']['sect']['index']]['info']['options'][$_smarty_tpl->getVariable('smarty')->value['section']['select']['index']][0];?>
">
<?php }elseif($_smarty_tpl->getVariable('el')->value[$_smarty_tpl->getVariable('smarty')->value['section']['sect']['index']]['info']['options'][$_smarty_tpl->getVariable('smarty')->value['section']['select']['index']]['groupend']){?>
</optgroup>
<?php }else{ ?>
<option<?php if ($_smarty_tpl->getVariable('el')->value[$_smarty_tpl->getVariable('smarty')->value['section']['sect']['index']]['info']['options'][$_smarty_tpl->getVariable('smarty')->value['section']['select']['index']][2]){?> selected="selected"<?php }?> value="<?php echo $_smarty_tpl->getVariable('el')->value[$_smarty_tpl->getVariable('smarty')->value['section']['sect']['index']]['info']['options'][$_smarty_tpl->getVariable('smarty')->value['section']['select']['index']][0];?>
"><?php echo $_smarty_tpl->getVariable('el')->value[$_smarty_tpl->getVariable('smarty')->value['section']['sect']['index']]['info']['options'][$_smarty_tpl->getVariable('smarty')->value['section']['select']['index']][1];?>
</option>
<?php }?>
<?php endfor; endif; ?>
</select>
<?php }?>
<?php if ($_smarty_tpl->getVariable('el')->value[$_smarty_tpl->getVariable('smarty')->value['section']['sect']['index']]['br']){?>
<br />
<?php }?>
<?php endfor; endif; ?>
</form>
</div>
