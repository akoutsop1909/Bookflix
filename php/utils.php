<?php
//we disable the warning/error logging because we serve json.
error_reporting(0);
require_once("models.php");

//All php's responses will be application/json
header('Content-Type: application/json');

/**
 * General respond method. Uses {@link GenericResponse} model
 * to return a json object with state, errorMessage, data
 * and a given http code for the response.
 *
 * It also terminates the execution after this command is
 * issued.
 *
 * @param bool $state
 * @param string|null $message
 * @param object|null $model
 * @param int $httpCode
 */
function respond($state = true, $message = null, $model = null, $httpCode = Http::OK)
{
    http_response_code($httpCode);
    die(json_encode(new GenericResponse($state, $message, $model)));
}

/**
 * Respond with an object using the generic {@link respond()} method
 * The default http code is {@link Http::OK} which is 200.
 *
 * @param $model
 * @param int $httpCode
 */
function respondObj($model, $httpCode = Http::OK)
{
    respond(true, null, $model, $httpCode);
}

/**
 * Respond with an error message and an http code.
 *
 * The default http code is {@link Http::ServerError} which is 500.
 * @param $message
 * @param int $httpCode
 */
function respondError($message, $httpCode = Http::ServerError)
{
    respond(false, $message, null, $httpCode);
}

/**
 * Responds with the underlying SQL error and a 500 http code.
 * @param $mysqlconn
 */
function respondDbError($mysqlconn)
{
    respond(false, mysqli_error($mysqlconn), null, Http::ServerError);
}

/**
 * This method combines the inputs of GET, POST and php://input
 * into a single object for easier access.
 * @return object
 */
function getInput()
{
    $arrayOfArrays = array();
    if (isset($_POST) && !empty($_POST)) {
        $arrayOfArrays[] = (array)$_POST;
    }
    if (isset($_GET) && !empty($_GET)) {
        $arrayOfArrays[] = (array)$_GET;
    }
    $arrayOfArrays[] = json_decode(file_get_contents("php://input", true));

    $finalArray = array();
    for ($i = 0; $i < count($arrayOfArrays); $i++) {
        $finalArray = array_merge($finalArray, (array)$arrayOfArrays[$i]);
    }

    return (object)$finalArray;
}

/**
 * If there's an active session it returns the user object of the logged in user.
 *
 * @return User|null
 */
function getActiveUser()
{
    if (isAuthenticated()) {
        return new User((int)$_SESSION['id'], $_SESSION['username'], $_SESSION['email']);
    }
    return null;
}

/**
 * Returns whether there's a user logged in for this request.
 *
 * @return bool
 */
function isAuthenticated()
{
    return isset($_SESSION['id']) && $_SESSION['id'] != null;
}

/**
 *  Ensures that a user is logged in for this request by
 *  terminating the execution and replying with 401 if
 *  is not authenticated.
 */
function ensureAuthenticated()
{
    if (!isAuthenticated()) {
        respondError("Unauthenticated", Http::Unauthenticated);
    }
}

/**
 *  Ensures that a user isn't logged in for this request by
 *  terminating the execution and replying with 403 if
 *  is authenticated.
 */
function ensureUnauthenticated()
{
    if (isAuthenticated()) {
        respondError("You are authenticated you can't do this.", Http::Unauthorized);
    }
}