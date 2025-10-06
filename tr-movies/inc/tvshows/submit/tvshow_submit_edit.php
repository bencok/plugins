<?php
if ( !defined('NONCE_KEY') ) die('Die!');

    $error = ''; $ok = ''; $content = ''; $content_season = '';

    $upload_dir = wp_upload_dir();

    if( isset($_POST['submit']) ) {
        
        if( empty($_POST['title']) ) {
            
            $error.= '<p>'.__('The tv show name field is empty.', TRMOVIES).'</p>';
            
        }
        
        if( empty($_POST['categories']) ) {
            
            $error.= '<p>'.__('You must select at least one category.', TRMOVIES).'</p>';
            
        }
        
        if( empty($_POST['content']) ) {
            
            $error.= '<p>'.__('You must complete the synopsis.', TRMOVIES).'</p>';
            
        }
        
        if( !empty( $_FILES['poster']['tmp_name'] ) ) {
         
            if( getimagesize( $_FILES['poster']['tmp_name'] ) =='' ){
            
                $error.= '<p>'.__('The poster is not a valid image.', TRMOVIES).'</p>';
                
            }
            
        }
        
        if( !empty( $_FILES['backdrop']['tmp_name'] ) ) {
         
            if( getimagesize( $_FILES['backdrop']['tmp_name'] ) =='' ){
            
                $error.= '<p>'.__('The backdrop is not a valid image.', TRMOVIES).'</p>';
                
            }
            
        }
        
        if( empty($error) ) {
            
            $my_post = array(
                'ID' => intval($_GET['id']),
                'post_title'    => stripslashes( wp_strip_all_tags( $_POST['title'] ) ),
                'post_content'  => stripslashes( $_POST['content'] ),
                'post_status'   => wp_strip_all_tags( $_POST['status_post'] ),
                'post_category' => $_POST['categories']
            );

            $post_id = wp_update_post( $my_post );
            
            update_post_meta($post_id, 'tr_post_type', 2);
            
            if( !empty( $_POST['original_title'] ) ) {

                update_post_meta( $post_id, TR_MOVIES_FIELD_ORIGINALTITLE, stripslashes( wp_strip_all_tags( $_POST['original_title'] )) );

            }
            
            if( !empty( $_POST['trc_directors'] ) ){
                
                wp_set_object_terms( $post_id, $_POST['trc_directors'], 'directors_tv' );
                
            }else{
                
                wp_set_object_terms( $post_id, '', 'directors_tv' );
                
            }
            
            if( !empty( $_POST['trc_cast'] ) ){
                
                wp_set_object_terms( $post_id, $_POST['trc_cast'], 'cast_tv' );
                
            }else{

                wp_set_object_terms( $post_id, '', 'cast_tv' );
                
            }
            
            if( !empty( $_POST['trc_tags'] ) ){
                
                wp_set_object_terms( $post_id, $_POST['trc_tags'], 'post_tag' );
                
            }else{
                
                wp_set_object_terms( $post_id, '', 'post_tag' );
                
            }
            
            if( !empty( $_POST['duration'] ) ) {
            
                update_post_meta($post_id, TR_MOVIES_FIELD_RUNTIME, wp_strip_all_tags( $_POST['duration'] ));
                
            }

            if( !empty( $_POST['first_air_date'] ) ) {

                update_post_meta($post_id, TR_MOVIES_FIELD_DATE, wp_strip_all_tags( $_POST['first_air_date'] ));
                
            }
            
            if( !empty( $_POST['last_air_date'] ) ) {

                update_post_meta($post_id, TR_MOVIES_FIELD_DATE_LAST, wp_strip_all_tags( $_POST['last_air_date'] ));
                
            }
                        
            if( !empty( $_POST['status'] ) ) {

                update_post_meta($post_id, TR_MOVIES_FIELD_STATUS, wp_strip_all_tags( $_POST['status'] ));
                
            }
            
            if( !empty( $_POST['in_production'] ) ) {

                update_post_meta($post_id, TR_MOVIES_FIELD_INPRODUCTION, wp_strip_all_tags( $_POST['in_production'] ));
                
            }
            
            if( !empty($_POST['trailer']) ) {

                update_post_meta( $post_id, TR_MOVIES_FIELD_TRAILER, addslashes( $_POST['trailer'] ) );

            }else{
                
                update_post_meta( $post_id, TR_MOVIES_FIELD_TRAILER, '' );                
            }
            
            if( !empty( $_FILES['poster']['tmp_name'] ) ) {
                
                if ( is_rtl() ) { $title_img = md5(sanitize_title($_POST['title'])); }else{ $title_img = sanitize_title($_POST['title']); }
                                
                if ( move_uploaded_file( $_FILES['poster']['tmp_name'] ,   $upload_dir['path'].'/'.sanitize_title($_POST['title']).'-'.$post_id.'-poster.jpg' ) ) {
                  
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
            
            /*
            // links
            
            $array_links = '';
            
            if(isset($_POST['trmovies_type'])){
            foreach ($_POST['trmovies_type'] as $key => $value){
                                    
                $array_links[$key]['type'] = serialize($value);

                $array_links[$key]['server'] = serialize($_POST['trmovies_server'][$key]);

                $array_links[$key]['lang'] = serialize($_POST['trmovies_lang'][$key]);

                $array_links[$key]['quality'] = serialize($_POST['trmovies_quality'][$key]);

                $array_links[$key]['link'] = serialize($_POST['trmovies_link'][$key]);

                $array_links[$key]['date'] = serialize($_POST['trmovies_date'][$key]);
                
                if(isset($array_links[$key])){
                
                    update_term_meta($key, TR_MOVIES_FIELD_LINK, serialize( $array_links[$key] ) );
                    
                }
                
            }    
            }*/
            
            echo '
                <script type="text/javascript">
                    window.location = "'.admin_url('admin.php?page=tr-movies-tv&action=edit&msj=2&id='.$post_id).'";
                </script>
            ';
            
        }

    }

?>