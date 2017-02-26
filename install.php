<?php
    require("./uploader_settings.inc.php");
    //make uploads directory
    if (!file_exists("./upload")) {
        if (!mkdir("./upload",0755,true)) {
            http_response_code(500);
            echo "Filesystem Error! Check permissions.";
            die();
        }
    }
    //create database table
    $conn = mysqli_connect($db_url,$db_user,$db_pass);
    if (mysqli_connect_errno()) {
        http_response_code(500); //server error
        echo "Database Error! Check configuration.";
        die();
    }
    if (!mysqli_query($conn,"CREATE DATABASE ".$db_database)) { //create database
        http_response_code(500);
        echo "Couldn't create database!";
        die();
    }
    mysqli_select_db($conn,$db_database);
    if (!mysqli_query($conn,"CREATE TABLE `content` (
      `shortened` char(5) NOT NULL,
      `type` varchar(4) NOT NULL,
      `value` varchar(65000) NOT NULL,
      `creation` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY (`shortened`)
    )")) {
        http_response_code(500);
        echo "Couldn't create table!";
        die();
    }
