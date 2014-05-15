<?php
session_start();

require_once('file_load.php');

//遷移した先へ渡す年月
$year  = $_GET['year'];
$month = $_GET['month'];
$day   = $_GET['day'];

//フォームからのpostデータを格納
$post_data = $_POST;

$_SESSION['post']  = $post_data;
$_SESSION['year']  = $year;
$_SESSION['month'] = $month;
$_SESSION['day']   = $day;

// $_SESSION['year']['month']['day'] = $_SESSION['post']['schedule_title'];
// $date = $_SESSION['year'].$_SESSION['month'].$_SESSION['day'];

//開始時間と終了時間
$start_time = $_SESSION['post']['start_year'].'-'.$_SESSION['post']['start_month'].'-'.$_SESSION['post']['start_day'].' '.$_SESSION['post']['start_hour'].':'.$_SESSION['post']['start_min'].':00';
$end_time   = $_SESSION['post']['end_year'].'-'.$_SESSION['post']['end_month'].'-'.$_SESSION['post']['end_day'].' '.$_SESSION['post']['end_hour'].':'.$_SESSION['post']['end_min'].':00';

// $start_year  = $_SESSION['post']['start_year'];
// $start_month = $_SESSION['post']['start_month'];
// $start_day   = $_SESSION['post']['start_day'];
// $start_hour  = $_SESSION['post']['start_hour'];
// $start_min   = $_SESSION['post']['start_min'];

// $end_year    = $_SESSION['post']['end_year'];
// $end_month   = $_SESSION['post']['end_month'];
// $end_day     = $_SESSION['post']['end_day'];  
// $end_hour    = $_SESSION['post']['end_hour'];
// $end_min     = $_SESSION['post']['end_min'];  

// if (!checkdate($start_month, $start_day, $start_year)) {
//     $timestamp = time();
//     $start_year  = strtotime(date('Y', $timestamp));
//     $start_month = date('m', $timestamp);
//     $start_day   = date('d', $timestamp);
// } else {
//     $timestamp = strtotime($start_year.$start_month.$start_day);
// }

// if (!checkdate($end_month, $end_day, $end_year)) {
//     $timestamp = time();
//     $end_year  = date('Y', $timestamp);
//     $end_month = date('m', $timestamp);
//     $end_day   = date('d', $timestamp);
// } else {
//     $timestamp = strtotime($end_year.$end_month.$end_day);
// }

// $start_time = $start_year.$start_month.$start_day;
// $end_time   = $end_year.$end_month.$end_day;

// var_dump($start_time);
// var_dump($end_time);
//exit;


//予定のタイトルと内容
$schedule_title    = $_SESSION['post']['schedule_title'];
$schedule_contents = $_SESSION['post']['schedule_contents'];

$id     = $_SESSION['post']['schedule_id'];
$delete = $_SESSION['post']['delete'];

$msg = '';

//エラーの分岐処理正しいならカレンダーへ誤りならスケジュールへ
if (strtotime($start_time) > strtotime($end_time)){
    $msg['time_error']     = '＊時間が遡っています。もう一度選択してください。';
}
if (strlen($schedule_title)    == 0){
    $msg['title_error']    = '＊タイトルが入力されていません。入力しなおしてください。';
}
if (strlen($schedule_contents) == 0){
    $msg['contents_error'] = '＊内容が入力されていません。入力しなおしてください。';
}

$_SESSION['error'] = $msg;

if (!empty($_SESSION['error']) ){
    setcookie('title_cookie',$schedule_title);
    return header("location: http://kensyu.aucfan.com/schedule.php?year=$year&month=$month&day=$day");    
}

$db_connect = db_connect();
//SQL処理開始
if (empty($id) && !is_null($schedule_title)){

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

} elseif (isset($id) && !isset($delete)){

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
if ( isset($start_time) && !empty($sql) ){
    $sql_result = mysqli_query($db_connect, $sql);
}
session_unset();
return header("location: http://kensyu.aucfan.com/?year=".$year."&month=".$month);

?>