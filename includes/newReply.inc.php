<?php
session_start();
$uid = $_SESSION["indexNo"];
$name = $_SESSION["name"];
$userName = $_SESSION["username"];
$accessLevel = $_SESSION["accessLevel"];

include_once '../db.php';
include_once 'func.inc.php';


if (isset($_POST["submit"])) {
    $postId = $_POST["postId"];
    $content = str_replace("'", "''", $_POST["content"]);
    $content = htmlspecialchars($content);
    $postDirectory = "../discussions/uploads/".$postId;
    date_default_timezone_set("Asia/Colombo");
    $date = "".date("Y/m/d");
    $time = "".date("h:i:sa");
    $isFile = "no";
    $ftp = "no";
    $fileLocation = "./discussions/uploads/".$postId;

    if (isset($_FILES['file'])) {
        $file = $_FILES["file"];
        print_r($file);
        $fileName = $file['name'];
        $fileTmpName = $file['tmp_name'];
        $fileSize = $file['size'];
        $fileError = $file['error'];
        $fileType = $file['type'];

        if ($fileError === 0) {
            $isFile = "yes";
            $arr = explode(".", $fileName);
            $string = end($arr);
            $fileExt = strtolower($string);
            $images = array('jpg', 'jpeg', 'png');
            if (in_array($fileExt, $images)) {
                $ftp = "img";
            } else {
                $ftp = "other";
            }
            $fname = uniqid("", true).".".$fileExt;
            $fileLocation = $fileLocation."/".$fname;
            $postDirectory = $postDirectory."/".$fname;
            move_uploaded_file($fileTmpName, $postDirectory);
        }

        
    }
    addEntry($conn, $postId, $uid, $userName, $name, $date, $time, $content, $isFile, $ftp, $fileLocation);
    header("Location: ../view.php?id=".$postId);
    exit();
    
} else {
    header("location: ../dashboard.php");
    exit();
}