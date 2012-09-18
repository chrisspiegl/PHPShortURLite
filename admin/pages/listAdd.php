<style>
	.url, .tag {
		width: 500px;
		font-size: 2em;
	}
	.submit {
		width: 500px;
		font-size: 2em;
	}
</style>
<center id="form">
	<h1>URL to shorten</h1>
	<form method="post" action="#" id="shortener">
		<p><input class="url" placeholder="Long URL" type="text" name="longurl" id="longurl"></p>
		<p><input class="tag" placeholder="Short Tag" type="text" name="tag" id="tag"></p>
		<p><input class="submit" type="submit" value="Shorten"></p>
		<p id="status" class="success"></p>
	</form>
</center>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>
<script type="text/javascript">
$(function () {
	$('#shortener').submit(function () {
		var req = $.ajax({
			url: '/?o=' + $('#longurl').val() + '&t=' + $('#tag').val() + '&API_USER=' + "<?=$_SERVER['PHP_AUTH_USER'];?>" + '&API_KEY=' + "<?=$_SERVER['PHP_AUTH_PW'];?>",
			complete: function (XMLHttpRequest, textStatus) {
				if( XMLHttpRequest.responseText === "URL is invalid"){
					$('#status').removeClass('success');
					$('#status').addClass('error');
				}else{
					$('#status').removeClass('error');
					$('#status').addClass('success');
				}
				$('#status').html(XMLHttpRequest.responseText);
			}
		});
		return false;
	});
});
</script>