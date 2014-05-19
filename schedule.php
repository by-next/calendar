<?php

session_start();

require_once('file_load.php');

//日とID取得
$year  = $_POST['year'];
$month = $_POST['month'];
$day   = $_POST['day'];
$hour  = date('G');
$min   = date('i');
$schedule_id = $_POST['id'];

if (!checkdate($month, $day, $year)) {
    $timestamp = time();
    $year  = date('Y', $timestamp);
    $month = date('m', $timestamp);
    $day   = date('d', $timestamp);
} else {
    $timestamp = strtotime($year.$month.$day);
}
//session年月日とGET年月日が一致しない場合はセッション削除かつidがある場合も削除
if((isset($_SESSION['year']) && $_SESSION['year'] != $year) || (isset($_SESSION['month']) && $_SESSION['month'] != $month) || (isset($_SESSION['day']) && $_SESSION['day'] != $day) || (isset($schedule_id))) {
    session_unset();
}

//DB接続
$db_connect = db_connect();

//idがある場合の表示SQL
$schedule_sql =<<<EOD
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
EOD;

// 配列へ格納
if ($result = mysqli_query($db_connect, $schedule_sql)) {
    while ($array_row= mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        list($start_year, $start_month, $start_day, $start_hour, $start_min) = explode('-', date('Y-m-j-G-i',strtotime($array_row['start_time'])));
        list($end_s_year, $end_s_month, $end_s_day,$end_s_hour,$end_s_min) = explode('-', date('Y-m-j-G-i',strtotime($array_row['end_time'])));
        $schedules[$start_year][$start_month][$start_day] = array(
            'title'       => $array_row['schedule_title'],
            'contents'    => $array_row['schedule_contents'],
            'schedule_id' => $array_row['schedule_id']
        );
    }
    mysqli_free_result($result);
}
mysqli_close($db_connect);

//idまたははじめの年がなければ当日の年月日を代入
if (!isset($schedule_id) || empty($start_year)) {
    $start_year  = $year;
    $start_month = $month;
    $start_day   = $day;
    $start_hour  = $hour;
    $start_min   = $min;
    $end_year    = $start_year;
    $end_month   = $start_month;
    $end_day     = $start_day;
    $end_hour    = $start_hour;
    $end_min     = $start_min;
} else {
    $year        = $start_year;
    $month       = $start_month;
    $day         = $start_day;
    $hour        = $start_hour;
    $min         = $start_min;
    $end_year    = $end_s_year;
    $end_month   = $end_s_month;
    $end_day     = $end_s_day;
    $end_hour    = $end_s_hour;
    $end_min     = $end_s_min;
}

//スケジュールid,title,contents取得配列
$schedule = $schedules[$start_year][$start_month][$start_day];

//コンボボックス年前後5年表示
$start_combo_year = $year-5;
$end_combo_year   = $year+5;

optionLoop($start, $end, $value = null);

//バリデーションの表示切り替えタイミング
if (!isset($_SESSION['post'])) {
    // カレンダーから来た時
    $schedule_title = $schedule['title'];
    $schedule_contents = $schedule['contents'];
} else {
    // リダイレクトで来た時
    $start_time = $_SESSION['post']['start_year'].'-'.
                  $_SESSION['post']['start_month'].'-'.
                  $_SESSION['post']['start_day'].' '.
                  $_SESSION['post']['start_hour'].':'.
                  $_SESSION['post']['start_min'].':00';
    $end_time   = $_SESSION['post']['end_year'].'-'.
                  $_SESSION['post']['end_month'].'-'.
                  $_SESSION['post']['end_day'].' '.
                  $_SESSION['post']['end_hour'].':'.
                  $_SESSION['post']['end_min'].':00';
    $schedule_title = $_SESSION['post']['schedule_title'];
    $schedule_contents = $_SESSION['post']['schedule_contents'];
    $schedule_id = $_SESSION['post']['schedule_id'];
}

?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <title>スケジュール編集</title>
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <div class="wrapper">
        <table class="schedule_table">
            <caption>スケジュール編集</caption>
            <thead>
                <tr>
                    <th>編集項目</th>
                </tr>
            </thead>
            <tbody>
                <form id="schedule" method="post" action="registration.php?year=<?php echo $year ?>&month=<?php echo $month ?>&day=<?php echo $day ?>">
                <tr>
                    <td>
                        開始日
                        <select name="start_year" class="submit_time">
                            <?php for ($i=$start_combo_year; $i <= $end_combo_year; $i++) : ?>
                                <option value="<?php echo $i ?>"<?php if($i == $start_year) echo 'selected' ?>><?php echo $i ?></option>
                            <?php endfor ?>
                        </select>年
                        <select name="start_month" class="submit_time" value="<?php echo $month;?>">
                            <?php for ($i=1; $i <= 12; $i++) : ?>
                                <option value="<?php echo $i ?>"<?php if($i == $start_month) echo 'selected' ?>><?php echo $i ?></option>
                            <?php endfor ?>
                        </select>月
                        <select name="start_day" class="submit_time" value="<?php echo $day;?>">
                            <?php for ($i=1; $i <= 31; $i++) : ?>
                                <option value="<?php echo $i ?>"<?php if($i == $start_day) echo 'selected' ?>><?php echo $i ?></option>
                            <?php endfor ?>
                        </select>日
                        <select name="start_hour" class="submit_time">
                            <?php for ($i=1; $i<=24; $i++):?>
                            <option name="start_hour" value="<?php echo $i;?>"<?php if ($i == $start_hour):?>selected<?php endif;?>><?php echo $i ?></option>
                            <?php endfor; ?>
                        </select>時
                        <select name="start_min" class="submit_time">
                            <?php for ($i=0; $i <=30; $i=$i+30):?>
                            <option name="start_min" value="<?php echo $i = sprintf('%02d',$i);?>"<?php if ($i == $start_min):?>selected<?php endif;?>><?php echo $i ?></option>
                            <?php endfor; ?>
                        </select>分
                    </td>  
                </tr>
                <tr>
                    <td>
                        終了日
                        <select name="end_year" class="submit_time">
                            <?php for ($i=$start_combo_year; $i <= $end_combo_year; $i++) : ?>
                                <option value="<?php echo $i ?>"<?php if($i == $end_year) echo 'selected' ?>><?php echo $i ?></option>
                            <?php endfor ?>
                        </select>年
                        <select name="end_month" class="submit_time" value="<?php echo $end_month;?>">
                            <?php for ($i=1; $i <= 12; $i++) : ?>
                                <option value="<?php echo $i ?>"<?php if($i == $end_month) echo 'selected' ?>><?php echo $i ?></option>
                            <?php endfor ?>
                        </select>月
                        <select name="end_day" class="submit_time" value="<?php echo $end_day;?>">
                            <?php for ($i=1; $i <= 31; $i++) : ?>
                                <option value="<?php echo $i ?>"<?php if($i == $end_day) echo 'selected' ?>><?php echo $i ?></option>
                            <?php endfor ?>
                        </select>日
                        <select name="end_hour" class="submit_time">
                            <?php for ($i=1; $i<=24; $i++):?>
                            <option name="end_hour" value="<?php echo $i;?>"<?php if ($i == $end_hour):?>selected<?php endif;?>><?php echo $i ?></option>
                            <?php endfor; ?>
                        </select>時
                        <select name="end_min" class="submit_time">
                            <?php for ($i=0; $i <=30; $i=$i+30):?>
                            <option name="end_min" value="<?php echo $i = sprintf('%02d',$i);?>"<?php if ($i == $end_min):?>selected<?php endif;?>><?php echo $i ?></option>
                            <?php endfor; ?>
                        </select>分
                    </td>  
                </tr>
                <tr>
                    <td>タイトル</td>
                </tr>
                <tr>
                    <td><input type="text" class="submit_text" id="schedule-text" name="schedule_title" value="<?php echo h($schedule_title);?>" /></td>
                </tr>
                <tr>
                    <td>スケジュール内容</td>
                </tr>
                <tr>
                    <td>
                        <textarea class="submit_text" name="schedule_contents" rows="7" cols="60"><?php echo h($schedule_contents);?></textarea>
                        <input id="schedule_num" type="hidden" name="id" value="<?php echo $schedule_id;?>" />
                    </td>
                </tr>
                <tr>
                    <td id="error_msg" class="error">
                    </td>
                </tr>
                <tr>
                    <td>
                        <?php if(!empty($schedule_id)):?>
                            <input id="update" class="submit_btn regist_update" type="submit" value="更新" />
                            <button id="delete" class="submit_btn delete" type="submit" name="delete" value="delete">削除</button>
                        <?php else:?>
                            <input id="regist" class="submit_btn regist_update" type="submit" value="登録" />
                        <?php endif;?>
                    </td>  
                </tr>
                </form>
            </tbody>
        </table>
    </div>
</body>
</html>