<?php

ini_set('session.cache_limiter', 'public');
session_cache_limiter(false);

session_start();

if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header('Location: index.php');
}

include_once 'DbConn.php';
$dbConn = new DbConn();

include_once 'AdminHelper.php';

if (isset($_POST['username'], $_POST['password'])) {
    $stmt = $dbConn->executeQuery(
        "SELECT * FROM Auth WHERE Username = :Username",
        ['Username' => $_POST['username']]
    );

    if ($record = $stmt->fetch()) {
        if ($record['Username'] == $_POST['username'] && password_verify($_POST['password'], $record['Password'])) {
            $_SESSION['loggedIn'] = true;
        } else {
            $_SESSION['loggedIn'] = false;
        }
    } else {
        $_SESSION['loggedIn'] = false;
    }
}

if (empty($_POST['category'])) {
    $_POST['category'] = null;
}

if (!is_null($_POST['category'])) {
    $_POST['category'] = explode(';', $_POST['category'])[0];
}

if (empty($_POST['superCategory'])) {
    $_POST['superCategory'] = null;
}

if (!is_null($_POST['superCategory'])) {
    $_POST['superCategory'] = explode(';', $_POST['superCategory'])[0];
}

?>

<!DOCTYPE html>
<html lang="de-at">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Quiz: Admin-Page</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<main>
    <?php if (!isset($_SESSION['loggedIn']) || !$_SESSION['loggedIn']) { ?>
        <header>
            <h1>Admin-Page</h1>
            <form>
                <button class="header-link" name="logout" id="logout">Zurück</button>
            </form>
        </header>
        <div class="content-box">
            <?php if (isset($_SESSION['loggedIn']) && !$_SESSION['loggedIn']) { ?>
                <p class="double-margin-bottom smaller-font"><strong>Hinweis:</strong> Benutzer / Passwort inkorrekt</p>
            <?php } ?>
            <form method="post">
                <div class="input-box">
                    <div class="form-userinput">
                        <label for="username">User: </label>
                        <input type="text" id="username" name="username">
                    </div>
                    <div class="form-userinput">
                        <label for="password">Password: </label>
                        <input type="password" id="password" name="password">
                    </div>
                    <button class="submit-btn" type="submit">Absenden</button>
                </div>
            </form>
        </div>
    <?php } else if (!isset($_GET['mode']) || !in_array($_GET['mode'], ['kategorie', 'quiz', 'user', 'questions'])) { ?>
        <header>
            <h1>Admin-Page</h1>
            <form>
                <button class="header-link" name="logout" id="logout">Logout</button>
            </form>
        </header>
        <div class="content-box">
            <p>Folgende Funktionen stehen Ihnen zur Auswahl: </p>
            <form>
                <ul>
                    <li class="more-margin-bottom">
                        <button class="btn-lnk" name="mode" id="mode-kategorie" value="kategorie">Kategorien anlegen / verändern</button>
                    </li>
                    <li class="more-margin-bottom">
                        <button class="btn-lnk" name="mode" id="mode-quiz" value="quiz">Quiz anlegen / verändern</button>
                    </li>
                    <li class="more-margin-bottom">
                        <button class="btn-lnk" name="mode" id="mode-user" value="user">Admin-User anlegen / verändern</button>
                    </li>
                </ul>
            </form>
        </div>
    <?php } else if ($_GET['mode'] == 'kategorie') { ?>
        <header>
            <h1>Kategorien anlegen / verändern</h1>
            <div>
                <a class="header-link" href="admin.php">Zurück</a>
            </div>
        </header>
        <div class="content-box">
            <?php
            if (!empty($_POST['mode']) && !empty($_POST['category'])) {
                if ($_POST['mode'] == 'insert') {
                    $dbConn->executeQuery(
                        "INSERT INTO Category (Description, fk_superCategoryID) VALUES (:Category, :superCategory)",
                        ['Category' => $_POST['category'], 'superCategory' => $_POST['superCategory']]
                    );
                } else if ($_POST['mode'] == 'update') {
                    $dbConn->executeQuery(
                        "UPDATE Category SET Description = :Category, fk_superCategoryID = :superCategory WHERE pk_CategoryID = :CategoryID",
                        ['Category' => $_POST['category'], 'superCategory' => $_POST['superCategory'], 'CategoryID' => $_POST['id']]
                    );
                } else if ($_POST['mode'] == 'delete') {
                    $dbConn->executeQuery(
                        "DELETE FROM Category WHERE pk_CategoryID = :CategoryID",
                        ['CategoryID' => $_POST['id']]
                    );
                }
            }
            ?>
            <form id="form-cat" action="admin.php?mode=kategorie" method="post" autocomplete="off">
                <div class="input-box">
                    <div class="form-userinput">
                        <label for="choose-kategorie">Kategorie-Suche: </label>
                        <select class="fill-avail-width" id="choose-kategorie">
                            <option value="" selected>Neue Kategorie</option>
                            <?php getCategory(null); ?>
                        </select>
                    </div>
                    <input type="hidden" id="cat_id" name="id">
                    <input type="hidden" id="cat_mode" name="mode" value="insert">
                    <div  class="form-userinput">
                        <label for="category">Kategoriename: </label>
                        <input class="fill-avail-width" type="text" id="category" name="category">
                    </div>
                    <div class="form-userinput">
                        <label for="super_category">Übergeordnete Kategorie: </label>
                        <select class="fill-avail-width" id="super_category" name="superCategory">
                            <option id="pre_super_category" value="" selected>keine</option>
                            <?php getCategory(null); ?>
                        </select>
                    </div>
                    <div class="btn-box more-margin-top">
                        <button class="btn-lnk" type="submit">Absenden</button>
                        <button class="btn-lnk" id="delete-button">Löschen</button>
                    </div>
                </div>
            </form>
        </div>
        <script>
            function getValueOfId(id) {
                for (const option of document.querySelector('#choose-kategorie').querySelectorAll('option')) {
                    if (option.value.split(';')[0] === id) {
                        return option.value;
                    }
                }
                return "";
            }

            let dataCategory = {
                id: document.querySelector('#cat_id'),
                mode: document.querySelector('#cat_mode'),
                category: document.querySelector('#category'),
                superCategory: document.querySelector('#super_category')
            };

            document.querySelector('#choose-kategorie').addEventListener('change', () => {
                let category = document.querySelector('#choose-kategorie').value.split(';');

                if (category[0] === "") {
                    dataCategory.id.value = "";
                    dataCategory.mode.value = "insert";
                    dataCategory.category.value = "";
                    dataCategory.superCategory.querySelectorAll('option').selected = false;
                    dataCategory.superCategory.querySelector('#pre_super_category').selected = true;
                } else {
                    dataCategory.id.value = category[0];
                    dataCategory.mode.value = "update";
                    dataCategory.category.value = category[1];
                    dataCategory.superCategory.querySelectorAll('option').selected = false;
                    let value = getValueOfId(category[2]);
                    if (value !== "") {
                        dataCategory.superCategory.querySelector(`option[value='${value}']`)
                            .selected = true;
                    } else {
                        dataCategory.superCategory.querySelector('#pre_super_category').selected = true;
                    }
                }
            });

            document.querySelector('#delete-button').addEventListener('click', () => {
                dataCategory.mode.value = "delete";
                document.querySelector('#form-cat').submit();
            });
        </script>
    <?php } else if ($_GET['mode'] == 'quiz') { ?>
        <header>
            <h1>Quiz anlegen / verändern</h1>
            <div>
                <a class="header-link" href="admin.php">Zurück</a>
            </div>
        </header>
        <div class="content-box">
            <?php
            if (!empty($_POST['mode']) && !empty($_POST['quizTitle'])) {
                if ($_POST['mode'] == 'insert') {
                    $dbConn->executeQuery(
                        "INSERT INTO Quiz (Title, Description, fk_CategoryID) VALUES (:Title, :Description, :CategoryID)",
                        ['Title' => $_POST['quizTitle'], 'Description' => $_POST['quizDescription'],
                            'CategoryID' => $_POST['category']]
                    );
                } else if ($_POST['mode'] == 'update') {
                    $dbConn->executeQuery(
                        "UPDATE Quiz SET Title = :Title, Description = :Description, 
                modifiedTimestamp = CURRENT_TIMESTAMP, fk_CategoryID = :CategoryID WHERE pk_QuizID = :QuizID",
                        ['Title' => $_POST['quizTitle'], 'Description' => $_POST['quizDescription'],
                            'CategoryID' => $_POST['category'], 'QuizID' => $_POST['id']]
                    );
                } else if ($_POST['mode'] == 'delete') {
                    $dbConn->executeQuery(
                        "DELETE FROM Quiz WHERE pk_QuizID = :QuizID",
                        ['QuizID' => $_POST['id']]
                    );
                }
            }
            ?>
            <form id="form-quiz" action="admin.php?mode=quiz" method="post" autocomplete="off">
                <p class="double-margin-bottom smaller-font"><strong>Hinweis:</strong> Bitte zuerst ein Quiz anlegen und erst danach auf den Fragen-Button klicken!</p>
                <div class="input-box">
                    <div class="form-userinput">
                        <label for="choose-kategorie">Übergeordnete Kategorie: </label>
                        <select class="fill-avail-width" id="choose-kategorie" name="category">
                            <?php getCategory(null); ?>
                        </select>
                    </div>
                    <div class="form-userinput">
                        <label for="choose-quiz">Quiz-Suche: </label>
                        <select class="fill-avail-width" id="choose-quiz">
                            <option id="pre-choose-quiz" value="" selected>Neues Quiz</option>
                            <?php
                            $stmt = $dbConn->executeQuery("SELECT * FROM quiz");
                            foreach ($stmt as $record) {
                                $data = "{$record['pk_QuizID']};{$record['Title']};{$record['Description']};{$record['fk_CategoryID']}";
                                echo "<option value='{$data}'>{$record['Title']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <input type="hidden" id="quiz-id" name="id">
                    <input type="hidden" id="quiz-mode" name="mode" value="insert">
                    <div class="form-userinput">
                        <label for="quiz-title">Quizname: </label>
                        <input class="fill-avail-width" type="text" id="quiz-title" name="quizTitle">
                    </div>
                    <div class="form-userinput">
                        <label for="quiz-description">Quizbeschreibung: </label>
                        <textarea class="fill-avail-width" id="quiz-description" name="quizDescription"></textarea>
                    </div>
                    <div class="btn-box more-margin-top">
                        <button class="btn-lnk" type="submit">Absenden</button>
                        <button class="btn-lnk" id="delete-button">Löschen</button>
                        <button class="btn-lnk" id="edit-questions" type="button" disabled>Fragen</button>
                    </div>
                </div>
            </form>
        </div>
        <script>
            /* JS mode quiz */

            let dataQuiz = {
                id: document.querySelector('#quiz-id'),
                mode: document.querySelector('#quiz-mode'),
                category: document.querySelector('#choose-kategorie'),
                title: document.querySelector('#quiz-title'),
                description: document.querySelector('#quiz-description')
            };

            function updateQuizzes() {
                let catId = document.querySelector('#choose-kategorie').value.split(';')[0];
                document.querySelector('#choose-quiz').querySelectorAll('option').forEach((e) => {
                    e.style.display = "none";
                });
                for (const element of document.querySelector('#choose-quiz')) {
                    if (element.value.split(";")[3] === catId) {
                        element.style.display = "block";
                    }
                }
                document.querySelector('#pre-choose-quiz').style.display = "block";
            }

            window.onload = updateQuizzes;

            document.querySelector('#choose-kategorie').addEventListener('change', updateQuizzes);

            document.querySelector('#choose-quiz').addEventListener('change', (e) => {
                let quiz = e.target.value.split(';');

                if (quiz[0] === "") {
                    dataQuiz.id.value = "";
                    dataQuiz.mode.value = "insert";
                    dataQuiz.title.value = "";
                    dataQuiz.description.value = "";
                    document.querySelector('#edit-questions').disabled = true;
                } else {
                    dataQuiz.id.value = quiz[0];
                    dataQuiz.mode.value = "update";
                    dataQuiz.title.value = quiz[1];
                    dataQuiz.description.value = quiz[2];
                    document.querySelector('#edit-questions').disabled = false;

                }
            });

            document.querySelector('#delete-button').addEventListener('click', () => {
                dataQuiz.mode.value = "delete";
                document.querySelector('#form-quiz').submit();
            });

            document.querySelector('#edit-questions').addEventListener('click', () => {
                document.querySelector('#form-quiz').action = "admin.php?mode=questions";
                document.querySelector('#form-quiz').submit();
            });
        </script>
    <?php } else if ($_GET['mode'] == 'questions') { ?>
        <header>
            <h1>Quiz-Fragen anlegen / verändern</h1>
            <div>
                <a class="header-link" href="admin.php">Zurück</a>
            </div>
        </header>
        <div class="content-box">
            <?php
            if (!empty($_POST['titleNew'])) {
                $dbConn->executeQuery(
                    "INSERT INTO Question (Title, Description, isMultipleChoice, OrderNr, fk_QuizID) 
                    VALUES (:Title, :Description, :isMultipleChoice, :OrderNr, :QuizID)",
                    ['Title' => $_POST['titleNew'], 'Description' => $_POST['descriptionNew'],
                        'isMultipleChoice' => false, 'OrderNr' => $_POST['orderNrNew'], 'QuizID' => $_POST['id']]
                );

                foreach (preg_grep('/^questionNewAns\d+$/', array_keys($_POST)) as $answer) {
                    $dbConn->executeQuery(
                        "INSERT INTO Answer (Text, isCorrect, OrderNr, fk_QuestionID) 
                    VALUES (:Text, :isCorrect, :OrderNr, LASTVAL(Question_ID))",
                        ['Text' => $_POST[$answer], 'isCorrect' => isset($_POST[$answer . 'isCorrect']),
                            'OrderNr' => $_POST[$answer . 'orderNr']]
                    );
                }
            }

            foreach (preg_grep('/^mode\d+$/', array_keys($_POST)) as $mode) {
                if ($_POST[$mode] == 'update') {
                    $questionId = preg_split('/(?<=\D)(?=\d)/', $mode)[1];
                    resetOrderNr($questionId);
                    $dbConn->executeQuery(
                        "UPDATE Question SET Title = :Title, Description = :Description,
                    isMultipleChoice = :isMultipleChoice, OrderNr = :OrderNr WHERE pk_QuestionID = :QuestionID",
                        ['Title' => $_POST['title' . $questionId], 'Description' => $_POST['description' . $questionId],
                            'isMultipleChoice' => false, 'OrderNr' => $_POST['orderNr' . $questionId], 'QuestionID' => $questionId]
                    );
                    foreach (preg_grep('/^' . $questionId . 'titleAns\d+$/', array_keys($_POST)) as $answer) {
                        $answerId = preg_split('/(?<=\D)(?=\d)/', $answer)[1];
                        $dbConn->executeQuery(
                            "UPDATE Answer SET Text = :Text, isCorrect = :isCorrect, OrderNr = :OrderNr
                        WHERE pk_AnswerID = :AnswerID",
                            ['Text' => $_POST[$answer], 'isCorrect' => !empty($_POST[$questionId . 'isCorrectAns' . $answerId]),
                                'OrderNr' => $_POST[$questionId . 'orderNrAns' . $answerId], 'AnswerID' => $answerId]
                        );
                    }
                    foreach (preg_grep('/^' . $questionId . 'titleAnsNew\d+$/', array_keys($_POST)) as $answer) {
                        if (!empty($_POST[$answer])) {
                            $answerNr = preg_split('/(?<=\D)(?=\d)/', $answer)[1];
                            $dbConn->executeQuery(
                                "INSERT INTO Answer (Text, isCorrect, OrderNr, fk_QuestionID) 
                                VALUES (:Text, :isCorrect, :OrderNr, :QuestionID)",
                                ['Text' => $_POST[$answer], 'isCorrect' => isset($_POST[$questionId . 'isCorrectAnsNew' . $answerNr]),
                                    'OrderNr' => $_POST[$questionId . 'orderNrAnsNew' . $answerNr], 'QuestionID' => $questionId]
                            );
                        }
                    }
                } else if ($_POST[$mode] == 'delete') {
                    $questionId = preg_split('/(?<=\D)(?=\d)/', $mode)[1];
                    $dbConn->executeQuery(
                        "DELETE FROM Question WHERE pk_QuestionID = :QuestionID",
                        ['QuestionID' => $questionId]
                    );
                }
            }

            foreach (preg_grep('/^\d+mode\d+$/', array_keys($_POST)) as $modeAns) {
                $questionId = preg_split('/((?<=\D)(?=\d))|(?<=\d)(?=\D)/', $modeAns)[0];
                $answerId = preg_split('/((?<=\D)(?=\d))|(?<=\d)(?=\D)/', $modeAns)[2];
                if ($_POST[$modeAns] == 'delete') {
                    $dbConn->executeQuery(
                        "DELETE FROM Answer WHERE pk_AnswerID = :AnswerID",
                        ['AnswerID' => $answerId]
                    );

                    $stmt = $dbConn->executeQuery(
                        "SELECT pk_AnswerID FROM Answer WHERE fk_QuestionID = :QuestionID ORDER BY OrderNr",
                        ['QuestionID' => $questionId]
                    );

                    $orderNr = 1;
                    foreach ($stmt as $record) {
                        $dbConn->executeQuery(
                            "UPDATE Answer SET OrderNr = :OrderNr WHERE pk_AnswerID = :AnswerID",
                            ['OrderNr' => $orderNr++, 'AnswerID' => $record['pk_AnswerID']]
                        );
                    }
                } else if ($_POST[$modeAns] == 'update') {
                    $dbConn->executeQuery(
                        "UPDATE Answer SET Text = :Text, isCorrect = :isCorrect, OrderNr = :OrderNr
                        WHERE pk_AnswerID = :AnswerID",
                        ['Text' => $_POST[$questionId . 'titleAns' . $answerId], 'isCorrect' => !empty($_POST[$questionId . 'isCorrectAns' . $answerId]),
                            'OrderNr' => $_POST[$questionId . 'orderNrAns' . $answerId], 'AnswerID' => $answerId]
                    );
                }
            }

            ?>
            <form id="form-questions" action="admin.php?mode=questions" method="post" autocomplete="off">
                <?php getQuestions($_POST['id'] ?? 0); ?>
                <div id="new-question" class="question-block">
                    <p class="more-margin-top more-margin-bottom">Neue Frage: </p>
                    <div class="form-userinput more-margin-bottom">
                        <label for='title-new'>Frage: </label>
                        <div class="input-field fill-avail-width">
                            <input class="input-fill-avail" type='text' id='title-new' name='titleNew'>
                            <input type='number' id='orderNr-new' name='orderNrNew' min="1" required>
                        </div>
                    </div>
                    <div class="form-userinput more-margin-bottom">
                        <label for='description-new'>Beschreibung: </label>
                        <textarea class="fill-avail-width" id='description-new' name='descriptionNew'></textarea>
                    </div>
                    <div class="input-field answer" id='question-new-ans-1'>
                        <label for='question-new-ans-1-text'>Antwort: </label>
                        <input class="input-fill-avail" type='text' id='question-new-ans-1-text'
                               name='questionNewAns1'>
                        <input type='number' name='questionNewAns1orderNr' value='1' min="1" required>
                        <input class='align-center' type='checkbox' name='questionNewAns1isCorrect'>
                        <button class='delete-answer' type='button'><img class="svg-btn" src='assets/x.svg' alt='X'></button>
                    </div>
                    <button class='add-answer' type='button'><img class="svg-btn" src='assets/plus.svg' alt='+'></button>
                </div>
                <div class="btn-box double-margin-top">
                    <button class="btn-lnk" type="submit">Absenden</button>
                </div>
            </form>
        </div>
        <script>
            let counterNew = document.querySelectorAll('.question-block').length;
            let answerCounter = {};

            document.querySelector("#orderNr-new").value = counterNew;

            document.querySelector("#new-question").id = `question-${counterNew}`;

            document.querySelectorAll('#form-questions > .question-block').forEach((element) => {
                let qId = element.id.split("-")[1];
                answerCounter[qId] = 0;
                element.querySelectorAll('.answer').forEach(() => {
                    answerCounter[qId]++;
                });
            });

            document.querySelector('#form-questions').addEventListener('click', (e) => {
                if (e.target.classList.contains('add-answer')) {
                    let qNr = e.target.parentNode.id.split('-')[1];
                    let element = document.createElement("div");
                    answerCounter[qNr]++;

                    try {
                        let qId = e.target.parentNode.firstElementChild.id.split("-")[1];
                        element.id = `question-${qNr}-ans-${answerCounter[qNr]}`;
                        element.classList.add("input-field");
                        element.classList.add("answer");
                        element.innerHTML = `
                        <label for='question-${qId}-ans-${answerCounter[qNr]}-text'>Antwort: </label>
                        <input type='text' class='input-fill-avail' id='question-${qId}-ans-${answerCounter[qNr]}' name='${qId}titleAnsNew${answerCounter[qNr]}'>
                        <input type='number' name='${qId}orderNrAnsNew${answerCounter[qNr]}' value='${answerCounter[qNr]}' min='1' required>
                        <input class='align-center' type='checkbox' name='${qId}isCorrectAnsNew${answerCounter[qNr]}'>
                        <button class='delete-answer' type='button'><img class='svg-btn' src='assets/x.svg' alt='X'></button>
                        `;
                        document.querySelector(`#mode-${qId}`).value = 'update';
                        document.querySelector(`#question-${qNr}-ans-${answerCounter[qNr] - 1}`).after(element);
                    } catch (exception) {
                        element.id = `question-new-ans-${answerCounter[qNr]}`;
                        element.classList.add("input-field");
                        element.classList.add("answer");
                        element.innerHTML = `
                        <label for='question-new-ans-${answerCounter[qNr]}-text'>Antwort: </label>
                        <input type='text' class='input-fill-avail' id='question-new-ans-${answerCounter[qNr]}-text' name='questionNewAns${answerCounter[qNr]}'>
                        <input type='number' name='questionNewAns${answerCounter[qNr]}orderNr' value='${answerCounter[qNr]}' min='1' required>
                        <input class='align-center' type='checkbox' name='questionNewAns${answerCounter[qNr]}isCorrect'>
                        <button class='delete-answer' type='button'><img class='svg-btn' src='assets/x.svg' alt='X'></button>
                        `;
                        e.target.parentNode.querySelector('div[id^=question-new-ans-]:last-of-type').after(element);
                    }
                } else if (e.target.classList.contains('delete-answer')) {
                    if (e.target.parentNode.id.match("question-new-ans-.*")) {
                        let ansChildren = 0;
                        e.target.parentNode.parentNode.childNodes.forEach((node) => {
                            if (node.hasChildNodes() && node.id.match('-ans-')) {
                                ansChildren++;
                            }
                        });
                        if (ansChildren > 1) {
                            let question = e.target.parentNode.parentNode;
                            answerCounter[question.id.split('-')[1]]--;
                            question.removeChild(e.target.parentNode);
                            let counter = 1;
                            question.querySelectorAll("input[type=number][name^=questionNewAns]").forEach((num) => {
                                num.value = counter++;
                            });
                            counter = 1;
                            question.querySelectorAll("div[id^=question-new-ans-]").forEach((element) => {
                                element.id = element.id.replace(/(?<=.)\d+$/, "" + counter++);
                            });
                        }
                    } else {
                        try {
                            e.target.parentNode.querySelector('input[type^=hidden]').value = 'delete';
                            document.querySelector('#form-questions').submit();
                        } catch (exception) {
                            let ansChildren = 0;
                            e.target.parentNode.parentNode.childNodes.forEach((node) => {
                                if (node.hasChildNodes() && node.id.match('-ans-')) {
                                    ansChildren++;
                                }
                            });
                            if (ansChildren > 1) {
                                let question = e.target.parentNode.parentNode;
                                answerCounter[question.id.split('-')[1]]--;
                                question.removeChild(e.target.parentNode);
                                let counter = 1;
                                question.querySelectorAll("input[type=number][name*=orderNrAns]").forEach((num) => {
                                    num.value = counter++;
                                });
                                counter = 1;
                                question.querySelectorAll("div[id^=question-]").forEach((element) => {
                                    console.log(element);
                                    element.id = element.id.replace(/(?<=.)\d+$/, "" + counter++);
                                });
                            }
                        }
                    }
                }
            });

            document.querySelectorAll('.question-block').forEach((block) => {
                block.addEventListener('change', (e) => {
                    if (e.target instanceof HTMLTextAreaElement) {
                        e.target.parentNode.parentNode.querySelector('input[id^=mode-]').value = 'update';
                    } else if (e.target.type === 'number') {
                        e.target.parentNode.parentNode.querySelector('input[id^=mode-]').value = 'update';
                    } else {
                        try {
                            e.target.parentNode.querySelector('input[id*=-mode-]').value = 'update';
                        } catch (exception) {
                            try {
                                e.target.parentNode.parentNode.parentNode.querySelector('input[id^=mode-]').value = 'update';
                            } catch (exception) {
                            }
                        }
                    }
                });
            });

            document.querySelectorAll('.delete-question').forEach((block) => {
                block.addEventListener('click', (e) => {
                    document.querySelectorAll('input[id^=mode-]').forEach((element) => {
                        element.value = 'update';
                    });
                    e.target.parentNode.parentNode.parentNode.querySelector('input[id^=mode-]').value = 'delete';
                    let counter = 1;
                    document.querySelectorAll('input[id^=mode-][value=update]+div>div>input[name^=orderNr]').forEach((element) => {
                        element.value = counter++;
                    });
                    document.querySelector('#form-questions').submit();
                });
            });
        </script>
    <?php } else if ($_GET['mode'] == 'user') { ?>
        <header>
            <h1>User anlegen / verändern</h1>
            <div>
                <a class="header-link" href="admin.php">Zurück</a>
            </div>
        </header>
        <div class="content-box">
            <?php
            if (!empty($_POST['mode']) && !empty($_POST['username'])) {
                if ($_POST['mode'] == 'insert') {
                    $dbConn->executeQuery(
                        "INSERT INTO Auth (Username, Password) VALUES (:Username, :Password)",
                        ['Username' => $_POST['username'], 'Password' => password_hash($_POST['password'], PASSWORD_DEFAULT)]
                    );
                } else if ($_POST['mode'] == 'update') {
                    $dbConn->executeQuery(
                        "UPDATE Auth SET Username = :Username, Password = :Password WHERE pk_UserID = :UserID",
                        ['Username' => $_POST['username'], 'Password' =>
                            password_hash($_POST['password'], PASSWORD_DEFAULT), 'UserID' => $_POST['id']]
                    );
                } else if ($_POST['mode'] == 'delete') {
                    $dbConn->executeQuery(
                        "DELETE FROM Auth WHERE pk_UserID = :UserID",
                        ['UserID' => $_POST['id']]
                    );
                }
            }
            ?>
            <form method="post" id="form-user">
                <div class="input-box">
                    <input type="hidden" id="id" name="id">
                    <input type="hidden" id="mode" name="mode" value="insert">
                    <div class="form-userinput">
                        <label for="choose-user">User-Suche: </label>
                        <select class="fill-avail-width" id="choose-user">
                            <option id="pre-choose-user" value="" selected>Neuer User</option>
                            <?php
                            $stmt = $dbConn->executeQuery("SELECT * FROM auth");
                            foreach ($stmt as $record) {
                                $data = "{$record['pk_UserID']};{$record['Username']}";
                                echo "<option value='{$data}'>{$record['Username']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-userinput">
                        <label for="username">User: </label>
                        <input type="text" id="username" name="username" class="fill-avail-width" required>
                    </div>
                    <div class="form-userinput">
                        <label for="password">Password: </label>
                        <input type="password" id="password" name="password" class="fill-avail-width" required>
                    </div>
                    <div class="btn-box more-margin-top">
                        <button class="btn-lnk" type="submit">Absenden</button>
                        <button class="btn-lnk" id="delete-button" type="button">Löschen</button>
                    </div>
                </div>
            </form>
            <script>
                function getValueOfId(id) {
                    for (const option of document.querySelector('#choose-user').querySelectorAll('option')) {
                        if (option.value.split(';')[0] === id) {
                            return option.value;
                        }
                    }
                    return "";
                }

                let data = {
                    id: document.querySelector('#id'),
                    mode: document.querySelector('#mode'),
                    username: document.querySelector('#username'),
                    password: document.querySelector('#password')
                };

                document.querySelector('#choose-user').addEventListener('change', () => {
                    let category = document.querySelector('#choose-user').value.split(';');

                    if (category[0] === "") {
                        data.id.value = "";
                        data.mode.value = "insert";
                        data.username.value = "";
                        data.superCategory.querySelectorAll('option').selected = false;
                        data.superCategory.querySelector('#pre-choose-user').selected = true;
                    } else {
                        data.id.value = category[0];
                        data.mode.value = "update";
                        data.username.value = category[1];
                    }
                });

                document.querySelector('#delete-button').addEventListener('click', () => {
                    data.mode.value = "delete";
                    document.querySelector('#form-user').submit();
                });
            </script>
        </div>
    <?php } ?>
</main>
</body>
</html>

