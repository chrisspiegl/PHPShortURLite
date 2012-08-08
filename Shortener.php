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
			$res = $this->db->prepare("SELECT id, original FROM $this->table_linkbase WHERE short = '$short' LIMIT 1;");
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

	public function shorten($original, $tag = ''){
		if( ! CUSTOMTAG && ! empty($tag)) die('Custom tags are deactivated');
		if(preg_match('/^[0-9]|[^a-zA-Z0-9]/', $tag)) die( '<strong>' . $tag . '</strong>: is a tag that does not match the cretieria (tags must NOT have a numerical as first character and must NOT contain any special characters)!');
		if( ! preg_match('|^http(s)?://[a-z0-9-]+(\.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|', $original)) die('URL is invalid');	// |^http(s)?://[a-z0-9-]+(\.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i
		if( ! $this->checkURL($original)) return;
		$this->dbconnect();
		$savedate = time();
		$res = $this->db->prepare("SELECT short FROM $this->table_linkbase WHERE original = '$original'" . ((CUSTOMTAG && ! empty($tag)) ? " OR short = '$tag'" : "") . " LIMIT 1;");
		$res->execute();

		if(($row = $res->fetch(PDO::FETCH_OBJ)) != null){
			if(CUSTOMTAG && ! empty($tag) && $row->short === $tag) die('The tag (<strong>' . $tag . '</strong>) is already in use.');
			return $row->short;
		}else{
			if( ! empty($tag)){
				$short = $tag;
				// Insert into table
				$this->db->prepare("INSERT INTO $this->table_linkbase ('original', 'short', 'savedate') VALUES ('$original', '$short', '$savedate');")->execute();
			}else{
				// Insert into table
				$this->db->prepare("INSERT INTO $this->table_linkbase ('original', 'savedate') VALUES ('$original', '$savedate');")->execute();
				// Get id to generate SHORT
				$id = $this->db->query("SELECT id FROM $this->table_linkbase ORDER BY id DESC LIMIT 1;")->fetch(PDO::FETCH_OBJ)->id;
				// Generate SHORT
				$short = $this->getShortenedURLFromID($id);
				// Update database (save SHORT)
				$this->db->query("UPDATE $this->table_linkbase SET short = '$short' WHERE id = '$id';");
			}
			return $short;
		}
	}

	// Calculate SHORT based on ID (deciding if CUSTOMTAG or not)
	private function getShortenedURLFromID($integer){
		return (CUSTOMTAG) ? $this->getShortenedURLFromIDCustom($integer) : $this->getShortenedURLFromIDnonCustom($integer);
	}

	// Calculates the SHORT url based on the ID (Without Custom)
	private function getShortenedURLFromIDnonCustom($integer){
		$base = ALLOWED_CHARS;
		$length = strlen($base);
		$out = '';
		while($integer > $length - 1){
			$out = $base[fmod($integer, $length)] . $out;
			$integer = floor( $integer / $length );
		}
		return $base[$integer] . $out;
	}

	// Calculates the SHORT url based on the ID (With Custom (meaning it forces numerical on first char!))
	private function getShortenedURLFromIDCustom($integer){
		$lastChar = substr($integer, strlen($integer)-1, strlen($integer));	// Last digit of the $integer is substracted of the number and forced to be the first char of the SHORT tag
		$short = (substr($integer, 0, strlen($integer)-1) > 0) ? $this->getShortenedURLFromIDnonCustom(substr($integer, 0, strlen($integer)-1)) : '';
		return $lastChar . $short;	// Call the getShortenedURLFromIDnonCustom using the numerical without the last digit
	}

	// Calulates the ID form the URL based on the SHORT url deciding if CUSTOM or not
	private function getIDFromShortenedURL($string){
		return (CUSTOMTAG) ? $this->getIDFromShortenedURLCustom($string) : $this->getIDFromShortenedURLnonCustom($string);
	}

	// Calculates the ID of the URL based on the SHORT url
	private function getIDFromShortenedURLnonCustom($string){
		$base = ALLOWED_CHARS;
		$length = strlen($base);
		$size = strlen($string) - 1;
		$string = str_split($string);
		$out = strpos($base, array_pop($string));
		foreach($string as $i => $char){
			$out += strpos($base, $char) * pow($length, $size - $i);
		}
		return $out;
	}

	// Calculates the ID of the URL based on the SHORT url
	private function getIDFromShortenedURLCustom($string){
		if(strlen($string) > 1) return $this->getIDFromShortenedURLnonCustom(substr($string, 1, strlen($string))) . substr($string, 0, 1);
		else return $string;
	}

	// Check if the url is valid (excludes all 404 sites)
	private function checkURL($url){
		if(CHECK_404_URL){
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