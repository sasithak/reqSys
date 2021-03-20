<?php

session_start();
$uid = $_SESSION["indexNo"];
$name = $_SESSION["name"];

include_once '../db.php';

if(isset($_POST["submit"])) {
    $action = $_POST["operation"];
    $postId = $_POST["postId"];

    if ($action === "error") {
        header("Location: ../view.php?id=".$postId);
        exit();
    }

    $status = $action."-".$uid."-".$name;

    $sql = "UPDATE discussions SET currStatus = '$status' readStatus = 'no' WHERE postId = '$postId';";
    mysqli_query($conn, $sql);
    
    header("Location: ../view.php?id=".$postId);
    exit();
}