<?php
    include_once './auth_session.php';
    $accessLevel = $_SESSION["accessLevel"];
    if($accessLevel === 1) {
        header("Location: dashboard.php");
        exit();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head id="nr_head">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Request</title>
    <link rel="stylesheet" href="./style_main.css">
</head>
<body>
    <?php
        include_once './includes/func.inc.php';
        head();
    ?>

    <section><h1>New Request</h1></section>

    <section class="requesting">
        <form action="./includes/newRequest.inc.php" method="post" class="new" enctype="multipart/form-data">
            <label for="subject">Request Type: </label>
            <select class="requesting_list" name="subject" id="subject">
                <option value="notSet"> </option>
                <option value="late-add-drop">Late add/drop request</option>
                <option value="extend-submission">Extend submission deadline</option>
                <option value="repeat-exams">Repeat exams as first attempt with the next batch</option>
            </select>
            <br /><br />
            <textarea name="content" id="content" placeholder="Content"></textarea>
            <br /><br />
            <input id="file_input" type="file" name="file">
            <br /><br />
            <button id="submit_button" type="submit" name="submit">Post</button>
        </form>
        <?php
            if (isset($_GET["incomplete"])) {
                if ($_GET["incomplete"] === "true") {
                    echo '<h3>Request type and Description are compulsory</h3>';
                }
            }
        ?>
    </section>

</body>
</html>