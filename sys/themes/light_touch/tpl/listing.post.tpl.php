<?
$post_time = $time ? '<span class="post_time">' . $time . '</span>' : '';
$post_counter = $counter ? '<span class="post_counter">' . $counter . '</span>' : '';
$post_actions = $this->section($actions, '<span class="post_action"><a href="{url}"><img src="{icon}" alt="" /></a></span>')
?>

<? if ($url) { ?><a <?= $hightlight ? 'class="hightlight" ' : '' ?>href="<?= $url ?>"><? } ?>
    <div<?= $hightlight ? ' class="hightlight"' : '' ?>>
        <table cellspacing="0" callpadding="0" width="100%">
            <? if ($image) { ?>            
                <tr>
                    <td class="post_image" rowspan="4">
                        <img src="<?= $image ?>" alt="" />
                    </td>
                    <td class="post_title">
                        <?= $title ?>
                        <?= $post_counter ?>
                    </td>

                    <td class="post_title_right">
                        <?= $post_time ?>
                        <?= $post_actions ?>
                    </td>
                </tr>
            <? } elseif ($icon) { ?>            
                <tr>
                    <td class="post_icon">
                        <img src="<?= $icon ?>" alt="" />
                    </td>
                    <td class="post_title">
                        <?= $title ?>
                        <?= $post_counter ?>
                    </td>

                    <td class="post_title_right">
                        <?= $post_time ?>
                        <?= $post_actions ?>
                    </td>
                </tr>
            <? } else { ?>
                <tr>
                    <td class="post_title">
                        <?= $title ?>
                        <?= $post_counter ?>
                    </td>

                    <td class="post_title_right">
                        <?= $post_time ?>
                        <?= $post_actions ?>
                    </td>
                </tr>
            <? } ?>

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
<? if ($url) { ?></a><? } ?>