<?php
/**
 * index.php
 *
 * @author Christoph Spiegl <chris@chrissp.com>
 * @package default
 */


ob_start();
require_once realpath(dirname(__FILE__) . '/..') . '/config.php';
ob_end_clean();

if (! isset($_SERVER['PHP_AUTH_USER']) ||
    ! isset($_SERVER['PHP_AUTH_PW']) ||
    $_SERVER['PHP_AUTH_PW'] != $API_AUTH[$_SERVER['PHP_AUTH_USER']]
) {
    header('WWW-Authenticate: Basic realm="' . SHORT_URL . ' - Shortener Admin"');
    header('HTTP/1.0 401 Unauthorized');
    //sleep(3);
    exit;
}

if ( ! isset($_GET['page'])) $_GET['page'] = 'home';
if ($_GET['page'] == 'home') $_GET['page'] = 'list';
require_once DOC_ROOT . '/admin/Admin.php';
$admin = new Admin();
?>
<!DOCTYPE html>
<html>
	<title><?=SHORT_URL;?> - Shortener Admin</title>
	<meta charset="utf-8" />
	<meta name="robots" content="noindex, nofollow">
	<link rel="stylesheet" href="_/css/jquery.jqplot.min.css?v=1.0" />
	<link rel="stylesheet" href="_/css/style.css?v=1.0" />
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js" type="text/javascript"></script>
    <script type="text/javascript" src="_/js/jquery.jqplot.min.js"></script>
    <script type="text/javascript" src="_/js/plugins/jqplot.highlighter.min.js"></script>
    <script type="text/javascript" src="_/js/plugins/jqplot.dateAxisRenderer.min.js"></script>
    <script type="text/javascript" src="_/js/plugins/jqplot.cursor.min.js"></script>
    <script type="text/javascript" src="../plugins/jqplot.ohlcRenderer.min.js"></script>
    <?php if ($_GET['page'] == 'stats'): ?>
    <script type="text/javascript" src="_/js/stats_graph.js"></script>
    <?php endif; ?>
</html>
<body>
	<header>
		<div class="error">Use with Caution: there are no safety nets!</div>
		<h1>URL - Shortener Admin</h1>
		<nav>
			<ul>
				<li><a href="?page=home">Home</a></li>
				<li><a href="?page=list">List</a></li>
				<li><a href="?page=stats">Stats</a></li>
				<li><a href="?page=cacheClear">Clear Cache</a></li>
				<li><a href="?page=statsImport">Import Stats</a></li>
			</ul>
		</nav>
	</header>

	<div id="main">
<?php
include DOC_ROOT . '/admin/pages/' . $_GET['page'] . '.php';
?>
	</div>

	<footer>
		Powered by <a href="https://github.com/cspiegl/PHPShortURLite/">PHPShortURLite</a> created by <a href="http://WhereIsChristoph.com">Chris Spiegl</a>.
	</footer>
</body>
</html>
