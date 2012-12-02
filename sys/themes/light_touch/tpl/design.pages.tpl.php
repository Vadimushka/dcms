<div class="pages">
    <?= $page == 1 ? '<span>1</span>' : '<a href="' . $link . 'page=1">1</a>' ?>
    <?= $page > 4 ? '..' : '' ?>
    <?
    for ($i = max(2, $page - 4); $i < min($k_page, $page + 3); $i++) {
        if ($i == $page)
            echo '<span>' . $i . '</span>';
        else
            echo '<a href="' . $link . 'page=' . $i . '">' . $i . '</a>';
    }
    ?>
    <?= $page < $k_page - 4 ? '..' : '' ?>
    <?= $page == $k_page ? '<span>' . $k_page . '</span>' : '<a href="' . $link . 'page=' . $k_page . '">' . $k_page . '</a>' ?>
</div>
