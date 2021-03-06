let usersItems = document.querySelector('.users__items');
let modal = document.querySelector('.modal'); 
let modalImage = document.querySelector('.modal .modal__inner .modal__image img');
let modalAvatar = document.querySelector('.modal .modal__inner .user__image img');
let modalAvatarLink = document.querySelector('.modal .modal__inner .user__image a');
let modalUsername = document.querySelector('.modal .modal__inner .username');

usersItems.onclick = function(event) {
    let target = event.target;
    if(target.tagName != 'IMG') return;
    showModal(target);
}


$.fn.disableScroll = function() {
    window.oldScrollPos = $(window).scrollTop();

    $(window).on('scroll.scrolldisabler',function ( event ) {
       $(window).scrollTop( window.oldScrollPos );
       event.preventDefault();
    });
};

$.fn.enableScroll = function() {
    $(window).off('scroll.scrolldisabler');
};

function showModal(elem) {
    modal.classList.add('active');
    $("body").disableScroll();
    let dataAttr = elem.getAttribute('data-link');
    let dataUser = elem.getAttribute('data-user');
    let src = elem.getAttribute('src');
    modalImage.src = src;
    modalImage.setAttribute('data-link', dataAttr);

    let avatar;
    $.ajax({ 
            url: '../ajax/ajax_get_users_info.php',
            method: 'POST',
            data: {'dataAttr' : dataUser},
            beforeSend: function() {
            inProgress = true;}
            }).done(function(data){
            data = jQuery.parseJSON(data);
            data = data[0];
            let countsub = data.countfld;
            if(data.countfld == null) {
                    countsub = 0;
            }
            data.avatar == '0' ? avatar = '/resource/noavatar.jpg' : avatar = 'resource/users/' + data.id_user + '/avatar/'+ data.avatar +'';
            modalAvatar.src = avatar;
            modalAvatarLink.href = data.login;
            modalUsername.innerHTML = '<a href=' + data.login + '>' + data.name + ' ' + data.surname + "<p>"+ countsub +" ??????????????????????</p>" + '</a>';
    });


    let commentsBody = document.querySelector('#comments__body');
    commentsBody.addEventListener('click', function(event) {
            let target = event.target;
            if(target.className == 'comment__action delete__comment') {
                    let dataPost = target.getAttribute('data-post');
                            $.ajax({ 
                                    url: '../ajax/ajax_delete_comments.php',
                                    method: 'POST',
                                    data: {'dataPost' : dataPost},
                                    beforeSend: function() {
                                    inProgress = true;}
                                    }).done(function(data){
                                    data = jQuery.parseJSON(data);
                                    target.parentElement.parentElement.parentElement.remove();
                            });
            }
    })
    

    let postReactions = document.querySelector('.post__reactions');
    let counterLikes = document.querySelector('.counter__likes');

    postReactions.onclick = function(event) {
            console.log(event.target);
        if(event.target.closest(".post__reactions-item.like")) {
                $.ajax({ 
                        url: '../ajax/ajax_likes.php',
                        method: 'POST',
                        data: {"dataAttr" : dataAttr,  "showonclick" : 1},
                        beforeSend: function() {
                        inProgress = true;}
                        }).done(function(data){
                        data = jQuery.parseJSON(data);
                        console.log(data);
                        let btnLikeSvg = document.querySelector('.btnLike svg');
                        if(data == false) {
                                btnLikeSvg.setAttribute('data-icon', 'ant-design:heart-filled');
                                btnLikeSvg.style.color = '#0A1465';
                                counterLikes.innerHTML = Number(counterLikes.innerHTML) + 1; 
                        } else {
                                btnLikeSvg.setAttribute('data-icon', 'ant-design:heart-outlined');
                                btnLikeSvg.style.color = '#0A1465';
                                counterLikes.innerHTML = Number(counterLikes.innerHTML) - 1;
                        }
                });
        }
    }

    let loader = '<img src="../img/loader.gif" alt="" class="loader-gif">';

    $.ajax({ 
            url: '../ajax/ajax_show_comments.php',
            method: 'POST',
            data: {"dataAttr" : dataAttr},
            beforeSend: function() {
            inProgress = true;
            $("#comments__body").empty();  
            $("#comments__body").append(loader);      
            }
            }).done(function(data){
                $('.loader-gif').remove();
            data = jQuery.parseJSON(data);
            let postCommentLength = document.querySelector('#post__reactions-count-comments');
            postCommentLength.textContent = data.length;
            if (data.length > 0) {
            $.each(data, function(index, data){
            let postDate = Date.parse(data.date) + 60 * 60 * 1000; 
            let deleteComment = data.id_user == data.me ? '?????????????? ??????????????????????' : ''; 
            let idBtnAction =  data.id_user == data.me ? 'delete__comment' : 'report__comment';
            $("#comments__body").append('<div class="comment"><div class="comment__avatar"> <a href="' + data.login +'"> <img src="resource/users/' + data.id_user + '/avatar/'+ data.avatar +'" alt="" class="user-avatar"> </a> </div><div class="comment__content"> <a href="'+ data.login +'"> <div class="comment__username">' + data.login +'</div> </a> <div class="comment__text">'+ data.text +'</div><div class="subcontent"><time class="timeago" title="">' + "???????????????????????? ?????????????? ??????????????????????: " + jQuery.timeago(postDate) + '</time><div class="comment__action ' + idBtnAction +'"'+ 'data-post="'+data.id+'">'+ deleteComment  + '</div></div></div></div>')
            });
            } else {
                    $('#comments__body').append(`
                    <div class="no-comments">
                                    <p>?????????? ?????? ????????????. ?????????? ?????????? ????????????, ???????????????? ??????????????????????</p>
                                </div>
                    `) 
            }
    });
    $.ajax({ 
            url: '../ajax/ajax_show_usercomment.php',
            method: 'POST',
            data: {"dataAttr" : dataAttr},
            beforeSend: function() { 
                    $("#user__comment").html('');
                    $("#user__date").html('');
            }
            }).done(function(data){
            data = jQuery.parseJSON(data);
            $("#user__comment").append( data[0].text_post );
            $("#user__date").append( data[0].created_at );
            let counterLikes = document.querySelector('.counter__likes');
            counterLikes.innerHTML = data[0].count;
    });

    $.ajax({ 
            url: '../ajax/ajax_likes.php',
            method: 'POST',
            data: {"dataAttr" : dataAttr},
            beforeSend: function() {
            inProgress = true;}
            }).done(function(data){
            data = jQuery.parseJSON(data);
            let btnLikeSvg = document.querySelector('.btnLike svg')
            if(data != false) {
                    btnLikeSvg.setAttribute('data-icon', 'ant-design:heart-filled');
                    btnLikeSvg.style.color = '#0A1465';
            } else {
                    btnLikeSvg.setAttribute('data-icon', 'ant-design:heart-outlined');
                    btnLikeSvg.style.color = '#0A1465';
            }
    });

}


modal.onclick = function(event) {
    let target = event.target;
    let targetClose = event.target.closest(".close-modal");
        if(target.className == 'modal active' || targetClose) {
            modal.classList.remove('active');   
            $("body").enableScroll();
    }
}


$("#comment__write_btn").click(
        function(){
                sendAjaxForm('comments__body', 'ajax_comment', '../ajax/ajax_add_comments.php');
                return false; 
        }
);


function sendAjaxForm(result_form, ajax_form, url) {
        let dataAttr = modalImage.getAttribute('data-link');
        let avatar;
$.ajax({
        url:     url, //url ???????????????? (action_ajax_form.php)
        type:     "POST", //?????????? ????????????????
        dataType: "html", //???????????? ????????????
        data: $("#"+ajax_form).serialize() + '&dataAttr='+dataAttr,
        success: function(response) { //???????????? ???????????????????? ??????????????
                data = $.parseJSON(response);
                if($(".no-comments")) {
                        $(".no-comments").remove();
                }
                console.log(data);
                let deleteComment = data.id_user == data.me ? '?????????????? ??????????????????????' : '????????????????????????'; 
                let idBtnAction =  data.id_user == data.me ? 'delete__comment' : 'report__comment';
                data.avatar == '0' ? avatar = '/resource/noavatar.jpg' : avatar = 'resource/users/' + data.id_user + '/avatar/'+ data.avatar +'';
                $("#comments__body").append('<div class="comment"><div class="comment__avatar"><img src="'+ avatar +'" alt="" class="user-avatar"></div><div class="comment__content"><div class="comment__username">'+ data.login +'</div><div class="comment__text">' + data.comment +'</div><div class="subcontent"><time class="timeago" title="">' +  jQuery.timeago(new Date()) + '</time><div class="comment__action ' + idBtnAction +'"'+ 'data-post="'+data.maxid+'">'+ deleteComment  + '</div></div></div></div>');
                $('#comment-write').val('');
        },
        error: function(response) { // ???????????? ???? ????????????????????
        $('#'+result_form).html('????????????. ???????????? ???? ????????????????????.');
        }
        });
}