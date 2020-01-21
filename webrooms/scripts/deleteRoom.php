<?php

// подключаем необходимые для работы файлы
// 1 - конфигурация
// 2 - набор необходимых функций
require_once("./config.php");
require_once("./baseFunc.php");

// если вдруг по ошибке каких-то данных не хватает
// то прекращаем работу
if (
    !isset ($_POST ["roomName"]) || 
    !isset ($_POST ["fPassword1"]) || 
    !isset ($_POST ["rPassword1"]) ||
    !isset ($_POST ["fPassword2"]) || 
    !isset ($_POST ["rPassword2"]) ||
    !isset ($_POST ["fPassword3"]) || 
    !isset ($_POST ["rPassword3"])
   )
    exit (0);

// получаем эти данные
$roomname = htmlspecialchars($_POST ["roomName"]);
$fpassword1 = htmlspecialchars($_POST ["fPassword1"]);
$rpassword1 = htmlspecialchars($_POST ["rPassword1"]);
$fpassword2 = htmlspecialchars($_POST ["fPassword2"]);
$rpassword2 = htmlspecialchars($_POST ["rPassword2"]);
$fpassword3 = htmlspecialchars($_POST ["fPassword3"]);
$rpassword3 = htmlspecialchars($_POST ["rPassword3"]);

// если хотя бы одна из этих частей не пройдет проверку, скрипт завершит
// выполнение

// проверяем первую пару паролей на эквивалентнось, функции в baseFunc.php
checkPasswords ($fpassword1, $rpassword1, "passwords 1 not match");

// проверяем вторую пару паролей на эквивалентнось
checkPasswords ($fpassword2, $rpassword2, "passwords 2 not match");

// проверяем третью пару паролей на эквивалентнось
checkPasswords ($fpassword3, $rpassword3, "passwords 3 not match");

// проверяем корректность ввода комнаты
checkRoomname ($roomname, "incorrect roomname");

// создаем соединение с базой данных
$mysqli = new mysqli ($host, $user, $password, $database);

// проверяем соединение на ошибки
checkMysqliConnection ($mysqli);

// преобразуем данные
$roomnameHash = hash ("sha256", $mysqli->real_escape_string($roomname));
$password1Hash = hash ("sha256", $mysqli->real_escape_string($fpassword1));
$password2Hash = hash ("sha256", $mysqli->real_escape_string($fpassword2));
$password3Hash = hash ("sha256", $mysqli->real_escape_string($fpassword3));

// выбираем нужную нам комнату, получаем ID комнаты
// и лишь по ID ее удаляем
$querySelect = "
                SELECT * 
                FROM `" . $table . "` 
                WHERE `ROOMNAME` = '" . $roomnameHash . "'
                and `PASSWORD1` = '" . $password1Hash . "'
                and `PASSWORD2` = '" . $password2Hash . "'
                and `PASSWORD3` = '" . $password3Hash . "'
             ;";


$result = $mysqli->query ($querySelect);

// проверяем правильность выполнения запроса SELECT
checkQueryResult ($mysqli, $result, "Error selecting rows from database");

// если комната нашлась и нет никаких "коллизий"
if ($result->num_rows == 1){
    $row = $result->fetch_assoc();
    // удаляем комнату по ее ID ($roomnameHash нужен для удаления папки комнаты)
    // из таблицы удаляется по ID
    // из фс по roomnameHash
    deleteRoom ($mysqli, $row ["ID"], $table, $roomnameHash);
    echo "if there is no err in conn then room deleted <a href = \"../mainpage/index.html\">go to mainpage</a>";
}
else
    echo "some input data is incorrect (check passwords/room name and try again) <a href = \"../mainpage/index.html\">go to mainpage</a>";

?>
