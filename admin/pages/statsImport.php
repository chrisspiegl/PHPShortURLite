<?php
if(isset($_GET['sure']) && $_GET['sure'] == true){
	$ret = $admin->importTrackFiles();
	if($ret >= 0){ ?>
		<div class="success">All track files have been imported (Lines: <?=$ret?>)!</div>
	<?php }else{ ?>
		<div class="error">There was an error importing the track files!</div>
	<?php }
}else{
?>
	<center>
		<h1>Are you sure you whish to import all stats files?</h1>
		<table>
			<tr>
				<td>Filename</td>
				<td>Size</td>
			</tr>

			<?php
			$overallsize = 0;
			$path = TRACK_DIR;
			if(is_dir($path)){
				$files = array_diff(scandir($path), array('.', '..', '.DS_Store'));
				foreach($files as $file){
					if(!preg_match('/^indb_/', $file)){
						$overallsize = $overallsize + filesize(TRACK_DIR.'/'.$file);
						?>
						<tr>
							<td class="center"><?=$file?></td>
							<td class="center"><?php printf("%01.4f", filesize(TRACK_DIR.'/'.$file)/1024/1024); ?> MiB</td>
						</tr>
						<?php
					}
				}
			}else echo '<div class="error">The folder in which the track files are stored was not found!</div>'
			?>
		</table>
		<?php if($overallsize > 0){ ?>
			<p class="success">Overall filesize to be imported: <?=printf("%01.4f", $overallsize/1024/1024); ?> MiB</p>
			<form method="post" action="?page=statsImport&sure=true" id="shortener">
				<p><input class="submit" type="submit" value="Yes I am Sure"></p>
			</form>
		<?php }else{ ?>
			<p class="success">Nothing to import!</p>
		<?php } ?>
	</center>
<?php
}