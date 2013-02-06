<?php
/**
 * /tmp/phptidy-sublime-buffer.php
 *
 * @author Christoph Spiegl <chris@chrissp.com>
 * @package default
 */


if (isset($_GET['days'])) $days = $_GET['days'];
else $days = 30;

$from = time();
$to = time() - (60*60*24*$days);

$data = $admin->getStats($from, $to);
$clicks = count($data);
if($clicks > 0){
    /**
     * Sort the data to calculate the overall sum for how often the following appears:
     * shortlink, an ip, a user_agent, a language, a referal
     */
    $arr = array(
        'short'=>array(),
        'ip'=>array(),
        'user_agent'=>array(),
        'language'=>array(),
        'ref'=>array()
    );
    foreach ($data as $d) {
        foreach ($arr as $key=>&$val) {
            $val[$d[$key]] = (isset($val[$d[$key]])) ? $val[$d[$key]]+1 : 1;
        }
    }
    foreach ($arr as $key=>&$val) {
        arsort($arr[$key]);
    }

    /**
     * Now let's go through and sum up the days
     */
    $daily_log = array();
    $log_day = strtotime("00:00:00");
    for($i = 0; $i <= $days; $i++){
        $daily_log[$log_day]= array('hits'=>0,'shorts'=>array());;
        $log_day = strtotime("-1 day", $log_day);
    }
    foreach ($data as $d) {
    	$log_day = strtotime("00:00:00", $d['time']);
        $daily_log[$log_day]['hits']++;
        if(!isset($daily_log[$log_day]['shorts'][$d['short']])) $daily_log[$log_day]['shorts'][$d['short']] = 1;
        else $daily_log[$log_day]['shorts'][$d['short']]++;
        array_multisort(array_values($daily_log[$log_day]['shorts']), array_keys($daily_log[$log_day]['shorts']), $daily_log[$log_day]['shorts']);
        $daily_log[$log_day]['shorts'] = array_reverse($daily_log[$log_day]['shorts']);
    }
    $daily_log_js = '[';
    $i = 0;
    foreach($daily_log as $key=>&$val){
        if($i < count($daily_log) && $i != 0) $daily_log_js .= ',';
        $daily_log_js .= "['".date('Y-m-d', $key)."',".$val['hits'];
        $daily_log_js .= ",'";
        $j = 0;
        foreach($val['shorts'] as $key1=>$val1){
            if($j > 5) { $daily_log_js .= '<br />'; $j = 0; }
            $daily_log_js .= "$key1: $val1, ";
            $j++;
        }
        $daily_log_js .= "'";
        $daily_log_js .= "]";

        $i++;
    }
    $daily_log_js .= ']';
}
?>

<form action="?page=stats" method="get">
    <label for="days">How many days should the stats show: </label><input name="days" type="text" value="<?=$days?>" \>
    <input type="hidden" name="page" value="stats" />
    <input type="submit" name="formSubmit" value="Submit">
</form>

<p class="success">Overall clicks in the last <?=$days?> days: <?=$clicks;?></p>
<?php if($clicks > 0): ?>
<div id="chartClicksLine" style="height:300px; width:100%; padding: 1em;"></div>

<script class="code" type="text/javascript">
$(document).ready(function(){
  var line1=<?=$daily_log_js; ?>;
  var plot1 = $.jqplot('chartClicksLine', [line1], {
      title:'Data Point Highlighting',
      axes:{
        xaxis:{
          renderer:$.jqplot.DateAxisRenderer,
          tickOptions:{
            formatString:'%b&nbsp;%#d'
          }
        },
        yaxis:{
          tickOptions:{
            formatString:'%d'
            }
        }
      },
      highlighter: {
        show: true,
        sizeAdjust: 7.5,
        yvalues: 3,
        formatString:'<h2>%s : %d Clicks<br /></h2><h3>Shorts</h3>%s'
      },
      cursor: {
        show: true,
        zoom: true
      }
  });
});
</script>






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
foreach ($arr as $key=>&$val) {
?>
		<td style="vertical-align: top;">
			<table>
				<?php foreach ($val as $k=>$v) { ?>
					<tr><td><?=$k;?></td><td><?=$v;?></td></tr>
				<?php } ?>
			</table>
		</td>
		<?php } ?>
	</tr>
</table>
<?php endif;