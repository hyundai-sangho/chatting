// 로그인 창의 폼
const form = document.querySelector('.login form');
// 로그인 버튼
const loginButton = form.querySelector('.button input');
// 에러 발생시 나오는 텍스트
const errorText = form.querySelector('.error-txt');
// 로그인 상태 유지 아이콘
const loginMaintain = document.querySelector('#loginMaintain');
// 로그인 상태 유지 텍스트
const loginStatusMaintain = document.querySelector('#loginStatusMaintain');

const loginChatIcon = document.querySelector('#loginChatIcon');

// 로그인 버튼 클릭 또는 로그인창에서 엔터키를 눌렀을시 submit 이벤트 발생
form.addEventListener('submit', async (event) => {
	// 이벤트가 발생하는 것을 방지
	event.preventDefault();

	await fetch('php/login.php', {
		method: 'POST',
		body: new FormData(form),
	})
		.then((response) => response.text())
		.then((data) => {
			// "성공" 메시지가 날라오면 users.php로 보내버림
			if (data == '성공') {
				location.href = 'users.php';
			}
			// "성공"이 아니라면 에러 메시지를 받아와서 화면에 보여줌
			else {
				// 문제 발생시 로그인 화면의 채팅 아이콘 화면에서 가리기
				loginChatIcon.style.display = 'none';

				errorText.innerHTML = data;
				errorText.style.display = 'block';
			}
		});
});

// 로그인 상태 유지 아이콘 클릭시 php/maintain-login.php로 email 데이터를 POST로 보내 이메일을 쿠키로 저장
loginMaintain.addEventListener('click', async () => {
	loginMaintain.classList.toggle('active');

	// php/loginMaintain.php 파일로 POST로 폼 데이터로 form 값들을 집어넣어 보내버림
	await fetch('php/maintain-login.php', {
		method: 'POST',
		body: new FormData(form),
	});
});

// 로그인 상태 유지 텍스트를 클릭시 php/maintain-login.php로 email 데이터를 POST로 보내 이메일을 쿠키로 저장
loginStatusMaintain.addEventListener('click', async () => {
	loginMaintain.classList.toggle('active');

	// php/loginMaintain.php 파일로 POST로 폼 데이터로 form 값들을 집어넣어 보내버림
	await fetch('php/maintain-login.php', {
		method: 'POST',
		body: new FormData(form),
	});
});
