<?php

function head() {
    $name = $_SESSION["name"];
    $uid =  $_SESSION["indexNo"];
    echo
    '<header>
        <table>
            <tr>
                <th><a href="./dashboard.php">Home</a></th>
                <th><a href="./profile.php?id='.$uid.'">Profile</a></th>
                <th><a href="./logout.php" class="logout">Logout</a></th>
            </tr>
        </table>
    </header>';
}

function pending($row, $cnt) {
    $postId = $row["postId"];
    $createdUser = $row["userFullName"];
    $createdUserId = $row["userId"];
    $postDate = $row["createdDate"];
    $postTime = $row["createdTime"];
    $status = $row["currStatus"];
    $subject = $row["postSubject"];

    echo '
                        <tr class="reqTable-pending">
                            <td class="reqTable-data no">'.$cnt.'</td>
                            <td class="reqTable-data subject"><a href="./view.php?id='.$postId.'">'.$subject.'</a></td>';
                            if($createdUserId !== $_SESSION["indexNo"]) {
                                echo '
                            <td class="reqTable-data user"><a href="./profile.php?id='.$createdUserId.'">'.$createdUser.'</td>';
                            }
                            echo'
                            <td class="reqTable-data date">'.$postDate.'</td>
                            <td class="reqTable-data time">'.$postTime.'</td>
                            <td class="reqTable-data status">Pending</td>
                        </tr>';
}

function approved($row, $cnt) {
    $postId = $row["postId"];
    $createdUser = $row["userFullName"];
    $createdUserId = $row["userId"];
    $postDate = $row["createdDate"];
    $postTime = $row["createdTime"];
    $status = $row["currStatus"];
    $subject = $row["postSubject"];
    $arr = explode("-", $status);
    $action = $arr[0];
    $actionUserName = $arr[2];
    $actionUserId = $arr[1];

    echo '
                        <tr class="reqTable-approved">
                            <td class="reqTable-data no">'.$cnt.'</td>
                            <td class="reqTable-data subject"><a href="./view.php?id='.$postId.'">'.$subject.'</a></td>';
                            if($createdUserId !== $_SESSION["indexNo"]) {
                                echo '
                            <td class="reqTable-data user"><a href="./profile.php?id='.$createdUserId.'">'.$createdUser.'</td>';
                            }
                            echo'
                            <td class="reqTable-data date">'.$postDate.'</td>
                            <td class="reqTable-data time">'.$postTime.'</td>
                            <td class="reqTable-data status">Approved by '.$actionUserName.'</td>
                        </tr>';
}

function declined($row, $cnt) {
    $postId = $row["postId"];
    $createdUser = $row["userFullName"];
    $createdUserId = $row["userId"];
    $postDate = $row["createdDate"];
    $postTime = $row["createdTime"];
    $status = $row["currStatus"];
    $subject = $row["postSubject"];
    $arr = explode("-", $status);
    $action = $arr[0];
    $actionUserName = $arr[2];
    $actionUserId = $arr[1];

    echo '
                        <tr class="reqTable-declined">
                            <td class="reqTable-data no">'.$cnt.'</td>
                            <td class="reqTable-data subject"><a href="./view.php?id='.$postId.'">'.$subject.'</a></td>';
                            if($createdUserId !== $_SESSION["indexNo"]) {
                                echo '
                            <td class="reqTable-data user"><a href="./profile.php?id='.$createdUserId.'">'.$createdUser.'</td>';
                            }
                            echo'
                            <td class="reqTable-data date">'.$postDate.'</td>
                            <td class="reqTable-data time">'.$postTime.'</td>
                            <td class="reqTable-data status">Declined by '.$actionUserName.'</td>
                        </tr>'; 
}

function more_info($row, $cnt) {
    $postId = $row["postId"];
    $createdUser = $row["userFullName"];
    $createdUserId = $row["userId"];
    $postDate = $row["createdDate"];
    $postTime = $row["createdTime"];
    $status = $row["currStatus"];
    $subject = $row["postSubject"];
    $arr = explode("-", $status);
    $action = $arr[0];
    $actionUserName = $arr[2];
    $actionUserId = $arr[1];

    echo '
                        <tr class="reqTable-moreInfo">
                            <td class="reqTable-data no">'.$cnt.'</td>
                            <td class="reqTable-data subject"><a href="./view.php?id='.$postId.'">'.$subject.'</a></td>';
                            if($createdUserId !== $_SESSION["indexNo"]) {
                                echo '
                            <td class="reqTable-data user"><a href="./profile.php?id='.$createdUserId.'">'.$createdUser.'</td>';
                            }
                            echo'
                            <td class="reqTable-data date">'.$postDate.'</td>
                            <td class="reqTable-data time">'.$postTime.'</td>
                            <td class="reqTable-data status">More information requested by '.$actionUserName.'</td>
                        </tr>'; 
}

function createRequest($conn, $postId, $uid, $userName, $name, $date, $time, $subject, $content, $status, $isFile, $ftp, $fileLocation) {
    $sql = "INSERT INTO discussions (postId, userId, userName, userFullName, createdDate, createdTime, postSubject, currStatus, readStatus) VALUES ('$postId', '$uid', '$userName', '$name', '$date', '$time', '$subject', '$status', 'yes');";
    if (mysqli_query($conn, $sql)) {
        $sql2 = "CREATE TABLE `".$postId."` (
            id int(11) AUTO_INCREMENT PRIMARY KEY NOT NULL,
            userId varchar(16) NOT NULL,
            userName varchar(32) NOT NULL,
            userFullName varchar(64) NOT NULL,
            postDate varchar(32) NOT NULL,
            postTime varchar(32) NOT NULL,
            content varchar(3000) NOT NULL,
            file varchar(16) NOT NULL,
            ftp varchar(16) NOT NULL,
            fileLocation varchar(256) NOT NULL
        );";
        if (mysqli_query($conn, $sql2)) {
            addEntry($conn, $postId, $uid, $userName, $name, $date, $time, $content, $isFile, $ftp, $fileLocation);
        } else {
            $sql3 = "DELETE FROM discussions WHERE postId = ".$postId.";";
            mysqli_query($conn, $sql3);
            header("Location: ../newRequest?error=dbError"."-".mysqli_error($conn));
            exit();
        }
    } else {
        header("Location: ../newRequest.php?error=dbError"."-".mysqli_error($conn));
        exit();
    }
}

function addEntry($conn, $postId, $uid, $userName, $name, $date, $time, $content, $isFile, $ftp, $fileLocation) {
    $sql = "INSERT INTO `".$postId."` (userId, userName, userFullName, postDate, postTime, content, file, ftp, fileLocation) VALUES ('$uid', '$userName', '$name', '$date', '$time', '$content', '$isFile', '$ftp', '$fileLocation')";
    mysqli_query($conn, $sql);
    $err = mysqli_error($conn);
    echo $err;
}

function getUser($conn, $viewId) {
    $query    = "SELECT * FROM userlist WHERE indexNo='$viewId'";
    $result = mysqli_query($conn, $query);
    $row;
    if ($result && $rows = mysqli_num_rows($result)) {
        $row = mysqli_fetch_assoc($result);
    }
    return $row;
}