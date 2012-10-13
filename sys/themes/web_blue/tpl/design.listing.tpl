<script charset="utf-8" src="{$path}/listing.js" type="text/javascript"></script> 
{if $sortable}
    <script>
        sortable( '{$sortable}');
    </script>
{/if}

<div class="listing">
    {section name=i loop=$post}
        {assign var='id' value=$post[i].id}
        {assign var='url' value=$post[i].url}
        {assign var='title' value=$post[i].title}
        {assign var='content' value=$post[i].post}
        {assign var='bottom' value=$post[i].edit}

        {assign var='is_new' value=$post[i].new}
        {assign var='is_hide' value=$post[i].hide}

        {assign var='actions' value=$post[i].act}

        {assign var='icon_size' value=$post[i].icon.size}
        {assign var='icon_src' value=$post[i].icon.src}

        {assign var='checkbox' value=$post[i].checkbox}
        {assign var='checkbox_checked' value=$post[i].checkbox.checked}
        {assign var='checkbox_name' value=$post[i].checkbox.name}

    {if $id}<div id="{$id}" class="sortable">{/if}

    {if $url}<a href="{$url}">{/if}
        <div class="{if $is_new}post_new{else}post{/if}">
            <table {if $is_hide}class="post_hide"{/if} cellspacing="0" callpadding="0" width="100%">

                {if $checkbox}
                    <tr class="post_title"><td><label><input type="checkbox"{if $checkbox_checked} checked="checked"{/if} name="{$checkbox_name}" value="1" />{$title}</label>
                            {elseif $icon_size == 'small'}
                    <tr class="post_title"><td><img class="icon_small" src="{$icon_src}" alt="" /> {$title}
                        {elseif $icon_size == 'big'}
                    <tr><td class="icon_big" rowspan="4"><img src="{$icon_src}" alt="" /></td><td>{$title}
                        {else}
                    <tr class="post_title"><td>{$title}
                        {/if}
                    </td>
                    {section name=z loop=$actions}
                        <td class="act"><a href="{$actions[z].1}"><img src="/sys/images/icons/act.{$actions[z].0}.png" alt="{$actions[z].0}" /></a></td>
                            {/section}
                            {if $url_end}
                        <td rowspan="100%">
                            <a class="url_end" href="{$url_end}"><img src="{$path}/for_css/url_end.png" alt="" /></a>
                        </td>
                    {/if}
                </tr>   




                {if $content}
                    <tr><td class="post_content" colspan="10">{$content}</td></tr>
                {/if}

                {if $bottom}
                    <tr><td class="post_bottom" colspan="10">{$bottom}</td></tr>
                {/if}


            </table>
        </div>
    {if $url}</a>{/if}  
{if $id}</div>{/if}
{/section}
</div>