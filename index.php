<?php
require_once('config.php');

if(isset($_GET['short']) || isset($_GET['s'])){
	$short = (isset($_GET['short'])) ? $_GET['short'] : $_GET['s'];
	try{
		m('<h1>From Short to Original</h1>');
		m('SHORT: ' . SHORT_URL . '/' . $short);
		$s = new Shortener();
		$original = $s->deshorten($short);
		m('ORIGINAL: ' . $original);
		if( ! DEBUG) {
			header('Location: ' . $original, null, 301);
			?>
				<meta http-equiv=refresh content="0;URL=<?php echo $original; ?>"><a href="<?php echo $original; ?>">Continue</a><script>location.href='<?php echo $original; ?>'</script>
			<?php
			exit;
		}
	}catch(Exception $e){
		if(DEBUG) printr($e->getMessage());
	}


}else if(isset($_GET['original']) || isset($_GET['o'])){
	if(!AUTH || (isset($_GET['API_KEY']) && $_GET['API_KEY'] && isset($_GET['API_USER']) && $_GET['API_USER'] && $API_AUTH[$_GET['API_USER']] === $_GET['API_KEY'])){
		$tag = (isset($_GET['t'])) ? $_GET['t'] : '';
		$original = (isset($_GET['original'])) ? $_GET['original'] : $_GET['o'];
		try{
			m('ORIGINAL: ' . $original . ' -> ' . $tag);
			$s = new Shortener();
			$short = $s->shorten($original, $tag);
			m('SHORT: ' . SHORT_URL . '/' . $short);
			if( ! DEBUG){
				header('Content-Type: text/plain;charset=UTF-8');
				echo SHORT_URL . '/' . $short;
			}
		}catch(Exception $e){
			if(DEBUG) printr($e->getMessage());
		}
	}else{
		header('Location: ' . DEFAULT_URL, null, 301);
	}

	
}else if(isset($_GET['bmk']) && BOOKMARK_CREATOR){
	require_once(DOC_ROOT . '/' . 'bookmarklet.php');


}else{
	if(AUTH){
		header('Location: ' . DEFAULT_URL, null, 301);
		?>
			<meta http-equiv=refresh content="0;URL=<?php echo $original; ?>"><a href="<?php echo $original; ?>">Continue</a><script>location.href='<?php echo $original; ?>'</script>
		<?php
		exit;
	}else{
		?>
<!DOCTYPE html>
<html>
	<title>URL Shortener</title>
	<meta name="robots" content="noindex, nofollow">
	<style>
		body {
			font-size: 20px;
		}
		.url {
			width: 500px;
			font-size: 2em;
		}
		.submit {
			width: 500px;
			font-size: 2em;
		}
	</style>
</html>
<body>
	<center>
		<h1>URL to shorten</h1>
		<form method="post" action="shorten.php" id="shortener">
			<p><input class="url" type="text" name="longurl" id="longurl"></p>
			<p><input class="submit" type="submit" value="Shorten"></p>
		</form>
	</center>
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>
	<script type="text/javascript">
	$(function () {
		$('#shortener').submit(function () {
			$.ajax({data: {original: $('#longurl').val()}, url: '?o', complete: function (XMLHttpRequest, textStatus) {
				$('#longurl').val(XMLHttpRequest.responseText);
			}});
			return false;
		});
	});
	</script>
</body>
</html>
		<?php
	}
}

function m($m, $showanyway=false){
	if(DEBUG || $showanyway){
		echo $m . '<br />';
	}
}