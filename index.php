<?php
// 日付のタームゾーンを変更
ini_set("date.timezone", "Asia/Tokyo");

// googleAPI祝日取得
define("CALENDAR_URL", "outid3el0qkcrsuf89fltf7a4qbacgt9@import.calendar.google.com");

if(isset($_GET['year']) && $_GET['year'] != '' && isset($_GET['month']) && $_GET['month'] != ''){
    $year  = $_GET['year'];
    $month = sprintf('%02d', $_GET['month']);
// 指定がなければ本日の年月
}else{
    $year  = date('Y');
    $month = date('m');
}
// $day   = date('d');//日取得

$month_date       = date('t', mktime(0,0,0,$month,1,$year));//月の日数表示(4月なら30日分)
$month_begin_cell = date('w', mktime(0, 0, 0, $month, 1, $year));//当月の曜日の数値取得
$last_day         = date('w', mktime(0, 0, 0, $month, $month_date, $year));//月末の曜日の数値の取得
$month_end_cell   = 6-$last_day;//空マス計算

$calendars = array();//カレンダー格納配列

$calendar_count = 3;//カレンダー表示数

$half = floor($calendar_count/2);//真ん中にくる月計算

$half_month = strtotime($year.$month.'01');//真ん中の月


// カレンダー生成
for($i=0; $i<$calendar_count; $i++){

    $count_num   = -$half + $i;//カレンダー表示数の半分の数値取得
    $count_month = sprintf('%02d',$month+$count_num);//中心からの差分
    $format_time = mktime(0, 0, 0, $count_month, 1, $year);

    $calendars[]= array(
        'year' => $year_num = date('Y',$format_time),//年取得
        'month' => $count_month = date('m',$format_time),//月取得
        'month_begin_cell' => date('w',mktime(0,0,0,$count_month,1,$year_num)),//月の日数表示(4月なら30日分)
        'month_date' => $month_date = date('t',mktime(0,0,0,$count_month,1,$year_num)),//当月の曜日の数値取得
        'month_end_cell' => 6-date('w', mktime(0, 0, 0, $count_month, $month_date, $year_num))//空マス計算
    );
}

// コンボボックスのループ設定
function optionLoop($start, $end, $value = null){
 
    for($i = $start; $i <= $end; $i++){
        if(isset($value) &&  $value == $i){
            echo '<option value="'.$i.'" selected="selected">'.$i.'</option>';
        }else{
            echo '<option value="'.$i.'">'.$i.'</option>';
        }
    }
}

// 先月
$prev = array(
    'year'  => date('Y', strtotime('last month', $half_month)),
    'month' => date('m', strtotime('last month', $half_month))
);
// 次月
$next = array(
    'year'  => date('Y', strtotime('next month', $half_month)),
    'month' => date('m', strtotime('next month', $half_month))
);

// 祝日取得開始
// クラス生成
class CalenderUtil{
    public static function getGoogleCalender($min_date, $max_date){
//祝日の配列
        $holidays = array();
// google apiのurl
        $url = 'http://www.google.com/calendar/feeds/%s/public/full-noattendees?%s';
// パラメータ
        $params = array(
            'start-min'   => $min_date,
            'start-max'   => $max_date,
            'max-results' => 100,
            'alt'         => 'json',
            );
        $queryString = http_build_query($params);
// URLを取得
        $getUrl = sprintf($url, CALENDAR_URL, $queryString);
// データ取得
        if($results = file_get_contents($getUrl)){ 
// デコードしたデータ
            $resultsDecode = json_decode($results, true);
// 休日を設定するリスト
            $holidays = array();
// リスト分出力
            foreach ($resultsDecode['feed']['entry'] as $key => $val){
// 日付
                $date = $val['gd$when'][0]['startTime'];
// タイトル
                $title = $val['title']['$t'];
                $title = explode(' / ', $title);
// 日付をキーに設定
                $holidays[$date] = $title[0];
            }
        }
        return $holidays;
    }
}

// 現在の年より年初～年末までを取得
$nowYear = date('Y');
$holiday_first = date('Y-m-d', strtotime("{$nowYear}0101"));
$holiday_end   = date('Y-m-d', strtotime("{$nowYear}1231"));
 
// 祝日出力
$holidays = CalenderUtil::getGoogleCalender($holiday_first, $holiday_end);

?>
<?php

$data = array();

$rss = simplexml_load_file('http://aucfan.com/article/feed/');
foreach ($rss->channel->item as $item) {
    $x = array();

    $x['date']  = (string)$item->pubDate;
    $date = date('Y-m-d', strtotime($date));
    $x['link']  = (string)$item->link;
    $x['title'] = (string)$item->title;
    $data[] = $x;
}
var_dump($data);


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
                            for($d = 1; $d <= $calendar['month_date']; $d++):
                                $date_str = $calendar['year'].'-'.$calendar['month'].'-'.sprintf('%02d',$d);
                                $class = '';
                                switch ($week) {
                                    case 6:
                                        $class .= 'sat ';
                                        break;
                                    case 0:
                                        $class .= 'sun ';
                                        break;
                                }
                                if( $is_holiday = isset($holidays[$date_str])){
                                    $class .= 'holiday';
                                }
                                ?>
                                <td class="<?php echo $class ?>">
                                    <?php echo $d; ?>
                                    <br />
                                    <?php if($is_holiday):?>
                                        <?php echo $holidays[$date_str]; ?>
                                    <?php endif ?>
                                </td>
                            <?php $week++ ?>
                                <?php if($week == 7): ?><!--土曜-->
                                    </tr><tr>
                                <?php $week=0; ?><!--日曜-->
                                <?php endif ?>
                            <?php $holiday_names = '';?><!--祝日-->
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
