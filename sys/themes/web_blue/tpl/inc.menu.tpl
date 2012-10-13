{section name=i loop=$menu}
{if $menu[i].razdel}
<span>{$menu[i].name}</span>
{else}
<a href="{$menu[i].url}">{$menu[i].name}</a>
{/if}
{/section}
