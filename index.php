<?php

ini_set('session.cache_limiter','public');
session_cache_limiter(false);

if (isset($_POST['reset'])) {
    session_start();
    session_unset();
    session_destroy();
}

session_start();

if (isset($_SESSION['quizRunning'])) {
    header("Location: quiz.php");
}

include_once 'DbConn.php';
$dbConn = new DbConn();

?>

<!DOCTYPE html>
<html lang="de-at">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Quiz: Startseite</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<main>
    <header>
        <h1>Quiz</h1>
        <a class="header-link" href="admin.php">Admin-Page</a>
    </header>
    <div class="content-box">
        <p>Folgende Kategorien stehen zur Auswahl:</p>
        <form action="category.php" method="post">
            <ul>
                <?php

                $stmt = $dbConn->executeQuery("SELECT * FROM category WHERE fk_superCategoryID IS NULL");

                foreach ($stmt as $record) {
                    echo "<li class='more-margin-bottom'><button class='btn-lnk' type='submit' value='{$record['pk_CategoryID']}' name='category'>{$record['Description']}</button></li>";
                }

                ?>
            </ul>
        </form>
    </div>
</main>
</body>
</html>

