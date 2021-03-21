<?php
session_start();
$uid = $_SESSION["indexNo"];
$name = $_SESSION["name"];
$accessLevel = $_SESSION["accessLevel"];
$userName = $_SESSION["username"];

include_once '../db.php';
include_once 'func.inc.php';

if (isset($_POST["submit"])) {
    $subject = $_POST["subject"];
    $content = str_replace("'", "''", $_POST["content"]);
    $content = htmlspecialchars($content);
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

    if ($subject === "notSet") {
        header("location: ../newRequest.php?incomplete=true");
        exit();
    } elseif ($subject === "late-add-drop") {
        $subject = "Late add or drop request";
    } elseif ($subject === "extend-submission") {
        $subject = "Extend submission deadline";
    } elseif ($subject === "repeat-exams") {
        $subject = "Repeat exams as first attempt with the next batch";
    }

    if (empty($content)) {
        header("location: ../newRequest.php?incomplete=true");
        exit();
    }

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
    createRequest($conn, $postId, $uid, $userName, $name, $date, $time, $subject, $content, $status, $isFile, $ftp, $fileLocation);
    header("Location: ../view.php?id=".$postId);
    exit();
    
    
} else {
    header("location: ../newRequest.php");
    exit();
}