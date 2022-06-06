$(function(){
    let adminUsers = document.querySelector('.admin__users');
    //Живой поиск
    $('.inp-search-user').bind("change keyup input click", function() {
        adminUsers.classList.add('none')
        if(this.value.length >= 2){
            $.ajax({ 
                url: '../ajax/ajax_admin_search.php',
                method: 'POST',
                data: {'search-users': this.value},
                beforeSend: function() {
                inProgress = true;}
                }).done(function(data){
                data = jQuery.parseJSON(data);
                $(".admin__users-search").html('');
                if (data.length > 0) {
                $.each(data, function(index, data){
                    let avatar = data.avatar == '0' ? '/resource/noavatar.jpg' : 'resource/users/' + data.id_user  + '/avatar/' + data.avatar;
                    $(".admin__users-search").append(`
                    <div class="admin__user-preview">
                        <div class="admin__user-preview_image">
                            <a href="`+ data.login +`">
                                <img src="`+ avatar +`">
                            </a>
                        </div>
                        <div class="admin__user-preview_info">
                            <a href="`+ data.login +`">
                                <div class="admin__user-preview_name">` + data.name +  ' ' + data.surname + `</div>
                            </a>
                            <a href="`+ data.login +`">    
                                <div class="admin__user-preview_usernick">`+ data.login +`</div>
                            </a>
                        </div>
                    </div>
                    `)
                });
                }});
        }
        if(this.value.length < 2) {
            adminUsers.classList.remove('none')
            $(".admin__users-search").html('');
        }
    })
        
    $(".admin__users-search").hover(function(){
        $(".inp-search-user").blur(); //Убираем фокус с input
    })
        
    //При выборе результата поиска, прячем список и заносим выбранный результат в input
    $(".admin__users-search").on("click", "li", function(){
        s_user = $(this).text();
        //$(".inp-search-user").val(s_user).attr('disabled', 'disabled'); //деактивируем input, если нужно
        $(".admin__users-search").fadeOut();
    })
    })

let searchBox = document.querySelector('.search__iconbox_icon');
let searchInp = document.querySelector('#admin__search_user');

searchBox.onclick = function() {
    searchInp.classList.toggle('none');
    document.querySelector('.subtitle__search').classList.toggle('none');
}