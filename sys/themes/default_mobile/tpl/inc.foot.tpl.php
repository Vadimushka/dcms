<? if ($actions) { ?>
    <div id="actions">
        <?= $this->section($actions, '<a class="gradient_blue border" href="{url}">{name}</a>'); ?>
    </div>
<? } ?>

<? if ($returns OR !IS_MAIN) { ?>
    <div id="returns">
        <?= $this->section($returns, '<a class="gradient_grey border" href="{url}">{name}</a>'); ?>
        <? if (!IS_MAIN) { ?>
            <a class="gradient_grey border" href='/'><?= __("На главную") ?></a>
        <? } ?>
    </div>
<? } ?>