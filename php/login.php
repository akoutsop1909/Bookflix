<?php session_start();
require_once("utils.php");

ensureUnauthenticated();

require_once("database.php");

$input = getInput();
//if username and/or password are not provided dies with 400 badrequest.
if (!isset($input->username) || !isset($input->password))
    respondError("Provide username and password.", Http::BadRequest);


//try to authenticate from the database.
$user = Database::getInstance()->authenticate($input->username, $input->password);

//if the result is null we throw 403 unauthorized
if ($user == null)
    respondError("Invalid username and/or password.", Http::Unauthorized);

//otherwise we initialize the session and storing user's data in it.
$_SESSION['username'] = $user->username;
$_SESSION['id'] = $user->id;
$_SESSION['email'] = $user->email;

respondObj($user);
?>