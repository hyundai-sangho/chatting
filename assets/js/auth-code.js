const form = document.querySelector('form');
const errorText = document.querySelector('.error-txt');

form.addEventListener('submit', async (event) => {
	event.preventDefault();

	await fetch('php/auth-code.php', {
		method: 'POST',
		body: new FormData(form),
	})
		.then((response) => response.text())
		.then((data) => {
			if (data == '이메일에서 받은 인증 코드와 다릅니다.') {
				authChatIcon.style.display = 'none';

				errorText.innerHTML = data;
				errorText.style.display = 'block';
			} else {
				location.href = 'verify-password.php';
			}
		});
});
