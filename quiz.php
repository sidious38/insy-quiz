<?php

ini_set('session.cache_limiter', 'public');
session_cache_limiter(false);

session_start();

if (isset($_POST['quizid'])) {
    $_SESSION['quizRunning'] = true;
    $_SESSION['quizid'] = $_POST['quizid'];
    $_SESSION['questionNr'] = $_POST['questionNr'] ?? 1;
} else if (isset($_SESSION['quizid'], $_SESSION['questionNr'])) {
    $_POST['quizid'] = $_SESSION['quizid'];
    $_POST['questionNr'] = $_SESSION['questionNr'] ?? 1;
}

if (!isset($_SESSION['quizRunning'])) {
    header("Location: index.php");
}

include_once 'DbConn.php';
$dbConn = new DbConn();

$_POST['quizid'] = $_POST['quizid'] ?? null;
$_POST['questionNr'] = $_POST['questionNr'] ?? 1;

// Answer-Counter
if (isset($_POST['lastAnswer']) && is_numeric($_POST['lastAnswer'])) {
    $_SESSION['answers'][$_POST['questionNr'] - 1] = $_POST['lastAnswer'];
}

// End results
$stmt = $dbConn->executeQuery(
    "SELECT * FROM question WHERE fk_QuizID = :QuizID AND OrderNr = :QuestionNr",
    ['QuizID' => $_POST['quizid'], 'QuestionNr' => $_POST['questionNr']]
);

if ($stmt->rowCount() < 1) {
    $_SESSION['quizId'] = $_POST['quizid'];
    $_SESSION['correctAnswers'] = 0;
    foreach ($_SESSION['answers'] as $answer) {
        $stmt = $dbConn->executeQuery(
            "SELECT isCorrect FROM answer WHERE pk_AnswerID = :AnswerID",
            ['AnswerID' => $answer]
        );
        if ($stmt->fetch()['isCorrect']) {
            $_SESSION['correctAnswers']++;
        }
    }
    header("Location: result.php");
}

?>

<!DOCTYPE html>
<html lang="de-at">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Quiz - Fragebogen</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<main>
    <header>
        <h1>Frage <?= $_POST['questionNr'] ?></h1>
        <form action="index.php" method="post">
            <button class="header-link" type="submit" id="reset" name="reset" value="true">Reset</button>
        </form>
    </header>
    <div class="content-box">
        <form method="post">
            <input type="hidden" name="quizid" value="<?= $_POST['quizid'] ?>">
            <?php

            $stmtQuestion = $dbConn->executeQuery(
                "SELECT * FROM question WHERE fk_QuizID = :QuizID AND OrderNr = :QuestionNr",
                ['QuizID' => $_POST['quizid'], 'QuestionNr' => $_POST['questionNr']]
            );

            if ($recordQuestion = $stmtQuestion->fetch()) {
                echo "<div class='question'>";
                echo "<h2>{$recordQuestion['Title']}</h2>";
                echo "<p class='description-question'><em>{$recordQuestion['Description']}</em></p>";
                echo "</div>";

                $stmtAnswer = $dbConn->executeQuery(
                    "SELECT * FROM answer WHERE fk_QuestionID = :QuestionID ORDER BY OrderNr",
                    ['QuestionID' => $recordQuestion['pk_QuestionID']]
                );

                $isMultipleChoice = ($recordQuestion['isMultipleChoice'] == 0) ? "radio" : "checkbox";

                echo "<ul class='options'>";

                foreach ($stmtAnswer as $recordAnswer) {
                    if (isset($_SESSION['answers'], $_SESSION['answers'][$_POST['questionNr']])) {
                        $isChecked = ($_SESSION['answers'][$_POST['questionNr']] == $recordAnswer['pk_AnswerID']) ? 'checked' : '';
                    } else {
                        $isChecked = '';
                    }
                    echo "<li>";
                    echo "<label class='option-label'>";
                    echo "<input class='option-checkbox' type='{$isMultipleChoice}' name='lastAnswer' id='{$recordAnswer['pk_AnswerID']}' value='{$recordAnswer['pk_AnswerID']}' {$isChecked} required>";
                    echo "<span class='option-text'>{$recordAnswer['Text']}</span>";
                    echo "</label>";
                    echo "</li>";
                }

                echo "</ul>";
            }

            ?>
            <br>
            <div class="btn-box smaller-gap">
                <?php if ($_POST['questionNr'] > 1) { ?>
                    <button type="button" class="submit-btn smaller-submit-btn" onclick="window.history.back()"><<<</button>
                <?php } ?>
                <button type="submit" class="submit-btn smaller-submit-btn" id="next" name="questionNr" value="<?= $_POST['questionNr'] + 1 ?>">>>></button>
            </div>
        </form>
    </div>
</main>
</body>
</html>

}