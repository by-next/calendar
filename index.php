<?php
//年月の指定があれば
// if(isset($_GET['Y']) && $_GET['Y'] != '' && isset($_GET['m']) && $_GET['m'] != ''){
//     $yyyy = $_GET['Y'];
//     $mm =   $_GET['m'];
// //指定がなければ本日の年月
// }else{
//     $yyyy = date('Y');
//     $mm =   date('m');
// }

$year  = date('Y');//年取得
$month = date('m');//月取得
$day   = date('d');//日取得

$month_date       = date('t', mktime(0,0,0,$month,1,$year));//月の日数表示(4月なら30日分)
$month_begin_cell = date('w', mktime(0, 0, 0, $month, 1, $year));//当月の曜日の数値取得
$last_day         = date('w', mktime(0, 0, 0, $month, $month_date, $year));//月末の曜日の数値の取得
$month_end_cell   = 6-$last_day;//空マス計算

$calendars = array();

//var_dump($calendars);
$calendar_count = 5;//カレンダー表示数

$half = floor($calendar_count/2);//真ん中にくる月計算


for($i=0; $i<$calendar_count; $i++){

    $count_num   = -$half + $i;//カレンダー表示数の半分の数値取得
    $count_month = sprintf('%02d',$month+$count_num);//中心からの差分
    $format_time   = mktime(0, 0, 0, $count_month, 1, $year);

    $calendars[]= array(
        'year' => $year_num = date('Y',$format_time),//年取得
        'month' => $count_month = date('m',$format_time),//月取得
        'month_begin_cell' => date('w',mktime(0,0,0,$count_month,1,$year_num)),//月の日数表示(4月なら30日分)
        'month_date' => $month_date = date('t',mktime(0,0,0,$count_month,1,$year_num)),//当月の曜日の数値取得
        'month_end_cell' => 6-date('w', mktime(0, 0, 0, $count_month, $month_date, $year_num))//空マス計算
    );
}

$prev = array(
    'year' => date('Y', strtotime('prev month', $half)),
    'month' => date('m', strtotime('', $half))
);
$next = array(
    'year' => date('Y', strtotime('next month', $half)),
    'month' => date('m', strtotime('next month', $half))
);

//var_dump($calendars);


?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Calendar</title>
    <link rel="stylesheet" href="">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header id="header" class="">
        <h1>3ViewCalendar</h1>
    </header><!-- /header -->
    <div class="cal_view">
        <div class="header_link">
            <a href="<?php echo '?year='.$prev['year'].'$month='.$prev['month'] ?>" class="prev">先月</a>
            <a href="">今月</a>
            <a href="<?php echo '?year='.$next['year'].'$month='.$next['month'] ?>" class="next">次月</a>
            <!-- <form method="$_GET"; >
                <select name="yyyy">
                <?php
                    $min_year = 1990;
                    $max_year = 2040;

                    for($i = $min_year; $i <= $max_year; $i++){
                        echo '<option value="'.$i.'"'; if($i == $yyyy) echo ' selected'; echo '>'.$i.'</option>'."\n";
                    }
                ?>
                </select>年
                <select name="mm">
                <?php
                    for($i = 1; $i <= 12; $i++){
                        echo '<option value="'.$i.'"'; if($i == $mm) echo ' selected'; echo '>'.$i.'</option>'."\n";
                    }
                ?>
                </select>月
                <input type="submit" value="表示する">
            </form> -->
        </div>
        <div class="box">
            <?php foreach ($calendars as $calendar) :?>    
                <table class="cal">
                    <caption><?php echo $calendar['year'].'年'.$calendar['month'].'月';?></caption>
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
                            <?php for($i=1; $i<=$calendar['month_begin_cell']; $i++):?>
                                <td></td>
                            <?php endfor ?>

                            <?php $week=$calendar['month_begin_cell'];
                            for($d=1; $d<=$calendar['month_date']; $d++):?>
                                <td><?php echo $d; ?></td>
                            <?php $week++ ?>
                                <?php if($week == 7): ?>
                                    </tr><tr>
                                <?php $week=0; ?>
                                <?php endif ?>
                            <?php endfor?>
                            
                            <?php for($i=1; $i<=$calendar['month_end_cell']; $i++):?>
                                <td><?php echo ''?></td>
                            <?php endfor;?>
                        </tr>
                    </tbody>
                </table>
            <?php endforeach ?>
        </div>
    </div>
</body>
</html>
