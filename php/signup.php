<?php session_start();
require_once("utils.php");

//we ensure that there's no active session for this request.
ensureUnauthenticated();

require_once("database.php");

$input = getInput();
//if username and/or password, email are not provided dies with 400 badrequest.
if (!isset($input->username))
    respondError("Username not provided.",  Http::BadRequest);

if (!isset($input->password))
    respondError("Password not provided.", Http::BadRequest);

if (!isset($input->email))
    respondError("Email not provided.", Http::BadRequest);

//creates the new user in the database.
$user = Database::getInstance()->createUser($input->username, $input->password, $input->email);

//returns the user object to the user with a 201 created.
respondObj($user,Http::Created);
?>