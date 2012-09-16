<?php

if(isset($_GET['edit']) && $_GET['edit'] == 'true'){
	if(isset($_POST['id']) &&
		isset($_POST['shorturl']) &&
		isset($_POST['longurl']) &&
		!empty($_POST['id']) &&
		!empty($_POST['shorturl']) &&
		!empty($_POST['longurl'])){

		if($admin->update($_POST['id'], $_POST['shorturl'], $_POST['longurl'])){ ?>
			<div class="success">The entry has been updated!</div>
		<?php }else { ?>
			<div class="error">An error occured while saving your edit! Make sure you have all data given!</div>
		<?php }
	}else{ ?>
		<div class="error">An error occured while saving your edit! Make sure you have all data given!</div>
	<?php }
}

$short = $admin->getOne($_GET['id']);
?>

<form method="post" action="?page=listEdit&edit=true&id=<?=$_GET['id'];?>" id="shortener">
	<p><label for="id">ID: </label><input class="url" type="text" name="id" id="id" value="<?=$short->id;?>" READONLY></p>
	<p><label for="shorturl">Short: </label><input class="url" type="text" name="shorturl" id="shorturl" value="<?=$short->short;?>"></p>
	<p><label for="longurl">Original: </label><input class="url" type="text" name="longurl" id="longurl" value="<?=$short->original;?>"></p>
	<p><label for="savedate">Save Date: </label><input class="savedate" type="text" name="savedate" id="savedate" value="<?=date("Y-m-d \a\\t H:m", $short->savedate);?>" READONLY></p>
	<p><label for="clicks">Clicks: </label><input class="clicks" type="text" name="clicks" id="clicks" value="<?=$short->clicks;?>" READONLY></p>
	<p><label for="last_visit">Last Visit: </label><input class="last_visit" type="text" name="last_visit" id="last_visit" value="<?=(!empty($short->last_visit)) ? date("Y-m-d \a\\t H:m", $short->last_visit) : 'n/a';?>" READONLY></p>
	<p><input class="submit" type="submit" value="Yes I am Sure"></p>
</form>