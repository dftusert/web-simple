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
    !isset ($_POST ["userName"]) || 
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
$username = htmlspecialchars($_POST ["userName"]);
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

if ($username == null || $username == "")
    $username = "Anonymous";

// создаем соединение с базой данных
$mysqli = new mysqli ($host, $user, $password, $database);

// проверяем соединение на ошибки
checkMysqliConnection ($mysqli);

// преобразуем данные
$roomnameHash = hash ("sha256", $mysqli->real_escape_string($roomname));
$password1Hash = hash ("sha256", $mysqli->real_escape_string($fpassword1));
$password2Hash = hash ("sha256", $mysqli->real_escape_string($fpassword2));
$password3Hash = hash ("sha256", $mysqli->real_escape_string($fpassword3));

// ищем комнату с заданными паролями
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

// если не нашлось строк или нашлось больше одной - завершаем выполнение скрипта
if($result->num_rows == 0 || $result->num_rows > 1)
    writeAndExitConn ("Error logIn room", $mysqli);

// иначе получаем данные строки
$row = $result->fetch_assoc();

// подготавливаем URL для перехода
$roomURL = "../rooms/" . $roomnameHash . "/index.php";

//куки устанавливаютя везде (не только на этой странице) на 1 час

// если куки уже были установлены
if (isset ($_COOKIE ["FSession"]) && $_COOKIE ["FSession"] == $row ["COOKIESET"]){
    // и добавляем текст о входе в файл в комнате text.php
    // text.php будет перемещен из  ./simpleInit/text.php
    file_put_contents("../rooms/" . $roomnameHash . "/text.php",
           "echo \"<span style = 'color: #00FF00'>" 
               . date("Y-m-d H:i:s") .  ", " . $username . ":</span><br />COME_BACK<br />\";\r\n",
               FILE_APPEND | LOCK_EX);

    // может быть и так что куки FName не установлены, тогда
    if (!isset ($_COOKIE ["FName"]))
      setcookie ("FName", $username, time () + 3600, "/");
    
    echo "<a href =\"" . $roomURL .  "\">now go to room</a>";
    // автоматически перенаправляем в комнату если такое действие возможно
    echo "<script type = \"text/javascript\">document.location.href =\"" . $roomURL .  "\";</script>";
}
else{
    // если же все куки не установлены
    // устанавливаем куки
    setcookie ("FSession", $row ["COOKIESET"], time () + 3600, "/");
    setcookie ("FName", $username, time () + 3600, "/");
    // и добавляем текст о входе в файл в комнате text.php
    // text.php будет перемещен из  ./simpleInit/text.php 
    file_put_contents("../rooms/" . $roomnameHash . "/text.php",
           "echo \"<span style = 'color: #00FF00'>" 
               . date("Y-m-d H:i:s") .  ", " . $username . ":</span><br />STATUS_NEWLY_IN<br />\";\r\n",
               FILE_APPEND | LOCK_EX);
    
    echo "<a href =\"" . $roomURL .  "\">now go to room</a>";
    // автоматически перенаправляем в комнату если такое действие возможно
    echo "<script type = \"text/javascript\">document.location.href =\"" . $roomURL .  "\";</script>";
}

?>



