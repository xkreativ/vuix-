let imgInp = document.querySelector('#imgInp');
let downImg = document.querySelector('#downImg')

imgInp.onchange = evt => {
        const [file] = imgInp.files
        if (file) {
          downImg.src = URL.createObjectURL(file)
        }
}

// let deleteAvatarBtn = document.querySelector('.delete-avatar');

// deleteAvatarBtn.addEventListener('click', function() {
//   downImg.src = '/resource/noavatar.jpg';
// });