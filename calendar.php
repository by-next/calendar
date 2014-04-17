
<?php
//カレンダーと祝日情報の生成用に年月日の変数を作成
$year = date('Y');
$month = date('n');
$day = 1;

//祝日情報の配列を変数に格納する（まだ使わない
//$holidays = getHolidays($year);
$holidays['label'] = 'holiday';

//祝日情報を連想配列にいれるがまだ使わない
$events = array($holidays, $tes);

//カレンダーの出力
echo getCalendar($year, $month, $day, $events);
//指定月のカレンダーを生成
function getCalendar($year, $month, $day, $events) {

    //ヘッダー部分の曜日表示
    $days = array('日', '月', '火', '水', '木', '金', '土');
 
    //本日を取得する
    $today = date('Ymd');

    //1行ごとの順番（1〜7）と、月全体の順番（1〜31（最大））
    $num_row = 1;
    $num_day = 1;

    //月の始まる曜日から、前月の空白分を取得する
    $num_blank = date('w', mktime(0, 0, 0, $month, 1, $year));
 
    //月の合計日
    $total = $month == 2 ? ($year % 4 ? 28 : ($year % 100 ? 29 : ($year % 400 ? 28 : 29))) : (($month - 1) % 7 % 2 ? 30 : 31);
 
    //カレンダーのヘッダー部分
    $calendar = '<table class="calendar">';
    $calendar .= '<thead><tr>';
    while ($num_row <= count($days)) {
        $calendar .= '<th>'.$days[$num_row-1].'</th>';
        $num_row++;
    }
    $calendar .= '</tr></thead>';
 
    //カレンダーの本体部分
    $calendar .= '<tbody><tr>';
    $num_row = 1;
 
    //前月の空白分のセルを生成
    while ($num_blank > 0) {
        $label = '';
        $label = ($num_row == 1) ? $label.' sun' : $label;
        $calendar .= '<td class="'.$label.'"></td>';
        $num_blank--;
        $num_row++;
    }
 
    //1日〜31日（最大）までのセルを生成
    while ($num_day <= $total) {
 
        //$labelは要素のクラスとして$contentは祝日などを入れる予定
        //$label = '';
        //$content = '';
 
        //配列を形式で文字列を生成
        //$key = $year.sprintf('%02d', $month).sprintf('%02d', $num_day);
 
        //左端なら日曜日、右端なら土曜日のためのラベルを付加する
        $label = ($num_row == 1) ? $label.'日' : $label;
        $label = ($num_row == 7) ? $label.'土' : $label;
 
        //本日の場合もクラスを付加する
        $label = ($key == $today) ? $label.' today' : $label;
 
        $calendar .= '<td class="'.$label.'">';
        $calendar .= '<div class="day">'.$num_day.'</div>';
        $calendar .= $content;
        $calendar .= '</td>';
        $num_day++;
        $num_row++;
 
        //1周間ごとに、新しい行を開始する
        if ($num_row > 7) {
            $calendar .= '</tr><tr>';
            $num_row = 1;
        }
    }
 
    //月の最終日以降の空白分のセルを生成
    for ($num_row = 1; $num_row > 1 && $num_row <= 7; $num_row++){
        $label = '';
        $label = ($num_row == 7) ? $label.'土' : $label;
        $calendar .= '<td class="'.$label.'"></td>';
    }
 
    //カレンダー終わり
    $calendar .= '</tr></tbody></table>';
 
    //文字列として生成したカレンダーを返す
    return $calendar;
}
?>