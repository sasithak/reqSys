<?php

session_start();
$uid = $_SESSION["userUid"];
$name = $_SESSION["userName"];

include_once 'dbh.inc.php';

if(isset($_POST["submit"])) {
    $action = $_POST["operation"];
    $postId = $_POST["postId"];

    if ($action === "error") {
        header("Location: ../view.php?id=".$postId);
    }

    $status = $action."-".$uid."-".$name;

    $sql = "UPDATE discussions SET currStatus = '$status' WHERE postId = '$postId';";
    mysqli_query($conn, $sql);
    
    header("Location: ../view.php?id=".$postId);
    exit();
}