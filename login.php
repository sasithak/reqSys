<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <title>Login</title>
    <link rel="stylesheet" href="style.css"/>
</head>
<body>
<?php
    require('db.php');
    session_start();
    // When form submitted, check and create user session.
    if (isset($_POST['username'])) {
        $username = stripslashes($_REQUEST['username']);    // removes backslashes
        $username = mysqli_real_escape_string($con, $username);
        $password = stripslashes($_REQUEST['password']);
        $password = mysqli_real_escape_string($con, $password);
        echo md5($password);
        // Check user is exist in the database
        $query    = "SELECT * FROM userlist WHERE username='$username' AND pwd='" . md5($password) . "'";
        $result = mysqli_query($con, $query);
        $rows = mysqli_num_rows($result);
        if ($rows == 1) {
            $row = mysqli_fetch_assoc($result);
            $accessLevel;
            if ($row['acc_type'] === "students") {
                $accessLevel = 0;
            } else if ($row['acc_type'] === "staff") {
                $accessLevel = 1;
            }
            $_SESSION['username'] = $username;
            $_SESSION['accessLevel'] = $accessLevel;
            $_SESSION['indexNo'] = $row['indexNo'];
            $_SESSION['name'] = $row['firstname']." ".$row['lastname'];
            // Redirect to user dashboard page
            header("Location: dashboard.php/?login=success");
        } else {
            echo "
                <div class='form'>
                    <h3>Incorrect Username/password.</h3><br/>
                    <p class='link'>Click here to <a href='login.php'>Login</a> again.</p>
                </div>";
        }
    } else {
?>
    <form class="form" method="post" name="login">
        <h1 class="login-title">Login</h1>
	<label for="username">Username:</label>
        <input type="text" class="login-input" name="username"/>
	<label for="password">Password:</label>
        <input type="password" class="login-input" name="password"/>
        <input type="submit" value="Login" name="Log in" class="login-button"/>
	<p>Not a member?</p>
        <p class="link">Click here to <a href="registration.php">Register.</a></p>
    </form>

<?php
    }
?>

</body>
</html>