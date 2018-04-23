<?php
define('DB_SERVER', 'Host');
define('DB_USERNAME', 'User Name');
define('DB_PASSWORD', 'Password');
define('DB_DATABASE', 'Database');
$connection = mysql_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD) or die(mysql_error());
$database = mysql_select_db(DB_DATABASE) or die(mysql_error());
?>