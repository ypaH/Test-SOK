<?php
session_cache_limiter("nocache");
session_cache_expire(30);
session_start();

//Очистка входящих данных
$_POST = filter_input_array(INPUT_POST, $_POST);

//Получение запрошенного адреса
$uri=explode("/",filter_var($_SERVER['REQUEST_URI'], FILTER_SANITIZE_URL));

//Подключение HTML заголовка
include_once ("html/header.php");

//Проверка уровня доступа
if (isset($_SESSION['loginOk'])){
    //Соединение с MySQL
    include_once ("lib/sql_conn.php");

    //Подключение рабочего файла
    include_once ("lib/inside.php");

    $mysql->close();
}else {
    include_once ("lib/login.php");
}

//Подключение HTML футера
include_once ("html/footer.php");

//Delete all errors
unset($_SESSION['errors']);



?>