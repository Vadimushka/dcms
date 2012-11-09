{capture assign="post_time"}
    {if $time}
        <span class="post_time">{$time}</span>
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

{if $url}<a {if $hightlight}class="hightlight" {/if}href="{$url}">{/if}
        <div {if $hightlight}class="hightlight"{/if}>
        <table>
            {if $image}
                <tr>
                    <td class="post_image" rowspan="4">
                        <img src="{$image}" alt="" />
                    </td>
                    <td class="post_title">
                        {$title}
                        {$post_counter}
                    </td>
                    
                    <td class="post_title_right">
                    
                    {$post_time}
                    {$post_actions}
                    </td>
                </tr>
            {elseif $icon}
                <tr>
                    <td class="post_icon">
                        <img src="{$icon}" alt="" />
                    </td>
                    <td class="post_title">
                        {$title}
                        {$post_counter}
                    </td>
                    
                    <td class="post_title_right">
                    
                    {$post_time}
                    {$post_actions}
                    </td>
                </tr>
            {else}
                <tr>
                    <td class="post_title">
                        {$title}
                        {$post_counter}
                    </td>
                    
                    <td class="post_title_right">
                    {$post_time}
                    {$post_actions}
                    </td>
                </tr>
            {/if}

            {if $content}
                <tr>
                    <td class="post_content" colspan="10">
                        {$content}
                    </td>
                </tr>
            {/if}

            {if $bottom}
                <tr>
                    <td class="post_bottom" colspan="10">
                        {$bottom}
                    </td>
                </tr>
            {/if}
        </table>
    </div>
{if $url}</a>{/if}