jQuery(document).ready(function($) {    
    function tr_readURL(input) {
        if (input.files && input.files[0]) {
            var tr_reader = new FileReader();
            
            tr_reader.onload = function (e) {
                $('#img-poster').attr('src', e.target.result);
            }
            
            tr_reader.readAsDataURL(input.files[0]);
        }
    }
    function tr_readURLb(input) {
        if (input.files && input.files[0]) {
            var tr_readerb = new FileReader();
            
            tr_readerb.onload = function (e) {
                $('#img-backdrop').attr('src', e.target.result);
            }
            
            tr_readerb.readAsDataURL(input.files[0]);
        }
    }
    function tr_readURLc(input) {
        if (input.files && input.files[0]) {
            var tr_readerb = new FileReader();
            
            tr_readerb.onload = function (e) {
                $('#img-poster-season').attr('src', e.target.result);
            }
            
            tr_readerb.readAsDataURL(input.files[0]);
        }
    }
    function tr_readURLd(input) {
        if (input.files && input.files[0]) {
            var tr_readerb = new FileReader();
            
            tr_readerb.onload = function (e) {
                $('#img-poster-episode').attr('src', e.target.result);
            }
            
            tr_readerb.readAsDataURL(input.files[0]);
        }
    }
    
    $("#inp-poster").change(function(){
        tr_readURL(this);
    });
    $("#inp-backdrop").change(function(){
        tr_readURLb(this);
    });
    $("#inp-season").change(function(){
        tr_readURLc(this);
        $('#img-poster-season').show();
    });
    $("#inp-episode").change(function(){
        tr_readURLd(this);
        $('#img-poster-episode').show();
    });
});