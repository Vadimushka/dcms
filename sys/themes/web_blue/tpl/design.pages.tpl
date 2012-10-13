<div class="pages">
{$lang->getString("Страницы")}: 
{if $page == 1}
<span>1</span>
{else}
<a href="{$link}page=1">1</a>
{/if}
{if $page>10}
..
{/if}
{section name=page loop=$k_page}
{if $smarty.section.page.iteration > 1 AND $smarty.section.page.iteration < $k_page  AND $smarty.section.page.iteration<=$page+9  AND $smarty.section.page.iteration>=$page-8}
{if $page == $smarty.section.page.iteration}
<span>{$smarty.section.page.iteration}</span>
{else}
<a href="{$link}page={$smarty.section.page.iteration}">{$smarty.section.page.iteration}</a>
{/if}
{/if}
{/section}
{if $page < $k_page-10}
..
{/if}
{if $page == $k_page}
<span>{$k_page}</span>
{else}
<a href="{$link}page={$k_page}">{$k_page}</a>
{/if}

</div>
