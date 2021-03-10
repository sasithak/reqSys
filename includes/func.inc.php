<?php

function studentUidExists($conn, $uid, $login) {
    $sql = "SELECT * FROM students WHERE userId = ?;";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        if ($login == false) {
        header("location: ../signup.php?error=stUidStmtFailed");
        exit();} else {
            header("location: ../index.php?error=stUidStmtFailed");
        exit();
        }
        exit();
    }

    mysqli_stmt_bind_param($stmt, "s", $uid);
    mysqli_stmt_execute($stmt);

    $resultData = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($resultData)) {
        return $row;
    } else {
        $result = false;
        return $result;
    }

    mysqli_stmt_close($stmt);
}

function staffUidExists($conn, $uid, $login) {
    $sql = "SELECT * FROM staff WHERE userId = ?;";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        if ($login == false) {
        header("location: ../signup.php?error=asUidStmtFailed");
        exit();} else {
            header("location: ../index.php?error=asUidStmtFailed");
        exit();
        }
        exit();
    }

    mysqli_stmt_bind_param($stmt, "s", $uid);
    mysqli_stmt_execute($stmt);

    $resultData = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($resultData)) {
        return $row;
    } else {
        $result = false;
        return $result;
    }

    mysqli_stmt_close($stmt);
}

function studentEmailExists($conn, $email) {
    $sql = "SELECT * FROM students WHERE email = ?;";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("location: ../signup.php?error=stEmailStmtFailed");
        exit();
    }
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);

    $resultData = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($resultData)) {
        return $row;
    } else {
        $result = false;
        return $result;
    }

    mysqli_stmt_close($stmt);
}

function staffEmailExists($conn, $email) {
    $sql = "SELECT * FROM staff WHERE email = ?;";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("location: ../signup.php?error=asEmailStmtFailed");
        exit();
    }
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);

    $resultData = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($resultData)) {
        return $row;
    } else {
        $result = false;
        return $result;
    }

    mysqli_stmt_close($stmt);
}

function createStudent($conn, $accessLevel, $fName, $lName, $dName, $email, $uid, $pwd) {
    $sql = "INSERT INTO students (firstName, lastName, displayName, email, userId, pwd, accessLevel) VALUES (?, ?, ?, ?, ?, ?, ?);";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("location: ../signup.php?error=createStStmtFailed");
        exit();
    }

    $hashedPwd = password_hash($pwd, PASSWORD_DEFAULT);

    mysqli_stmt_bind_param($stmt, "ssssssi", $fName, $lName, $dName, $email, $uid, $hashedPwd, $accessLevel);
    mysqli_stmt_execute($stmt);
    session_start();
    $uidExists = uidExists($conn, $uid);
    $_SESSION["userId"] = $uidExists["id"];
    $_SESSION["userUid"] = $uidExists["userId"];
    $_SESSION["userName"] = $uidExists["displayName"];
    $_SESSION["accessLevel"] = $uidExists["accessLevel"];
    header("location: ../dashboard.php?signup=success");
    mysqli_stmt_close($stmt);
    exit();
}

function createTeacher($conn, $accessLevel, $fName, $lName, $dName, $email, $uid, $pwd) {
    $sql = "INSERT INTO staff (firstName, lastName, displayName, email, userId, pwd, accessLevel) VALUES (?, ?, ?, ?, ?, ?, ?);";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("location: ../signup.php?error=createAsStmtFailed");
        exit();
    }

    $hashedPwd = password_hash($pwd, PASSWORD_DEFAULT);

    mysqli_stmt_bind_param($stmt, "ssssssi", $fName, $lName, $dName, $email, $uid, $hashedPwd, $accessLevel);
    mysqli_stmt_execute($stmt);
    session_start();
    $uidExists = uidExists($conn, $uid);
    $_SESSION["userId"] = $uidExists["id"];
    $_SESSION["userUid"] = $uidExists["userId"];
    $_SESSION["userName"] = $uidExists["displayName"];
    $_SESSION["accessLevel"] = $uidExists["accessLevel"];
    header("location: ../dashboard.php?signup=success");
    mysqli_stmt_close($stmt);
    exit();
}

function getStudent($conn, $userId) {
    $sql = "SELECT * FROM students WHERE userId = ?;";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("Location: ../view.php?id=$userId&error=stmtFailed");
        exit();
    }

    mysqli_stmt_bind_param($stmt, "s", $userId);
    mysqli_stmt_execute($stmt);

    $resultData = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($resultData)) {
        return $row;
    } else {
        $result = false;
        return $result;
    }

    mysqli_stmt_close($stmt);

}

function getTeacher($conn, $userId) {
    $sql = "SELECT * FROM staff WHERE userId = ?;";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("Location: ../view.php?id=$userId&error=stmtFailed");
        exit();
    }

    mysqli_stmt_bind_param($stmt, "s", $userId);
    mysqli_stmt_execute($stmt);

    $resultData = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($resultData)) {
        return $row;
    } else {
        $result = false;
        return $result;
    }

    mysqli_stmt_close($stmt);

}