<?php


/**
 * Http Class acting as enum for HTTP Codes.
 */
abstract class Http
{
    const NotFound = 404;
    const BadRequest = 400;
    const ServerError = 500;
    const Unauthenticated = 401;
    const Unauthorized = 403;
    const Conflict = 409;
    const OK = 200;
    const Created = 201;
}


/**
 * The default layout for each response comming out
 * from php.
 */
class GenericResponse
{
    public $state;
    public $errorMessage;
    public $payload;

    function __construct($state, $message = "", $data = null)
    {
        $this->state = $state;
        $this->errorMessage = $message;
        $this->payload = $data;
    }
}

/**
 * Class BookStateResponse
 */
class BookStateResponse
{
    public $userId;
    public $bookStates;

    public function __construct(int $userId, array $bookStates)
    {
        $this->userId = $userId;
        $this->bookStates = $bookStates;
    }
}

/**
 * Class User
 */
class User
{
    public $id;
    public $username;
    public $email;

    function __construct($id, $username, $email)
    {
        $this->username = $username;
        $this->email = $email;
        $this->id = $id;
    }
}

/**
 * Class Book
 */
class Book
{
    public $bookID;
    public $title;
    public $author;

    public function __construct($bookID, $title, $author)
    {
        $this->bookID = $bookID;
        $this->title = $title;
        $this->author = $author;
    }
}

/**
 * Class BookState
 */
class BookState
{
    public $bookID;
    public $favorited;
    public $booked;

    public function __construct($bookID, $favorited, $booked)
    {
        $this->bookID = $bookID;
        $this->favorited = $favorited;
        $this->booked = $booked;
    }
}


/**
 * Class Booking
 */
class Booking
{
    public $bookingDate;
    public $returnDate;
    public $book;

    public function __construct($book, $bookingDate, $returnDate)
    {
        $this->book = $book;
        $this->bookingDate = $bookingDate;
        $this->returnDate = $returnDate;
    }
}

/**
 * Class Config
 */
class Config
{
    public $databaseHostname;
    public $databasePort;
    public $databaseUsername;
    public $databasePassword;
    public $databaseName;

    public function __construct(string $databaseHostname = "localhost", int $databasePort = 3306, string $databaseUsername = "root", string $databasePassword = "", string $databaseName = "")
    {
        $this->databaseHostname = $databaseHostname;
        $this->databasePort = $databasePort;
        $this->databaseUsername = $databaseUsername;
        $this->databasePassword = $databasePassword;
        $this->databaseName = $databaseName;
    }
}

?>