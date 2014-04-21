<?php


$year = date('2014');//年取得
$month = date('02');//月取得

$monthDate = date('t',mktime(0,0,0,$month,1,$year));//月の日数表示
$monthBeginSpace = date('w', mktime(0, 0, 0, $month, 1, $year));//当月の曜日の数値取得
$lastday = date('w', mktime(0, 0, 0, $month, $monthDate, $year));//月末の曜日の数値の取得
$monthEndSpace = 6-$lastday;//空マス計算

echo $monthEndSpace;

//年月表示
//echo $yearMonth.'<br />';

// //月はじめの空セル生成
//  for($n=1; $n<=$monthBeginSpace; $n++){
//      echo '空';
//  }

//月の日数表示

// for($i=1; $i<=$monthDate; $i++){
//    echo $i;
// }
//月末の空セル計算表示
// for($x=1; $x<$null; $x++){
//     echo 'kara'.'<br />';
// }


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
            <?php for($n=1; $n<=$monthBeginSpace; $n++):?>
                <td><?php echo '空'; ?></td>
            <?php endfor ?>

            <?php for($i=1; $i<=$monthDate; $i++):?>
                <td><?php echo $i; ?></td>
            <?php endfor ?>

            <?php for($x=1; $x<=$monthEndSpace; $x++):?>
                <td><?php echo '空'; ?></td>
            <?php endfor;?>
            </tr>
        </tbody>
    </table>
</body>
</html>
