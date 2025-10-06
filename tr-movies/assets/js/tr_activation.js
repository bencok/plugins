jQuery(document).ready(function($){

	$(document.body).on('click', 'button[name="tr_movies_activation_bt"]' ,function(){
        var $this = this;
        $(this).text(cnArgs.loading);
		$.post(cnArgs.ajaxurl, { 'action': 'tr_movies_activation_action', 'nonce': cnArgs.nonce, 'txt': $('input[name="tr_movies_activation_text"]').val(), 'function': $('input[name="tr_theme_chsopt"]:checked').val() }, function(html){
            if(html==1){ location.reload(); }else{ $('#tr_movies_activation_bt').text(cnArgs.txt); $('#tr_movies_activation').html(cnArgs.fail); }
		});
    });
    
});