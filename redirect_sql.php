<?php
header("location: http://kensyu.aucfan.com/");

//スケジュール登録変更削除
//DB接続
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

//フォームからpostされたデータを格納

$post_data = $_POST;
//開始時間と終了時間
$start_time = $post_data['start_hour'].':'.$post_data['start_min'].':00';
$end_time   = $post_data['end_hour'].':'.$post_data['end_min'].':00';
//開始日と終了日
$start_time = $post_data['start_year'].'-'.$post_data['start_month'].'-'.$post_data['start_day'].' '.$start_time;
$end_time   = $post_data['end_year'].'-'.$post_data['end_month'].'-'.$post_data['end_day'].' '.$end_time;
//予定のタイトルと詳細
$schedule_title    = $post_data['schedule_title'];
$schedule_contents = $post_data['schedule_contents'];
$id = $post_data['schedule_id'];
$between_begin = $calendars[1].'-01 00:00:01';
$between_end = $calendars[3].'-'.$end_times[3].' 23:59:59';

print_r($post_data);

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

}
elseif(isset($id) && !isset($post_data['delete'])) {

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

}
elseif ($post_data['delete'] == 'delete') {

$sql=<<<EOD
    UPDATE
        cal_schedule
    SET
        deleted_at  = NOW()
    WHERE
        schedule_id = "$id"
EOD;

}

print_r($sql);

//SQL実行
if (isset($start_time) && !empty($sql)) {
    $sql_result = mysqli_query($connect, $sql);
}

exit;
?>