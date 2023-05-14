<?php

ini_set('session.cache_limiter', 'public');
session_cache_limiter(false);

session_start();

include_once 'DbConn.php';
$dbConn = new DbConn();

$_POST['category'] = $_POST['category'] ?? null;

$categoryName = $dbConn->executeQuery(
    "SELECT Description FROM category WHERE pk_CategoryID = :CategoryID",
    ['CategoryID' => $_POST['category']]
)->fetch()['Description'];

$stmtCategory = $dbConn->executeQuery(
    "SELECT * FROM category WHERE fk_superCategoryID = :CategoryID",
    ['CategoryID' => $_POST['category']]
);
$stmtQuiz = $dbConn->executeQuery(
    "SELECT * FROM quiz WHERE fk_CategoryID = :CategoryID",
    ['CategoryID' => $_POST['category']]
);

$hasContent = false;

?>

<!DOCTYPE html>
<html lang="de-at">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Quiz - Kategorie</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<main>
    <header>
        <h1>Kategorie <?= $categoryName ?></h1>
        <a class="header-link" onclick="window.history.back()">Zurück</a>
    </header>
    <div class="content-box">
        <?php if ($stmtCategory->rowCount() > 0) { ?>
            <p>Folgende Unterkategorien stehen zur Auswahl:</p>
            <form action="category.php" method="post">
                <ul class="double-margin-bottom">
                    <?php

                    foreach ($stmtCategory as $record) {
                        echo "<li><button class='btn-lnk' type='submit' value='{$record['pk_CategoryID']}' name='category'>{$record['Description']}</button></li>";
                    }

                    $hasContent = true;

                    ?>
                </ul>
            </form>
        <?php } ?>
        <?php if ($stmtQuiz->rowCount() > 0) { ?>
            <p>Folgende Quizzes stehen zur Auswahl:</p>
            <form action="quiz.php" method="post">
                <ul>
                    <?php

                    foreach ($stmtQuiz as $record) {
                        echo "<li><button class='btn-lnk' type='submit' value='{$record['pk_QuizID']}' name='quizid'>{$record['Title']}</button></li>";
                    }

                    $hasContent = true;

                    ?>
                </ul>
            </form>
        <?php } ?>
        <?php if (!$hasContent) { ?>
            <p>Diese Kategorie hat derzeit keinen Inhalt!</p>
        <?php } ?>
    </div>
</main>
</body>
</html>
