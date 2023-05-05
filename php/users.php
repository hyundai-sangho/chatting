<?php

require_once '../db/Database.php';

// 세션 사용
session_start();

// 디비 객체 생성
$database = new Database();

// users 테이블의 전체 데이터 가져오기
$database->getAllData();

// 디비 커넥션 끊기
$database->connectionClose();
