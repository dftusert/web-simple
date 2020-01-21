<?php

// данные для базы данных
// сервер с которым будет установлено соединение
$host = "127.0.0.1";

// логин (change it!)
$user = "user";

// пароль (change it!)
$password = "password";

// база данных с которой будет взаимодействие
$database = "CHAT";

// и таблица в ней
$table = "CHATTABLE";


// для генерации cookies
// пример вызова: getSalt ($selectFromHere, $selectCount, $saltMin, $saltMax)
// и getSalt ($selectFromHere, $selectCount, $saltMin  + $appendDiap, $saltMax + $appendDiap)
// функция getSalt находится в baseFunc.php
$saltMin = 5;
$saltMax = 8;
$selectFromHere = "06a17b28c3d49e5f";
$selectCount = 1;
$appendDiap = 3;



?>
