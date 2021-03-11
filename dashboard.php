<?php
//include auth_session.php file on all user panel pages
include("auth_session.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="./styles/stylesMain.css">
</head>
<body>
    <?php
    include_once './includes/funcMain.inc.php';
    include_once './includes/dbh.inc.php';
    head();
    
    $userId = $_SESSION["userUid"];
    $userName = $_SESSION["userName"];
    $accessLevel = $_SESSION["accessLevel"];

    if (isset($_GET["login"])) {
        echo "<h3>Welcome $userName</h3>";
    }

    if ($accessLevel === 0) {
        echo '
            <section id="new"><a href="newRequest.php"><h3>Add new request</h3></a></section>
            <section id="requests">
                <h3>My requests</h3>';
        $sql = "SELECT * FROM discussions WHERE userId = '$userId';";
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
                            <td id="sts">Approved by '.$actionUserId.'</td>
                        </tr>';                        
                    } elseif ($action === "declined") {
                        echo '
                        <tr class="declined">
                            <td id="no">'.$cnt.'</td>
                            <td id="sub"><a href="view.php?id='.$postId.'">'.$subject.'</a></td>
                            <td id="dt">'.$postDate.'</td>
                            <td id="tm">'.$postTime.'</td>
                            <td id="sts">Declined by '.$actionUserId.'</td>
                        </tr>';                        
                    } elseif ($action === "moreInfo") {
                        echo '
                        <tr class="moreInfo">
                            <td id="no">'.$cnt.'</td>
                            <td id="sub"><a href="view.php?id='.$postId.'">'.$subject.'</a></td>
                            <td id="dt">'.$postDate.'</td>
                            <td id="tm">'.$postTime.'</td>
                            <td id="sts">More information requested by '.$actionUserId.'</td>
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
    } elseif ($accessLevel === 1) {
        $sql = "SELECT * FROM discussions;";
        $results = mysqli_query($conn, $sql);
        if ($results and mysqli_num_rows($results) > 0) {
            $cnt = 1;
            echo '
                <div>
                    <table class="reqTable">
                        <tr class="tableHead" >
                            <th id="no">No.</th>
                            <th id="sub">Subject</th>
                            <th id="sub">Created User</th>
                            <th id="dt">Created Date</th>
                            <th id="tm">Created Time</th>
                            <th id="sts">Status</th>
                        </tr>';
            while($row = mysqli_fetch_assoc($results)) {
                $postId = $row["postId"];
                $createdUser = $row["userName"];
                $createdUserId = $row["userId"];
                $postDate = $row["createdDate"];
                $postTime = $row["createdTime"];
                $status = $row["currStatus"];
                $subject = $row["postSubject"];

                if ($status === "pending"){
                    echo '
                        <tr class="pending">
                            <td id="no">'.$cnt.'</td>
                            <td id="sub"><a href="view.php?id='.$postId.'">'.$subject.'</a></td>
                            <td id="usr">'.$createdUser.'</td>
                            <td id="dt">'.$postDate.'</td>
                            <td id="tm">'.$postTime.'</td>
                            <td id="sts">Pending</td>
                        </tr>';
                } else {
                    $arr = explode("-", $status);
                    $action = $arr[0];
                    $actionUserName = $arr[2];
                    $actionUserId = $arr[1];

                    if ($action === "moreInfo") {
                        echo '
                        <tr class="moreInfo">
                            <td id="no">'.$cnt.'</td>
                            <td id="sub"><a href="view.php?id='.$postId.'">'.$subject.'</a></td>
                            <td id="usr">'.$createdUser.'</td>
                            <td id="dt">'.$postDate.'</td>
                            <td id="tm">'.$postTime.'</td>
                            <td id="sts">More information requested by '.$actionUserName.'</td>
                        </tr>';                        
                    }
                }
                $cnt += 1;                
            }
            echo '
                    </table>
                </div>
            </section>';
        }
    }


    ?>
    
</body>
</html>