// 이메일 인증 코드 화면의 폼
const form = document.querySelector('form');

// 이메일 인증 코드 입력 후 에러 발생시 나오는 div 텍스트
const errorText = document.querySelector('.error-txt');

// 이메일 인증 코드 input
const emailAuthCode = document.querySelector('#emailAuthCode');

// 이메일 인증 코드 submit 이벤트 발생시
form.addEventListener('submit', async (event) => {
	// 폼이 넘어가는 것을 방지
	event.preventDefault();

	// 비동기로 user/auth-code.php 호출
	await fetch('user/auth-code.php', {
		method: 'POST',
		headers: {
			'Content-Type': 'application/json',
		},
		body: JSON.stringify({
			emailAuthCode: emailAuthCode.value,
		}),
	})
		.then((response) => response.text())
		.then((data) => {
			if (data == '이메일에서 받은 인증 코드와 다릅니다.') {
				authChatIcon.style.display = 'none';

				errorText.innerHTML = data;
				errorText.style.display = 'block';
			} else if (data == '비밀번호 찾기 인증 성공') {
				location.href = 'verify-password.php';
			}
		});
});
