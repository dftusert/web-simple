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

// преобразуем данные для вставки в таблицу 
$roomnameHash = hash ("sha256", $mysqli->real_escape_string($roomname));
$password1Hash = hash ("sha256", $mysqli->real_escape_string($fpassword1));
$password2Hash = hash ("sha256", $mysqli->real_escape_string($fpassword2));
$password3Hash = hash ("sha256", $mysqli->real_escape_string($fpassword3));

// не может быть две комнаты с одинаковым хешем их имени
// чтобы всегда была одна комната, проверяем чтобы она была одна

// 1 - смотрим по таблице
$querySelect = "
                SELECT `ID` 
                FROM `" . $table . "` 
                WHERE `ROOMNAME` = '" . $roomnameHash . "'
              ;";


$result = $mysqli->query ($querySelect);

// проверяем правильность выполнения запроса SELECT
checkQueryResult ($mysqli, $result, "Error selecting rows from database");

// какой-то мусор ...
if ($result->num_rows == 0 && file_exists ("../rooms/" . $roomnameHash))
    destroy_dir ("../rooms/" . $roomnameHash);

// если находим похожие хеши - останавливаем выполнения скрипта
// 2 - если папка с таким же хешем есть - делаем то же потому что
// возможен такой случай:
// в таблице не найдено схожих по хешу комнат, однако папка комнаты существуем
// немного это исправляем это
if($result->num_rows > 0 || file_exists ("../rooms/" . $roomnameHash))
    writeAndExitConn ("Try to set another room name", $mysqli);

$result->close ();

// если проверки пройдены - составляем INSERTзапрос и выполняем
$query = "
          INSERT INTO " . $table . "(
                        `ROOMNAME`,
                        `PASSWORD1`,
                        `PASSWORD2`,
                        `PASSWORD3`,
                        `COOKIESET`
                                    ) 
                VALUES (
                        '" . $roomnameHash . "',
                        '" . $password1Hash . "',
                        '" . $password2Hash . "',
                        '" . $password3Hash . "',
                        '" . $roomnameHash . "'
                       );";

$result = $mysqli->query ($query);

// проверяем INSERT
checkQueryResult ($mysqli, $result, "Error inserting rows to database");

// дальше нужно получить ID только что созданной в таблице комнаты
$result = $mysqli->query ($querySelect);

// проверяем SELECT запрос
checkQueryResult ($mysqli, $result, "Error selecting rows from database");

// получаем строку вместе c ID
$row = $result->fetch_array(MYSQLI_NUM);

$result->close ();

// И обновляем COOKIESET в таблице =
// Произвольное количество символов + ID + Произвольное количество символов + 
// + Несколько раз захешированный хеш комнаты + Произвольное количество символов
$query = "
          UPDATE " . $table . " 
          SET `COOKIESET` = '" . 
                              getSalt ($selectFromHere, $selectCount, $saltMin, $saltMax) . 
                              $row [0] . 
                              getSalt ($selectFromHere, $selectCount, $saltMin, $saltMax) .
                              hash ("sha256", hash ("sha256", hash ("sha256", $roomnameHash))) . 
                              getSalt ($selectFromHere, $selectCount, $saltMin  + $appendDiap, $saltMax + $appendDiap) . 
                            "'
          WHERE `ID` = " . $row [0] . "
        ;";


$result = $mysqli->query ($query);

// проверяем ошибки выполнения запроса
checkQueryResult ($mysqli, $result, "Error updating database rows");

// создаем в папке rooms папки $roomnameHash и $roomnameHash/docs
if (!mkdir ("../rooms/" . $roomnameHash) || !mkdir ("../rooms/" . $roomnameHash . "/docs"))
    writeAndExitConnWithDelete ("Can't create room, something wrong (mkdir error)", $mysqli, $row [0], $table, $roomnameHash);

// ../simpleInit/index.php переносим в ../rooms/$roomnameHash/index.php
if (!copy ("../simpleInit/index.php", "../rooms/" . $roomnameHash . "/index.php"))
    writeAndExitConnWithDelete ("Can't create room, something wrong (copy error)", $mysqli, $row [0], $table, $roomnameHash);

// ../simpleInit/text.php переносим в ../rooms/$roomnameHash/text.php
if (!copy ("../simpleInit/text.php", "../rooms/" . $roomnameHash . "/text.php"))
    writeAndExitConnWithDelete ("Can't create room, something wrong (copy error)", $mysqli, $row [0], $table, $roomnameHash);

// ../simpleInit/docs/index.php переносим в ../rooms/$roomnameHash/docs/index.php
if (!copy ("../simpleInit/docs/index.php", "../rooms/" . $roomnameHash . "/docs/index.php"))
    writeAndExitConnWithDelete ("Can't create room, something wrong (copy error)", $mysqli, $row [0], $table, $roomnameHash);

// закрываем соединение
$mysqli->close();

// выводим сообщение об успешном создании комнаты
echo "new room successfully created.<br /><a href = \"../mainpage/index.html\">go to main page and now enter room</a>";

// Если произойдет ошибка в запросах после INSERT (что-то случится с базой данных), то COOKIESET не будет обновлено и останется
// $roomnameHash, что небезопасно, но работать комната будет (поэтому всегда лучше использовать regenerate cookie в самой комнате)
// Если же не удастся создание папок/файлов комнаты, то данные об этой комнате из таблицы будут удалены 
// (если что-то вдруг не случится с базой данных)
?>
