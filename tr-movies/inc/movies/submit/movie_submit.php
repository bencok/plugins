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
              'post_title'    => stripslashes( wp_strip_all_tags( $_POST['title'] ) ),
              'post_content'  => stripslashes( $_POST['content'] ),
              'post_status'   => 'draft',
              'post_category' => $_POST['categories']
            );

            $post_id = wp_insert_post( $my_post );
            
            if( !empty($_POST['trailer']) ) {

                update_post_meta( $post_id, TR_MOVIES_FIELD_TRAILER, addslashes( $_POST['trailer'] ) );

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
            
                update_post_meta($post_id, TR_MOVIES_FIELD_RUNTIME, lav_minutes_to_hours_mins(wp_strip_all_tags( $_POST['duration'] )));
                
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
                
                wp_set_object_terms( $post_id, $_POST['trc_directors'] , 'directors' );
                
            }
            
            if( !empty( $_POST['trc_cast'] ) ){
                
                wp_set_object_terms( $post_id, $_POST['trc_cast'] , 'cast' );
                
            }
            
            if( !empty( $_POST['trc_countries'] ) ){
                
                wp_set_object_terms( $post_id, $_POST['trc_countries'] , 'country' );
                
            }
            
            if( !empty( $_POST['trc_tags'] ) ){
                
                wp_set_object_terms( $post_id, $_POST['trc_tags'] , 'post_tag' );
                
            }
            
            echo '
                <script type="text/javascript">
                    window.location = "'.admin_url('admin.php?page=tr-movies-movie&action=edit&msj=1&id='.$post_id).'";
                </script>
            ';
                        
        }
        
        if( isset($_POST['content']) and empty($ok) ){

            $content = $_POST['content'];

        }
        
    }
?>