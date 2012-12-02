<?
$div = $hightlight ? 'post_hightlight' : 'post';
$post_time = $time ? '<span class="post_time">' . $time . '</span>' : '';
$post_counter = $counter ? '<span class="post_counter">' . $counter . '</span>' : '';
$checked_st = $checked ? ' checked="checked"' : '';
?>

<label for="<?= $name ?>">
    <div class="<?= $div ?>">
        <table cellspacing="0" callpadding="0" width="100%">

            <tr>
                <td style="width:16px">
                    <input type="checkbox" id="<?= $name ?>" name="<?= $name ?>" <?= $checked_st ?> />
                </td>
                <td class="post_title">
                    <?= $title ?>
                </td>
                <?= $post_time ?>
                <?= $post_counter ?>
            </tr>


            <? if ($content) { ?>
                <tr>
                    <td class="post_content" colspan="10">
                        <?= $content ?>
                    </td>
                </tr>
            <? } ?>

            <? if ($bottom) { ?>
                <tr>
                    <td class="post_bottom" colspan="10">
                        <?= $bottom ?>
                    </td>
                </tr>
            <? } ?>
        </table>
    </div>
</label>