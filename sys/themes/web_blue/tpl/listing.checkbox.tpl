{if $hightlight}
    {assign var="div" value="post_hightlight"}
{else}
    {assign var="div" value="post"}
{/if}


{capture assign="post_time"}
    {if $time}
        <td class="post_time">{$time}</td>
    {/if}
{/capture}

{capture assign="post_counter"}
    {if $counter}
        <td class="post_counter">{$counter}</td>
    {/if}
{/capture}

{capture assign="checked_st"}
    {if $checked}
        checked="checked"
    {/if}
{/capture}


<div id="{$id}" class="sortable">
    <label for="{$name}">
        <div class="{$div}">
            <table cellspacing="0" callpadding="0" width="100%">

                <tr>
                    <td style="width:16px">
                        <input type="checkbox" id="{$name}" name="{$name}" {$checked_st} />
                    </td>
                    <td class="post_title">
                        {$title}
                    </td>
                    {$post_time}
                    {$post_counter}
                </tr>


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
    </label>
</div>