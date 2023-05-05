<?php

// 세션 사용
session_start();

require_once '../db/Database.php';

$name = $_POST['name'];
$email = $_POST['email'];
$password = $_POST['password'];

// 디비 객체 생성
$database = new Database();
$database->signUp($name, $email, $password);

// 디비 커넥션 끊기
$database->connectionClose();
