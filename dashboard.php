<?php
include("auth_session.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="./style_main.css">
</head>
<body>
    <?php
    include_once './includes/func.inc.php';
    include_once './db.php';
    $name = $_SESSION["name"];
    $uid =  $_SESSION["indexNo"];
    echo '
    <header>
        <table>
            <tr>
                <th><a class="active" href="./dashboard.php">Home</a></th>
                <th><a href="./profile.php?id='.$uid.'">Profile</a></th>
                <th><a href="./logout.php" class="logout">Logout</a></th>
            </tr>
        </table>
    </header>';
    
    $userId = $_SESSION["indexNo"];
    $username = $_SESSION["username"];
    $accessLevel = $_SESSION["accessLevel"];
    $name = $_SESSION["name"];

    if (isset($_GET["login"])) {
        echo '<section id="greeting"><h1>Welcome '.$name.'</h1></section>';
    }

    if ($accessLevel === 0) {
        $sql1 = "SELECT * FROM discussions WHERE userId = '$userId' AND readStatus = 'no';";
        $results1 = mysqli_query($conn, $sql1);
        if ($results1 and mysqli_num_rows($results1) > 0) {
            echo '
            <section class="notifications">
                <h2>Notifications</h2>
                <ul class="notification-list">
                ';
            while($row1 = mysqli_fetch_assoc($results1)) {
                $status = $row1["currStatus"];
                $arr1 = explode("-", $status);
                $action = $arr1[0];
                $postId = $row1["postId"];

                if ($action === "approved") {
                    echo '<li class="notification-item">Your request has been approved.<br>Click <a href="./view.php?id='.$postId.'&notification=reset">here</a> to view more.';
                } elseif ($action === "declined") {
                    echo '<li class="notification-item">Your request has been declined.<br>Click <a href="./view.php?id='.$postId.'&notification=reset">here</a> to view more.';                     
                } elseif ($action === "moreInfo") {
                    echo '<li class="notification-item">More info requested on your request.<br>Click <a href="./view.php?id='.$postId.'&notification=reset">here</a> to view more.';
                }
                
            }
            echo '</ul></section>';
        }


        echo '
            <section class="requests">
                <a href="./newRequest.php"><h3>Add new request</h3></a>
                <h2>My requests</h2>';
        $sql = "SELECT * FROM discussions WHERE userId = '$userId';";
        $results = mysqli_query($conn, $sql);
        if ($results and mysqli_num_rows($results) > 0) {
            $cnt = 1;
            echo '
                <div>
                    <table class="reqTable">
                        <tr class="reqTable-heading" >
                            <th class="reqTable-headItem no">No.</th>
                            <th class="reqTable-headItem subject">Subject</th>
                            <th class="reqTable-headItem date">Created Date</th>
                            <th class="reqTable-headItem time">Created Time</th>
                            <th class="reqTable-headItem status">Status</th>
                        </tr>';
            while($row = mysqli_fetch_assoc($results)) {
                $status = $row["currStatus"];

                if ($status === "pending"){
                    pending($row, $cnt);
                } else {
                    $arr = explode("-", $status);
                    $action = $arr[0];

                    if ($action === "approved") {
                        approved($row, $cnt) ;                      
                    } elseif ($action === "declined") {
                        declined($row, $cnt);                     
                    } elseif ($action === "moreInfo") {
                        more_info($row, $cnt);                       
                    }
                }
                $cnt += 1;                
            }
            echo '
                    </table>
                </div>
            </section>';
        } else {
            echo '<div><h4>No posts to display</h4></div>';
        }
    } elseif ($accessLevel === 1) {
        ?>
        <section class="filter-sec">
        <div class="filters">
            <h2 id="filter">Filters</h2>
            <form method="post" action="./dashboard.php" class="filter">
                <table class="filter-table">
                    <tr class="filter-row">
                        <td class="filter-item">
                            <label for="filter-status">Status</label><br /><br />
                            <select name="filter-status" id="filter-status">
                                <option value="notSet"> </option>
                                <option value="approved">approved</option>
                                <option value="declined">declined</option>
                                <option value="pending">pending</option>
                            </select>
                        </td>
                        <td class="filter-item">
                            <label for="filter-type">Request Type</label><br /><br />
                            <select name="filter-type" id="filter-type">
                                <option value="notSet"> </option>
                                <option value="late-add-drop">Late add or drop request</option>
                                <option value="extend-submission">Extend submission deadline</option>
                                <option value="repeat-exams">Repeat exams as first attempt with the next batch</option>
                            </select>
                        </td>
                        <td class="filter-item">
                            <label for="filter-index">Index No.</label><br /><br />
                            <input type="text" name="filter-index" id="filter-index">
                        </td>
                        <td class="filter-item">
                            <label for="filter-uname">Student Username</label><br /><br />
                            <input type="text" name="filter-uname" id="filter-uname">
                        </td>
                        <td class="filter-item">
                            <label for="filter-name">Student Name</label><br /><br />
                            <input type="text" name="filter-name" id="filter-name">
                        </td>
                    </tr>
                </table>
                <input type="hidden" name="filter-hidden" value="set">
                <br />
                <button class="filter-button">Filter</button>
            </form>
        </div>
        </section>
        <?php
        if(isset($_POST['filter-hidden'])) {
            $status = $_POST['filter-status'];
            $subject = $_POST['filter-type'];
            $index = $_POST['filter-index'];
            $uname = $_POST['filter-uname'];
            $fi_name = $_POST['filter-name'];
            $sql = "WHERE";
            $setStatus = false;
            echo '
            <section class="filters-applied">
            <div class="list">
                <h2>Applied Filters</h2>
                <table class="list-table">';
            if($status !== "notSet") {
                $sql = $sql." status='$status'";
                $setStatus = true;
                echo '
                    <tr>
                        <td class="list-heading">Status</td>
                        <td class="list-data">'.$status.'
                    </tr>';
            }
            if($subject !== "notSet") {
                if ($subject === "late-add-drop") {
                    $subject = "Late add or drop request";
                } elseif ($subject === "extend-submission") {
                    $subject = "Extend submission deadline";
                } elseif ($subject === "repeat-exams") {
                    $subject = "Repeat exams as first attempt with the next batch";
                }
                $sql = $sql." postSubject='$subject'";
                $setStatus = true;
                echo '
                    <tr>
                        <td class="list-heading">Type</td>
                        <td class="list-data">'.$subject.'
                    </tr>';
            }
            if(!empty($index)) {
                $sql = $sql." userId='$index'";
                $setStatus = true;
                echo '
                    <tr>
                        <td class="list-heading">Index No.</td>
                        <td class="list-data">'.$index.'
                    </tr>';
            }
            if(!empty($uname)) {
                $sql = $sql." userName='$uname'";
                $setStatus = true;
                echo '
                    <tr>
                        <td class="list-heading">User Name</td>
                        <td class="list-data">'.$uname.'
                    </tr>';
            }
            if(!empty($fi_name)) {
                $sql = $sql." userFullName='$fi_name'";
                $setStatus = true;
                echo '
                    <tr>
                        <td class="list-heading">Name</td>
                        <td class="list-data">'.$fi_name.'
                    </tr>';
            }
            echo '</table>';
            if ($setStatus) {
                $sql = "SELECT * FROM discussions ".$sql.";";
                echo '<br /><a href="./dashboard.php"><button class="filter-button">Reset</button></a>';
            } else {
                echo "<h3>No filters applied</h3>
                </section>";
                $sql = "SELECT * FROM discussions;";
            }
        } else {
            $sql = "SELECT * FROM discussions WHERE currStatus='pending' OR currStatus LIKE '%more%';";
        }
        $results = mysqli_query($conn, $sql);
        echo '<section class="requests">';
        if ($results and mysqli_num_rows($results) > 0) {
            $cnt = 1;
            echo '
                <div>
                    <h2>Requests</h2>
                    <table class="reqTable">
                        <tr class="reqTable-heading" >
                            <th class="reqTable-headItem no">No.</th>
                            <th class="reqTable-headItem subject">Subject</th>
                            <th class="reqTable-headItem user">Created User</th>
                            <th class="reqTable-headItem date">Created Date</th>
                            <th class="reqTable-headItem time">Created Time</th>
                            <th class="reqTable-headItem status">Status</th>
                        </tr>';
            while($row = mysqli_fetch_assoc($results)) {
                $status = $row["currStatus"];

                if ($status === "pending") {
                    pending($row, $cnt);
                } else {
                    $arr = explode("-", $status);
                    $action = $arr[0];

                    if ($action === "approved") {
                        approved($row, $cnt) ;                      
                    } elseif ($action === "declined") {
                        declined($row, $cnt);                     
                    } elseif ($action === "moreInfo") {
                        more_info($row, $cnt);
                    }
                }
                $cnt += 1;                
            }
            echo '
                    </table>
                </div>';
        } else {
            echo "<h2>No requests</h2>
            </section>";
        }
    }


    ?>
    
</body>
</html>