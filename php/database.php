<?php
require_once("utils.php");

/**
 * This class is used to manage everything around the
 * interaction between PHP and our Database.
 *
 * It's using Singleton Pattern to ensure that
 * only a single instance of the database connection
 * is initialized through out this request.
 *
 * It can be accessed by using {@link Database::getInstance()}
 */
class Database
{
    private static $instance;

    /**
     * It initializes, if it's not initialized,
     * a Database object and returns it
     * @return Database
     */
    public static function getInstance(): Database
    {
        if (!isset(Database::$instance)) {
            Database::$instance = new Database(require("config.php"));
        }
        return Database::$instance;
    }

    private $config;
    private $db;

    protected function __construct(Config $config)
    {
        $this->config = $config;
        //We establish the connection to the database server without selecting the database.
        $this->db = new mysqli($config->databaseHostname, $config->databaseUsername, $config->databasePassword, null, $config->databasePort);
        if ($this->db->connect_error)
            respondDbError($this->db);

        //We try to select the given database, if we can't we try to create it and connect again.
        if (!$this->db->select_db($config->databaseName)) {
            $sqlSource = file_get_contents('db.sql');
            if (!$this->db->multi_query($sqlSource))
                respondDbError($this->db);

            while ($this->db->more_results())
                $this->db->next_result(); //fix: Commands out of sync, you can't run the command now

            $this->db->select_db($config->databaseName);
        }
    }

    /**
     * This method returns if a combination of username/email and password is valid
     * and if it is, it's returning a {@link User} object, otherwise it returns null.
     *
     * In case any of the inputs is missing it {@link die()}s with a 400 BadRequest.
     * @param string $usernameOrEmail The username or the email of the user
     * @param string $password The password of the user
     * @return User|null
     */
    public function authenticate(string $usernameOrEmail, string $password)
    {
        if (!isset($usernameOrEmail)) {
            respondError("username/email is missing", Http::BadRequest);
        }
        if (!isset($password)) {
            respondError("password is missing", Http::BadRequest);
        }

        $hashedPassword = md5($password);
        $query = $this->db->prepare("SELECT * FROM `users` WHERE (username=? or email=?) and password=?");
        $query->bind_param("sss", $usernameOrEmail, $usernameOrEmail, $hashedPassword);

        if (!$query->execute()) respondDbError($this->db);

        $result = $query->get_result();
        $rows = $result->num_rows;

        if ($rows >= 1) {
            $row = $result->fetch_assoc();
            return new User($row['id'], $row['username'], $row['email']);
        }
        return null;
    }

    /**
     * Stores a book into the database.
     *
     * In case any of the inputs is missing it {@link die()}s with a 400 BadRequest.
     * @param Book $book
     * @return Book
     */
    public function createBook(Book $book)
    {
        if (!isset($book) || $book == null) {
            respondError("book data are missing", Http::BadRequest);
        }
        if (!isset($book->bookID) || $book->bookID == null) {
            respondError("bookId is missing", Http::BadRequest);
        }
        if (!isset($book->title) || $book->title == null) {
            respondError("title is missing", Http::BadRequest);
        }
        if (!isset($book->author) || $book->author == null) {
            respondError("author is missing", Http::BadRequest);
        }

        $query = $this->db->prepare("INSERT INTO `books` (id,bookId,title,author) VALUES(NULL,?,?,?)");
        $query->bind_param("sss", $book->bookID, $book->title, $book->author);

        if (!$query->execute()) respondDbError($this->db);

        return new Book($book->bookID, $book->title, $book->author);
    }

    /**
     * It returns a Book from the database with the given
     * bookId, if the book doesn't exists it returns null.
     *
     * In case any of the inputs is missing it {@link die()}s with a 400 BadRequest.
     * @param string $bookId
     * @return Book|null
     */
    public function getBook(string $bookId)
    {
        if (!isset($bookId) || $bookId == null) {
            respondError("bookId is missing", Http::BadRequest);
        }

        //we check if book exists
        $query = $this->db->prepare("SELECT * FROM `books` WHERE bookId=?");
        $query->bind_param("s", $bookId);

        if (!$query->execute()) respondDbError($this->db);

        $result = $query->get_result();
        $rows = $result->num_rows;

        if ($rows == 1) {
            //book exists we return it.
            $row = $result->fetch_assoc();
            return new Book($row['bookId'], $row['title'], $row['author']);
        }
        return null;
    }

    /**
     * It tries to get a book from the database.
     * If the book doesn't exist instead
     * it tries to create it and then return it.
     *
     * In case any of the inputs is missing it {@link die()}s
     * with a 400 BadRequest.
     * @param Book $book
     * @return Book
     */
    public function getOrCreateBook(Book $book)
    {
        if (!isset($book) || $book == null) {
            respondError("book data are missing", Http::BadRequest);
        }

        if (!isset($book->bookID) || $book->bookID == null) {
            respondError("bookId is missing", Http::BadRequest);
        }

        $_book = $this->getBook($book->bookID);
        if ($_book != null)
            return $_book;

        return $this->createBook($book);
    }

    /**
     * We get a list from all the books a user has interacted
     * with and the states whether it has favorited or booked
     * any of them.
     * <table style='border:1px solid black;' align='center'>
     *  <thead>
     *   <tr><td>Book</td><td>Booked</td><td>Favorited</td></tr>
     *  </thead>
     *  <tbody>
     *   <tr><td>Book 1</td><td align='center'>True</td><td align='center'>False</td></tr>
     *   <tr><td>Book 2</td><td align='center'>False</td><td align='center'>False</td></tr>
     *   <tr><td>Book 3</td><td align='center'>True</td><td align='center'>True</td></tr>
     *  </tbody>
     * </table>
     *
     * @param string|null $bookId
     * @param int|null $userId
     * @return array
     */
    public function getBookStates(string $bookId = null, int $userId = null)
    {
        if (!isset($userId) || $userId == null) {
            $userId = getActiveUser()->id;
        }

        $bookStates = array();
        $sql = "
            SELECT books.bookId                      as bookId,
                   u.id                              as userId,
                   IF(f.bookId is NULL, FALSE, TRUE) as favorited,
                   IF(b.bookId is NULL, FALSE, TRUE) as booked
            FROM books
                     LEFT JOIN bookings b
                               on books.bookId = b.bookId
                     LEFT JOIN favorites f
                               on books.bookId = f.bookId
                     LEFT JOIN users u
                               on u.id = f.userId or
                                  u.id = b.userId
            WHERE u.id = ?";

        $query = null;
        if ($bookId != null) {
            $sql .= " and books.bookId = ?";
            $query = $this->db->prepare($sql);
            $query->bind_param("is", $userId, $bookId);
        } else {
            $query = $this->db->prepare($sql);
            $query->bind_param("i", $userId);
        }

        if (!$query->execute()) respondDbError($this->db);

        $result = $query->get_result();
        $rows = $result->num_rows;

        if ($rows >= 1) {
            while ($row = $result->fetch_assoc()) {
                $bookStates[] = new BookState($row['bookId'], (bool)$row['favorited'], (bool)$row['booked']);
            }
        }
        return $bookStates;

    }

    /**
     * Gets a user from the database if userId is provided,
     * or returns the active user from the session if the
     * userId is not provide.
     *
     * @param int|null $userId
     * @return User|null
     */
    public function getUser(int $userId = null)
    {
        if (!isset($userId) || $userId == null) {
            $userId = getActiveUser()->id;
        }

        if ($userId == getActiveUser()->id)
            return getActiveUser();

        $query = $this->db->prepare("SELECT * FROM `users` WHERE id=?");
        $query->bind_param("i", $userId);

        if (!$query->execute()) respondDbError($this->db);

        $result = $query->get_result();
        $rows = $result->num_rows;

        if ($rows == 1) {
            $row = $result->fetch_assoc();
            return new User($row['userId'], $row['username'], $row['email']);
        } else {
            return null;
        }
    }

    /**
     * Creates a user and stores it to the database.
     *
     * If user with the same email and/or username
     * exists in the database it {@link die()s with 409 conflict.}
     *
     * In case any of the inputs is missing it {@link die()}s
     * with a 400 BadRequest.
     * @param string $username
     * @param string $password
     * @param string $email
     * @return User
     */
    function createUser(string $username, string $password, string $email)
    {
        if (!isset($username)) {
            respondError("username is missing", Http::BadRequest);
        }
        if (!isset($password)) {
            respondError("password is missing", Http::BadRequest);
        }
        if (!isset($email)) {
            respondError("email is missing", Http::BadRequest);
        }
        $query = $this->db->prepare("SELECT * FROM `users` WHERE username=? or email=?");
        $query->bind_param("ss", $username, $email);

        if (!$query->execute()) respondDbError($this->db);

        $result = $query->get_result();
        $rows = $result->num_rows;

        if ($rows >= 1) {
            respondError("A user already exists with this email or username.",Http::Conflict);
        }

        $query = $this->db->prepare("INSERT INTO `users` (id,username,email,password,reg_date) VALUES(NULL,?,?,?,NOW())");
        $hashedPassword = md5($password);
        $query->bind_param("sss", $username, $email, $hashedPassword);

        if (!$query->execute()) respondDbError($this->db);

        $last_id = $this->db->insert_id;
        return new User($last_id, $username, $email);
    }

    /**
     * It gets the bookings for a user with the given $userId,
     * if $userId is omitted it gets the active user's id.
     *
     * @param int|null $userId
     * @return array
     */
    public function getBookings(int $userId = null): array
    {
        if (!isset($userId) || $userId == null) {
            $userId = getActiveUser()->id;
        }

        $bookings = array();
        $query = $this->db->prepare("
            SELECT b.bookId as bookId,
                   b.title as title,
                   b.author as author,
                   bookingDate,
                   returnDate
            FROM `bookings`
                     INNER JOIN `books` b
                                on bookings.bookId = b.bookId
            WHERE userId = ?");
        $query->bind_param("i", $userId);

        if (!$query->execute()) respondDbError($this->db);

        $result = $query->get_result();
        $rows = $result->num_rows;
        if ($rows >= 1) {
            while ($row = $result->fetch_assoc()) {
                $book = new Book($row['bookId'], $row['title'], $row['author']);
                $bookings[] = new Booking($book, $row['bookingDate'], $row['returnDate']);
            }
        }
        return $bookings;
    }

    /**
     * It gets the favorite books for a suer with the given
     * $userId. If $userId is omitted it gets the active user's id.
     *
     * @param int|null $userId
     * @return array
     */
    function getFavorites(int $userId = null)
    {
        if (!isset($userId) || $userId == null) {
            $userId = getActiveUser()->id;
        }

        $favorites = array();
        $query = $this->db->prepare("
            SELECT b.bookId as bookId,
                   b.title as title,
                   b.author as author
            FROM `favorites`
                     INNER JOIN `books` b
                                on favorites.bookId = b.bookId
            WHERE userId = ?");
        $query->bind_param("i", $userId);

        if (!$query->execute()) respondDbError($this->db);

        $result = $query->get_result();
        $rows = $result->num_rows;

        if ($rows >= 1) {
            while ($row = $result->fetch_assoc()) {
                $favorites[] = new Book($row['bookId'], $row['title'], $row['author']);
            }
        }
        return $favorites;
    }

    /**
     * Creates a booking for a user with the $userId.
     * if $userId is omitted it gets the active user's id.
     *
     * If a booking with the same bookId and userId
     * exists in the database it {@link die()s with 409 conflict.}
     *
     * In case any of the inputs is missing it {@link die()}s
     * with a 400 BadRequest.
     *
     * @param Book $book
     * @param float $returnDate
     * @param int|null $userId
     * @return Booking|null
     */
    public function createBooking(Book $book, float $returnDate, int $userId = null)
    {
        if (!isset($book->bookID)) {
            respondError("bookId is missing", Http::BadRequest);
        }
        if (!isset($returnDate)) {
            respondError("returnDate is missing", Http::BadRequest);
        }
        if (!isset($book->author)) {
            respondError("author is missing", Http::BadRequest);
        }
        if (!isset($book->title)) {
            respondError("title is missing", Http::BadRequest);
        }

        $book = $this->getOrCreateBook($book);

        if (!isset($userId) || $userId == null) {
            $userId = getActiveUser()->id;
        }

        $bookingDate = date('Y-m-d H:i:s');
        $returnDate = gmdate("Y-m-d H:i:s", $returnDate);;
        $query = $this->db->prepare("SELECT * FROM `bookings` WHERE bookId=? and userId=?");
        $query->bind_param("si", $book->bookID, $userId);

        if (!$query->execute()) respondDbError($this->db);

        $result = $query->get_result();
        $rows = $result->num_rows;
        if ($rows == 1) {
            respondError("This booking already exists.",Http::Conflict);
            return null;
        } else {
            $query = $this->db->prepare("INSERT INTO `bookings` (id, userId, bookId, bookingDate, returnDate) VALUES(NULL,?,?,?,?)");
            $query->bind_param("isss", $userId, $book->bookID, $bookingDate, $returnDate);

            if (!$query->execute()) respondDbError($this->db);
            return new Booking($book, $bookingDate, $returnDate);
        }
    }

    /**
     * Deletes a booking for the user with the given
     * $userId. If $userId is omitted it gets the active
     * user's id.
     *
     * In case any of the inputs is missing it {@link die()}s
     * with a 400 BadRequest.
     * @param $bookId
     * @param null $userId
     * @return bool
     */
    public function deleteBooking($bookId, $userId = null)
    {
        if (!isset($bookId)) {
            respondError("bookId is missing", Http::BadRequest);
        }

        if (!isset($userId) || $userId == null) {
            $userId = getActiveUser()->id;
        }

        $query = $this->db->prepare("SELECT * FROM `bookings` WHERE bookId=? and userId=?");
        $query->bind_param("si", $bookId, $userId);

        if (!$query->execute()) respondDbError($this->db);

        $result = $query->get_result();
        $rows = $result->num_rows;
        if ($rows != 1) {
            respondError("User doesn't have this booking.", Http::NotFound);
        }
        $query = $this->db->prepare("DELETE FROM `bookings` WHERE bookId=? and userId=?");
        $query->bind_param("si", $bookId, $userId);
        if (!$query->execute())
            respondDbError($this->db);
        return true;
    }

    /**
     * Adds a book as a favorite for a user with the given
     * $userId. If $userId is omitted it gets the active
     * user's id.
     *
     * In case any of the inputs is missing it {@link die()}s
     * with a 400 BadRequest.
     * @param Book $book
     * @param int|null $userId
     * @return Book
     */
    function createFavorite(Book $book, int $userId = null)
    {
        if (!isset($book)) {
            respondError("book details are missing", Http::BadRequest);
        }
        if (!isset($book->bookID) || $book->bookID == null) {
            respondError("bookId is missing", Http::BadRequest);
        }
        if (!isset($book->author) || $book->author == null) {
            respondError("author is missing", Http::BadRequest);
        }
        if (!isset($book->title) || $book->title == null) {
            respondError("title is missing", Http::BadRequest);
        }

        if (!isset($userId) || $userId == null) {
            $userId = getActiveUser()->id;
        }

        $book = $this->getOrCreateBook($book);

        $query = $this->db->prepare("SELECT * FROM `favorites` WHERE bookId=? and userId=?");
        $query->bind_param("si", $book->bookID, $userId);

        if (!$query->execute()) respondDbError($this->db);

        $result = $query->get_result();
        $rows = $result->num_rows;

        if ($rows == 1) {
            respondError("User have already favorited this book.", Http::NotFound);
        }

        $query = $this->db->prepare("INSERT INTO `favorites` (id, userId, bookId) VALUES(NULL,?,?)");
        $query->bind_param("is", $userId, $book->bookID);

        if (!$query->execute()) respondDbError($this->db);

        if ($query->affected_rows == 0) {
            respondDbError($this->db);
        }

        return new Book($book->bookID, $book->title, $book->author);
    }

    /**
     * Removes a book from the user's favorite list with the given
     * $userId. If $userId is omitted it gets the active user's id
     *
     * In case any of the inputs is missing it {@link die()}s
     * with a 400 BadRequest.
     * @param string $bookId
     * @param int|null $userId
     * @return bool
     */
    function deleteFavorite(string $bookId, int $userId = null)
    {
        if (!isset($bookId)) {
            respondError("bookId is missing", Http::BadRequest);
        }
        if (!isset($userId) || $userId == null) {
            $userId = getActiveUser()->id;
        }

        $query = $this->db->prepare("SELECT * FROM `favorites` WHERE bookId=? and userId=?");
        $query->bind_param("si", $bookId, $userId);

        if (!$query->execute()) respondDbError($this->db);

        $result = $query->get_result();
        $rows = $result->num_rows;

        if ($rows == 0) {
            respondError("User doesn't have this book to his favorites.", Http::NotFound);
        }

        $query = $this->db->prepare("DELETE FROM `favorites` WHERE bookId=? and userId=?");
        $query->bind_param("si", $bookId, $userId);
        if (!$query->execute()) {
            respondDbError($this->db);
        }
        return true;
    }
}

?>