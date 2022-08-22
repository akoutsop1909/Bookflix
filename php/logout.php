<?php session_start();

//we just destroy the session. No checks are required.
session_destroy();
require_once("utils.php");
respond();
?>

