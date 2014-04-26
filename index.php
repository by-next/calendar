<?php
$year = date('Y');//年取得
$month = 6; //date('m');//月取得
$day =  date('t',mktime(0,0,0,$month,1,$year));
$week = (int)($day / 7) + 1;
$week_array = array(
	"sun" => "日",
	"mon" => "月",
	"thu" => "火",
	"wed" => "水",
	"thi" => "木",
	"fir" => "金",
	"sat" => "土"
);
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<title>Calendar</title>
		<link rel="stylesheet" href="">
	</head>

	<body>
		<table border="1">
			<caption><?php echo $year.$month;?></caption>
			<thead>
				<tr>
					<?php foreach($week_array as $key => $value): ?>
					<th class="<?= $key ?>"><?= $value ?></th>
					<?php endforeach ?>
				</tr>
			</thead>
			<tbody>
				<?php $current_day = 1;  ?>
				<?php $index_day = $current_day; ?>

				<?php for($i=0; $i<$week; $i++): ?>
				<?php $current_week_num = 0;  ?>
				<tr>
					<?php for($n=$current_day; $n<$index_day+7; $n++):?>
					<?php if( $current_week_num == date('w', mktime(0, 0, 0, $month, $current_day, $year)) && $current_day <= $day): ?>
					<td class="date<?= $month.sprintf("%02d", $current_day) ?>"><?= $current_day ?></td>
					<?php $current_day++;  ?>
					<?php else: ?>
					<td>NaN</td>
					<?php endif; ?>
					<?php $current_week_num++;  ?>
					<?php endfor;?>
					<?php $index_day = $current_day; ?>
				</tr>
				<?php endfor; ?>
			</tbody>
		</table>
	</body>
</html>


