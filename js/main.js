let mainPostsWrapper = document.querySelector('.user-posts');

mainPostsWrapper.onclick = function(event) {
    let target = event.target;
    if(target.className == 'more-comments') {
        let postElement = target.closest(".user-post");
        let morePostsElement = target;
        let dataComment = target.getAttribute('data-comment');
        let dataPost = target.getAttribute('data-post');
        let loader = '<img src="../img/loader.gif" alt="" class="loader-gif">';
        morePostsElement.remove();
        $.ajax({ 
            url: '../ajax/ajax_more_comments.php',
            method: 'POST',
            data: {"dataComment" : dataComment, "dataPost" : dataPost},
            beforeSend: function() {
                $(postElement).append(loader);
            inProgress = true;}
            }).done(function(data){
                $('.loader-gif').remove();
            data = jQuery.parseJSON(data);
            if (data.length > 0) {
            $.each(data, function(index, data){
            $(postElement).append(
                `
                <div class="user-comment">
                    <div class="user-comment-photo"> 
                        <a href="${data.login}"> 
                            <img src="${(data.avatar == 0) ? '/resource/noavatar.jpg' :  '/resource/users/' + data.id_user + '/avatar/' + data.avatar}" alt="" >
                        </a> 
                    </div>
                    <div class="comment__content"> 
                    <a href="${data.login}">
                        <div class="user-comment-name">${data.login}</div> 
                    </a> 
                    <div class="user-comment-text">
                        ${data.text}
                    </div>
                    <div class="user-subcontent">
                    
                        <time class="user-timeago" title="">Пользователь оставил комментарий: ${data.date}</time>
                        <div class="user-comment-action"></div>
                            
                    </div>
                </div>
                `
            )
            });
            }
        });
    }

    targetLike = event.target.closest(".user-info-like-count");
    if(targetLike) {
        let dataAttr = targetLike.getAttribute('data-post');
        let counterLikes = targetLike.querySelector('p');
            $.ajax({ 
                    url: '../ajax/ajax_likes.php',
                    method: 'POST',
                    data: {"dataAttr" : dataAttr,  "showonclick" : 1},
                    beforeSend: function() {
                    inProgress = true;}
                    }).done(function(data){
                    data = jQuery.parseJSON(data);
                    if(data == false) {
                        targetLike.style.color = '#080B2E';
                        targetLike.style.background = '#fafafa';
                        counterLikes.innerHTML = Number(counterLikes.innerHTML) + 1; 
                    } else {
                        targetLike.style.color = '#fafafa';
                        targetLike.style.background = '#080B2E';
                        counterLikes.innerHTML = Number(counterLikes.innerHTML) - 1;
                    }
            });
    }
    
}