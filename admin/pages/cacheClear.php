<?php
if(isset($_GET['sure']) && $_GET['sure'] == true){
	if($admin->cacheClear()){ ?>
		<div class="success">The cach is now empty</div>
	<?php }else{ ?>
		<div class="error">There was an error clearing the cache!</div>
	<?php }
}else{
?>
	<center>
		<h1>Are you sure you whish to clear the cache?</h1>
		<form method="post" action="?page=cacheClear&sure=true" id="shortener">
			<p><input class="submit" type="submit" value="Yes I am Sure"></p>
		</form>
	</center>
<?php
}