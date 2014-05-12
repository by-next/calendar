<?php
// 日付のタームゾーンを変更
ini_set("date.timezone", "Asia/Tokyo");
// googleAPI祝日取得
define("CALENDAR_URL", "outid3el0qkcrsuf89fltf7a4qbacgt9@import.calendar.google.com");
//+祝日取得開始
function getGoogleCalender($min_date, $max_date){
    // 祝日の配列
    $holidays = array();
    // google apiのurl
    $url = 'http://www.google.com/calendar/feeds/%s/public/full-noattendees?%s';
    // パラメータ
    $params = array(
        'start-min'   => $min_date,
        'start-max'   => $max_date,
        'max-results' => 30,
        'alt'         => 'json',
        );
    $queryString = http_build_query($params);
    // URLを取得
    $getUrl = sprintf($url, CALENDAR_URL, $queryString);
    // データ取得
    if($results = file_get_contents($getUrl)){
        // デコードしたデータ
        $resultsDecode = json_decode($results, true);
        // 休日を設定するリスト
        $holidays = array();
        // リスト分出力
        foreach ($resultsDecode['feed']['entry'] as $key => $val){
            // 日付
            $date = $val['gd$when'][0]['startTime'];
            // タイトル
            $title = $val['title']['$t'];
            $title = explode(' / ', $title);
            // 日付をキーに設定
            $holidays[$date] = $title[0];
        }
    }
    return $holidays;
}
?>