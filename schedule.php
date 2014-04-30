<?php
$year_month_day = isset($_GET['ymd']) ? $_GET['ymd'] : date('Y-n-d');
$timestamp = strtotime($year_month_day);
if ($timestamp === false) {
    $timestamp = time();
}
$sdate = $year_month_day;
list($year, $month, $day) = explode('-', $sdate);// 日にちを文字列の分解
var_dump($year);

$update ='';
$schedule_id = $_GET['id'];
$date = sprintf('%02d', $day);

// DB接続
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'cal';

// MySQL に接続し、データベースを選択
$connect = mysqli_connect($host, $user, $password, $database);

// 接続状況をチェック
if (mysqli_connect_errno()) {
    die(mysqli_connect_error());
}

//削除分非表示SQL
$schedule_sql =<<<END
    SELECT
         schedule_id, start_time, end_time, schedule_title, schedule_contents
    FROM
         cal_schedule
    WHERE
         schedule_id="$schedule_id"
    AND
         deleted_at
    IS
         null
END;

// SQL実行
if ($result = mysqli_query($connect, $schedule_sql)) {
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        list($s_year, $s_month, $s_day) = explode('-', date('Y-m-j',strtotime($row['start_time'])));
        list($end_s_year, $end_s_month, $end_s_day) = explode('-', date('Y-m-j',strtotime($row['end_time'])));
        $data = array($row['schedule_title'],$row['schedule_contents']);
        $schedules[$s_year][$s_month][$s_day][$row['schedule_id']]['title'] = $data;
        $schedules[$end_s_year][$end_s_month][$end_s_day][$row['schedule_id']]['title'] = $data;
    }
    mysqli_free_result($result);
}
mysqli_close($connect);

if (!isset($schedule_id)) {
    $end_year  = $year;
    $end_month = $month;
    $end_day   = $day;
} else {
    $year  = $s_year;
    $month = $s_month;
    $day   = $s_day;
    $end_year  = $end_s_year;
    $end_month = $end_s_month;
    $end_day   = $end_s_day;
}

?>

<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
<title></title>
<link href="css/style.css" rel="stylesheet">
</head>
<body>
    <h1>スケジュール登録</h1>
    <div>
        <form method="post" action="http://kensyu.aucfan.com/">
        開始日
        <input type="text" name="start_year"  value="<?php echo $year;?>" />年
        <input type="text" name="start_month" value="<?php echo $month;?>" />月
        <input type="text" name="start_day"   value="<?php echo $day;?>" />日<br />
        終了日
        <input type="text" name="end_year"  value="<?php echo $end_year;?>" />年
        <input type="text" name="end_month" value="<?php echo $end_month;?>" />月
        <input type="text" name="end_day"   value="<?php echo $end_day;?>" />日<br />
        タイトル
        <input type="text" id="schedule_title" name="schedule_title" value="<?php echo $schedules[$s_year][$s_month][$s_day][$schedule_id]['title'];?>" /><br />
        詳細
        <textarea id="schedule_contents" name="schedule_contents" rows=5 cols=40><?php echo $schedules[$s_year][$s_month][$s_day][$schedule_id]['contents'];?></textarea>
        <input type="hidden" name="schedule_id" value="<?php echo $schedule_id;?>" />
    </div>
    <div>
        <?php if(!empty($schedule_id)):?>
                <input class="submit_button" type="submit" value="更新" />
        <?php else:?>
                <input class="submit_button" type="submit" value="登録" />
        <?php endif;?>
        </form>
    </div>
    <div>
        <form method="post" action="http://kensyu.aucfan.com/">
            <input type="hidden" id="delete" name="delete" value="delete" />
            <input type="hidden" name="schedule_id" value="<?php echo $schedule_id;?>" />
            <input type="submit" value="削除" />
        </form>
    </div>
</body>
</html>