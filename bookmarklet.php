<?php
$code = array();
$code['prompt'] = <<<EOF
var q=prompt('URL:', 'http://'); var t=prompt('Tag:', ''); if(q){document.location='http://TARGET/?o='+encodeURIComponent(q)+"&t="+t+'&API_KEY=%API_KEY%&API_USER=%API_USER%';};
EOF;

$code['this'] = <<<EOF
var t=prompt('Tag:', ''); document.location='http://TARGET/?o='+encodeURIComponent(location.href)+"&t="+t+'&API_KEY=%API_KEY%&API_USER=%API_USER%';
EOF;

foreach($code as $key=>$val){
	$code[$key] = str_replace('TARGET', SHORT_URL, trim($code[$key]));
	$code[$key] = str_replace('%API_KEY%', $API_AUTH[$_GET['bmk']], trim($code[$key]));
	$code[$key] = str_replace('%API_USER%', $_GET['bmk'], trim($code[$key]));
	//echo $code[$key] . '<br /> ';
}
?>
<p>
	<a href="javascript:<?= rawurlencode($code['prompt']) ?>">Prompt URL</a>
</p>
<p>
	<a href="javascript:<?= rawurlencode($code['this']) ?>">Shorten URL</a>
</p>

<?php

exit;