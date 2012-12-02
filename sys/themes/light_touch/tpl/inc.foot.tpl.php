<? if ($actions) { ?>
    <div class="actions">
        <?= $this->section($actions, '<a href="{1}"><div>{0}</div></a>'); ?>
    </div>
<? } ?>

<? if ($returns OR !IS_MAIN) { ?>
    <div class="returns">        
        <?= $this->section($returns, '<a href="{1}"><div>{0}</div></a>', true); ?>
        <? if (!IS_MAIN) { ?>
            <a href='/'><div><?= __("На главную") ?></div></a>
        <? } ?>  
    </div>
<? } ?>


