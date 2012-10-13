{section name=i loop=$err}
<div class="err">{$err[i].text|output_text}{if $err[i].help} [<a title="Помощь" href="/faq.php?info={$err[i].help}&amp;return={$return|urlencode}">?</a>]{/if}</div>
{/section}
{section name=i loop=$msg}
<div class="msg">{$msg[i].text|output_text}{if $msg[i].help} [<a title="Помощь" href="/faq.php?info={$msg[i].help}&amp;return={$return|urlencode}">?</a>]{/if}</div>
{/section}