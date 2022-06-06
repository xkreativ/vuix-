$(function(){
    window.addEventListener('click', function(event) {
        let searchResult = document.querySelector('.search_result');
        target = event.target;
        if(target.className != 'who') {
            searchResult.style.opacity = 0;
        }
        if(target.className == 'who') {
            searchResult.style.opacity = 1;
        }
    })
    //Живой поиск
    $('.who').bind("change keyup input click", function() {
        if(this.value.length >= 2){
            $.ajax({ 
                url: '../ajax/ajax_search.php',
                method: 'POST',
                data: {'search-users': this.value},
                beforeSend: function() {
                inProgress = true;}
                }).done(function(data){
                data = jQuery.parseJSON(data);
                $(".search_result").html('');
                if (data.length > 0) {
                $.each(data, function(index, data){
                    $(".search_result").append(`
                    <li> <a href="`+ data.login +`"> `+ data.name + ' ' +  data.surname + ` - `+ data.login +` </a>  </li>
                    `)
                });
                inProgress = false;
                startFrom += 6;
                }});
        }
        if(this.value.length < 2) {
            $(".search_result").html('');
        }
    })
        
    $(".search_result").hover(function(){
        $(".who").blur(); //Убираем фокус с input
    })
        
    //При выборе результата поиска, прячем список и заносим выбранный результат в input
    $(".search_result").on("click", "li", function(){
        s_user = $(this).text();
        //$(".who").val(s_user).attr('disabled', 'disabled'); //деактивируем input, если нужно
        $(".search_result").fadeOut();
    })
    })