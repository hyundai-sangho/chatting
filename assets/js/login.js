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

const loginEmail = document.querySelector('#loginEmail');
const loginPassword = document.querySelector('#loginPassword');

// 로그인 버튼 클릭 또는 로그인창에서 엔터키를 눌렀을시 submit 이벤트 발생
form.addEventListener('submit', async (event) => {
	// 이벤트가 발생하는 것을 방지
	event.preventDefault();

	await fetch('user/login.php', {
		method: 'POST',
		headers: {
			'Content-Type': 'application/json',
		},
		body: JSON.stringify({
			loginEmail: loginEmail.value,
			loginPassword: loginPassword.value,
		}),
	})
		.then((response) => response.text())
		.then((data) => {
			// "성공" 메시지가 날라오면 users.php로 보내버림
			if (data == '성공') {
				location.href = 'users.php';
			}
			// "성공"이 아니라면 에러 메시지를 받아와서 화면에 보여줌
			else {
				errorText.innerHTML = data;
				errorText.style.display = 'block';
			}
		});
});

// 로그인 상태 유지 아이콘 클릭시 user/maintain-login.php로 email 데이터를 POST로 보내 이메일을 쿠키로 저장
loginMaintain.addEventListener('click', async () => {
	loginMaintain.classList.toggle('active');

	if (loginEmail.value == '') {
		alert('이메일을 입력해 주세요.');
	} else if (loginPassword.value == '') {
		alert('비밀번호를 입력해 주세요.');
	} else {
		// user/loginMaintain.php 파일로 이메일과 비밀번호를 json 형식으로 보냄
		await fetch('user/maintain-login.php', {
			method: 'POST',
			headers: {
				'Content-Type': 'application/json',
			},
			body: JSON.stringify({
				loginEmail: loginEmail.value,
				loginPassword: loginPassword.value,
			}),
		})
			.then((response) => response.text())
			.then((data) => {
				if (data == '입력하신 이메일과 비밀번호를 다시 확인해 주세요.') {
					alert(data);
				} else {
					console.log(data);
				}
			});
	}
});

// 로그인 상태 유지 텍스트를 클릭시 user/maintain-login.php로 email 데이터를 POST로 보내 이메일을 쿠키로 저장
loginStatusMaintain.addEventListener('click', async () => {
	loginMaintain.classList.toggle('active');

	if (loginEmail.value == '') {
		alert('이메일을 입력해 주세요.');
	} else if (loginPassword.value == '') {
		alert('비밀번호를 입력해 주세요.');
	} else {
		// user/loginMaintain.php 파일로 이메일과 비밀번호를 json 형식으로 보냄
		await fetch('user/maintain-login.php', {
			method: 'POST',
			headers: {
				'Content-Type': 'application/json',
			},
			body: JSON.stringify({
				loginEmail: loginEmail.value,
				loginPassword: loginPassword.value,
			}),
		})
			.then((response) => response.text())
			.then((data) => {
				if (data == '입력하신 이메일과 비밀번호를 다시 확인해 주세요.') {
					alert(data);
				} else {
					console.log(data);
				}
			});
	}
});
