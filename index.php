<?php

$year = date('Y');//年取得
$month = date('m');//月取得

$month_date = date('t',mktime(0,0,0,$month,1,$year));//月の日数表示(4月なら30日分)
$month_begin_cell = date('w', mktime(0, 0, 0, $month, 1, $year));//当月の曜日の数値取得
$last_day = date('w', mktime(0, 0, 0, $month, $month_date, $year));//月末の曜日の数値の取得
$month_end_cell = 6-$last_day;//空マス計算

$day = $month_date = date('t',mktime(0,0,0,$month,1,$year));

$week= date("w", mktime(0,0,0,$month,$day,$year));//月初の1日の曜日の数値

    echo $week;

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Calendar</title>
    <link rel="stylesheet" href="">
</head>
<body>
    <table border="1">
        <caption><?php echo $year.$month;?></caption>
        <thead>
            <tr>
                <th class="sun">日</th>
                <th>月</th>
                <th>火</th>
                <th>水</th>
                <th>木</th>
                <th>金</th>
                <th class="sat">土</th>
            </tr>
        </thead>
        <tbody>
            <tr>
            <?php for($i=1; $i<=$month_begin_cell; $i++):?>
                <td><?php echo ''; ?></td>
            <?php endfor ?>
            <?php for($i=1; $i<=$month_date; $i++):?>
                <td><?php echo $i; ?></td>
            <?php if($week == 6):?>
                </tr>
            <?php endif ?>
            <?php endfor?>
            <?php for($i=1; $i<=$month_end_cell; $i++):?>
                <td><?php echo ''; ?></td>
            <?php endfor;?>
            </tr>
        </tbody>
    </table>
</body>
</html>
