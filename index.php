<?php
session_start();
session_destroy();

require_once('file_load.php');

if(isset($_GET['year']) && $_GET['year'] != '' && isset($_GET['month']) && $_GET['month'] != ''){
    $year  = $_GET['year'];
    $month = sprintf('%02d', $_GET['month']);
// 指定がなければ本日の年月
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


if ($result = mysqli_query($db_connect, $schedule_sql)) {
    while ($array_row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        list($s_year, $s_month, $s_day) = explode('-', date('Y-m-j',strtotime($array_row['start_time'])));
        list($end_s_year, $end_s_month, $end_s_day) = explode('-', date('Y-m-j',strtotime($array_row['end_time'])));
        $schedules[$s_year][$s_month][$s_day][] = array(
            'title'       => $array_row['schedule_title'],
            'contents'    => $array_row['schedule_contents'],
            'schedule_id' => $array_row['schedule_id']
        );

        if (strtotime($array_row['start_time']) >= strtotime($array_row['end_time'])) {
            continue;
        }

//一致した日に＋1日して予定吐き出し
        $n_day   = $s_day;
        $n_month = $s_month;
        $n_year  = $s_year;

        // for($i=1;strtotime($array_row['start_time'].'+'.$i.' day')<=strtotime($array_row['end_time']);$i++)

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

// 可変年コンボボックス
$s_combo_year = $year-5;
$e_combo_year = $year+5;

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Calendar</title>
    <link rel="stylesheet" href="css/style.css">
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
                <select class="submit_btn" name="year">
                    <?php for ($i=$s_combo_year; $i <= $e_combo_year; $i++) : ?>
                        <option value="<?php echo $i ?>"<?php if($i == $year) echo 'selected' ?>><?php echo $i ?></option>
                    <?php endfor ?>
                </select>年
                <select class="submit_btn" name="month">
                    <?php for ($i=1; $i <= 12; $i++) : ?>
                        <option value="<?php echo $i ?>"<?php if($i == $month) echo 'selected' ?>><?php echo $i ?></option>
                    <?php endfor ?>
                </select>月
                <input class="submit_btn" type="submit" value="表示">
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
                                    <!-- 日付出力 -->
                                    <a href="schedule.php?ymd=<?php echo $date_str; ?>"><?php echo $day;?></a>

                                    <!-- 祝日 -->
                                    <?php if($holidays):?>
                                        <?php echo h($holidays[$date_str]); ?><br />
                                    <?php endif ?>

                                    <!-- オクトピ -->
                                    <?php if(!empty($auc_topic[$date_str])):?>
                                        <a class="topic" href="<?php echo $auc_link[$date_str];?>" target="_blank" >
                                            <?php echo mb_strimwidth($auc_topic[$date_str], 0, 13,'…'); ?>
                                            <span><strong>トピック内容</strong><br /><?php echo h(mb_strimwidth($auc_topic[$date_str], 0, 50,'…')); ?></span>
                                        </a>
                                    <?php endif ?>

                                    <!-- 予定 -->
                                    <?php $tmp = $schedules[$calendar['year']][$calendar['month']][$day];
                                    if(!empty($tmp)) foreach ($tmp as $schedule) : ?>
                                        <a class="tooltip" href="schedule.php?ymd=<?php echo $date_str; ?>&id=<?php echo $schedule['schedule_id'] ?>">
                                            <?php echo h(mb_strimwidth($schedule['title'], 0, 10,'…')); ?><br />
                                            <span><strong>スケジュール内容</strong><br /><?php echo h(mb_strimwidth($schedule['contents'], 0, 30,'…')); ?></span>
                                        </a>
                                    <?php endforeach ?>

                                </td>
                            <?php $week++ ?>

                                <!-- 土曜日 -->
                                <?php if($week == 7): ?>
                                    </tr><tr>

                                <!-- 日曜日 -->
                                <?php $week=0; ?>
                                <?php endif ?>
                            <?php endfor?>
                            <?php for($i=1; $i<=$calendar['month_end_cell']; $i++):?>
                                <td><?php echo '' ?></td>
                            <?php endfor;?>
                        </tr>
                    </tbody>
                </table>
            <?php endforeach ?>
        </div>
    </div>
</body>
</html>