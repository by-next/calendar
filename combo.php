<?php
//コンボボックス内表示年数
$yearMin = '1970';
$yearMax = '2040';
//1月〜12月
$monthMin = '1';
$monthMax = '12';
//1日〜31日
$dayMin = '1';
$dayMax = '31';
//コンボボックス
function optionLoop($start, $end, $value = null){
 
    for($i = $start; $i <= $end; $i++){
        if(isset($value) ||  $value == $i){
            echo "<option value=\"{$i}\" selected=\"selected\">{$i}</option>";
        }else{
            echo "<option value=\"{$i}\">{$i}</option>";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>combo</title>
    <link rel="stylesheet" href="">
</head>
<body>
    <div id="header">
        <select name="year">
            <?php optionLoop($yearMin,$yearMax);?>
        </select>
        <p>年</p>
        <select name="year">
            <?php optionLoop($monthMin,$monthMax);?>
        </select>
        <p>月</p>
        <select name="day">
            <?php optionLoop($dayMin,$dayMax);?>
        </select>
        <p>日</p>
    </div>
    <div class="main">
    </div>
    <div id="footer">
    </div>
</body>
</html>
