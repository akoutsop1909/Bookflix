<?php
require_once("models.php");
$config = new Config();

//modify bellow
$config->databaseHostname = "localhost";
$config->databasePort = 3306;
$config->databaseUsername = "username";
$config->databasePassword = "password";
$config->databaseName = "databaseName";
//stop modifying from this point on.

return $config;
?>