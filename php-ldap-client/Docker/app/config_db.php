<?php
$db_port	  = intval(getenv('DB_PORT')) ?: 5432;
$db_host	  = getenv('DB_HOST') ?: "127.0.0.1";
$db_name	  = getenv('DB_NAME') ?: "oauth_db";
$db_type 	  = getenv('DB_TYPE') ?: "pgsql";
$db_user 	  = getenv('DB_USER') ?: "oauth";
$db_pass 	  = getenv('DB_PASS') ?: "oauth_secure-pass";
$dsn	      = $db_type . ":dbname=" . $db_name . ";host=" . $db_host . ";port=" . $db_port;

/* Uncomment the line below to set date.timezone to avoid E.Notice raise by strtotime() (in Pdo.php)
 * If date.timezone is not defined in php.ini or with this function, Mattermost could return a bad token request error
*/
//date_default_timezone_set ('Europe/Paris');
