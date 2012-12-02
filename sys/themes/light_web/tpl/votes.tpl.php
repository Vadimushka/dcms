<div class="votes gradient_grey border">
    <span class="vote_name"><?= $name ?></span>
    <table style="width: 100%">
        <?
        foreach ($votes AS $vote) {
            ?>
            <tr>
                <td colspan="2">
                    <?= $vote['name'] ?>
                    <?= $vote['count'] ? ' (' . $vote['count'] . ')' : '' ?>
                </td>
            </tr>
            <tr style="height: 16px;">
                <td class="gradient_grey invert border radius" style="width: 100%">
                    <div class="gradient_blue border radius" style="height: 100%;padding: 0 4px;text-align: right;<?= 'width: ' . max($vote['pc'], 6) . '%' ?>">
                        <?= $vote['pc'] ?>%
                    </div>
                </td>
                <? if ($is_add) { ?>
                    <td style="width: 24px">
                        <a class="gradient_blue border radius" style="display: block; height: 100%; text-align: center;" href="<?= $vote['url'] ?>">+</a>                        
                    </td>
                <? } ?>
            </tr>        
        <? } ?>
    </table>
</div>