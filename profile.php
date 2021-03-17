<?php
include_once 'auth_session.php';
include_once './db.php';
include_once './includes/func.inc.php';

$userId = $_SESSION["indexNo"];
$name = $_SESSION["name"];
$userName = $_SESSION["username"];
$accessLevel = $_SESSION["accessLevel"];

if (isset($_GET['id'])){
    $viewId = $_GET['id'];
    
    echo '
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>';
    
    if ($accessLevel === 0) {
        if ($viewId === $userId) {
            $userData = getUser($conn, $viewId);
            $displayName = $userData["firstname"]." ".$userData["lastname"];
            echo $displayName.'
    </title>
    <link rel="stylesheet" href="./style_main.css">
    </head>
    <body>
    <header>
        <table>
            <tr>
                <th><a href="./dashboard.php">Home</a></th>
                <th><a class="active" href="./profile.php?id='.$userId.'">'.$name.'</a></th>
                <th><a href="./logout.php">Logout</a></th>
            </tr>
        </table>
    </header>
        </br>
        <section><h1>'.$displayName.'</h1></section>
        
        <section class="userData">
            <table style="text-align:left">
                <tr>
                    <th>Index No</th>
                    <td>'.$userData["indexNo"].'</td>
                </tr>
                <tr>
                    <th>First Name</th>
                    <td>'.$userData["firstname"].'</td>
                </tr>
                <tr>
                    <th>Last Name</th>
                    <td>'.$userData["lastname"].'</td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td>'.$userData["email"].'</td>
                </tr>
            </table>
        </section>';
        } else {
            header("Location: dashboard.php?access=unauthorized");
            exit();
        }

    } else {
        $userData = getUser($conn, $viewId);
        $displayName = $userData["firstname"]." ".$userData["lastname"];
        echo $displayName.'
    </title>
    <link rel="stylesheet" href="./style_main.css">
    </head>
    <body>
        <header>
            <table>
                <tr>
                    <th><a href="./dashboard.php">Home</a></th>';
                    if ($_SESSION["indexNo"] === $viewId) {
                        echo '
                        <th><a class="active" href="./profile.php?id='.$_SESSION["indexNo"].'">'.$name.'</a></th>';
                    } else {
                        echo '
                        <th><a href="./profile.php?id='.$uid.'">'.$name.'</a></th>';
                    }
                    echo '
                    <th><a href="./logout.php">Logout</a></th>
                </tr>
            </table>
        </header>
        </br>
        <section><h1>'.$displayName.'</h1></section>
        
        <section class="userData">
            <table style="text-align:left">
                <tr>
                    <th>Index No</th>
                    <td>'.$userData["indexNo"].'</td>
                </tr>
                <tr>
                    <th>First Name</th>
                    <td>'.$userData["firstname"].'</td>
                </tr>
                <tr>
                    <th>Last Name</th>
                    <td>'.$userData["lastname"].'</td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td>'.$userData["email"].'</td>
                </tr>
            </table>
        </section>';
        if ($userData["acc_type"] === "students") {
            echo '
            <section id="requests">
                <h3>Requests by '.$displayName.'</h3>';
            $sql = "SELECT * FROM discussions WHERE userId = '$viewId';";
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
                echo '<div><h3>No posts to display</h3></div>';
            }
        }
    }
    
    echo '    
    </title>
</head>
<body>
    
</body>
</html>';
}