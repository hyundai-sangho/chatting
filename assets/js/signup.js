// 회원가입 화면의 폼
const form = document.querySelector('.signup form');

// 에러 발생시 나오는 텍스트
const errorText = form.querySelector('.error-txt');

const emailAuthentication = document.querySelector('#emailAuthentication');

const emailAuthenticationTimerText = document.querySelector('#emailAuthenticationTimer');

const signupChatIcon = document.querySelector('#signupChatIcon');

const signupImageFile = document.querySelector('#signupImageFile');

const signupName = document.querySelector('#signupName');
const signupEmail = document.querySelector('#signupEmail');
const signupAuthCode = document.querySelector('#signupAuthCode');
const signupPassword = document.querySelector('#password');

let compressedFile;

let isEmailAuthenticationTimeLeft = true;

// 회원가입 화면에서 사진 선택시 파일이 1MB를 초과하면 alert() 발생 시작 ========================================
signupImageFile.addEventListener('change', async () => {
	// 회원가입 화면에서 업로드한 프로필 사진 이미지 압축하기 시작 ==================================================
	const imageFile = signupImageFile.files[0];

	const splitImageFileArray = imageFile['name'].split('.');
	const imageFileExtension = splitImageFileArray[splitImageFileArray.length - 1];

	// 회원가입 화면에서 업로드한 사진의 파일 타입 jpeg, jpg, png, webp일 때만
	if (imageFileExtension == 'jpeg' || imageFileExtension == 'jpg' || imageFileExtension == 'png' || imageFileExtension == 'webp') {
		// 이미지 최대 사이즈 1MB
		let maxSize = 1024 * 1024 * 1;
		// 이미지 파일 사이즈
		let fileSize = imageFile.size;

		console.log('파일 사이즈: ' + fileSize);

		// 이미지 파일 사이즈가 최대 사이즈보다 크다면 alert() 발생
		if (fileSize > maxSize) {
			alert('첨부파일 사이즈는 1MB 이내로 등록 가능합니다.');

			// 파일 선택된 값은 비워버림
			signupImageFile.value = '';

			// 반환
			return false;
		}

		// 파일 1MB 미만이라면 파일 압축
		else {
			const fileReader = new FileReader();

			fileReader.onload = (base64) => {
				const image = new Image();

				image.src = base64.target.result;

				image.onload = (e) => {
					const canvas = document.createElement(`canvas`);
					const ctx = canvas.getContext(`2d`);

					canvas.width = e.target.width;
					canvas.height = e.target.height;

					ctx.drawImage(e.target, 0, 0);

					// 용량이 줄어든 base64 이미지
					console.log(canvas.toDataURL(`image/jpeg`, 0.5));
					compressedFile = canvas.toDataURL(`image/jpeg`, 0.5);
				};
			};

			fileReader.readAsDataURL(imageFile);
		}
	} else {
		alert('사진의 타입은 jpeg, jpg, png, webp만 가능합니다.');

		signupImageFile.value = '';

		return false;
	}
});

/* // 이메일 인증 코드 오른쪽 부분에 시간 표시 출력
emailAuthenticationTimerText.innerText = '시간'; */

// 인증 시간 타이머 구현
class AuthEmailTimer {
	constructor() {
		// 5분 타이머
		this.timeLeft = 60 * 3;
	}

	start() {
		// 1초마다 timeStart() 실행
		this.timerInterval = setInterval(() => {
			this.timerStart();
		}, 1000);
	}

	timerStart() {
		if (Math.floor(this.timeLeft / 60) > 0) {
			this.minuteAndSecond = Math.floor(this.timeLeft / 60) + '분 ' + (this.timeLeft % 60) + '초'; // 남은 시간 계산
		} else {
			this.minuteAndSecond = (this.timeLeft % 60) + '초'; // 남은 시간 계산
		}

		emailAuthenticationTimerText.style.color = 'red';
		emailAuthenticationTimerText.innerText = this.minuteAndSecond;

		// timLeft의 값이 0이 된다면(타이머 시간이 0초가 된다면)
		// clearInterval로 타이머 작동 중단
		// alert()로 화면에 "인증 시간 초과" 출력
		if (this.timeLeft < 0) {
			this.timerEnd();
		}

		// 1초씩 남은 시간 감소
		this.timeLeft--;
	}

	timerEnd() {
		clearInterval(this.timerInterval);

		// 이메일 인증 코드 세션 변수 제거
		fetch('php/emailAuthCode-sessionDestroy.php');

		emailAuthenticationTimerText.innerText = '만료';

		isEmailAuthenticationTimeLeft = true;

		alert('인증 시간이 만료되었습니다. 다시 인증해주시기 바랍니다.');
	}
}

emailAuthentication.addEventListener('click', async (event) => {
	let regex = new RegExp('[a-z0-9]+@[a-z]+.[a-z]{2,3}');

	// 이메일 인풋창의 값이 이메일 형식이 맞을 때에만 인증 작업 시작
	if (regex.test(signupEmail.value)) {
		if (isEmailAuthenticationTimeLeft) {
			isEmailAuthenticationTimeLeft = false;
			// 인증 시간 타이머 시작
			let AuthTimer = new AuthEmailTimer();
			AuthTimer.start();

			emailAuthenticationTimerText.innerText = '';

			await fetch('php/auth-email.php', {
				method: 'POST',
				body: new FormData(form),
			})
				.then((response) => response.text())
				.then((data) => {
					console.log(data);

					if (data == '메시지를 보낼 수 없습니다.') {
						errorText.innerHTML = data;
						errorText.style.display = 'block';
					} else {
						console.log(data);
					}
				});
		}
	} else {
		alert('이메일을 형식에 맞게 입력하고 인증 버튼을 눌러주세요.');
	}
});

// 회원가입 버튼 클릭 또는 입력창에서 엔터키를 눌렀을 시에 form에 submit 이벤트가 발생하면
form.addEventListener('submit', async (event) => {
	// 이벤트가 발생하는 것을 방지해서 폼 값이 넘어가는 것을 막고
	event.preventDefault();

	let formData = new FormData();
	formData.append('compressedFile', compressedFile);
	formData.append('name', signupName.value);
	formData.append('email', signupEmail.value);
	formData.append('authCode', signupAuthCode.value);
	formData.append('password', signupPassword.value);

	// 비동기로 php/signup.php 파일로 POST로 폼 데이터에 form 값들을 집어넣어 보내버림
	await fetch('php/signup.php', {
		method: 'POST',
		headers: {
			'Content-Type': 'application/json',
		},
		body: JSON.stringify({
			compressedFile: compressedFile,
			name: signupName.value,
			email: signupEmail.value,
			authCode: signupAuthCode.value,
			password: signupPassword.value,
		}),
	})
		.then((response) => response.text())
		.then((data) => {
			console.log(
				JSON.stringify({
					compressedFile: compressedFile,
					name: signupName.value,
					email: signupEmail.value,
					authCode: signupAuthCode.value,
					password: signupPassword.value,
				})
			);
			console.log(data);
			// "성공" 메시지가 날라오면 회원가입이 제대로 됐다는 의미이므로
			// 대화 상대방을 고를 수 있는 users.php로 보내버림
			if (data === '회원가입 성공') {
				location.href = 'users.php';
			} else if (data === '인증코드 입력 시간 3분이 지났습니다.') {
				errorText.innerHTML = '인증코드 입력 시간 3분이 지났습니다.<br>이메일 인증을 다시 해주시길 바랍니다.';
				errorText.style.display = 'block';

				emailAuthentication.innerText = '재인증';
			}
			// "성공"이 아니라면 에러 메시지를 받아와서 화면에 보여줌
			else {
				errorText.innerHTML = data;
				errorText.style.display = 'block';
			}
		});
});
