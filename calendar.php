
<?php
//カレンダーと祝日情報の生成用に年月日の変数を作成
$year = date('Y');
$month = date('n');
$day = 1;

$last_date = mktime(0,0,0,$month,0,$year);

//date関数に前月の最終日のタイムスタンプを指定
//echo date("Y年m月d日",$last_date);

//ヘッダー部分の曜日表示
$days    = array('日', '月', '火', '水', '木', '金', '土');
$daysEng = array('sun', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat');

$nday = mktime();

for($i = 0; $i < 31; $i++){
    $w = (date('w') + $i) % 7;
    if ($w != 0){
        print date('d (' . $days[$w] . ')',mktime(0, 0, 0, date('m'), date('d')+$i, date('y'))) . "";
    }
}

echo $firstWeek;
echo $firstWeeks;

//祝日情報の配列を変数に格納する（まだ使わない
//$holidays = getHolidays($year);
$holidays['label'] = 'holiday';

//祝日情報を連想配列にいれるがまだ使わない
$events = array($holidays, $tes);

//カレンダーの出力
echo getCalendar($year, $month, $day, $events);
//指定月のカレンダーを生成
function getCalendar($year, $month, $day, $events) {
    global $days, $daysEng;

    //本日を取得する
    $today = date('Ymd');

    //1行ごとの順番1〜7と、月全体の順番1〜31日
    $num_row = 1;
    $num_day = 1;

    //月の始まる曜日から、前月の空白分を取得する
    $num_blank = date('w', mktime(0, 0, 0, $month, 1, $year));
 
    //月の合計日
    $total = $month == 2 ? ($year % 4 ? 28 : ($year % 100 ? 29 : ($year % 400 ? 28 : 29))) : (($month - 1) % 7 % 2 ? 30 : 31);
 
    //カレンダーのヘッダー部分
    $calendar = '<table class="calendar">';
    $calendar .= '<thead><tr>';
    for ($num_row=1; $num_row <= count($days); $num_row++) {
        $calendar .= '<th>'.$days[$num_row-1].'</th>';
    }
    $calendar .= '</tr></thead>';
 
    //カレンダーの本体部分
    $calendar .= '<tbody><tr>';
    $num_row = 1;
 
    //前月の空白分のセルを生成
    for ($i=$num_blank; $i > 0; $i--) {
       $label = '';
       $label = ($num_row == 1) ? $label.' sun' : $label;
       $calendar .= '<td class="'.$label.'"></td>';
       $num_row++;
    }
 
    //1日〜31日（最大）までのセルを生成
    for ($num_day=1; $num_day <= $total; $num_day++) {
 
        //$labelは要素のクラスとして$contentは祝日などを入れる予定
        //$label = '';
        //$content = '';
 
        //配列を形式で文字列を生成
        //$key = $year.sprintf('%02d', $month).sprintf('%02d', $num_day);
 
        //左端なら日曜日、右端なら土曜日のためのラベルを付加する
        //$label = ($num_row == 1) ? '日' : '';
        //$label = ($num_row == 7) ? '土' : '';
        $daysEng[$num_row];
        $label = $daysEng[$num_row-1];
 
        //本日の場合もクラスを付加する
        //$label = ($key == $today) ? $label.' today' : $label;
 
        $calendar .= '<td class="'.$label.'">';
        $calendar .= '<div class="day">'.$num_day.'</div>';
        $calendar .= $content;
        $calendar .= '</td>';
        // $num_day++;
        $num_row++;
 
        //1周間ごとに、新しい行を開始する
        if ($num_row > 7) {
            $calendar .= '</tr><tr>';
            $num_row = 1;
        }
    }
 
    //月の最終日以降の空白分のセルを生成
    for ($i = $num_row; $i <= 7; $i++){
        $calendar .= '<td></td>';
    }
 
    //カレンダー終わり
    $calendar .= '</tr></tbody></table>';
 
    //文字列として生成したカレンダーを返す
    return $calendar;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>3cal</title>
    <link rel="stylesheet" href="">
</head>
<body>
    <header id="header">
    </header><!-- /header -->
        <content>
            <div>
                <table>
                    <caption>table title and/or explanatory text</caption>
                    <thead>
                        <tr>
                        
                            <th><?php echo $calendar; ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>data</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </content>
    <footer>
    </footer>
</body>
</html>