// Добавляем класс в кнопку добавления публикации
let publishBtn = document.querySelector('.add-publish-btn');

publishBtn.addEventListener('click', () => {
    publishBtn.classList.toggle('active');
});

var inProgress = false;
var startFrom = 6;

    let btn = $('#more-posts');

        $('#more-posts').click(function() {

        $.ajax({ 
                url: '../ajax/ajax_profile.php',
                method: 'POST',
                data: {"startFrom" : startFrom},
                beforeSend: function() {
                inProgress = true;}
                }).done(function(data){
                data = jQuery.parseJSON(data);
                if (data.length > 0) {
                $.each(data, function(index, data){
                $("#posts__wrapper").append('<div class="user__post">' + '<img src="/resource/users/' + data.id_user + '/posts/' + data.photo + '"' + "data-link='" + data.id_post + "'" + ' alt= "Изображение 1" class="post-image">'+'<div class="user__post-delete" data-delete="'+data.id_post+'">' + '</div>');
                });
                inProgress = false;
                startFrom += 6;
                }});
        });

let moreBtn = document.querySelector('.more');

/*---------------------------- СКРЫТИЕ КНОПКИ ЕСЛИ НЕТ ПОСТОВ ------------------------------------- */
if(moreBtn) {
        moreBtn.addEventListener('click', () => {
                let postCounter = document.querySelector('#post__counter .count');
                let postCount = document.querySelectorAll('.user__post');
        
                if((postCount.length + 6) >= Number(postCounter.textContent)) {
                moreBtn.classList.add('none');
                }
        });
}

let modalBtn = document.querySelector('.btn-edit');
let modalWrapper = document.querySelector('.modal-wrapper');


/* Отображение загружаемого поста */
let postInpPost = document.querySelector('#post-inp');
let downImgPost = document.querySelector('#post-img')

postInpPost.onchange = evt => {
        const [file] = postInpPost.files
        if (file) {
                downImgPost.src = URL.createObjectURL(file)
                console.log('asdfas');
        }
}

/* Открытие блока добавления новой публикации */

let newPostBtn = document.querySelector('.add-publish-btn');

newPostBtn.addEventListener('click', function() {
        let newPostWrapper = document.querySelector('.new-post-wrapper');

        newPostWrapper.classList.toggle('none');
});