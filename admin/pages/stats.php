<?php

if(isset($_GET['days'])) $days = $_GET['days'];
else $days = 30;

$from = time();
$to = time() - (60*60*24*$days);

$data = $admin->getStats($from, $to);
$clicks = count($data);
$arr = array(
	'short'=>array(),
	'ip'=>array(),
	'user_agent'=>array(),
	'language'=>array(),
	'ref'=>array()
);
foreach($data as $d){
	//$d['short'] = '\'' . $d['short'] . '\'';
	foreach($arr as $key=>&$val){
		$val[$d[$key]] = (isset($val[$d[$key]])) ? $val[$d[$key]]+1 : 1;
	}
}

foreach($arr as $key=>&$val){
	arsort($arr[$key]);
}

//printr($arr);

?>
<p class="success">Overall clicks in the last <?=$days?> days: <?=$clicks;?></p>
<table>
	<tr>
		<td>Shortlink</td>
		<td>IPs</td>
		<td>User-Agents</td>
		<td>Languages</td>
		<td>Refs</td>
	</tr>
	<tr>
		<?php
			foreach($arr as $key=>$val){
		?>
		<td style="vertical-align: top;">
			<table>
				<?php foreach($val as $k=>$v){ ?>
					<tr><td><?=$k;?></td><td><?=$v;?></td></tr>
				<?php } ?>
			</table>
		</td>
		<?php } ?>
	</tr>
</table>