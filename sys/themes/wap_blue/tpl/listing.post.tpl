{if $hightlight}
    {assign var="div" value="post_hightlight"}
{else}
    {assign var="div" value="post"}
{/if}


{capture assign="post_time"}
    {if $time}
        <span class="post_time">({$time})</span>
    {/if}
{/capture}

{capture assign="post_counter"}
    {if $counter}
        <span class="post_counter">{$counter}</span>
    {/if}
{/capture}

{capture assign="post_actions"}
    {section name=i loop=$actions}
        <span class="post_action"><a href="{$actions[i].url}"><img src="{$actions[i].icon}" alt="" /></a></span>
            {/section}
        {/capture}


{if $image}

    <img class="post_image" src="{$image}" alt="" />

{/if}

<div class="{$div}">
    <div class="post_title">
        {if $icon}
            <img src="{$icon}" alt="" />
        {/if}
    {if $url}<a href="{$url}">{/if}{$title}{if $url}</a>{/if}
    {$post_time}
    {$post_counter}
</div>
<div class="post_content">
    {$content}
</div>
<div class="post_bottom">
    {$bottom}
</div>
</div>
