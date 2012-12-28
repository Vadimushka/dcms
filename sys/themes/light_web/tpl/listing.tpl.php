<div class="listing" id="<?=$id?>">
    <?=$content?>
</div>
<?if ($ajax_update_url){?>
<script>
    listing_auto_update(document.getElementById('<?=$id?>'), '<?=$ajax_update_url?>');
</script>
<?}?>