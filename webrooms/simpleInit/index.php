<?php

// файл скопируется в комнату, вне комнаты он скорее всего совсем работать не будет
// данный файл должен лежать в корне комнаты после ее создания!

if (!isset ($_COOKIE ["FSession"])){
    echo "cookies not set! <a href = \"../../mainpage/index.html\">go to mainpage</a>";
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

$roomnameHash = basename(dirname(__FILE__));

// запрос для проверки доступа
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
    writeAndExitConn ("cookies not match,  <a href = \"../../mainpage/index.html\">go to mainpage</a>", $mysqli);

// далее идут действия которые "были переданы с этой страницы"

// добавление нового сообщения в конец файла text.php
// на странице находится iframe который в итоге и отображает text.php...
// а с ним и добавленный таким способом текст...
// используется обычное echo...
// создается также span для каждого сообщения для отображения цветного заголовка...
// можно было придумать и лучше, но так ведь тоже работает...

if (isset ($_POST ["text"])){
    // от xss
    $text = htmlspecialchars ($_POST ["text"]);

    // заменяем новую строку на br для правильного отображения с помощью echo
    $text = str_replace ("\r\n", "<br />", $text);
    $text = str_replace ("\n", "<br />", $text);

    // пользователь может сам исправить cookies на скрипт, поэтому используем
    // htmlspecialchars
    $username = htmlspecialchars ($_COOKIE ["FName"]);
    // если же пользователь их изменил/удалил
    if ($username == null || $username == "")
        $username = "Anonymous";

    // добавляем в text.php нужную информацию с сообщением
    file_put_contents("text.php",
           "echo \"<span style = 'color: #00FF00'>" 
               . date("Y-m-d H:i:s") .  ", " . $username . ":</span><br />" . $text . "<br />\";\r\n",
               FILE_APPEND | LOCK_EX);
}

// команда выхода пользователя из комнаты (очищение cookies)
// передается с этой страницы

if (isset ($_POST ["logout"])){
    // таким образом "очищаем" cookies
    setcookie("FSession", "", time() - 3600, "/");
    setcookie("FName", "", time() - 3600, "/");
    // оповещаемпользователя
    echo "Log out status - good, now <a href = \"../../mainpage/index.html\">go to mainpage</a>";
}

// замена COOKIESET в таблице
// если кто-то зашел в комнату (знает roomnameHash)
// и cookies у него правильные (знает COOKIESET) но он не знает пароли (PASSWORD1....)
// можно переустановить значение COOKIESET что будет означать logout всех пользователей
// и заново войти в комнату и получить уже другие cookies

if (isset ($_POST ["regenerate"])){
    // запрос на получение ID комнаты (лучше * заменить на ID)
    $querySelect = "
                    SELECT * 
                    FROM `" . $table . "` 
                    WHERE `ROOMNAME` = '" . $roomnameHash . "'
                 ;";

    // проверка правильности выполнения запроса
    $result = $mysqli->query ($querySelect);
    checkQueryResult ($mysqli, $result, "Error selecting rows from database");

    $row = $result->fetch_assoc();

    // запрос на обновление COOKIESET
    $query = "
          UPDATE " . $table . " 
          SET `COOKIESET` = '" . 
                              getSalt ($selectFromHere, $selectCount, $saltMin, $saltMax) . 
                              $row ["ID"] . 
                              getSalt ($selectFromHere, $selectCount, $saltMin, $saltMax) .
                              hash ("sha256", hash ("sha256", hash ("sha256", $roomnameHash))) . 
                              getSalt ($selectFromHere, $selectCount, $saltMin  + $appendDiap, $saltMax + $appendDiap) . 
                            "'
          WHERE `ID` = " . $row ["ID"] . "
        ;";

     // проверка правильности выполнения запроса
     $result = $mysqli->query ($query);
     checkQueryResult ($mysqli, $result, "Error updating cookies");
     
     // вывод сообщения
     echo "cookies regenerated! <a href = \"../../mainpage/index.html\">go to mainpage to log in this room</a>";
}

// смена паролей без logout пользователей
// будет действовать для "новых пользователей, желающих войти в комнату"
// или после перезахода пользователя в комнату
// перезапишутся все 3 пароля!
// поэтому если нужно оставить какой-либо пароль, то его тоже нужно ввести
// в соответствующем месте
// передается с этой страницы

if (
    isset ($_POST ["fPassword1"]) && 
    isset ($_POST ["rPassword1"]) &&
    isset ($_POST ["fPassword2"]) && 
    isset ($_POST ["rPassword2"]) &&
    isset ($_POST ["fPassword3"]) && 
    isset ($_POST ["rPassword3"])
    ){
    
    // получение паролей
    $fpassword1 = htmlspecialchars($_POST ["fPassword1"]);
    $rpassword1 = htmlspecialchars($_POST ["rPassword1"]);
    $fpassword2 = htmlspecialchars($_POST ["fPassword2"]);
    $rpassword2 = htmlspecialchars($_POST ["rPassword2"]);
    $fpassword3 = htmlspecialchars($_POST ["fPassword3"]);
    $rpassword3 = htmlspecialchars($_POST ["rPassword3"]);

    // проверяем первую пару паролей на эквивалентнось, функции в baseFunc.php
    checkPasswords ($fpassword1, $rpassword1, "passwords 1 not match");

    // проверяем вторую пару паролей на эквивалентнось
    checkPasswords ($fpassword2, $rpassword2, "passwords 2 not match");

    // проверяем третью пару паролей на эквивалентнось
    checkPasswords ($fpassword3, $rpassword3, "passwords 3 not match");

    // получение их хешей
    $password1Hash = hash ("sha256", $mysqli->real_escape_string($fpassword1));
    $password2Hash = hash ("sha256", $mysqli->real_escape_string($fpassword2));
    $password3Hash = hash ("sha256", $mysqli->real_escape_string($fpassword3));

    // запрос на обновление паролей
    $query = "
          UPDATE " . $table . " 
          SET PASSWORD1 = '" . $password1Hash . "',
              PASSWORD2 = '" . $password2Hash . "',
              PASSWORD3 = '" . $password3Hash . "' 
          WHERE ROOMNAME = '" . $roomnameHash . "'
        ;";

    // проверка правильности выполнения запроса
    $result = $mysqli->query ($query);
    checkQueryResult ($mysqli, $result, "Error updating passwords");

    // вывод соотв. сообщения
    echo "passwords reset!";
}

// закрываем соединение
$mysqli->close ();


// загрузка zip файла в ./docs/<имя_файла>.zip
// передается с этой страницы

if (isset ($_FILES ["zipfile"]["name"])){
    // если произошла ошибка
    if($_FILES["zipfile"]["error"]){
        echo "Error: " . $_FILES["zipfile"]["name"];
        die ("");
    }
    // сначала предполагалось решать задач немного иначе
    //$accepted_types = array("application/zip", "application/x-zip-compressed", "multipart/x-zip", "application/x-compressed");
    $filename = $_FILES ["zipfile"]["name"];

    // проверка что файл zip-файл
    if (pathinfo ($filename, PATHINFO_EXTENSION) != "zip"){
        echo "file is not a zip file!";
        die ("");
    }

    // перемещение в ./docs/<имя_файла>.zip
    if (move_uploaded_file($_FILES["zipfile"]["tmp_name"], "docs/" . $filename))
        echo "file uploaded, link: (relative path) ./docs/" . $filename . "<br />";
    else
        echo "some error happens: (might be too big size of file)";

}

// удаление файла из docs
// пользователь должен указать имя файла без ./docs/
// автоматически просматривается docs
// передается с этой страницы

if (isset ($_POST ["rmfile"])){
    if (file_exists ("docs/" . htmlspecialchars ($_POST ["rmfile"])) && unlink ("docs/" . htmlspecialchars ($_POST ["rmfile"])))
        echo "file deleted";
    else
        echo "error deleting file";
}

// удаление переписки (text.php) полностью и перемещение файла text.php из ../../simpleInit/text.php
// как в createRoom.php
// передается с этой страницы

if (isset ($_POST ["delText"])){
    if (file_exists ("text.php") && unlink ("text.php") && copy ("../../simpleInit/text.php", "text.php"))
        echo "text deleted";
    else
        echo "error deleting text";
}
?>

<html>
    <head>
        <meta charset = "utf-8" />
        <!-- стили для дополнительных действий -->
        <style>
            .hidden {
                display : none;
            }
            .visible {
                display : block;
            }
        </style>
        <!-- открытие/закрытие ,блока доп. действий -->
        <script type = "text/javascript">
            function showHide (){
                var div = document.getElementById ("settings");
                if (div.className == "hidden")
                    div.className = "visible";
                else
                    div.className = "hidden";
            }
        </script>
    </head>
    <body>
        <!-- отображение файла переписки -->
        <iframe src = "text.php" style = "width:100%; height:75%;"></iframe>
        <div style = "width:100%">
            <!-- добавление нового сообщения в переписку -->
            <form method = "POST" action = "index.php">
                <textarea name = "text" style = "width:100%"></textarea><br />
                <input type = "submit" style = "width:100%" value = "send message" />
            </form>
            <br />
            <!-- обновление переписки (просто обычное обновление страницы) -->
            <form method = "POST" action = "index.php">
                <input type = "submit" style = "width:100%" value = "update chat" />
            </form>
            <br />
            <!-- загрузка zip-файла -->
            <form action="index.php" method="POST" enctype="multipart/form-data">
	            Zip:<br />
                <input type = "file" name = "zipfile" style = "width:100%" />
	            <input type = "submit" name = "submit" value = "upload zip" style = "width:100%" />
            </form>
            <br />
            <!-- удаление загруженного файла -->
            <form action="index.php" method="POST">
                <input type = "text" name = "rmfile"
                       placeholder = "input relative path (without docs) to file in docs to delete file" style = "width:100%" />
	            <input type = "submit" name = "submit" value = "delete file" style = "width:100%" />
            </form>
            <br />
            <!-- удаление файла переписки -->
            <form action="index.php" method="POST">
                <input type = "text" name = "delText" style = "display:none" />
                <input type = "submit" name = "submit" value = "delete all text" style = "width:100%" />
            </form>
            <br />
            <!-- показать/скрыть доп. действия -->
            <input type = "button" style = "width:100%" value = "show/hide settings" onclick = "showHide ()"/>
            <p></p>
            <div class = "hidden" id = "settings">
               <!-- выйти из переписки -->
                <form method = "POST" action = "index.php">
                    <input type = "text" name = "logout" style = "display:none" />
                    <input type = "submit" value = "logout" />
                </form>
                <p></p>
                <!-- перезаписать COOKIESET в таблице, logout всех -->
                <form method = "POST" action = "index.php">
                    <input type = "text" name = "regenerate" style = "display:none" />
                    <input type = "submit" value = "regenerate cookie" />
                </form>
                <p></p>
                <!-- перезаписать пароли (если какой-то прошлый пароль нужен - вписать его тоже) -->
                <form method = "POST" action = "index.php">
                    <input type = "password" name = "fPassword1" placeholder = "room password 1" /><br />
                    <input type = "password" name = "rPassword1" placeholder = "repeat room password 1" /><br />
                    <input type = "password" name = "fPassword2" placeholder = "room password 2" /><br />
                    <input type = "password" name = "rPassword2" placeholder = "repeat room password 2" /><br />
                    <input type = "password" name = "fPassword3" placeholder = "room password 3" /><br />
                    <input type = "password" name = "rPassword3" placeholder = "room password 3" />
                    <p></p>
                    <input type = "submit" value = "set new passwords" />
                </form>
                <!-- удалить комнату (ссылка на mainpage) -->
                <span><a href = "../../mainpage/index.html">delete room</a><span>
            </div>
        </div>
    </body>
</html>
