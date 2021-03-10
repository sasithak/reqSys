<?php

include_once 'func.inc.php';


function emptyInputSignUp($fName, $lName, $dName, $email, $uid, $pwd, $re_pwd) {
    $result;
    if (empty($fName) || empty($lName) || empty($dName) || empty($email) || empty($uid) || empty($pwd) || empty($re_pwd)) {
        $result = true;
    } else {
        $result = false;
    }
    return $result;
}

function invalidUid($uid) {
    $result;
    if (!preg_match("/[a-z]*/", $uid)) {
        $result = true;
    } else {
        $result = false;
    }
    return $result;
}

function invalidEmail($email) {
    $result;
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $result = true;
    } else {
        $result = false;
    }
    return $result;
}

function pwdMatch($pwd, $re_pwd) {
    $result;
    if ($pwd !== $re_pwd) {
        $result = true;
    } else {
        $result = false;
    }
    return $result;
}

function uidExists($conn, $uid, $login=false) {
    $student = studentUidExists($conn, $uid, $login);
    $teacher = staffUidExists($conn, $uid, $login);
    if ($student === false) {
        if ($teacher === false) {
            return false;
        } else {
            return $teacher;
        }
    } else {
        return $student;
    }
}

function emailExists($conn, $email) {
    $student = studentEmailExists($conn, $email);
    $teacher = staffEmailExists($conn, $email);
    if ($student === false) {
        if ($teacher === false) {
            return false;
        } else {
            return $teacher;
        }
    } else {
        return $student;
    }
}

function createUser($conn, $type, $fName, $lName, $dName, $email, $uid, $pwd) {
    $accessLevel;
    if ($type === "students") {
        $accessLevel = 0;
        createStudent($conn, $accessLevel, $fName, $lName, $dName, $email, $uid, $pwd);
    } elseif ($type === "staff") {
        $accessLevel = 1;
        createTeacher($conn, $accessLevel, $fName, $lName, $dName, $email, $uid, $pwd);
    }
}

function emptyInputLogIn($uid, $pwd) {
    $result;
    if (empty($uid) || empty($pwd)) {
        $result = true;
    } else {
        $result = false;
    }
    return $result;
}

function loginUser($conn, $uid, $pwd){
    $uidExists = uidExists($conn, $uid, true);

    if ($uidExists === false) {
        header("location: ../index.php?error=wrongLoginUid");
        exit();
    }

    $pwdHashed = $uidExists["pwd"];
    echo $pwdHashed;
    $checkPwd = password_verify($pwd, $pwdHashed);

    if ($checkPwd === false) {
        header("location: ../index.php?error=wrongLoginPwd");
        exit();
    } elseif ($checkPwd === true) {
        session_start();
        $_SESSION["userId"] = $uidExists["id"];
        $_SESSION["userUid"] = $uidExists["userId"];
        $_SESSION["userName"] = $uidExists["displayName"];
        $_SESSION["accessLevel"] = $uidExists["accessLevel"];
        header("location: ../dashboard.php?login=success");
        exit();
    }
}

function head() {
    $name = $_SESSION["userName"];
    $uid =  $_SESSION["userUid"];
    echo
    '<header>
        <table>
            <tr>
                <th><a href="dashboard.php">Home</a></th>
                <th><a href="#"><a href="./profile.php?id='.$uid.'">'.$name.'</a></th>
                <th><a href="logout.php">Logout</a></th>
            </tr>
        </table>
    </header>';
}

function createRequest($conn, $postId, $uid, $name, $date, $time, $subject, $content, $status, $isFile, $ftp, $fileLocation) {
    $sql = "INSERT INTO discussions (postId, userId, userName, createdDate, createdTime, postSubject, currStatus) VALUES ('$postId', '$uid', '$name', '$date', '$time', '$subject', '$status');";
    if (mysqli_query($conn, $sql)) {
        $sql2 = "CREATE TABLE `".$postId."` (
            id int(11) AUTO_INCREMENT PRIMARY KEY NOT NULL,
            userId varchar(10) NOT NULL,
            userName varchar(50) NOT NULL,
            postDate varchar(20) NOT NULL,
            postTime varchar(20) NOT NULL,
            content varchar(3000) NOT NULL,
            file varchar(10) NOT NULL,
            ftp varchar(10) NOT NULL,
            fileLocation varchar(256) NOT NULL
        );";
        if (mysqli_query($conn, $sql2)) {
            addEntry($conn, $postId, $uid, $name, $date, $time, $content, $isFile, $ftp, $fileLocation);
        } else {
            $sql3 = "DELETE FROM discussions WHERE postId = ".$postId.";";
            mysqli_query($conn, $sql3);
            header("Location: ../newRequest?error=dbError"."-".mysqli_error($conn));
        }
    } else {
        header("Location: ../newRequest?error=dbError"."-".mysqli_error($conn));
    }
}

function addEntry($conn, $postId, $uid, $name, $date, $time, $content, $isFile, $ftp, $fileLocation) {
    $sql = "INSERT INTO `".$postId."` (userId, userName, postDate, postTime, content, file, ftp, fileLocation) VALUES ('$uid', '$name', '$date', '$time', '$content', '$isFile', '$ftp', '$fileLocation')";
    mysqli_query($conn, $sql);
}

function getUser($conn, $userId) {
    $student = getStudent($conn, $userId);
    $teacher = getTeacher($conn, $userId);
    if ($student === false) {
        if ($teacher === false) {
            return false;
        } else {
            return $teacher;
        }
    } else {
        return $student;
    }
}