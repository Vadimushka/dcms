<script charset="utf-8" src="{$path}/listing.js" type="text/javascript"></script> 
{if $sortable}
    <script>
        sortable( '{$sortable}');
    </script>
{/if}

<div class="listing">
    {$content}
</div>