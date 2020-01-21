<?php

// вывести сообщение и прекратить работу скрипта
function writeAndExit ($message){
    echo $message;
    die ("");
}

// закрыть соединение и вызвать writeAndExit ($message), которая ...
function writeAndExitConn ($message, $conn){
    $conn->close ();
    writeAndExit ($message);
}

// удалить папки + записи в таблице + вызвать writeAndExitConn ($message, $mysqli), которая ...
function writeAndExitConnWithDelete ($message, $mysqli, $id, $table, $roomnameHash){
   
    if (file_exists ("../rooms/" . $roomnameHash))
        deleteRoom ($mysqli, $id, $table, $roomnameHash);
    else
        deleteRoomError ($mysqli, $id, $table);

    writeAndExitConn ($message, $mysqli);
}

// проверка паролей на правильность
function checkPasswords ($pass1, $pass2, $msg){
    if ($pass1 != $pass2)
        writeAndExit ($msg);
}
// проверка имени комнаты на правильность
function checkRoomname ($roomname, $msg){
    if ($roomname == null || $roomname == "")
        writeAndExit ($msg);
}

// проверка соединения не правильность
function checkMysqliConnection ($mysqli){
    if ($mysqli->connect_error)
        writeAndExit ("Ошибка подключения (" . $mysqli->connect_errno . ") " . $mysqli->connect_error);
}

// проверка на выполнение правильного запроса
function checkQueryResult ($mysqli, $result, $errmsg){
    if (!$result)
        writeAndExitConn ($errmsg, $mysqli);
}

// получение строки произвольного количества символов
function getSalt ($strFrom, $selectCount, $min1, $max1){

    // получаем количество проходов
    $it = rand ($min1, $max1);
    $result = "";

    // узнаем длину строки из которой будет выборка символов
    $len = strlen ($strFrom) - 1;
    
    for ($i = 0; $i < $it; $i++){
        for ($j = 0; $j < $selectCount; $j++)   // в итоге длина result = it * selectCount
            $result .= $strFrom [rand (0, $len)]; // добавляем к строке случайный символ
    }

    return $result;
}

// удаление комнаты только из таблицы
function deleteRoomError ($mysqli, $id, $table){
    $query = "DELETE FROM " . $table . " WHERE ID = '" . $id . "';";
    $mysqli->query ($query);
}

// удаление комнаты полностью
function deleteRoom ($mysqli, $id, $table, $roomnameHash){
    deleteRoomError ($mysqli, $id, $table); // из таблицы
    destroy_dir ("../rooms/" . $roomnameHash); // из фс
}

// рекурсивное удаление папок (stackoverflow)
function destroy_dir($dir) { 
    if (!is_dir($dir) || is_link($dir)) return unlink($dir); 
    foreach (scandir($dir) as $file) {
        if ($file == '.' || $file == '..') continue; 
        if (!destroy_dir($dir . DIRECTORY_SEPARATOR . $file)) { 
            chmod($dir . DIRECTORY_SEPARATOR . $file, 0777); 
            if (!destroy_dir($dir . DIRECTORY_SEPARATOR . $file)) return false; 
        }
    } 
    return rmdir($dir); 
}

?>
