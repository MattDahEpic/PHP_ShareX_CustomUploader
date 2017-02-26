<?php
    require("./uploader_settings.inc.php");

    $conn = mysqli_connect($db_url,$db_user,$db_pass,$db_database);
    if (mysqli_connect_errno()) {
        http_response_code(500); //server error
        die();
    }
    //handle content
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        //all required data?
        if (!isset($_POST['type']) || !isset($_POST['value']) || !isset($_POST['pass'])) {
            http_response_code(400); //bad request
            echo "Malformed request!";
            die();
        }
        //correct password?
        if ($_POST['pass'] != $upload_pass) {
            http_response_code(401); //not authorized
            echo "Missing auth!";
            die();
        }
        //actually do it
        $value =  $_POST['value'];
        $random = random_str(5);
        do {
            $query = mysqli_query($conn,"SELECT * FROM ".$db_table." WHERE shortened = '".$random."';");
            if (mysqli_num_rows($query) == 0) {
                break;
            } else {
                $random = random_str(5);
            }
        } while (true);
        switch ($_POST['type']) {
            case "url":
            case "text":
                mysqli_query($conn,"INSERT INTO ".$db_table." (shortened,type,value) VALUES ('".$random."','".$_POST['type']."','".mysqli_real_escape_string($conn,$value)."');");
                break;
            case "file":
                $file = $_FILES['fileToUpload'];
                $newfile = "./upload/".$random.".".mysqli_real_escape_string($conn,pathinfo($file['name'],PATHINFO_EXTENSION));
                if ($file['error'] == UPLOAD_ERR_OK) {
                    if (!move_uploaded_file($file['tmp_name'], $newfile)) {
                        http_response_code(500);
                        echo "File error!";
                        die();
                    }
                    mysqli_query($conn, "INSERT INTO " . $db_table . " (shortened,type,value) VALUES ('" . $random . "','file','" . $newfile . "');");
                } else {
                    http_response_code(500);
                    echo "Upload error!";
                    die();
                }
                break;
            //TODO: delete
        }
        http_response_code(200);
        echo $upload_returnURL.$random;
        mysqli_close($conn);
        die();
    } else { //getting a URL
        $query = mysqli_query($conn,"SELECT * FROM ".$db_table." WHERE shortened = '".mysqli_real_escape_string($conn,$_GET['selector'])."';");
        mysqli_close($conn);
        if (mysqli_num_rows($query) > 0) {
            $array = mysqli_fetch_array($query);
            $value = $array['value'];
            switch ($array['type']) {
                case "url":
                    header("Location: ".$value);
                    die();
                case "text":
                    echo $value;
                    die();
                case "file":
                    header("Content-Type: ".mime_content_type($value));
                    fpassthru(fopen($value,"rb"));
                    die();
            }
        } else {
            header("Location: ".$upload_failRedirect);
            die();
        }
    }

function random_str($length, $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ')
{
    $str = '';
    $max = mb_strlen($keyspace, '8bit') - 1;
    for ($i = 0; $i < $length; ++$i) {
        $str .= $keyspace[random_int(0, $max)];
    }
    return $str;
}