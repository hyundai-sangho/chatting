// 프로필 수정 화면 폼
const form = document.querySelector('form');

// 에러 발생시 나오는 텍스트
const errorText = form.querySelector('.error-txt');

const editUserChatIcon = document.querySelector('#editUserChatIcon');

const editProfileName = document.querySelector('#editProfileName');
const editProfileEmail = document.querySelector('#editProfileEmail');
const editProfilePassword = document.querySelector('#editProfilePassword');
const editProfileImage = document.querySelector('#editProfileImage');

let compressedFile;

// 프로필 수정 화면에서 사진 선택시 파일이 1MB를 초과하면 alert() 발생 시작 ========================================
editProfileImage.addEventListener('change', async () => {
	// 회원가입 화면에서 업로드한 프로필 사진 이미지 압축하기 시작 ==================================================
	const imageFile = editProfileImage.files[0];

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
			editProfileImage.value = '';

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

// 변경 완료 버튼 클릭 또는 입력창에서 엔터키를 눌렀을 시에 form에 submit 이벤트가 발생하면
form.addEventListener('submit', async (event) => {
	// 이벤트가 발생하는 것을 방지해서 폼 값이 넘어가는 것을 막고
	event.preventDefault();

	// 비동기로
	// user/signup.php 파일로 POST로 폼 데이터에 form 값들을 집어넣어 보내버림
	await fetch('user/edit-profile.php', {
		method: 'PUT',
		headers: {
			'Content-Type': 'application/json',
		},
		body: JSON.stringify({
			name: editProfileName.value,
			email: editProfileEmail.value,
			password: editProfilePassword.value,
			image: compressedFile,
		}),
	})
		.then((response) => response.text())
		.then((data) => {
			// "프로필 수정 성공" 메시지가 날라오면 수정이 제대로 됐다는 의미이므로
			// 대화 상대방을 고를 수 있는 users.php로 보내버림
			if (data === '프로필 수정 성공') {
				location.href = 'users.php';
			}

			// 프로필 수정 화면에서 아무것도 안 바꾸고 그대로 변경 완료 버튼을 눌렀다면
			// '이름, 이메일, 비밀번호 모두 변경된 값이 없습니다.' 화면에 출력한 뒤
			// users.php로 보내버림
			else if (data === '이름, 이메일, 비밀번호 모두 변경된 값이 없습니다.') {
				errorText.textContent = data;
				errorText.style.display = 'block';

				setTimeout(() => {
					location.href = 'users.php';
				}, 3000);
			}
			// "성공"이 아니라면 에러 메시지를 받아와서 화면에 보여줌
			else {
				errorText.textContent = data;
				errorText.style.display = 'block';
			}
		});
});
