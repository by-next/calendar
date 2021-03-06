<?php

session_start();

require_once('file_load.php');

if(isset($_GET['year']) && $_GET['year'] != '' && isset($_GET['month']) && $_GET['month'] != ''){
    $year  = $_GET['year'];
    $month = sprintf('%02d', $_GET['month']);
}else{
    $year  = date('Y');
    $month = date('m');
}

$today   = date('Y-m-d');// 本日取得
$month_date       = date('t', mktime(0, 0, 0, $month, 1, $year));// 月の日数表示(4月なら30日分)
$month_begin_cell = date('w', mktime(0, 0, 0, $month, 1, $year));// 当月の曜日の数値取得
$last_day         = date('w', mktime(0, 0, 0, $month, $month_date, $year));// 月末の曜日の数値の取得
$month_end_cell   = 6-$last_day;// 空マス計算

// カレンダー表示配列
$calendars = array();
// カレンダー表示数
$calendar_count = 3;

// 真ん中にくる月計算
$half = floor($calendar_count/2);
// 真ん中の月
$half_month = strtotime($year.$month.'01');

// カレンダー生成
for($i=0; $i<$calendar_count; $i++){
// カレンダー表示数の半分の数値取得
    $count_num   = -$half + $i;
// 中心からの差分
    $count_month = sprintf('%02d',$month+$count_num);
    $format_time = mktime(0, 0, 0, $count_month, 1, $year);
// カレンダー計算
    $calendars[]= array(
        'year'             => $year_num = date('Y',$format_time),// 年取得
        'month'            => $count_month = date('m',$format_time),// 月取得
        'month_begin_cell' => date('w',mktime(0,0,0,$count_month,1,$year_num)),// 月の日数表示(4月なら30日分)
        'month_date'       => $month_date = date('t',mktime(0,0,0,$count_month,1,$year_num)),// 当月の曜日の数値取得
        'month_end_cell'   => 6-date('w', mktime(0, 0, 0, $count_month, $month_date, $year_num))// 空マス計算
    );
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

// 現在の年より年初～年末までを取得
$nowYear = date('Y');
$holiday_first = date('Y-m-d', strtotime("{$nowYear}0101"));
$holiday_end   = date('Y-m-d', strtotime("{$nowYear}1231"));

// 祝日出力
$holidays = getGoogleCalender($holiday_first, $holiday_end);

// +オクトピ取得
$rss  = simplexml_load_file('http://aucfan.com/article/feed/');// フィード取得URL

$date  = array();// 日付の値挿入
$title = array();// オクトピタイトル挿入
$link  = array();// リンクURL挿入
$auc_topic = array();// オクトピの配列

foreach ( $rss->channel->item as $key => $value) {
    $title = (string)$value->title;
    $date  = date('Y-m-d', strtotime((string)$value->pubDate));// 日付を整形して代入
    $link  = (string)$value->link;
    $auc_topic[$date] = $title;
    $auc_link[$date]  = $link;
}

//DB接続
$db_connect = db_connect();
//スケジュール表示
$schedule_sql =<<<EOD

    SELECT
         schedule_id, start_time, end_time, schedule_title, schedule_contents
    FROM
         cal_schedule
    WHERE
         deleted_at
    IS
         null
EOD;

//スケジュールSQL実行代入
if ($result = mysqli_query($db_connect, $schedule_sql)) {
    while ($array_row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        list($start_year, $start_month, $start_day) = explode('-', date('Y-m-j',strtotime($array_row['start_time'])));
        list($end_s_year, $end_s_month, $end_s_day) = explode('-', date('Y-m-j',strtotime($array_row['end_time'])));
        $schedules[$start_year][$start_month][$start_day][] = array(
            'title'       => $array_row['schedule_title'],
            'contents'    => $array_row['schedule_contents'],
            'schedule_id' => $array_row['schedule_id']
        );
        if (strtotime($array_row['start_time']) >= strtotime($array_row['end_time'])) {
            continue;
        }
        //一致した日に＋1日して予定吐き出し
        $n_day   = $start_day;
        $n_month = $start_month;
        $n_year  = $start_year;

        while ($n_day != $end_s_day || $n_month != $end_s_month || $n_year != $end_s_year) {
            $ymd_day = date('Y-m-j',strtotime('tomorrow',strtotime($n_year.'-'.$n_month.'-'.$n_day)));
            list($n_year, $n_month, $n_day) = explode('-', $ymd_day);
            $schedules[$n_year][$n_month][$n_day][] = array(
                'title'       => $array_row['schedule_title'],
                'contents'    => $array_row['schedule_contents'],
                'schedule_id' => $array_row['schedule_id']
            );
        }
    }
    mysqli_free_result($result);
}
mysqli_close($db_connect);

// 年可変用変数
$start_combo_year = $year-5;
$end_combo_year = $year+5;

?>
<header>
    <span>3ViewCalendar</span>
</header>
<div class="cal_view">
    <div class="header_link">
        <a href="<?php echo '?year='.$prev['year'].'&month='.$prev['month'] ?>" class="button move_month">先月</a>
        <a href="index.php" class="button">今月</a>
        <a href="<?php echo '?year='.$next['year'].'&month='.$next['month'] ?>" class="button move_month">次月</a>
        <form class="index-form" method="get" action="<?php $_SERVER['PHP_SELF']; ?>">
            <select class="submit_btn move_ym" name="year">
                <?php for ($i=$start_combo_year; $i <= $end_combo_year; $i++) : ?>
                    <option value="<?php echo $i ?>"<?php if($i == $year) echo 'selected' ?>><?php echo $i ?></option>
                <?php endfor ?>
            </select>年
            <select class="submit_btn move_ym" name="month">
                <?php for ($i=1; $i <= 12; $i++) : ?>
                    <option value="<?php echo $i ?>"<?php if($i == $month) echo 'selected' ?>><?php echo $i ?></option>
                <?php endfor ?>
            </select>月
            <input class="submit_btn" id="view_output" type="button" value="表示">
        </form>
    </div>
    <div id="wrapper">
        <div class="main_box">
            <?php foreach ($calendars as $calendar) :?>
                <div class="view_row">
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
                            for($day = 1; $day <= $calendar['month_date']; $day++):
                                $date_str = $calendar['year'].'-'.$calendar['month'].'-'.sprintf('%02d',$day);
                                $class = '';
                                switch ($week) {
                                    case 6:
                                        $class .= 'sat ';
                                        break;
                                    case 0:
                                        $class .= 'sun ';
                                        break;
                                }
                                if(isset($holidays[$date_str])){
                                    $class .= 'holiday ';
                                }
                                if(isset($auc_topic[$date_str])){
                                    $class .= 'topic ';
                                }
                                if($date_str == $today){
                                    $class .= 'today';
                                }
                                ?>
                                <td class="<?php echo $class ?>">
                                    <a class="calendar_days" href="schedule.php?year=<?php echo $calendar['year']; ?>&month=<?php echo $calendar['month']; ?>&day=<?php echo $day; ?>"><?php echo $day;?></a>
                                    <?php if($holidays):?>
                                        <?php echo h($holidays[$date_str]); ?><br />
                                    <?php endif ?>
                                    <?php if(!empty($auc_topic[$date_str])):?>
                                        <a class="topic" href="<?php echo $auc_link[$date_str];?>" target="_blank" >
                                            <?php echo mb_strimwidth($auc_topic[$date_str], 0, 13,'…'); ?>
                                            <span><strong>トピック内容</strong><br /><?php echo h(mb_strimwidth($auc_topic[$date_str], 0, 50,'…')); ?></span>
                                        </a><br />
                                    <?php endif ?>
                                    <?php $tmp = $schedules[$calendar['year']][$calendar['month']][$day];
                                    if(!empty($tmp)) foreach ($tmp as $schedule) : ?>
                                        <a class="tooltip calendar_days" href="schedule.php?year=<?php echo $calendar['year']; ?>&month=<?php echo $calendar['month']; ?>&day=<?php echo $day; ?>&id=<?php echo $schedule['schedule_id'] ?>">
                                            <?php echo h(mb_strimwidth($schedule['title'], 0, 10,'…')); ?><br />
                                            <span><strong>スケジュール内容</strong><br /><?php echo h(mb_strimwidth($schedule['contents'], 0, 30,'…')); ?></span>
                                        </a>
                                    <?php endforeach ?>
                                </td>
                            <?php $week++ ?>
                                <?php if($week == 7): ?>
                                    </tr><tr>
                                <?php $week=0; ?>
                                <?php endif ?>
                            <?php endfor?>
                            <?php for($i=1; $i<=$calendar['month_end_cell']; $i++):?>
                                <td><?php echo '' ?></td>
                            <?php endfor;?>
                        </tr>
                    </tbody>
                </table>
                </div>
            <?php endforeach ?>
        </div>
    </div>
</div>