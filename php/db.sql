SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT = @@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS = @@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION = @@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;


CREATE TABLE IF NOT EXISTS `bookings`
(
    `id`          bigint(20)                          NOT NULL AUTO_INCREMENT,
    `userId`      bigint(20)                          NOT NULL,
    `bookId`      varchar(20) COLLATE utf8_unicode_ci NOT NULL,
    `bookingDate` datetime                            NOT NULL,
    `returnDate`  datetime                            NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  AUTO_INCREMENT = 3
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `books`
(
    `id`     bigint(20)                                             NOT NULL AUTO_INCREMENT,
    `bookId` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
    `title`  text CHARACTER SET utf8 COLLATE utf8_unicode_ci        NOT NULL,
    `author` text CHARACTER SET utf8 COLLATE utf8_unicode_ci        NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  AUTO_INCREMENT = 4
  DEFAULT CHARSET = utf8;

CREATE TABLE IF NOT EXISTS `favorites`
(
    `id`     bigint(20)                                             NOT NULL AUTO_INCREMENT,
    `userId` bigint(20)                                             NOT NULL,
    `bookId` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  AUTO_INCREMENT = 3
  DEFAULT CHARSET = utf8;

CREATE TABLE IF NOT EXISTS `users`
(
    `id`       bigint(20)                          NOT NULL AUTO_INCREMENT,
    `username` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
    `email`    varchar(32) COLLATE utf8_unicode_ci NOT NULL,
    `password` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
    `reg_date` timestamp                           NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  AUTO_INCREMENT = 3
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT = @OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS = @OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION = @OLD_COLLATION_CONNECTION */;
