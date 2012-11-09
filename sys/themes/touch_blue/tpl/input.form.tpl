<div class="form">
<form{if $method} method="{$method}"{/if}{if $action} action="{$action}"{/if}{if $files} enctype="multipart/form-data"{/if}>
{section name=sect loop=$el}
{if $el[sect].title}{$el[sect].title}:<br />{/if}
{if $el[sect].type eq 'text'}
{$el[sect].value}
{elseif $el[sect].type eq 'captcha'}
<input type="hidden" name="captcha_session" value="{$el[sect].session}" />
<img id="captcha" src="/captcha.php?captcha_session={$el[sect].session}&amp;{$smarty.const.SID}" alt="captcha" /><br />
<script>{literal}
function captcha_reload(){
document.getElementById('captcha').src = "/captcha.php?captcha_session={/literal}{$el[sect].session}{literal}&amp;" 
+ Math.random()+"&amp;{/literal}{$smarty.const.SID}{literal}";}{/literal}</script>
<a href="javascript:captcha_reload();">{$lang->getString("Обновить картинку")}</a><br />
{$lang->getString("Введите число с картинки")}:<br /><input type="text" name="captcha" size="5" maxlength="5" />
{elseif $el[sect].type eq 'input_text'}
<input type="text"{if $el[sect].info.size} size="{$el[sect].info.size}"{/if}{if $el[sect].info.disabled} disabled="disabled"{/if}{if $el[sect].info.name} name="{$el[sect].info.name}"{/if} value="{$el[sect].info.value|for_value}"{if $el[sect].info.maxlength} maxlength="{$el[sect].info.maxlength}"{/if} />
{elseif $el[sect].type eq 'radio'}
{html_radios name=$el[sect].info.name values=$el[sect].info.values output=$el[sect].info.output selected=$el[sect].info.selected separator="<br />"}
{elseif $el[sect].type eq 'hidden'}
<input type="hidden"{if $el[sect].info.name} name="{$el[sect].info.name}"{/if}{if $el[sect].info.value} value="{$el[sect].info.value|for_value}"{/if} />
{elseif $el[sect].type eq 'password'}
<input type="password"{if $el[sect].info.size} size="{$el[sect].info.size}"{/if}{if $el[sect].info.name} name="{$el[sect].info.name}"{/if}{if $el[sect].info.value} value="{$el[sect].info.value|for_value}"{/if}{if $el[sect].info.maxlength} maxlength="{$el[sect].info.maxlength}"{/if} />
{elseif $el[sect].type eq 'textarea'}
<textarea{if $el[sect].info.disabled} disabled="disabled"{/if}{if $el[sect].info.name} name="{$el[sect].info.name}"{/if}>{if $el[sect].info.value}{$el[sect].info.value|for_value}{/if}</textarea>
<br /><a href="/smiles.php?return={$URL}">{$lang->getString("Список смайлов")}</a>
{elseif $el[sect].type eq 'checkbox'}
<label><input type="checkbox"{if $el[sect].info.name} name="{$el[sect].info.name}"{/if}{if $el[sect].info.value} value="{$el[sect].info.value}"{/if}{if $el[sect].info.checked} checked="checked"{/if} />{if $el[sect].info.text} {$el[sect].info.text}{/if}</label>
{elseif $el[sect].type eq 'submit'}
<input type="submit"{if $el[sect].info.name} name="{$el[sect].info.name}"{/if}{if $el[sect].info.value} value="{$el[sect].info.value}"{/if} />
{elseif $el[sect].type eq 'file'}
<input type="file"{if $el[sect].info.name} name="{$el[sect].info.name}"{/if} />
{elseif $el[sect].type eq 'select'}
<select name="{$el[sect].info.name}">
{section name=select loop=$el[sect].info.options}
{if $el[sect].info.options[select].groupstart}
<optgroup label="{$el[sect].info.options[select].0}">
{elseif $el[sect].info.options[select].groupend}
</optgroup>
{else}
<option{if $el[sect].info.options[select].2} selected="selected"{/if} value="{$el[sect].info.options[select].0}">{$el[sect].info.options[select].1}</option>
{/if}
{/section}
</select>
{/if}
{if $el[sect].br}
<br />
{/if}
{/section}
</form>
</div>
