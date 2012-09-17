<?php

class Admin {
	public $table_linkbase = 'linkbase';
	public $table_track_log = 'track_log';
	private $db = null;

	public function __construct(){
		$this->dbconnect();
	}

	public function __destruct(){
		$this->dbclose();
	}

	public function getAll(){
		//$res = $this->db->prepare();
		$query = "SELECT * FROM $this->table_linkbase;";
		foreach($this->db->query($query) as $row){
			$ret[] = $row;
		}
		return $ret;	
	}

	public function getOne($id){
		$res = $this->db->prepare("SELECT * FROM $this->table_linkbase WHERE id = '$id' LIMIT 1;");
		$res->execute();
		if(($row = $res->fetch(PDO::FETCH_OBJ)) != null){
			$ret = $row;
		}else{
			$ret = -1;
		}
		return $ret;
	}

	public function update($id, $s, $o){
		$res = $this->db->prepare("SELECT short FROM $this->table_linkbase WHERE id = '$id' LIMIT 1;");
		$res->execute();
		if(($row = $res->fetch(PDO::FETCH_OBJ)) != null){
			$this->cacheDeleteOne($row->short);
		}
		return $this->db->query("UPDATE $this->table_linkbase SET short = '$s', original = '$o' WHERE id = '$id';");
	}

	public function cacheDeleteOne($s){
		$path = realpath(CACHE_DIR . '/' . $s);
		if(file_exists($path)){
			unlink($path);
		}
	}
	public function cacheClear(){
		return $this->deleteFolderContent(CACHE_DIR);
	}

	public function importTrackFiles(){
		$path = TRACK_DIR;
		$ret = 0;
		if(is_dir($path)){
			$files = array_diff(scandir($path), array('.', '..', '.DS_Store'));
			foreach($files as $file){
				if(!preg_match('/^indb_/', $file)){
					$ret += $this->importTrackFile($file);
					if($ret == -1) break;
				}
			}
		}else{
			$ret = -1;
		}
		return $ret;
	}

	public function importTrackFile($file){
		$logfile = realpath(TRACK_DIR) . '/' . $file;
		$logfilem =  realpath(TRACK_DIR) . '/' . 'indb_' . time() . '_' . $file;
		rename($logfile, $logfilem);
		if($fs = @fopen($logfilem, 'r')) {
			$lines = 0;
			$lines_error = '';
			while ($line = chop(fgets($fs, 4096))) {
				$lines++;
				$array = unserialize($line);
				//$query = ;
				//echo $query;
				$this->db->prepare("INSERT INTO $this->table_track_log ('short', 'time', 'ip', 'user_agent', 'language', 'ref') VALUES ('".$array['short']."', '".$array['time']."', '".$array['ip']."', '".$array['user_agent']."', '".$array['language']."', '".$array['ref']."');")->execute();
			}
		}else{ $lines = -1; echo '<p class="error">The file did not exist! DO NOT RELOAD THIS PAGE!</p>'; }
		return $lines;
	}

	public function getStats($datefrom, $dateto){
		$query = "SELECT * FROM $this->table_track_log WHERE time > $dateto AND time < $datefrom;";
		foreach($this->db->query($query) as $row){
			$ret[] = $row;
		}
		return $ret;
	}
	private function deleteFolderContent($path){
		if(is_dir($path)){
			$files = array_diff(scandir($path), array('.', '..'));
			foreach($files as $file){
				$this->deleteFolderContent(realpath($path) . '/' . $file);
			}
			return true;
		}else{
			return unlink($path);
		}
		return false;
	}

	private function dbconnect(){
		if($this->db == null) $this->db = new PDO('sqlite:' . DB_FILE_NAME);
	}

	private function dbclose(){
		$this->db = null;
	}
}