<?php

	//エスケープ処理
	function h($s){
		return htmlspecialchars($s,ENT_QUOTES,'UTF-8');
	}
	

	//timestamp

	$ym = isset($_GET['ym']) ? $_GET['ym'] : date("Y-m");

	$timeStamp = strtotime($ym . "-01");

	//もし不正な値の場合当月を表示
	if ($timeStamp === false) {
		$timeStamp = time();
	}

	//先月リンク
	$prev = date("Y-m",mktime(0,0,0,date("m",$timeStamp)-1,1,date("Y",$timeStamp)));
	//次月リンク
	$next = date("Y-m",mktime(0,0,0,date("m",$timeStamp)+1,1,date("Y",$timeStamp)));


	//最終日
	$lastDay = date("t",$timeStamp);

	$weekday = date("w",mktime(0,0,0,date("m",$timeStamp),1,date("Y",$timeStamp)));

	//var_dump($lastDay);
	//var_dump($week);

	$weeks = array();
	$week = "";

	$week .= str_repeat("<td></td>", $weekday);

	for ($day=1; $day <= $lastDay; $day++, $weekday++) { 
		$week .= sprintf('<td class="youbi_%d">%d</td>', $weekday % 7, $day);

		if ($weekday % 7 == 6 OR $day == $lastDay) {
			if (condition) {
				$week .= str_repeat('<td></td>', 6 - ($weekday % 7));
			}
			$weeks[] = '<tr>' . $week . '</tr>';
			$week = '';
		}
	}

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
				<th><a href="?ym=<?php echo h($prev); ?>">&laquo;</a></th>
				<th colspan="5"><?php echo h(date("Y",$timeStamp)."-".date("m",$timeStamp)); ?></th>
				<th><a href="?ym=<?php echo h($next); ?>">&raquo;</a></th>
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
				foreach ($weeks as $week) {
					echo $week;
				}
			?>
		</tbody>
	</table>

</body>
</html>