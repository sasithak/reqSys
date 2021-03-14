<?php
    include_once 'auth_session.php';
    include_once './includes/func.inc.php';
    include_once './db.php';

    $userId = $_SESSION["indexNo"];
    $name = $_SESSION["name"];
    $username = $_SESSION["username"];
    $accessLevel = $_SESSION["accessLevel"];

    $postId = $_GET["id"];
    if (isset($postId) != true) {
        header("Location: dashboard.php");
        exit();
    }
    $string = explode("-", $postId);
    $postUid = end($string);
    if ($accessLevel === 0 and $userId != $postUid) {
        header("Location: dashboard.php?access=unauthorized");
        exit();
    }

    $sql = "SELECT * FROM discussions WHERE postId = '$postId';";
    $results = mysqli_query($conn, $sql);
    $result = mysqli_fetch_array($results);
    $subject = $result["postSubject"];
    $status = $result["currStatus"];
    $canReply = true;
    $createdBy = $result["userFullName"];
    $createdDate = $result["createdDate"];
    $createdTime = $result["createdTime"];
    $createdIndex = $result["userId"];

    if ($status === "pending") {
        $action = "pending";
    } else {
        $arr = explode("-", $status);
        $action = $arr[0];
        $actionUserName = $arr[1];
        $actionUserId = $arr[2];
    }

    echo '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>'.$subject.'</title>
    </head>
    <body>';
    head();

    echo '
        <section class="topic">
            <h1 class="subject">'.$subject.'</h1>
            <h2 class="name">Created by <a href="./profile.php?id='.$createdIndex.'">'.$createdBy.'</a> <span class="datetime">'.$createdDate.' '.$createdTime.'</h2></span>
            <h2 class="name">Status: ';
            if ($status === "pending") {
                echo '<span class="pending">Pending</span>';
            } else {
                $arr = explode("-", $status);
                $action = $arr[0];
                $actionUserName = $arr[2];
                $actionUserId = $arr[1];
                
                if ($action == "approved") {
                    $canReply = false;
                    echo '<span class="approved">Approved </span>';
                } elseif ($action == "declined") {
                    $canReply = false;
                    echo '<span class="declined">Declined </span>';
                } elseif ($action == "moreInfo") {
                    echo '<span class="pending">Pending - More Info requested </span>';
                }
                echo '<span>by <a href="./profile.php?id='.$actionUserId.'">'.$actionUserName.'</a></h2>';
            }
            echo '
            </h2>
        </section>';
        

    $sql = "SELECT * FROM `$postId`;";
    $result = mysqli_query($conn, $sql);
    
    if ($result and mysqli_num_rows($result) > 0) {
    // output data of each row
    while($row = mysqli_fetch_assoc($result)) {
        $postUsername = $row["userFullName"];
        $postUserId = $row["userId"];
        $postDate = $row["postDate"];
        $postTime = $row["postTime"];
        $content = $row["content"];
        $file = $row["file"];
        $ftp = $row["ftp"];
        $fileLocation = $row["fileLocation"];

        echo '
            <section class="heading">
                <h3><span class="name"><a href="./profile.php?id='.$postUserId.'">'.$postUsername.'</a></span> <span class="datetime">'.$postDate.' '.$postTime.'</span></h3>
            </section>
            <section class="postBody">
                <div class="content">
                    <p>'.$content.'</p>
                </div>
                <div class = "attachments">
                    <h4>Attachments</h4>';
        if ($file === "yes") {
            if ($ftp === "img"){
                echo '
                    <div class="image">
                        <a href='.$fileLocation.' target="_blank"><img src='.$fileLocation.'></a>
                    </div>';
            } else {
                echo '
                    <div class="others">
                        <ul><li>
                        <p><a href='.$fileLocation.' target="_blank">Attachment</a>
                        </li></ul>
                    </div>
                </section>';
            }
        } else {
            echo '<span id="noAttachments">No attachments</span>';
        }
    }
    }
    echo '<br />';
    if (isset($_GET["reply"])) {
        $reply = $_GET["reply"];
        if ($reply === "enabled") {
            echo '
                <section class="requesting">
                    <h2 class=name>New Reply</h2>
                    <form action="./includes/newReply.inc.php" method="post" class="new" enctype="multipart/form-data">
                        <textarea name="content" id="content" placeholder="Content"></textarea>
                        <br /><br />
                        <input type="file" name="file" />
                        <br /><br />
                        <input type="hidden" id="postId" name="postId" value="'.$postId.'">
                        <button type="submit" name="submit">Submit</button>
                    </form>
                </section>';
        }
    } elseif ($canReply) {
        echo '
            <br />
            <section id=replyButton>
                <a href="./view.php?id='.$postId.'&reply=enabled"><button type="submit" name=submit>Reply</button></a>
            </section>';
    }

    if ($accessLevel === 1 and $canReply) {
        echo '
        <br />
        <section class="operations">
            <h2>Actions</h2>
            <form action="includes/requestOperations.inc.php" method="post">
                <select name="operation" id="operation">
                    <option value="error"> </option>
                    <option value="approved">Approve</option>
                    <option value="declined">Decline</option>
                    <option value="moreInfo">Request more info</option>
                </select>
                <label for="operation">Select Action: </label>
                <br /><br />
                <input type="hidden" id="postId" name="postId" value="'.$postId.'">
                <button type="submit" name="submit">Send</button>
            </form>
        </section>';
    }
        
    echo '</body>
    </html>';