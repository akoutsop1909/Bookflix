<?php session_start();
require_once("utils.php");

ensureAuthenticated();

require_once("database.php");
$input = getInput();
$user = getActiveUser();


if ($_SERVER['REQUEST_METHOD'] == "GET") {
    // if the request we got have method GET we return the user, user's favorite books, bookings and bookstates.
    $profileResponse = array();
    $profileResponse["user"] = $user;
    $profileResponse["favorites"] = Database::getInstance()->getFavorites();
    $profileResponse["bookings"] = Database::getInstance()->getBookings();
    $profileResponse["bookStates"] = Database::getInstance()->getBookStates();
    respond(true, null, $profileResponse);
} else if ($_SERVER['REQUEST_METHOD'] == "POST") {
    // if the request we got have method POST we check the input field "type"
    // type:
    //       returnBooking: we add we remove the booking with the given bookId of the currently logged in user.
    //       addFavorite: we add a new favorite book for the currently logged in user
    //       removeFavorite: we remove a boook with the given bookId from the Favorite list of the currently
    //                       logged in user.
    //       unknown: we return that the type is unknown with 400 error bad request
    // we also check if the required fields are there. if they arent' we also throw 400 error bad request.
    if (!isset($input->bookID))
        respondError("bookID is missing", Http::BadRequest);

    $type = $input->type;
    $bookId = $input->bookID;
    switch ($type) {
        case "returnBooking":
            Database::getInstance()->deleteBooking($bookId);
            respond();
            break;
        case "addFavorite":
            if (!isset($input->title))
                respondError("title is missing", Http::BadRequest);
            if (!isset($input->author))
                respondError("author is missing", Http::BadRequest);
            if (!isset($input->type))
                respondError("type is missing", Http::BadRequest);
            $favorite = Database::getInstance()->createFavorite(new Book($input->bookID, $input->title, $input->author));
            respond(true, null, $favorite);
            break;
        case "removeFavorite":
            Database::getInstance()->deleteFavorite($bookId);
            respond();
            break;
        default:
            respondError("Unknown type: $type",  Http::BadRequest);
    }
} else {
    // if the request we got have unknown method we return 400 bad request.
    respondError('Unsupported HTTP Method: ' . $_SERVER['REQUEST_METHOD'] . '.', Http::BadRequest);
}