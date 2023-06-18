# INSY-Quiz
## Info
Die Applikation "INSY-Quiz" wurde im Rahmen des INSY-Unterrichts an der [HTL Rennweg](https://htl.rennweg.at) entwickelt. Das Ziel bestand darin, die Programmiersprache PHP im Rahmen eines Projekts zu erlernen und erstmals anzuwenden.

## Installation
### Benötigte Software

 - **PHP-fähiger Webserver** (z.B. Apache2 mit PHP-Modul)
 - **MariaDB-Datenbank** (>= Version 10.3)

### Installationshinweise
Legen Sie alle Dateien und Ordner des Git-Repositories, mit Ausnahme von `db_files`, in einem Ordner auf Ihrem Webserver ab. Damit die Anwendung ordnungsgemäß funktioniert, müssen Sie außerdem die Datenbank `db_files/database.sql` auf Ihrem Datenbankserver installieren. Anschließend können Sie die Datenbankeinstellungen in der Datei `Config.php` konfigurieren.

**Standard-Administrator-Konto:** admin/12345

## Features

-   Hierarchische Kategorien mit unbegrenzten Unterkategorien
-   Blättern durch die Fragen in Seitenansicht (einschließlich Speicherung der Antworten)
-   Detaillierte Auswertung der Fragen mit Punktzahl am Ende
-   Verwendung von Sessions, um das Quiz jederzeit unterbrechen und fortsetzen zu können
 - Admin-Page
	 - Kategorien anlegen / verändern
	 - Quizzes anlegen / verändern
	 - Quiz-Fragen & -Antworten anlegen / verändern
	 - Admin-User anlegen / verändern