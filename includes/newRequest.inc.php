<?php
session_start();
$uid = $_SESSION["userUid"];
$name = $_SESSION["userName"];
$accessLevel = $_SESSION["accessLevel"];

include_once 'dbh.inc.php';
include_once 'funcMain.inc.php';

if (isset($_POST["submit"])) {
    $subject = $_POST["subject"];
    $content = htmlspecialchars($_POST["content"]);
    $status = "pending";
    $postId = uniqid("", true)."-".$uid;
    $postDirectory = "../discussions/uploads/".$postId;
    mkdir($postDirectory, 0777, true);
    date_default_timezone_set("Asia/Colombo");
    $date = "".date("Y/m/d");
    $time = "".date("h:i:sa");
    $isFile = "no";
    $ftp = "no";
    $fileLocation = "./discussions/uploads/".$postId;

    if (isset($_FILES['file'])) {
        $file = $_FILES["file"];
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
    createRequest($conn, $postId, $uid, $name, $date, $time, $subject, $content, $status, $isFile, $ftp, $fileLocation);
    header("Location: ../view.php?id=".$postId);
    exit();
    
    
} else {
    header("location: ../newRequest.php");
    exit();
}