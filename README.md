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

# Custom Tags or Not

Custom tags are usefull if you would like to set your own short link tags for some urls. Those are better to remember and to put on business cards or the like. But they come with a disadvantage: they need some letters to be taken from the overall pool. Thus you can activate them and deactivate them in the *config.php* file. **BUT**: do not change that setting **EVER**! In this implementation, the custom tags and the standard short links are differentiated by the fact that standard short links have always start with a number (0-9). This number is the last digit of the ID in the database.

# API

If you would like to shorten your URL in in another script:

## PHP

	<?php
	$serverAPIurl = 'shortener.imac:8888';
	$api_user = 'cspiegl';
	$api_key = '27459b72456f8fd37e7080bb7fcbe6884c54697f';
	$url_to_be_shortened = 'http://cspiegl.com/2012/08/08/force-ssl';

	$shorturl = file_get_contents('http://' . $serverAPIurl . '/?o=' . urlencode($url_to_be_shortened) . '&API_USER=' . $api_user . '&API_KEY=' . $api_key);
	echo $shorturl;

# Bookmark

To easily install the bookmarklet go to: *http://YOURDOMAIN.com/?bmk=**AUTH_USER*** where **AUTH_USER** is your user set in the array (*config.php*). You will get the corosponding links, drag them into your bookmarks bar and you are set!

# Inspiration / Orientation / Thanks

I got some inspiring orientation form the following open source projects on GitHub. Thanks!

* [github.com - PHP-URL-Shortener](https://github.com/briancray/PHP-URL-Shortener)
* [github.com - php-url-shortener](https://github.com/mathiasbynens/php-url-shortener)
* [github.com - URL-Shortener](https://github.com/MaxKDevelopment/URL-Shortener)

# Version / Changelog

## Roadmap

* Logfile to database automation script
* Stats analysis
* Admin interface
* Shorten interface

## 2012-08-08: v0.1.1

* Added custom shortlink algorithm

## 2012-08-07: v0.1

* Started the project
* Simple base implementation
* Writing logs into monthly log files (serialized array of data)
* Caching shortlink
* No custom tags