<div id="<?= $id ?>" class="chart"></div>
<script charset="utf-8" src="/sys/themes/.common/highcharts.js" type="text/javascript"></script>
<script>
    $(function () {
        $('#<?=$id?>').highcharts({
            title:{
              text: <?=json_encode($title)?>
            },
            xAxis: {
                categories: <?=json_encode($categories)?>
            },
            yAxis: {
                title: {
                    text: <?=json_encode($y_text)?>
                },
                plotLines: [
                    {
                        value: 0,
                        width: 1,
                        color: '#808080'
                    }
                ]
            },
            tooltip: {
                valueSuffix: <?=json_encode($value_suffix)?>
            },
            series: <?=json_encode($series)?>
        });
    });
</script>