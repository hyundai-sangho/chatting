// 채팅 화면의 div 폼
const form = document.querySelector('.typing-area');
// 글 입력창
const textareaField = document.querySelector('.chattingMessage');
// 채팅 div 박스
const chatBox = document.querySelector('.chat-box');

const chatHeaderProfile = document.querySelector('#header');

const messageSendButton = document.querySelector('#messageSendButton');

const pictureImage = document.querySelector('#pictureImage');

const chatImage = document.querySelector('#chatImage');
const outgoingId = document.querySelector('#outgoingId');
const incomingId = document.querySelector('#incomingId');
const chatMessage = document.querySelector('#chatMessage');

pictureImage.addEventListener('click', (event) => {
	event.preventDefault();

	chatImage.click();
});

// chatBox 채팅 오토 스크롤링 기본값 true로 설정
let isChatBoxScrolled = true;

// 이모지 Textarea 생성
let el = $('.chattingMessage').emojioneArea({});

/**
 * php/get-chat.php에 비동기로 데이터 요청
 */
const run = async () => {
	// 사용자 데이터 가져오기 전까지 로딩 이미지 화면에 출력
	chatHeaderProfile.innerHTML = "<div style='margin: 0 auto;'><img style='width: 200px; border-radius: 10px;' src='https://media1.giphy.com/media/KG4PMQ0jyimywxNt8i/giphy.gif?cid=ecf05e47sjwb5je4u2vmqy7ise7yg74au85dbxrbw2uujg5a&ep=v1_gifs_search&rid=giphy.gif&ct=g'></div>";

	await fetch('php/get-chat.php', {
		method: 'POST',
		body: new FormData(form),
	})
		.then((response) => response.text())
		.then((data) => {
			chatBox.innerHTML = data;
		});

	await fetch('php/get-profile.php')
		.then((response) => response.text())
		.then((data) => {
			chatHeaderProfile.innerHTML = data;
		});
};

// 기본 실행 함수
run();

// 마우스가 chatBox div 내에 들어온다면 채팅 오토 스크롤링 방지
chatBox.addEventListener('mouseenter', () => {
	isChatBoxScrolled = false;
});

// 마우스가 chatBox div 내에 들어온다면 채팅 오토 스크롤링 실행
chatBox.addEventListener('mouseleave', () => {
	isChatBoxScrolled = true;
});

// 이미지 압축 내용으로 저장한 blob 데이터 변수
let compressedFile;

let insertChat = async () => {
	if (chatImage.files[0]) {
		// 회원가입 화면에서 업로드한 프로필 사진 이미지 압축하기 시작 ==================================================
		const imageFile = chatImage.files[0];

		const splitImageFileArray = imageFile['name'].split('.');
		const imageFileExtension = splitImageFileArray[splitImageFileArray.length - 1];

		// 회원가입 화면에서 업로드한 사진의 파일 타입 jpeg, jpg, png, webp일 때만
		if (imageFileExtension == 'jpeg' || imageFileExtension == 'jpg' || imageFileExtension == 'png' || imageFileExtension == 'webp') {
			// 이미지 최대 사이즈 1MB
			let maxSize = 1024 * 1024 * 1;
			// 이미지 파일 사이즈
			let fileSize = imageFile.size;

			// 이미지 파일 사이즈가 최대 사이즈보다 크다면 alert() 발생
			if (fileSize > maxSize) {
				alert('첨부파일 사이즈는 1MB 이내로 등록 가능합니다.');

				// 파일 선택된 값은 비워버림
				chatImage.value = '';

				// 반환
				return false;
			}

			// 파일 1MB 미만이라면 파일 압축

			const fileReader = new FileReader();

			fileReader.onload = (base64) => {
				const image = new Image();

				image.src = base64.target.result;

				image.onload = async (e) => {
					const canvas = document.createElement(`canvas`);
					const ctx = canvas.getContext(`2d`);

					canvas.width = e.target.width;
					canvas.height = e.target.height;

					ctx.drawImage(e.target, 0, 0);

					// 용량이 줄어든 base64 이미지
					console.log(canvas.toDataURL(`image/jpeg`, 0.5));
					compressedFile = canvas.toDataURL(`image/jpeg`, 0.5);

					await fetch('php/insert-chat.php', {
						method: 'POST',
						headers: {
							'Content-Type': 'application/json',
						},
						body: JSON.stringify({
							outgoingId: outgoingId.value,
							incomingId: incomingId.value,
							image: compressedFile,
						}),
					})
						.then((response) => response.text())
						.then((data) => {
							console.log(data);

							chatImage.value = '';

							el[0].emojioneArea.setText('');
							el[0].emojioneArea.setFocus();
						});
				};
			};

			fileReader.readAsDataURL(imageFile);
		} else {
			alert('사진의 타입은 jpeg, jpg, png, webp만 가능합니다.');

			chatImage.value = '';

			return false;
		}
	} else {
		// textarea 필드에 값이 없지 않은(있는) 상태에서 메시지 보내기 버튼을 누르면.
		// trim() 함수를 이용해 공백을 제거해주지 않으면 아무것도 안 썼지만 공백이 포함돼
		// 값이 넘어가버린다.
		if (el[0].emojioneArea.getText().trim() !== '') {
			await fetch('php/insert-chat.php', {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
				},
				body: JSON.stringify({
					outgoingId: outgoingId.value,
					incomingId: incomingId.value,
					message: chatMessage.value,
				}),
			})
				.then((response) => response.text())
				.then((data) => {
					el[0].emojioneArea.setText('');
					el[0].emojioneArea.setFocus();

					console.log(data);

					if (data == 'INSERT 쿼리 실패') {
						alert(data);
					}
				});
		}
	}
};

// 채팅 메시지 전송 버튼 누르면 insertChat 함수 실행
// event.preventDefault()를 안 하면 클릭시 폼이 넘어가버림
// 그래서 insertChat() 함수가 실행도 못하고 form이 넘어감.
messageSendButton.addEventListener('click', (event) => {
	event.preventDefault();

	insertChat();
});

let getChatAndProfileData = async () => {
	await fetch('php/get-chat.php', {
		method: 'POST',
		body: new FormData(form),
	})
		.then((response) => response.text())
		.then((data) => {
			chatBox.innerHTML = data;

			if (isChatBoxScrolled) {
				scrollTopBottom();
			}
		});

	await fetch('php/get-profile.php')
		.then((response) => response.text())
		.then((data) => {
			chatHeaderProfile.innerHTML = data;
		});
};

setInterval(async () => {
	await getChatAndProfileData();
}, 500);

/**
 * 채팅 메시지가 화면을 넘어가 스크롤이 되면 자동으로
 * 메시지가 화면에 보이도록 위쪽으로 올라오게 하는 함수
 */
function scrollTopBottom() {
	chatBox.scrollTop = chatBox.scrollHeight;
}

const deleteMessage = async (messageId) => {
	await fetch('php/delete-message.php', {
		method: 'DELETE',
		headers: {
			'Content-Type': 'application/json',
		},
		body: JSON.stringify({
			deleteMessageId: messageId,
		}),
	});
};
