<?php
    include_once 'header.php';
    $accessLevel = $_SESSION["accessLevel"];
    if($accessLevel === 1) {
        header("Location: dashboard.php");
        exit();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Request</title>
</head>
<body>
    <?php
        include_once './includes/funcMain.inc.php';
        head();
    ?>

    <section><h1>New Request</h1></section>

    <section class="requesting">
        <form action="./includes/newRequest.inc.php" method="post" class="new" enctype="multipart/form-data">
            <input type="text" name="subject" placeholder="Subject">
            <br /><br />
            <textarea name="content" id="content" placeholder="Content"></textarea>
            <br /><br />
            <input type="file" name="file">
            <br /><br />
            <button type="submit" name=submit>Post</button>
        </form>
    </section>

</body>
</html>