<?php
require_once('calendar.php');

//エスケープ処理
function h($s)
{
    return htmlspecialchars($s,ENT_QUOTES,'UTF-8');
}
//timestamp
$ym = isset($_GET['ym']) ? $_GET['ym'] : date('Y-m');

$cal = new Calendar($ym);

?>
<!DOCTYPE html>
<html lang="UTF-8">
<head>
    <link rel="stylesheet" href="css/style.css">
    <title>calendar</title>
</head>
<body>
    <table>
        <thead>
            <tr>
                <th><a href="?ym=<?php echo h($cal->prev()); ?>">&laquo;</a></th>
                <th colspan="5"><?php echo h(date("Y",$timeStamp)."-".date("m",$timeStamp)); ?></th>
                <th><a href="?ym=<?php echo h($cal->next()); ?>">&raquo;</a></th>
            </tr>
            <tr>
                <td>日</td>
                <td>月</td>
                <td>火</td>
                <td>水</td>
                <td>木</td>
                <td>金</td>
                <td>土</td>
            </tr>
        </thead>
        <tbody>
            <?php
                foreach ($cal->getWeeks() as $week) {
                    echo $week;
                }
            ?>
        </tbody>
    </table>
</body>
</html>