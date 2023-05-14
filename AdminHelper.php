<?php

require_once 'DbConn.php';

function getCategory($superCategory, $space = "")
{
    $dbConn = new DbConn();
    $stmt = null;
    if (is_null($superCategory)) {
        $stmt = $dbConn->executeQuery(
            "SELECT * FROM category WHERE fk_superCategoryID IS NULL"
        );
    } else {
        $stmt = $dbConn->executeQuery(
            "SELECT * FROM category WHERE fk_superCategoryID = :superCategoryID",
            ['superCategoryID' => $superCategory]
        );
    }
    if ($stmt->rowCount() < 1) {
        return;
    }
    foreach ($stmt as $record) {
        $data = join(';', array_unique($record));
        echo "<option value='{$data}'>{$space}{$record['Description']}</option>";
        getCategory($record['pk_CategoryID'], $space . "&emsp;");
    }
}

function getQuestions($quiz)
{
    $dbConn = new DbConn();

    $stmt = $dbConn->executeQuery(
        "SELECT *, q.OrderNr AS qOrderNr, a.OrderNr AS aOrderNr FROM Question q LEFT JOIN Answer a 
                ON (q.pk_QuestionID = a.fk_QuestionID) WHERE fk_QuizID = :QuizID ORDER BY q.OrderNr, a.OrderNr",
        ['QuizID' => $quiz]
    );

    echo "<input type='hidden' name='id' value='{$quiz}'>";

    $questionCounter = 1;
    $answerCounter = 1;
    $questionID = -1;

    foreach ($stmt as $record) {
        if ($questionID != $record['pk_QuestionID']) {
            if ($questionID != -1) {
                echo "<button class='add-answer' type='button'><img class='svg-btn' src='assets/plus.svg' alt='+'></button>";
                echo "</div>";
                echo "<hr class='question-separator'>";
                $questionCounter++;
            }
            $answerCounter = 1;
            $questionID = $record['pk_QuestionID'];
            echo "<div id='question-{$questionCounter}' class='question-block'>";
            echo "<input type='hidden' id='mode-{$questionID}' name='mode{$questionID}' value='none'>";
            echo "<div class='form-userinput more-margin-bottom'>";
            echo "<label for='title-{$questionID}'>Frage: </label>";
            echo "<div class='input-field fill-avail-width'>";
            echo "<input class='input-fill-avail' type='text' id='title-{$questionID}' name='title{$questionID}' value='{$record['Title']}'>";
            echo "<input type='number' name='orderNr{$questionID}' value='{$record['qOrderNr']}' min='1' required>";
            echo "<button id='delete-button-{$questionID}' class='delete-question' type='button'><img class='svg-btn' src='assets/x.svg' alt='X'></button>";
            echo "</div>";
            echo "</div>";
            echo "<div class='form-userinput more-margin-bottom'>";
            echo "<label for='description-{$questionID}'>Beschreibung: </label>";
            echo "<textarea class='fill-avail-width' id='description-{$questionID}' name='description{$questionID}'>{$record['Description']}</textarea>";
            echo "</div>";
        }
        $answerID = $record['pk_AnswerID'];
        echo "<div id='question-{$questionCounter}-ans-{$answerCounter}' class='input-field answer'>";
        echo "<input type='hidden' id='{$questionID}-mode-{$answerID}' name='{$questionID}mode{$answerID}' value='none'>";
        echo "<label for='{$questionID}-title-{$answerID}'>Antwort: </label>";
        echo "<input class='input-fill-avail' type='text' id='{$questionID}-title-{$answerID}' name='{$questionID}titleAns{$answerID}' value='{$record['Text']}'>";
        echo "<input type='number' name='{$questionID}orderNrAns{$answerID}' value='{$record['aOrderNr']}'  min='1' required>";
        echo "<input class='align-center' type='checkbox' name='{$questionID}isCorrectAns{$answerID}' " . ($record['isCorrect'] ? 'checked' : '') . ">";
        echo "<button class='delete-answer' type='button'><img class='svg-btn' src='assets/x.svg' alt='X'></button>";
        echo "</div>";
        $answerCounter++;
    }
    if ($questionID != -1) {
        echo "<button class='add-answer' type='button'><img class='svg-btn' src='assets/plus.svg' alt='+'></button>";
        echo "</div>";
        echo "<hr class='question-separator'>";
    }
}

function resetOrderNr($questionId) {
    $dbConn = new DbConn();

    $dbConn->executeQuery(
        "UPDATE Question SET OrderNr = NULL WHERE pk_QuestionID = :QuestionID",
        ['QuestionID' => $questionId]
    );

    $dbConn->executeQuery(
        "UPDATE Answer SET OrderNr = NULL WHERE fk_QuestionID = :QuestionID",
        ['QuestionID' => $questionId]
    );
}