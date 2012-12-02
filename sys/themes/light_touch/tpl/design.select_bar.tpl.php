<div class="select_bar">
    <?
    foreach ($select AS $option) {
        if (!empty($option[2]))
            echo '<span>' . $option[1] . '</span>';
        else
            echo '<a href="' . $option[0] . '">' . $option[1] . '</a>';
    }
    ?>
</div>