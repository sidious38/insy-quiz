<?php

ini_set('session.cache_limiter','public');
session_cache_limiter(false);

session_start();

if (!isset($_SESSION['quizRunning'])) {
    header("Location: index.php");
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
    <title>Quiz: Results</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<main>
    <header>
        <h1>Ergebnis</h1>
        <form action="index.php" method="post">
            <button class="header-link" type="submit" id="reset" name="reset" value="true">Reset</button>
        </form>
    </header>
    <div class="content-box">
        <?php if (isset($_SESSION['correctAnswers'], $_SESSION['answers'])) { ?>
            <h2><?= round($_SESSION['correctAnswers'] / count($_SESSION['answers']), 2) * 100 ?>%</h2>
            <p class="double-margin-bottom">Sie haben <?= $_SESSION['correctAnswers'] ?> von <?= count($_SESSION['answers']) ?> Punkten erreicht!</p>
            <hr>
            <?php
                $stmtQuestion = $dbConn->executeQuery(
                  "SELECT * FROM Question WHERE fk_QuizID = :QuizID ORDER BY OrderNr",
                    ['QuizID' => $_SESSION['quizId']]
                );

                $questionCounter = 1;

                foreach ($stmtQuestion as $recordQuestion) {
                    echo "<div class='content-box'>";
                    echo "<h3>{$recordQuestion['Title']}</h3>";

                    $stmtAnswer = $dbConn->executeQuery(
                        "SELECT * FROM Answer WHERE fk_QuestionID = :QuestionID ORDER BY OrderNr",
                        ['QuestionID' => $recordQuestion['pk_QuestionID']]
                    );
                    echo "<div>";
                    foreach ($stmtAnswer as $recordAnswer) {
                        if ($recordAnswer['pk_AnswerID'] == $_SESSION['answers'][$questionCounter] && !$recordAnswer['isCorrect']) {
                            echo "<p class='ans-incorrect'>{$recordAnswer['Text']}</p>";
                        } else if ($recordAnswer['isCorrect']) {
                            echo "<p class='ans-correct'>{$recordAnswer['Text']}</p>";
                        } else {
                            echo "<p class='ans-neutral'>{$recordAnswer['Text']}</p>";
                        }
                    }
                    echo "</div>";
                    echo "</div>";
                    $questionCounter++;
                }
            ?>
        <?php } else { ?>
            <p>Das Quiz enthält keine Fragen.</p>
        <?php } ?>
    </div>
</main>
</body>
</html>

<?php

session_unset();
session_destroy();

?>