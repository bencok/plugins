<?php
if ( !defined('NONCE_KEY') ) die('Die!');

    $error = ''; $ok = ''; $content = '';

    if( isset($_POST['submit']) ) {
        
        if( empty($_POST['title']) ) {
            
            $error.= '<p class="msjadm-error">'.__('The movie name field is empty.', TRMOVIES).'</p>';
            
        }
        
        if( empty($_POST['categories']) ) {
            
            $error.= '<p class="msjadm-error">'.__('You must select at least one category.', TRMOVIES).'</p>';
            
        }
        
        if( empty($_POST['content']) ) {
            
            $error.= '<p class="msjadm-error">'.__('You must complete the synopsis.', TRMOVIES).'</p>';
            
        }
        
        if( !empty( $_FILES['poster']['tmp_name'] ) ) {
         
            if( getimagesize( $_FILES['poster']['tmp_name'] ) =='' ){
            
                $error.= '<p class="msjadm-error">'.__('The poster is not a valid image.', TRMOVIES).'</p>';
                
            }
            
        }
        
        if( !empty( $_FILES['backdrop']['tmp_name'] ) ) {
         
            if( getimagesize( $_FILES['backdrop']['tmp_name'] ) =='' ){
            
                $error.= '<p class="msjadm-error">'.__('The backdrop is not a valid image.', TRMOVIES).'</p>';
                
            }
            
        }
        
        if( empty($error) ) {
                        
            $upload_dir = wp_upload_dir();
            
            $my_post = array(
                'ID' => intval($_POST['id_post']),
                'post_title'    => stripslashes( wp_strip_all_tags( $_POST['title'] ) ),
                'post_content'  => stripslashes( $_POST['content'] ),
                'post_status'   => wp_strip_all_tags( $_POST['status'] ),
                'post_category' => $_POST['categories']
            );

            $post_id = wp_update_post( $my_post );
            
            if( !empty($_POST['trailer']) ) {

                update_post_meta( $post_id, TR_MOVIES_FIELD_TRAILER, addslashes( $_POST['trailer'] ) );

            }else{
                
                update_post_meta( $post_id, TR_MOVIES_FIELD_TRAILER, '' );
                
            }

            if( !empty( $_POST['original_title'] ) ) {

                update_post_meta( $post_id, TR_MOVIES_FIELD_ORIGINALTITLE, stripslashes( wp_strip_all_tags( $_POST['original_title'] )) );

            }

            if( !empty( $_POST['id'] ) ) {
            
                update_post_meta( $post_id, TR_MOVIES_FIELD_ID, wp_strip_all_tags ( $_POST['id'] ) );
            
            }
            
            if( !empty( $_POST['imdb_id'] ) ) {

                update_post_meta($post_id, TR_MOVIES_FIELD_IMDBID, wp_strip_all_tags ( $_POST['imdb_id'] ) );
                
            }

            
            if( !empty( $_POST['release'] ) ) {
            
                update_post_meta($post_id, TR_MOVIES_FIELD_DATE, wp_strip_all_tags ( $_POST['release'] ) );
                
            }

            if( !empty( $_POST['duration'] ) ) {
            
                update_post_meta($post_id, TR_MOVIES_FIELD_RUNTIME, lav_minutes_to_hours_mins(wp_strip_all_tags( $_POST['duration'] ) ) );
                
            }
            
            if( !empty( $_FILES['poster']['tmp_name'] ) ) {
                
                if ( is_rtl() ) { $title_img = md5(sanitize_title($_POST['title'])); }else{ $title_img = sanitize_title($_POST['title']); }
                                
                if ( move_uploaded_file( $_FILES['poster']['tmp_name'] , $upload_dir['path'].'/'.$title_img.'-'.$post_id.'-poster.jpg' ) ) {
                  
                    $attachment = array(
                        'guid' => $upload_dir['path'].'/'.$title_img.'-'.$post_id.'-poster.jpg', 
                        'post_mime_type' => 'image/jpeg',
                        'post_title' => $title_img.'-'.$post_id.'-poster.jpg',
                        'post_content' => '',
                        'post_status' => 'inherit'
                    );

                    $attach_id = wp_insert_attachment( $attachment, $upload_dir['path'].'/'.$title_img.'-'.$post_id.'-poster.jpg',$post_id);

                    require_once(ABSPATH . 'wp-admin/includes/image.php');

                    $attach_data = wp_generate_attachment_metadata( $attach_id, $upload_dir['path'].'/'.$title_img.'-'.$post_id.'-poster.jpg' );
                    wp_update_attachment_metadata( $attach_id, $attach_data );

                    set_post_thumbnail($post_id, $attach_id);
                    
                }
                
            }
            
            if( !empty( $_FILES['backdrop']['tmp_name'] ) ) {
                
                if ( is_rtl() ) { $title_backdrop = md5(sanitize_title($_POST['title'])); }else{ $title_backdrop = sanitize_title($_POST['title']); }
                                
                if ( move_uploaded_file( $_FILES['backdrop']['tmp_name'] ,   $upload_dir['path'].'/'.$title_backdrop.'-'.$post_id.'-backdrop.jpg' ) ) {

                    $image = wp_get_image_editor( $upload_dir['path'].'/'.$title_backdrop.'-'.$post_id.'-backdrop.jpg' );

                    if ( ! is_wp_error( $image ) ) {
                        $image->resize( 800, 562, false );
                        $image->save( $upload_dir['path'].'/'.$title_backdrop.'-'.$post_id.'-backdrop.jpg' );
                    }
                    
                    $filetype = wp_check_filetype( $upload_dir['path'].'/'.$title_backdrop.'-'.$post_id.'-backdrop.jpg' );

                    $attachment_backdrop = array(
                        'guid' => $upload_dir['path'].'/'.$title_backdrop.'-'.$post_id.'-backdrop.jpg', 
                        'post_mime_type' => $filetype['type'],
                        'post_title' => $title_backdrop.'-'.$post_id.'-backdrop.jpg',
                        'post_content' => '',
                        'post_status' => 'inherit'
                    );

                    $attach_id_backdrop = wp_insert_attachment( $attachment_backdrop, $upload_dir['path'].'/'.$title_backdrop.'-'.$post_id.'-backdrop.jpg',$post_id);

                    $attach_data_backdrop = wp_generate_attachment_metadata( $attach_id_backdrop, $upload_dir['path'].'/'.$title_backdrop.'-'.$post_id.'-backdrop.jpg' );
                    wp_update_attachment_metadata( $attach_id_backdrop, $attach_data_backdrop );

                    update_post_meta($post_id, TR_MOVIES_FIELD_BACKDROP, $attach_id_backdrop);
                    
                }
                
            }
            
            if( !empty( $_POST['trc_directors'] ) ){
                
                wp_set_object_terms( $post_id, $_POST['trc_directors'], 'directors' );
                
            }else{
                
                wp_set_object_terms( $post_id, '', 'directors' );                
                
            }
            
            if( !empty( $_POST['trc_cast'] ) ){
                
                wp_set_object_terms( $post_id, $_POST['trc_cast'], 'cast' );
                
            }else{
                
                wp_set_object_terms( $post_id, '', 'cast' );
                
            }
            
            if( !empty( $_POST['trc_countries'] ) ){
                
                wp_set_object_terms( $post_id, $_POST['trc_countries'], 'country' );
                
            }else{
                
                wp_set_object_terms( $post_id, '', 'country' );
                
            }
            
            if( !empty( $_POST['trc_tags'] ) ){
                
                wp_set_object_terms( $post_id, $_POST['trc_tags'], 'post_tag' );
                
            }else{
                
                wp_set_object_terms( $post_id, '', 'post_tag' );
                
            }
                               
            echo '
                <script type="text/javascript">
                    window.location = "'.admin_url('admin.php?page=tr-movies-movie&action=edit&id='.intval($_POST['id_post'])).'&msj=2";
                </script>
            ';
                        
        }
        
        if( isset($_POST['content']) and empty($ok) ){

            $content = $_POST['content'];

        }
        
        // links
        
        $array = array(); $server_coms = ''; $lang_coms = ''; $quality_coms = '';
        
        if(isset($_POST['trmovies_link'])){
            
        for ($i = 0; $i <= count($_POST['trmovies_link'])-1; $i++) {
            
            if ( !empty( $_POST['trmovies_type'][$i] ) ) {

                $array[$i]['type'] = $_POST['trmovies_type'][$i];

            }
            
            if ( !empty( $_POST['trmovies_server'][$i] ) ) {

                $array[$i]['server'] = $_POST['trmovies_server'][$i];
                
                $server_ar[$i] = get_term_by('id', intval($_POST['trmovies_server'][$i]), 'server');
                
                $server_coms .= $server_ar[$i]->name.','; 

            }
            
            if ( !empty( $_POST['trmovies_lang'][$i] ) ) {

                $array[$i]['lang'] = $_POST['trmovies_lang'][$i];
                
                $lang_ar[$i] = get_term_by('id', intval($_POST['trmovies_lang'][$i]), 'language');

                $lang_coms .= $lang_ar[$i]->name.',';

            }
            
            if ( !empty( $_POST['trmovies_quality'][$i] ) ) {

                $array[$i]['quality'] = $_POST['trmovies_quality'][$i];

                $quality_ar[$i] = get_term_by('id', intval($_POST['trmovies_quality'][$i]), 'quality');

                $quality_coms .= $quality_ar[$i]->name.','; 

            }
            
            if ( !empty( $_POST['trmovies_link'][$i] ) ) {
                
                if (preg_match('/<iframe(.*?)>(.*?)<\/iframe>/i', $_POST['trmovies_link'][$i])) {
                    
                    preg_match('/<iframe.*src=\"(.*)\".*><\/iframe>/isU', stripslashes($_POST['trmovies_link'][$i]), $matches[$i]);
                                        
                    $array[$i]['link'] = $matches[$i][1];
                                        
                }else{
                    $array[$i]['link'] = $_POST['trmovies_link'][$i];                    
                }

            }
            
            if ( !empty( $_POST['trmovies_date'][$i] ) ) {

                $array[$i]['date'] = $_POST['trmovies_date'][$i];

            }
                        
        }
            
        }
        
        if(isset($array)){ 
            
            update_post_meta( $post_id, TR_MOVIES_FIELD_LINK, serialize($array) ); 
            wp_set_object_terms($post_id, explode(',', $server_coms), 'server');
            wp_set_object_terms($post_id, explode(',', $lang_coms), 'language');
            wp_set_object_terms($post_id, explode(',', $quality_coms), 'quality');
             
        }
        
        // links
        
    }
    
    if(isset($_GET['msj']) and $_GET['msj']==1){
        $ok='<p class="msjadm-ok">'.__('The movie was added.', TRMOVIES).'</p>';
    }

    if(isset($_GET['msj']) and $_GET['msj']==2){
        $ok='<p class="msjadm-ok">'.__('The movie was updated.', TRMOVIES).'</p>';
    }

    $post = get_post( intval($_GET['id']) );

    $post_categories = wp_get_post_categories( $post->ID );

    $content = $post->post_content;

    $term_cast = wp_get_post_terms($post->ID, 'cast', array("fields" => "names"));

    $term_directors = wp_get_post_terms($post->ID, 'directors', array("fields" => "names"));

    $term_tags = wp_get_post_terms($post->ID, 'post_tag', array("fields" => "names"));

    $term_countries = wp_get_post_terms($post->ID, 'country', array("fields" => "names"));

?>