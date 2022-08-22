<?php
require_once("models.php");
$config = new Config();

//modify bellow
$config->databaseHostname = "****";
$config->databasePort = ****;
$config->databaseUsername = "****";
$config->databasePassword = "****";
$config->databaseName = "****";
//stop modifying from this point on.

return $config;
?>