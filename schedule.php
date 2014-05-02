<?php

include 'db.php';
// getで日にち取得
$year_month_day = isset($_GET['ymd']) ? $_GET['ymd'] : date('Y-n-d');
$timestamp = strtotime($year_month_day);
if ($timestamp === false) {
    $timestamp = time();
}

// 時分
$hour = date('G');
$min = date('i');

// 日にちを文字列の分解
$sdate = $year_month_day;
list($year, $month, $day) = explode('-', $sdate);

$update ='';
$schedule_id = $_GET['id'];
$date = sprintf('%02d', $day);

// DB接続
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'cal';

// MySQLに接続し、データベースを選択
$connect = mysqli_connect($host, $user, $password, $database);

// 接続状況をチェック
if (mysqli_connect_errno()) {
    die(mysqli_connect_error());
}


//表示SQL
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

// SQL実行後値を格納
if ($result = mysqli_query($connect, $schedule_sql)) {
    while ($array_row= mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        list($s_year, $s_month, $s_day, $s_hour, $s_min) = explode('-', date('Y-m-j-G-i',strtotime($array_row['start_time'])));
        list($end_s_year, $end_s_month, $end_s_day,$end_s_hour,$end_s_min) = explode('-', date('Y-m-j-G-i',strtotime($array_row['end_time'])));
        $schedules[$s_year][$s_month][$s_day][$array_row['schedule_id']]['title'] = $array_row['schedule_title'];
        $schedules[$s_year][$s_month][$s_day][$array_row['schedule_id']]['contents'] = $array_row['schedule_contents'];
    }
    mysqli_free_result($result);
}
mysqli_close($connect);

if (!isset($schedule_id)) {
    $s_year    = $end_year  = $year;
    $s_month   = $end_month = $month;
    $s_day     = $end_day   = $day;
    $s_hour    = $end_hour  = $hour;
    $s_min     = $end_min   = $min;
} else {
    $year      = $s_year;
    $month     = $s_month;
    $day       = $s_day;
    $hour      = $s_hour;
    $min       = $s_min;
    $end_year  = $end_s_year;
    $end_month = $end_s_month;
    $end_day   = $end_s_day;
    $end_hour  = $end_s_hour;
    $end_min   = $end_s_min;
}

//コンボボックス年前後5年表示
$s_combo_year = $year-5;
$e_combo_year = $year+5;
//1月〜12月
$month_min = '1';
$month_max = '12';
//1日〜31日
$day_min = '1';
$day_max = '31';

function optionLoop($start, $end, $value = null){
    for($i = $start; $i <= $end; $i++){
        if(isset($value) &&  $value == $i){
            echo '<option value="'.$i.'" selected="selected">'.$i.'</option>';
        }else{
            echo '<option value="'.$i.'">'.$i.'</option>';
        }
    }
}
var_dump($start_time);


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
        <table border="1">
            <caption>スケジュール編集</caption>
            <thead>
                <tr>
                    <th>編集項目</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <form class="submit_insert" method="post" action="http://kensyu.aucfan.com/redirect_sql.php?year=<?php echo $year ?>&month=<?php echo $month?>">
                        開始日
                        <select name="start_year">
                            <?php for ($i=$s_combo_year; $i <= $e_combo_year; $i++) : ?>
                                <option value="<?php echo $i ?>"<?php if($i == $s_year) echo 'selected' ?>><?php echo $i ?></option>
                            <?php endfor ?>
                        </select>年
                        <select name="start_month" value="<?php echo $month;?>">
                            <?php optionLoop($month_min,$month_max,$s_month);?>
                        </select>月
                        <select name="start_day" value="<?php echo $day;?>">
                            <?php optionLoop($day_min,$day_max,$s_day);?>
                        </select>日
                        <select name="start_hour">
                            <?php for ($i=1; $i<24; $i++):?>
                            <option name="start_hour" value="<?php echo $i;?>"<?php if ($i == $s_hour):?>selected<?php endif;?>><?php echo $i ?></option>
                            <?php endfor; ?>
                        </select>時
                        <select name="start_min" >
                            <option class="start_min" value="00">00</option>
                            <option class="start_min" value="30">30</option>
                        </select>分
                    </td>  
                </tr>
                <tr>
                    <td>
                        終了日
                        <select name="end_year">
                            <?php for ($i=$s_combo_year; $i <= $e_combo_year; $i++) : ?>
                                <option value="<?php echo $i ?>"<?php if($i == $end_year) echo 'selected' ?>><?php echo $i ?></option>
                            <?php endfor ?>
                        </select>年
                        <select name="end_month" value="<?php echo $end_month;?>">
                            <?php optionLoop($month_min,$month_max,$end_month);?>
                        </select>月
                        <select name="end_day" value="<?php echo $end_day;?>">
                            <?php optionLoop($day_min,$day_max,$end_day);?>
                        </select>日
                        <select name="end_hour">
                            <?php for ($i=1; $i<24; $i++):?>
                            <option name="end_hour" value="<?php echo $i;?>"<?php if ($i == $end_hour):?>selected<?php endif;?>><?php echo $i ?></option>
                            <?php endfor; ?>
                        </select>時
                        <select name="end_min">
                            <option class="s_min" value="00">00</option>
                            <option class="s_min" value="30">30</option>
                        </select>分
                    </td>  
                </tr>
                <tr>
                    <td>タイトル</td>
                </tr>
                <tr>
                    <td><input type="text" id="schedule_title" name="schedule_title" value="<?php echo $schedules[$s_year][$s_month][$s_day][$schedule_id]['title'];?>" /></td>
                </tr>
                <tr>
                    <td>スケジュール内容</td>
                </tr>
                <tr>
                    <td>
                        <textarea id="schedule_contents" name="schedule_contents" rows="7" cols="60"><?php echo $schedules[$s_year][$s_month][$s_day][$schedule_id]['contents'];?></textarea>
                        <input type="hidden" name="schedule_id" value="<?php echo $schedule_id;?>" />
                    </td>
                </tr>
                <tr>
                    <td>
                        <?php if(!empty($schedule_id)):?>
                            <input class="submit_button" type="submit" value="更新" />
                        <?php else:?>
                            <input class="submit_button" type="submit" value="登録" />
                        <?php endif;?>
                        </form>
                        
                        <form class="delete" method="post" action="http://kensyu.aucfan.com/redirect_sql.php">
                            <input type="hidden" name="delete" value="delete" />
                            <input type="hidden" name="schedule_id" value="<?php echo $schedule_id;?>" />
                            <input id="submit_delete" type="submit" value="削除" />
                        </form>
                    </td>  
                </tr>
            </tbody>
        </table>
        <input type="button" value="&lt; 前に戻る" onclick="history.back()" />
    </div>
</body>
</html>