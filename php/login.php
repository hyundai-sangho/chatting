<?php

require_once '../db/Database.php';

// POST로 받은 email 데이터를 $email 변수에 저장
$email = $_POST['email'];

// POST로 받은 password 데이터를 $password 변수에 저장
$password = $_POST['password'];

// 디비 객체 사용
$database = new Database();

// login 메서드에 $email, $password 값을 보내 로그인 가능 여부를 체크하고
// 가능하면 로그인을 시켜버림
$database->login($email, $password);

// 디비 커넥션 끊기
$database->connectionClose();

?>
