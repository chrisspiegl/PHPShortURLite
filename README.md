# PHPShortURLite
is a PHP based project to shorten urls without a MySql database. Instead we use a SQLite database to store the shortlink and original link. Additionally there is the availibility to cache the links. The log is done in a monthly file and implementation of the log-file to database connection is not yet implemented (That means: data is collected but not used yet).

# Installation

1. Move the files up to your FTP (desired directory)
2. Set the folder */data* to CHMOD 777
3. Make a copy of the *db.example.sqlite* file named *db.sqlite*
4. Make a copy of the *config.example.php* file named *config.php*
5. Edit your *config.php* file
6. Go to: http://YOURDOMAIN.com/?bmk=USERNAME (input your data before heading there)
7. Drag the bookmarkslets to your bookmark bar
8. Start shortening

# API

If you would like to shorten your URL in in another script:

## PHP

	$api_user = '';
	$api_key = '';
	
	$shortenedurl = file_get_contents('http://yourdomain.com/shorten/' . urlencode('http://' . $_SERVER['HTTP_HOST']  . '/' . $_SERVER['REQUEST_URI'])?API_USER=$api_user&API_KEY=$api_key);

# Bookmark

To easily install the bookmarklet go to: *http://YOURDOMAIN.com/?bmk=**AUTH_USER*** where **AUTH_USER** is your user set in the array (*config.php*). You will get the corosponding links, drag them into your bookmarks bar and you are set!

# Inspiration / Orientation / Thanks

I got some inspiring orientation form the following open source projects on GitHub. Thanks!

* [github.com - PHP-URL-Shortener](https://github.com/briancray/PHP-URL-Shortener)
* [github.com - php-url-shortener](https://github.com/mathiasbynens/php-url-shortener)
* [github.com - URL-Shortener](https://github.com/MaxKDevelopment/URL-Shortener)