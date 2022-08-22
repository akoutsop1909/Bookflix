<?php session_start();
require_once("utils.php");

ensureAuthenticated();

require_once("database.php");

$input = getInput();
if ($_SERVER['REQUEST_METHOD'] == "GET") {
    // if the request we got have method GET we return the bookstates for the user.
    $bookId = null;
    if (isset($input->bookID)) {
        $bookId = $input->bookID;
    }

    $bookStates = Database::getInstance()->getBookStates($bookId);
    $bookStatesResponse = new BookStateResponse(getActiveUser()->id,$bookStates);
    respondObj($bookStatesResponse);
} else if ($_SERVER['REQUEST_METHOD'] == "POST") {
    // if the request we got have method POST we check the input field "type"
    // type:
    //       addBooking: we add new booking using the rest of the fields and the currently logged in user.
    //       addFavorite: we add a new favorite book for the currently logged in user
    //       unknown: we return that the type is unknown with 400 error bad request
    // we also check if the required fields are there. if they arent' we also throw 400 error bad request.
    if (!isset($input->bookID))
        respondError("bookID is missing", Http::BadRequest);
    if (!isset($input->title))
        respondError("title is missing", Http::BadRequest);
    if (!isset($input->author)) {
        //respondError("author is missing", Http::BadRequest);
        $input->author = "Uncredited";
    }
    if (!isset($input->type))
        respondError("type is missing", Http::BadRequest);

    switch ($input->type) {
        case "addBooking":
            if (!isset($input->returnDate))
                respondError("returnDate is missing", Http::BadRequest);

            $booking = Database::getInstance()->createBooking(new Book($input->bookID, $input->title, $input->author), $input->returnDate);
            respondObj($booking, Http::Created);
            break;
        case "addFavorite":
            $favorite = Database::getInstance()->createFavorite(new Book($input->bookID, $input->title, $input->author));
            respondObj($favorite, Http::Created);
            break;
        default:
            respondError("Unknown type: $input->type for HTTP Method " . $_SERVER['REQUEST_METHOD'] . ".", Http::BadRequest);
    }
} else if ($_SERVER["REQUEST_METHOD"] == "DELETE") {
    // if the request we got have method DELETE we check the input field "type"
    // type:
    //       returnBooking: we remove the currently booked book from the currently logged in user.
    //       removeFavorite: we remove a book from the currently logged in user's favorite list.
    //       unknown: we return that the type is unknown with 400 error bad request
    // we also check if the required fields are there. if they arent' we also throw 400 error bad request.
    if (!isset($input->bookID))
        respondError("bookId is missing", Http::BadRequest);

    if (!isset($input->type))
        respondError("type is missing", Http::BadRequest);

    switch ($input->type) {
        case "returnBooking":
            Database::getInstance()->deleteBooking($input->bookID);
            respond();
            break;
        case "removeFavorite":
            Database::getInstance()->deleteFavorite($input->bookID);
            respond();
            break;
        default:
            respondError("Unknown type: $input->type for HTTP Method " . $_SERVER['REQUEST_METHOD'] . ".", Http::BadRequest);
    }

} else {
    // if the request we got have unknown method we return 400 bad request.
    respondError('Unsupported HTTP Method: ' . $_SERVER['REQUEST_METHOD'] . '.', Http::BadRequest);
}
?>
