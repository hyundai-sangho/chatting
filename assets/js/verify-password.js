const form = document.querySelector('form');

form.addEventListener('submit', async (event) => {
	event.preventDefault();

	await fetch('php/verify-password.php')
		.then((response) => response.text())
		.then((data) => {
			if (data == '이름, 이메일, 인증 코드 쿠키 삭제 성공') {
				location.href = 'login.php';
			}
		});
});
