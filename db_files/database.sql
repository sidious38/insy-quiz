--
-- Quiz-Datenbank
-- @author Bernd Reither 3CI
--
DROP DATABASE IF EXISTS quiz;
CREATE DATABASE IF NOT EXISTS quiz;
USE quiz;

CREATE SEQUENCE Category_ID START WITH 1 INCREMENT BY 1 NOCACHE;
CREATE SEQUENCE Quiz_ID START WITH 1 INCREMENT BY 1 NOCACHE;
CREATE SEQUENCE Question_ID START WITH 1 INCREMENT BY 1 NOCACHE;
CREATE SEQUENCE Answer_ID START WITH 1 INCREMENT BY 1 NOCACHE;
CREATE SEQUENCE Auth_ID START WITH 1 INCREMENT BY 1 NOCACHE;

CREATE TABLE Category
(
    pk_CategoryID INTEGER PRIMARY KEY DEFAULT NEXTVAL(Category_ID),
    Description VARCHAR(255) NOT NULL UNIQUE,
    fk_superCategoryID INTEGER
);

CREATE TABLE Quiz
(
    pk_QuizID INTEGER PRIMARY KEY DEFAULT NEXTVAL(Quiz_ID),
    Title VARCHAR(255) NOT NULL UNIQUE,
    Description VARCHAR(255),
    createdTimestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    modifiedTimestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fk_CategoryID INTEGER NOT NULL

);

CREATE TABLE Question
(
    pk_QuestionID INTEGER PRIMARY KEY DEFAULT NEXTVAL(Question_ID),
    Title VARCHAR(255) NOT NULL,
    Description VARCHAR(1000),
    isMultipleChoice BOOLEAN NOT NULL,
    OrderNr INTEGER,
    fk_QuizID INTEGER NOT NULL,
    UNIQUE (fk_QuizID, OrderNr)
);

CREATE TABLE Answer
(
    pk_AnswerID INTEGER PRIMARY KEY DEFAULT NEXTVAL(Answer_ID),
    Text VARCHAR(1000) NOT NULL,
    isCorrect BOOLEAN NOT NULL,
    OrderNr INTEGER,
    fk_QuestionID INTEGER NOT NULL,
    UNIQUE (fk_QuestionID, OrderNr)
);

CREATE TABLE Auth
(
    pk_UserID INTEGER PRIMARY KEY DEFAULT NEXTVAL(Auth_ID),
    Username VARCHAR(255) UNIQUE,
    Password VARCHAR(255)
);

ALTER TABLE Category ADD FOREIGN KEY (fk_superCategoryID) REFERENCES Category (pk_CategoryID) ON DELETE CASCADE;
ALTER TABLE Quiz ADD FOREIGN KEY (fk_CategoryID) REFERENCES Category (pk_CategoryID) ON DELETE CASCADE;
ALTER TABLE Question ADD FOREIGN KEY (fk_QuizID) REFERENCES Quiz (pk_QuizID) ON DELETE CASCADE;
ALTER TABLE Answer ADD FOREIGN KEY (fk_QuestionID) REFERENCES Question (pk_QuestionID) ON DELETE CASCADE;

--
-- Testdaten
--

INSERT INTO Category (Description, fk_superCategoryID) VALUES ('NWT', null);
INSERT INTO Category (Description, fk_superCategoryID) VALUES ('SYT-BS', null);
INSERT INTO Category (Description, fk_superCategoryID) VALUES ('Linux Bash', 2);
INSERT INTO Quiz (Title, Description, fk_CategoryID)
    VALUES ('ACL Quiz', 'Ein Quiz über Access Control Lists auf Cisco Routern', 1);
INSERT INTO Question (Title, isMultipleChoice, OrderNr, fk_QuizID)
    VALUES ('Mit welchem Befehl wird eine neue NAMED ACL erstellt?', false, 1, 1);
INSERT INTO Answer (Text, isCorrect, OrderNr, fk_QuestionID)
    VALUES ('ip acl create NAME', false, 1, LASTVAL(Question_ID));
INSERT INTO Answer (Text, isCorrect, OrderNr, fk_QuestionID)
    VALUES ('ip access-list extended NAME', true, 2, LASTVAL(Question_ID));

INSERT INTO Auth (Username, Password) VALUES ('admin','$2y$10$f1EU11Y9owsXPMT8NM0MFeJLG0Y4Cke4uA4lu/M8SdAcUzz.YGIUu');
