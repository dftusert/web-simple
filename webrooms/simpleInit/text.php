<?php

// файл скопируется в комнату, вне комнаты он скорее всего совсем работать не будет
// данный файл должен лежать в корне комнаты после ее создания!

if (!isset ($_COOKIE ["FSession"])){
    echo "cookies not set!";
    die ("");
}

// подключаем необходимые для работы файлы
// 1 - конфигурация
// 2 - набор необходимых функций
// работает только в созданной комнате

require_once("../../scripts/config.php");
require_once("../../scripts/baseFunc.php");

// создаем соединение с базой данных
$mysqli = new mysqli ($host, $user, $password, $database);

// получаем roomnameHash из названия директории в которой находимся
$roomnameHash = basename(dirname(__FILE__));

// запрос для проверки доступа к тексту
$querySelect = "
                SELECT `COOKIESET` 
                FROM `" . $table . "` 
                WHERE `ROOMNAME` = '" . $roomnameHash . "'
              ;";

$result = $mysqli->query ($querySelect);
$row = $result->fetch_assoc ();

// если куки неправильные - прекращение работы скрипта
// на всякий случай htmlspecialchars...
if (htmlspecialchars ($_COOKIE ["FSession"]) != $row ["COOKIESET"])
    writeAndExitConn ("cookies not match, go to <a href = \"../../mainpage/index.html\">go to mainpage</a>", $mysqli);

// закрываем соединение
$mysqli->close ();


