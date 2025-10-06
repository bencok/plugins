jQuery(document).ready(function($) {
        
    $( ".tr_tab" ).click(function() {

        var tab = $( this ).data( "tab" ); 
        $('.tr_tab').removeClass('current');
        $(this).addClass('current');
        $('.tr_movies_tab').hide();
        $('#'+tab).show();
        
    });
    
    $( ".tr-config-tab button" ).click(function() {

        var tab = $( this ).data( "tab" ); 
        
        $('.tr-config-tab li').removeClass('Current');
        $(this).parent().addClass('Current');
        
        $('.tr-config-1,.tr-config-2').hide();
        $('.tr-config-'+tab).show();
        
    });
    
  // add links
    $("body").on("click", "#trmovies_addlink", function(e){
        e.preventDefault();
        
        $( ".tr-movies-row:first" ).clone().insertBefore( ".tr-movies-row:first" );
        $( ".tr-movies-row:first select option" ).prop("selected", false);
        $( ".tr-movies-row:first input" ).val('');
        var today = new Date();
        var month = today.getMonth()+1;
        $( ".tr-movies-row:first input[name=\"trmovies_date[]\"]" ).val(today.getDate()+'/'+month+'/'+today.getFullYear());
    });

    // delete links
    $("body").on("click", "#trmovies_removelink", function(e){
        e.preventDefault();
        
        var num = $('.tr-movies-row').length;
        var id = $( this ).data( "id" );
                
        if(num>1){
            $(this).parent().parent().remove();
        }else{
            $( ".tr-movies-row:first select option" ).prop("selected", false);
            $( ".tr-movies-row:first input" ).val('');
        }
        
        if(id!=''){
            
            $.post( TrMovies.ajaxurl, { action: "tr_movieslinks_action", 'nonce': TrMovies.nonce_links, 'post_id': TrMovies.post_id, 'type': 2 })
              .done(function( data ) {
                $('.trmovieswarning').remove();
                $('.ToroPlay-tblcn').before(data);
            });
            
        }
        
    });
    
    // generate links
    $("body").on("click", "#trmovies_generate", function(e){
        e.preventDefault();
        
        $('.trmoviesnotsupport').hide();
        
        $('#wp-content-media-buttons').prepend('<div class="loadingtrmovies">'+TrMovies.loading+'</div>');
        
        var url = $('input[name=url_generate]').val();
        
        if(url==''){
            $('.loadingtrmovies').remove();
            $('.trmoviesnotsupport').show();
        }else{
            
            $.post( TrMovies.ajaxurl, { action: "tr_movieslinks_action", url: url, 'nonce': TrMovies.nonce_links, 'post_id': TrMovies.post_id })
              .done(function( data ) {
                $('.loadingtrmovies').remove();
                $('input[name=url_generate]').val('');
                if(data==1){
                    $('.trmoviesnotsupport').show();                    
                }else{
                    $('.ToroPlay-tbl tbody').html(data);
                }
            });
            
        }
    });
    
    $(document.body).on('click', '.Season .AALink' ,function(event){
        $(this).toggleClass('on');
        $('.AALink').not($(this)).removeClass('on');
    });
    
    $('.select-all,#select-all').click(function(event) {
        //var id = $( this ).data( "id" );
        if ($('input[type=checkbox]').prop('checked')) {
            $( 'input[type=checkbox]' ).prop( "checked", true );
        }else{
            $( 'input[type=checkbox]' ).prop( "checked", false );
        }
    });
    
    // Move links
    
    $(document.body).on('click', '.tr-move-up,.tr-move-down' ,function(event){
        event.preventDefault();
        var row = $(this).parents("tr:first");
        if ($(this).is(".tr-move-up")) {
            row.insertBefore(row.prev());
        } else if ($(this).is(".tr-move-down")) {
            row.insertAfter(row.next());
        }
    });
    
    // seasons tabs
    
    $('#tr_movies_tab_4 .AACrdn').find('.AALink').click(function(){
        $(this).toggleClass('on');
        $('.AALink').not($(this)).removeClass('on');
    });
    
    $(document.body).on('click', '.tr-season-tab .AATbNv li' ,function(event){
        
        var current_tab = $('.tr-season-tab li.on').data("tab");
        
        $('#'+current_tab).html(TrMovies.loading);
        
        var tab = $( this ).data( "tab" );
        $('.tr-season-tab .AATbNv li').removeClass('on');
        $(this).addClass('on');
        
        $.post( TrMovies.ajaxurl, { action: "trmovieslinks", 'nonce': TrMovies.nonce_links, 'id': $('input[name="tr_post_id_links"]').val(), 'season': $(this).text() })
          .done(function( data ) {
            $('#'+tab).html(data);

            $('.tr-season-tab .AATbCn ').removeClass('on');
            $('#'+tab).addClass('on');
        });
                
    });
    
    // save links episodes
    
    $(document.body).on('click', '.tr_link_save' ,function(event){

        var $this = $(this);
        
        $($this).text(TrMovies.loading);
        var trmovies = $(".trmoviesget").serialize();
        
        $.post( TrMovies.ajaxurl, { action: "trmovieslinks", 'nonce': TrMovies.nonce_links, 'type': 1, 'trmovies': trmovies })
          .done(function( data ) {
            $($this).text(data);
        });
        
    });
    
    // add links series
    
    $(document.body).on('click', '.tr_link_add' ,function(event){

        var tab = $( this ).data( "class" );

        $( "."+tab+":first" ).clone().insertBefore( "."+tab+":first" );
        $( "."+tab+":first select option" ).prop("selected", false);
        $( "."+tab+":first input" ).val('');
        var today = new Date();
        var month = today.getMonth()+1;
        $( "."+tab+":first input[name=\"trmovies_date[]\"]" ).val(today.getDate()+'/'+month+'/'+today.getFullYear());
        
        
    });
    
    // delete links series
    
    $("body").on("click", ".tr_link_remove", function(e){
        e.preventDefault();
        
        var tab = $( this ).data( "class" );
        
        var num = $('.'+tab).length;
        var id = $( this ).data( "id" );
                
        if(num>1){
            $(this).parent().parent().remove();
        }else{
            $( "."+tab+":first select option" ).prop("selected", false);
            $( "."+tab+":first input" ).val('');
        }
        
        /*
        if(id!=''){
            
            $.post( TrMovies.ajaxurl, { action: "tr_movieslinks_action", 'nonce': TrMovies.nonce_links, 'post_id': TrMovies.post_id, 'type': 2 })
              .done(function( data ) {
                $('.trmovieswarning').remove();
                $('.ToroPlay-tblcn').before(data);
            });
            
        }*/
        
    });
    
});