<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <title>Registration</title>
    <link rel="stylesheet" href="style.css"/>
</head>
<body>
<?php
    require('db.php');
    // When form submitted, insert values into the database.
    if (isset($_REQUEST['username'])) {
        // removes backslashes
        $username = stripslashes($_REQUEST['username']);
        //escapes special characters in a string
        $username = mysqli_real_escape_string($con, $username);
        $email    = stripslashes($_REQUEST['email']);
        $email    = mysqli_real_escape_string($con, $email);
	$index = stripslashes($_REQUEST['index']);
        $index = mysqli_real_escape_string($con, $index);
        $password = stripslashes($_REQUEST['password']);
        $password = mysqli_real_escape_string($con, $password);
        $firstname = stripslashes($_REQUEST['firstname']);
        $firstname = mysqli_real_escape_string($con, $firstname);
	$lastname = stripslashes($_REQUEST['lastname']);
        $lastname = mysqli_real_escape_string($con, $lastname);
	$acc_type = stripslashes($_REQUEST['acc_type']);
        $acc_type = mysqli_real_escape_string($con, $acc_type);
        $query    = "INSERT into `userlist` (username, pwd, email, firstname, lastname, acc_type, indexNo) 
                    VALUES ('$username', '" . md5($password) . "', '$email', '$firstname', '$lastname','$acc_type', '$index')";
        $result   = mysqli_query($con, $query);
        if ($result) {
            echo  "
                <div class='form'>
                    <h3>You are registered successfully.</h3><br/>
                    <p class='link'>Click here to <a href='login.php'>Login</a></p>
                </div>";
        } else {
            echo "
                <div class='form'>
                    <h3>Required fields are missing.</h3><br/>
                    <p class='link'>Click here to <a href='registration.php'>register</a> again.</p>
                </div>";
        }
    } else {
?>

    <form class="form" action="" method="post">
        <h1 class="login-title">Registration</h1>
	<label>First name:</label><br>
	<input type="text" class="login-input" name="firstname" required /><br>
	<label>Last name:</label><br>
	<input type="text" class="login-input" name="lastname" required /><br>
	<label>Index number / Staff ID:</label><br>
	<input type="text" class="login-input" name="index" required /><br>
	<label>Preferred username:</label><br>
        <input type="text" class="login-input" name="username" required /><br>
	<label>Email address:</label><br>
        <input type="text" class="login-input" name="email"><br>
	<label>Password:</label><br>
        <input type="password" class="login-input" name="password"><br>
	<label for="acc_type">Account type: </label><br>
        <select name="acc_type" id="acc_type" style="width: calc(100% - 23px)">
        	<option value="students">Student</option>
		    <option value="staff">Academic Staff</option>
	</select><br>
	<hr color="#000055">
	<center><input type="submit" name="submit" value="Register" class="login-button"><center>
        <p class="link">Click to <a href="login.php">Login</a></p>
    </form>

<?php
    }
?>

</body>
</html>