<?php
include_once 'auth_session.php';
include_once './includes/dbh.inc.php';
include_once './includes/funcMain.inc.php';

$userId = $_SESSION["userUid"];
$userName = $_SESSION["userName"];
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
            
            echo $userData["displayName"].'
    </title>
    <body>';
            head();
            echo '
        </br>
        <section><h1>'.$userData["displayName"].'</h1></section>
        
        <section class="userData">
            <table style="text-align:left">
                <tr>
                    <th>Index No</th>
                    <td>'.$userData["userId"].'</td>
                </tr>
                <tr>
                    <th>Display Name</th>
                    <td>'.$userData["displayName"].'</td>
                </tr>
                <tr>
                    <th>First Name</th>
                    <td>'.$userData["firstName"].'</td>
                </tr>
                <tr>
                    <th>Last Name</th>
                    <td>'.$userData["lastName"].'</td>
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
        echo $userData["displayName"].'
    </title>
    <body>';
            head();
            echo '
        </br>
        <section><h1>'.$userData["displayName"].'</h1></section>
        
        <section class="userData">
            <table style="text-align:left">
                <tr>
                    <th>Index No</th>
                    <td>'.$userData["userId"].'</td>
                </tr>
                <tr>
                    <th>Display Name</th>
                    <td>'.$userData["displayName"].'</td>
                </tr>
                <tr>
                    <th>First Name</th>
                    <td>'.$userData["firstName"].'</td>
                </tr>
                <tr>
                    <th>Last Name</th>
                    <td>'.$userData["lastName"].'</td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td>'.$userData["email"].'</td>
                </tr>
            </table>
        </section>';
        if ($userData["accessLevel"] === 0) {
            echo '
            <section id="requests">
                <h3>Requests by '.$userData["displayName"].'</h3>';
            $sql = "SELECT * FROM discussions WHERE userId = '$viewId';";
            $results = mysqli_query($conn, $sql);
            if ($results and mysqli_num_rows($results) > 0) {
                $cnt = 1;
                echo '
                    <div>
                        <table class="reqTable">
                            <tr class="tableHead" >
                                <th id="no">No.</th>
                                <th id="sub">Subject</th>
                                <th id="dt">Created Date</th>
                                <th id="tm">Created Time</th>
                                <th id="sts">Status</th>
                            </tr>';
                while($row = mysqli_fetch_assoc($results)) {
                    $postDate = $row["createdDate"];
                    $postTime = $row["createdTime"];
                    $status = $row["currStatus"];
                    $subject = $row["postSubject"];
                    $postId = $row["postId"];

                    if ($status === "pending"){
                        echo '
                            <tr class="pending">
                                <td id="no">'.$cnt.'</td>
                                <td id="sub"><a href="view.php?id='.$postId.'">'.$subject.'</a></td>
                                <td id="dt">'.$postDate.'</td>
                                <td id="tm">'.$postTime.'</td>
                                <td id="sts">Pending</td>
                            </tr>';
                    } else {
                        $arr = explode("-", $status);
                        $action = $arr[0];
                        $actionUserName = $arr[1];
                        $actionUserId = $arr[2];

                        if ($action === "approved") {
                            echo '
                            <tr class="approved">
                                <td id="no">'.$cnt.'</td>
                                <td id="sub"><a href="view.php?id='.$postId.'">'.$subject.'</a></td>
                                <td id="dt">'.$postDate.'</td>
                                <td id="tm">'.$postTime.'</td>
                                <td id="sts">Approved by <a href="profile.php?id='.$actionUserName.'">'.$actionUserId.'</td>
                            </tr>';                        
                        } elseif ($action === "declined") {
                            echo '
                            <tr class="declined">
                                <td id="no">'.$cnt.'</td>
                                <td id="sub"><a href="view.php?id='.$postId.'">'.$subject.'</a></td>
                                <td id="dt">'.$postDate.'</td>
                                <td id="tm">'.$postTime.'</td>
                                <td id="sts">Declined by <a href="profile.php?id='.$actionUserName.'">'.$actionUserId.'</td>
                            </tr>';                        
                        } elseif ($action === "moreInfo") {
                            echo '
                            <tr class="moreInfo">
                                <td id="no">'.$cnt.'</td>
                                <td id="sub"><a href="view.php?id='.$postId.'">'.$subject.'</a></td>
                                <td id="dt">'.$postDate.'</td>
                                <td id="tm">'.$postTime.'</td>
                                <td id="sts">More information requested by <a href="profile.php?id='.$actionUserName.'">'.$actionUserId.'</td>
                            </tr>';                        
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