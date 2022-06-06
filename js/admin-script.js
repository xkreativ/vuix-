let adminHelp = document.querySelector('.admin__help');
        
adminHelp.onclick = function(event) {
        let target = event.target;
        if(target.className == 'admin__help_btn admin__help_btn-delete') {
                let dataDelete = target.getAttribute('data-help');
                        $.ajax({ 
                                url: '../ajax/ajax_delete_help.php',
                                method: 'POST',
                                data: {'dataDelete' : dataDelete},
                                beforeSend: function() {
                                inProgress = true;}
                                }).done(function(data){
                                data = jQuery.parseJSON(data);
                                target.closest(".admin__help_block").remove();
                        });
        }
        if(target.className == 'admin__help_btn admin__help_btn-answer') {
                let dataPost = target.closest('.admin__help_block').getAttribute('data-request');
                let textAnswer = document.querySelector('.admin__help_textarea-request').value;
                console.log(textAnswer);
                $.ajax({ 
                        url: '../ajax/ajax_add_answer.php',
                        method: 'POST',
                        data: {'dataPost' : dataPost, 'textAnswer' : textAnswer},
                        beforeSend: function() {
                        inProgress = true;}
                        }).done(function(data){
                        data = jQuery.parseJSON(data);
                        target.closest(".admin__help_block").remove();
                });
        }
}
