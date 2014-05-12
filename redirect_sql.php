<?php
session_start();

require_once('db.php');

$year = $_GET['year'];
$month = $_GET['month'];

//フォームからのpostデータを格納
$post_data = $_POST;

$_SESSION['post'] = $post_data;

//開始時間と終了時間
$start_time = $_SESSION['post']['start_year'].'-'.$_SESSION['post']['start_month'].'-'.$_SESSION['post']['start_day'].' '.$_SESSION['post']['start_hour'].':'.$_SESSION['post']['start_min'].':00';
$end_time   = $_SESSION['post']['end_year'].'-'.$_SESSION['post']['end_month'].'-'.$_SESSION['post']['end_day'].' '.$_SESSION['post']['end_hour'].':'.$_SESSION['post']['end_min'].':00';
//予定のタイトルと内容
$schedule_title = $_SESSION['post']['schedule_title'];
$schedule_contents = $_SESSION['post']['schedule_contents'];

$id = $_SESSION['post']['schedule_id'];
$delete = $_SESSION['post']['delete'];

//エラーの分岐処理正しいならカレンダーへ誤りならスケジュールへ
if (strtotime($start_time) > strtotime($end_time)){
    $msg['time_error'] = '＊時間が遡っています。もう一度選択してください。';
}
if (!isset($schedule_title) || $schedule_title === '') {
    $msg['title_error'] = '＊タイトルが入力されていません。入力しなおしてください。';
}
if (!isset($schedule_contents) || $schedule_contents === '') {
    $msg['contents_error'] = '＊内容が入力されていません。入力しなおしてください。';
}
$_SESSION['error'] = $msg;

if(!$delete && !empty($_SESSION['error'])) {
    return header("location: http://kensyu.aucfan.com/schedule.php?year=".$year."&month=".$month);    
}

//SQL処理開始
if (empty($id) && ($schedule_title != null)) {

$sql=<<<EOD
    INSERT INTO
        cal_schedule
    SET
        start_time        = "$start_time",
        end_time          = "$end_time",
        schedule_title    = "$schedule_title",
        schedule_contents = "$schedule_contents",
        update_at         = NOW(),
        created_at        = NOW(),
        deleted_at        = null
EOD;

} elseif(isset($id) && !isset($delete)) {

$sql=<<<EOD
    UPDATE
        cal_schedule
    SET
        start_time        = "$start_time",
        end_time          = "$end_time",
        schedule_title    = "$schedule_title",
        schedule_contents = "$schedule_contents",
        update_at         = NOW()
    WHERE
        schedule_id       = "$id"
EOD;

} else {

$sql=<<<EOD
    UPDATE
        cal_schedule
    SET
        update_at   = NOW(),
        deleted_at  = NOW()
    WHERE
        schedule_id = "$id"
EOD;

}

//SQL実行
if (isset($start_time) && !empty($sql)) {
    $sql_result = mysqli_query($db_connect, $sql);
}

return header("location: http://kensyu.aucfan.com/?year=".$year."&month=".$month);

?>