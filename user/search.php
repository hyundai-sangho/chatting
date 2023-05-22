<?php

require_once '../db/Database.php';

// 세션 사용
session_start();

// users.js에서 보내온 검색어 json 데이터를 디코딩한 후 $json_Array 배열 변수에 저장
$json_array = json_decode(file_get_contents('php://input'), true);


// users.php에서 POST로 받은 searchTerm 데이터를 $searchTerm 변수에 저장
$searchTerm = $json_array['searchTerm'];

// 디비 객체 생성
$database = new Database();

// 입력받은 검색어를 토대로 users 테이블의 사용자 데이터를 가져와 화면에 보여줌.
$database->getDataBySearchTerm($searchTerm);

// 디비 커넥션 끊기
$database->connectionClose();

?>
