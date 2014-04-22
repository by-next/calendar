<?php
define(GOOGLE_CAL_URL, 'japanese__ja@holiday.calendar.google.com');
//日付のタームゾーンを変更
ini_set("date.timezone", "Asia/Tokyo");

//年月の指定があれば
if (isset($_GET['year'])) {
    $year = $_GET['year'];
}
if (isset($_GET['month'])) {
    $month = sprintf('%02d',$_GET['month']);
}//指定がなければ当月
else{
    $year  = date('Y');
    $month = date('m');
}

// $year  = date('Y');//年取得
// $month = date('m');//月取得
// $day   = date('d');//日取得

$month_date       = date('t', mktime(0,0,0,$month,1,$year));//月の日数表示(4月なら30日分)
$month_begin_cell = date('w', mktime(0, 0, 0, $month, 1, $year));//当月の曜日の数値取得
$last_day         = date('w', mktime(0, 0, 0, $month, $month_date, $year));//月末の曜日の数値の取得
$month_end_cell   = 6-$last_day;//空マス計算

$calendars = array();//カレンダー格納配列

$calendar_count = 3;//カレンダー表示数

$half = floor($calendar_count/2);//真ん中にくる月計算

$half_month = strtotime($year.$month.'01');//真ん中の月


//カレンダー生成
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

//コンボボックスのループ設定
function optionLoop($start, $end, $value = null){
 
    for($i = $start; $i <= $end; $i++){
        if(isset($value) &&  $value == $i){
            echo '<option value="'.$i.'" selected="selected">'.$i.'</option>';
        }else{
            echo '<option value="'.$i.'">'.$i.'</option>';
        }
    }
}

//先月
$prev = array(
    'year' => date('Y', strtotime('last month', $half_month)),
    'month' => date('m', strtotime('last month', $half_month))
);
//次月
$next = array(
    'year' => date('Y', strtotime('next month', $half_month)),
    'month' => date('m', strtotime('next month', $half_month))
);

//祝日取得
function get_holidays_this_month($month){
    $holidays_url = sprintf(
            'http://www.google.com/calendar/feeds/%s/public/full-noattendees?start-min=%s&amp;start-max=%s&amp;max-results=%d&amp;alt=json' ,
            'outid3el0qkcrsuf89fltf7a4qbacgt9@import.calendar.google.com' ,
            $year.$month.'-01' ,  // 取得開始日
            $year.$month.'-31' ,  // 取得終了日
            50            // 最大取得数
            );
    if ( $results = file_get_contents($holidays_url) ) {
            $results = json_decode($results, true);
            $holidays = array();
            foreach ($results['feed']['entry'] as $val ) {
                    $date  = $val['gd$when'][0]['startTime'];
                    $week = date('w',strtotime($date));
                    $title = $val['title']['$t'];
                    $holidays[$date] = $title;
                    if( $week == 0) {
                        $nextday = date('Y-m-d',strtotime('+1 day', strtotime($date)));
                        $holidays[$nextday] = '振替休日';
                    }
                    $before_yesterday = date('Y-m-d',strtotime('-2 day', strtotime($date)));
                    if(isset($holidays[$before_yesterday])){
                        $yesterday = date('Y-m-d',strtotime('-1 day', strtotime($date)));
                        $holidays[$yesterday] = '国民の休日';
                    }
            }
            ksort($holidays);
    }
    return $holidays;
}
var_dump($holidays);
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
            <a href="<?php echo '?year='.$prev['year'].'&month='.$prev['month'] ?>" class="button medium">先月</a>
            <a href="index.php" class="button medium">今月</a>
            <a href="<?php echo '?year='.$next['year'].'&month='.$next['month'] ?>" class="button medium">次月</a>
            <form method="get" action="<?php $_SERVER['PHP_SELF']; ?>">
                <select name="year">
                    <?php optionLoop('1995', '2030',date('Y'));?></select>年
                    <select name="month"><?php optionLoop('1', '12', '6');?></select>月
                <input type="submit" value="表示">
            </form>
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
