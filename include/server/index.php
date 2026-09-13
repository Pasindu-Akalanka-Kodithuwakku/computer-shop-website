<?php
    $serverName = '';
    $username = '';
    $password = '';
    $dbName = '';
    $conn = mysqli_connect($serverName, $username, $password, $dbName);
    if (!$conn) {
        die("Connection failed! try again.");
    }
/* create new Status table */
    $sql = "CREATE TABLE IF NOT EXISTS `Status` (
        `statusId` VARCHAR(8) NOT NULL,
        `pathName` LONGBLOB NOT NULL,
        `type` VARCHAR(15) NOT NULL,
        `description` LONGBLOB,
        `duration` INT DEFAULT 24,
        `uploadDate` DATETIME DEFAULT NOW(),
        `lastUpdateDate` DATETIME DEFAULT NOW(),
        `trash` INT DEFAULT 0,
        PRIMARY KEY(`statusId`)
    )";
    $stmt = mysqli_query($conn, $sql);
    if (!$stmt) {
        die("Sql query executing failed! try again.");
    }
/* create new Update table */
    $sql = "CREATE TABLE IF NOT EXISTS `Update` (
        `updateId` VARCHAR(8) NOT NULL,
        `description` LONGBLOB NOT NULL,
        `uploadDate` DATETIME DEFAULT NOW(),
        `lastUpdateDate` DATETIME DEFAULT NOW(),
        `trash` INT DEFAULT 0,
        PRIMARY KEY(`updateId`)
    )";
    $stmt = mysqli_query($conn, $sql);
    if (!$stmt) {
        die("Sql query executing failed! try again.");
    }
/* create new Product table */
    $sql = "CREATE TABLE IF NOT EXISTS `Product` (
        `productId` VARCHAR(8) NOT NULL,
        `productName` VARCHAR(50) NOT NULL,
        `category` VARCHAR(25) NOT NULL,
        `brand` VARCHAR(25),
        `model` VARCHAR(25),
        `price` DOUBLE(10, 2) NOT NULL,
        `discount` DOUBLE(10, 2) NOT NULL,
        `quantityInStock` INT DEFAULT 0,
        `warranty` VARCHAR(10) DEFAULT 'No',
        `usedType` VARCHAR(10) DEFAULT 'Brand New',
        `specification` VARCHAR(500),
        `description` VARCHAR(1500),
        `image` LONGBLOB,
        `uploadDate` DATETIME DEFAULT NOW(),
        `lastUpdateDate` DATETIME DEFAULT NOW(),
        `trash` INT DEFAULT 0,
        PRIMARY KEY(`productId`)
    )";
    $stmt = mysqli_query($conn, $sql);
    if (!$stmt) {
        die("Sql query executing failed! try again.");
    }
/* create new Member table */
    $sql = "CREATE TABLE IF NOT EXISTS `Member` (
        `memberId` VARCHAR(8) NOT NULL,
        `firstName` VARCHAR(25) NOT NULL,
        `lastName` VARCHAR(25),
        `contact` VARCHAR(10) NOT NULL UNIQUE,
        `email` VARCHAR(150),
        `image` LONGBLOB,
        `password` VARCHAR(50) NOT NULL,
        `optionOne` INT,
        `customOne` VARCHAR(250),
        `answerOne` VARCHAR(50) NOT NULL,
        `optionTwo` INT,
        `customTwo` VARCHAR(250),
        `answerTwo` VARCHAR(50) NOT NULL,
        `registerDate` DATETIME DEFAULT NOW(),
        `lastLoginDate` DATETIME DEFAULT NOW(),
        `trash` INT DEFAULT 0,
        PRIMARY KEY(`memberId`)
    )";
    $stmt = mysqli_query($conn, $sql);
    if (!$stmt) {
        die("Sql query executing failed! try again.");
    }
/* create new Complaint table */
    $sql = "CREATE TABLE IF NOT EXISTS `Complaint` (
        `complaintId` VARCHAR(8) NOT NULL,
        `description` LONGBLOB NOT NULL,
        `about` VARCHAR(8) DEFAULT 'General',
        `category` VARCHAR(15) DEFAULT 'General',
        `uploadDate` DATETIME DEFAULT NOW(),
        `trash` INT DEFAULT 0,
        PRIMARY KEY(`complaintId`)
    )";
    $stmt = mysqli_query($conn, $sql);
    if (!$stmt) {
        die("Sql query executing failed! try again.");
    }
/* create new Message table */
    $sql = "CREATE TABLE IF NOT EXISTS `Message` (
        `messageId` VARCHAR(8) NOT NULL,
        `description` LONGBLOB,
        `attachment` LONGBLOB,
        `sender` VARCHAR(8) NOT NULL,
        `receiver` VARCHAR(8) NOT NULL,
        `clearBySender` INT DEFAULT 0,
        `clearByReceiver` INT DEFAULT 0,
        `reply` VARCHAR(8),
        `seen` INT DEFAULT 0,
        `sentDate` DATETIME DEFAULT NOW(),
        `trash` INT DEFAULT 0,
        PRIMARY KEY(`messageId`),
        CONSTRAINT FK_MessageReply FOREIGN KEY(`reply`) REFERENCES `Message`(`messageId`) ON DELETE CASCADE ON UPDATE NO ACTION
    )";
    $stmt = mysqli_query($conn, $sql);
    if (!$stmt) {
        die("Sql query executing failed! try again.");
    }
/* create new Admin table */
    $sql = "CREATE TABLE IF NOT EXISTS `Admin` (
        `adminId` VARCHAR(8) NOT NULL,
        `firstName` VARCHAR(25) NOT NULL,
        `lastName` VARCHAR(25),
        `contact` VARCHAR(10) NOT NULL UNIQUE,
        `nic` VARCHAR(12) NOT NULL UNIQUE,
        `email` VARCHAR(150),
        `image` LONGBLOB,
        `password` VARCHAR(50) NOT NULL,
        `optionOne` INT,
        `customOne` VARCHAR(250),
        `answerOne` VARCHAR(50) NOT NULL,
        `optionTwo` INT,
        `customTwo` VARCHAR(250),
        `answerTwo` VARCHAR(50) NOT NULL,
        `registerDate` DATETIME DEFAULT NOW(),
        `lastLoginDate` DATETIME DEFAULT NOW(),
        `trash` INT DEFAULT 0,
        PRIMARY KEY(`adminId`)
    )";
    $stmt = mysqli_query($conn, $sql);
    if (!$stmt) {
        die("Sql query executing failed! try again.");
    }
/* create new ItemChat table */
    $sql = "CREATE TABLE IF NOT EXISTS `ItemChat` (
        `chatId` VARCHAR(8) NOT NULL,
        `description` LONGBLOB,
        `sender` VARCHAR(8) NOT NULL,
        `reply` VARCHAR(8),
        `itemId` VARCHAR(8) NOT NULL,
        `sentDate` DATETIME DEFAULT NOW(),
        `trash` INT DEFAULT 0,
        PRIMARY KEY(`chatId`),
        CONSTRAINT FK_ChatReply FOREIGN KEY(`reply`) REFERENCES `ItemChat`(`chatId`) ON DELETE CASCADE ON UPDATE NO ACTION,
        CONSTRAINT FK_ChatItem FOREIGN KEY(`itemId`) REFERENCES `Product`(`productId`) ON DELETE CASCADE ON UPDATE NO ACTION
    )";
    $stmt = mysqli_query($conn, $sql);
    if (!$stmt) {
        die("Sql query executing failed! try again.");
    }
/* create new ViewItem table */
    $sql = "CREATE TABLE IF NOT EXISTS `ViewItem` (
        `viewId` VARCHAR(8) NOT NULL,
        `productId` VARCHAR(8) NOT NULL,
        `deviceName` VARCHAR(150) NOT NULL,
        `viewDate` DATETIME DEFAULT NOW(),
        `trash` INT DEFAULT 0,
        PRIMARY KEY(`viewId`),
        CONSTRAINT FK_ViewProduct FOREIGN KEY(`productId`) REFERENCES `Product`(`productId`) ON DELETE CASCADE ON UPDATE NO ACTION
    )";
    $stmt = mysqli_query($conn, $sql);
    if (!$stmt) {
        die("Sql query executing failed! try again.");
    }
/* create supply dealers table */
    $sql = "CREATE TABLE IF NOT EXISTS `Dealer` (
        `supplyId` VARCHAR(8) NOT NULL,
        `productId` VARCHAR(8) NOT NULL,
        `companyName` VARCHAR(50) NOT NULL,
        `stockPrice` DOUBLE(10, 2) NOT NULL,
        PRIMARY KEY(`supplyId`),
        CONSTRAINT FK_SupplyProduct FOREIGN KEY(`productId`) REFERENCES `Product`(`productId`) ON DELETE CASCADE ON UPDATE NO ACTION
    )";
    $stmt = mysqli_query($conn, $sql);
    if (!$stmt) {
        die("Sql query executing failed! try again.");
    }
?>