<?php
if (!isset($_SESSION['loginOk']) || time()-$_SESSION['loginOk']>1800){
    session_destroy();
    header('Location: /');
}

$mysql=new mysqli("localhost","web_site","dvECgy3KrVdewE39","sok_test");
if ($mysql->connect_errno){
    die("MySQL error: $mysql->connect_errno - $mysql->connect_error<br>");
}

function Select($query){
    global $mysql;
    if (!$result=$mysql->query($query)){
        echo $query."<br>";
        die ($mysql->error);
    }
    return $result->fetch_assoc();
}

function Selects($query){
    global $mysql;
    if (!$result=$mysql->query($query)){
        echo $query."<br>";
        die ($mysql->error);
    }
    return $result;
}

function Insert($query){
    global $mysql;
    if (!$result=$mysql->query($query)){
        echo $query."<br>";
        die ($mysql->error);
    }
}

function Update($query){
    global $mysql;
    if (!$result=$mysql->query($query)){
        echo $query."<br>";
        die ($mysql->error);
    }
}

function Delete($query){
    global $mysql;
    if (!$result=$mysql->query($query)){
        echo $query."<br>";
        die ($mysql->error);
    }
}