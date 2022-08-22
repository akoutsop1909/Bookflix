<?php session_start();
require_once("utils.php");

//ensure user is authenticated
ensureAuthenticated();

//return active user
respondObj(getActiveUser());
?>