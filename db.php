<?php
// DB接続
function db_connect(){
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
    return $connect;
}

?>