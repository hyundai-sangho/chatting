<?php

require_once '../db/Database.php';


// login.js에서 보내온 이메일, 패스워드 json 데이터를 디코딩한 후 $json_Array 배열 변수에 저장
$json_array = json_decode(file_get_contents('php://input'), true);

$email = $json_array['loginEmail'];
$password = $json_array['loginPassword'];

// 디비 객체 사용
$database = new Database();

// login 메서드에 $email, $password 값을 보내 로그인 가능 여부를 체크하고
// 가능하면 로그인을 시켜버림
$database->login($email, $password);

// 디비 커넥션 끊기
$database->connectionClose();

?>
