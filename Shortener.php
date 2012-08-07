<?php
class Shortener{
	public $table_linkbase = 'linkbase';
	public $use_cache = CACHE;
	private $db = null;

	public function __construct(){}

	public function __destruct(){
		$this->dbclose();
	}

	public function deshorten($short, $track = true){
		$short_org = $short;
		$short = preg_replace('/[^a-zA-Z0-9]/si', '', $short);

		if($this->use_cache){
			$original = @file_get_contents(CACHE_DIR . '/' . $short);
			if( ! empty($original)) m('Loaded from CACHE');
		}
		if(empty($original)) {
			m('Loaded from DB');
			$this->dbconnect();
			$res = $this->db->prepare("SELECT id, original FROM $this->table_linkbase WHERE short = '$short' LIMIT 1");
			$res->execute();
			if(($row = $res->fetch(PDO::FETCH_OBJ)) != null){
				$original = $row->original;
				if( ! is_dir(CACHE_DIR)) mkdir(CACHE_DIR, 0777);
				$handle = fopen(CACHE_DIR . '/' . $short, 'w+');
				if(fwrite($handle, $original)) m('Cache updated for ' . $short);
				fclose($handle);
			}else{
				$original = DEFAULT_URL . '/' . $short_org;
				$short = null;
			}
		}

		if($track && ! empty($short)) $this->log($short);
		return $original;
	}

	public function shorten($original){
		if(substr($original, 0, 7) != 'http://' && substr($original, 0, 8) != 'https://') $original = 'http://' . $original;
		if( ! $this->checkURL($original)) return;
		$this->dbconnect();
		$savedate = time();
		$res = $this->db->prepare("SELECT short FROM $this->table_linkbase WHERE original = '$original' LIMIT 1");
		$res->execute();
		if(($row = $res->fetch(PDO::FETCH_OBJ)) != null){
			return $row->short;
		}else{
			// Insert into table
			$this->db->prepare("INSERT INTO $this->table_linkbase ('original', 'savedate') VALUES ('$original', '$savedate')")->execute();
			// Get id to generate SHORT
			$id = $this->db->query("SELECT id FROM $this->table_linkbase ORDER BY id DESC LIMIT 1;")->fetch(PDO::FETCH_OBJ)->id;
			// Generate SHORT
			$short = $this->getShortenedURLFromID($id);
			// Update database (save SHORT)
			$this->db->query("UPDATE $this->table_linkbase SET short = '$short' WHERE id = '$id'");
			return $short;
		}
	}

	// Calculates the SHORT url based on the ID
	private function getShortenedURLFromID($integer, $base = ALLOWED_CHARS){
		$length = strlen($base);
		$out = null;
		while($integer > $length - 1)
		{
			$out = $base[fmod($integer, $length)] . $out;
			$integer = floor( $integer / $length );
		}
		return $base[$integer] . $out;
	}

	// Calculates the ID of the URL based on the SHORT url
	private function getIDFromShortenedURL($string, $base = ALLOWED_CHARS){
		$length = strlen($base);
		$size = strlen($string) - 1;
		$string = str_split($string);
		$out = strpos($base, array_pop($string));
		foreach($string as $i => $char){
			$out += strpos($base, $char) * pow($length, $size - $i);
		}
		return $out;
	}

	// Check if the url is valid (excludes all 404 sites)
	private function checkURL($url){
		if(CHECK_VALID_URL){
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch,  CURLOPT_RETURNTRANSFER, TRUE);
			$response = curl_exec($ch);
			if(curl_getinfo($ch, CURLINFO_HTTP_CODE) == '404'){
				curl_close($ch);
				//die('Not a valid URL');
				throw new Exception('Not a valid URL');
			}
			curl_close($ch);
		}
		return true;
	}

	// Log visits in log file
	private function log($short){
		if(TRACK){
			//$this->db->exec("UPDATE $this->table_linkbase SET clicks = clicks+1, last_visit = '$time' WHERE id = '$id'");
			$arr = array(
				'short' => $short,
				'time' => time(),
				'ip' => $_SERVER['REMOTE_ADDR'],
				'user_agent' => $_SERVER['HTTP_USER_AGENT'],
				'language' => $_SERVER['HTTP_ACCEPT_LANGUAGE'],
				'ref' => (isset($_SERVER["HTTP_REFERER"])) ? $_SERVER["HTTP_REFERER"] : ''
			);
			if( ! is_dir(TRACK_DIR)) mkdir(TRACK_DIR, 0777);
			$handle = fopen(TRACK_DIR . '/' . TRACK_FILE_NAME, 'a');
			flock($handle, LOCK_EX);
			fputs($handle, serialize($arr) . "\n");
			flock($handle, LOCK_UN);
			fclose($handle);
		}
	}

	private function dbconnect(){
		if($this->db == null) $this->db = new PDO('sqlite:' . DB_FILE_NAME);
	}

	private function dbclose(){
		$this->db = null;
	}
}
?>