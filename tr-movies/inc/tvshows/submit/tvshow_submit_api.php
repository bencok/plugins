<?php
if ( !defined('NONCE_KEY') ) die('Die!');

    $error = ''; $ok = ''; $content = '';

    // API

    if( !empty($_POST['trmovie_id']) ){
        
        $tr_movies_curl = trmovies_curl('https://api.themoviedb.org/3/tv/'.$_POST['trmovie_id'].'?api_key='.TR_MOVIES_API_KEY.'&language='.get_bloginfo("language"));

        $tr_movies_curl = json_decode($tr_movies_curl, true);
        
        $tr_movies_videos_curl = trmovies_curl('https://api.themoviedb.org/3/tv/'.$_POST['trmovie_id'].'/videos?api_key='.TR_MOVIES_API_KEY.'&language='.get_bloginfo("language"));

        $tr_movies_videos_curl = json_decode($tr_movies_videos_curl, true);

        if(!empty($_POST['post_id_act'])){

            $my_post = array(
              'ID' => intval($_POST['post_id_act']),
              'post_title'    => stripslashes($tr_movies_curl['name']),
              'post_content'  => stripslashes($tr_movies_curl['overview']),
              'post_status'   => 'draft',
            );

            $post_id=wp_update_post( $my_post );

        }else{

            $my_post = array(
              'post_title'    => stripslashes($tr_movies_curl['name']),
              'post_content'  => stripslashes($tr_movies_curl['overview']),
              'post_status'   => 'draft',
            );

            $post_id=wp_insert_post( $my_post );

        }
        
        if($tr_movies_videos_curl['results'][0]['site']=='YouTube'){
            update_post_meta($post_id, TR_MOVIES_FIELD_TRAILER, '<iframe width="560" height="315" src="https://www.youtube.com/embed/'.$tr_movies_videos_curl['results'][0]['key'].'" frameborder="0" allowfullscreen></iframe>');
        }

        if(!empty($tr_movies_curl['original_name'])){
            update_post_meta($post_id, TR_MOVIES_FIELD_ORIGINALTITLE, $tr_movies_curl['original_name']);
        }

        update_post_meta($post_id, TR_MOVIES_FIELD_IMDBID, $tr_movies_curl['id']);

        update_post_meta($post_id, 'tr_post_type', 2);

        if($tr_movies_curl['poster_path']!=''){
            update_post_meta($post_id, TR_MOVIES_POSTER_HOTLINK, $tr_movies_curl['poster_path']);
        }

        if(TR_MOVIES_UPLOAD_IMAGES==1){

            // upload image post

            $upload_dir = wp_upload_dir();

            if($tr_movies_curl['poster_path']!=''){
                
                if ( is_rtl() ) { $title_img = md5(sanitize_title($tr_movies_curl['name'])); }else{ $title_img = sanitize_title($tr_movies_curl['name']); }
                
                copy('http://image.tmdb.org/t/p/original/'.$tr_movies_curl['poster_path'], $upload_dir['path'].'/'.$title_img.'-'.$post_id.'-poster.jpg');

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

       // backdrop

        if($tr_movies_curl['backdrop_path']!=''){

            update_post_meta($post_id, 'backdrop_hotlink', $tr_movies_curl['backdrop_path']);

            if(TR_MOVIES_UPLOAD_IMAGES==1){
                
                if ( is_rtl() ) { $title_backdrop = md5(sanitize_title($tr_movies_curl['name'])); }else{ $title_backdrop = sanitize_title($tr_movies_curl['name']); }

                copy('http://image.tmdb.org/t/p/original/'.$tr_movies_curl['backdrop_path'], $upload_dir['path'].'/'.$title_backdrop.'-'.$post_id.'-backdrop.jpg');

                $image = wp_get_image_editor( $upload_dir['path'].'/'.$title_backdrop.'-'.$post_id.'-backdrop.jpg' );

                if ( ! is_wp_error( $image ) ) {
                    $image->resize( 800, 562, false );
                    $image->save( $upload_dir['path'].'/'.$title_backdrop.'-'.$post_id.'-backdrop.jpg' );
                }

                $attachment_backdrop = array(
                    'guid' => $upload_dir['path'].'/'.$title_backdrop.'-'.$post_id.'-backdrop.jpg', 
                    'post_mime_type' => 'image/jpeg',
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


        update_post_meta($post_id, TR_MOVIES_FIELD_DATE, $tr_movies_curl['first_air_date']);
        update_post_meta($post_id, TR_MOVIES_FIELD_DATE_LAST, $tr_movies_curl['last_air_date']);

        foreach ($tr_movies_curl['genres'] as $geners) {
            $geners_array[] = $geners['name'];
        }

        wp_set_object_terms($post_id, $geners_array, 'category');

        update_post_meta($post_id, TR_MOVIES_FIELD_INPRODUCTION, $tr_movies_curl['in_production']);

        update_post_meta($post_id, TR_MOVIES_FIELD_RUNTIME, $tr_movies_curl['episode_run_time']);

        update_post_meta($post_id, TR_MOVIES_FIELD_STATUS, $tr_movies_curl['status']);

        update_post_meta($post_id, TR_MOVIES_FIELD_NEPISODES, 0);

        update_post_meta($post_id, TR_MOVIES_FIELD_NSEASONS, 0);

        foreach ($tr_movies_curl['created_by'] as $created_by) {
            $createdby_array[] = $created_by['name'];
        }

        wp_set_object_terms($post_id, $createdby_array, 'directors_tv');

        $tr_movies_credits_curl = trmovies_curl('https://api.themoviedb.org/3/tv/'.$_POST['trmovie_id'].'/credits?api_key='.TR_MOVIES_API_KEY.'&language='.get_bloginfo("language"));

        $tr_movies_credits_curl = json_decode($tr_movies_credits_curl, true);

        foreach ($tr_movies_credits_curl['cast'] as $cast) {
            $cast_array[] = $cast['name'];
            $cast_array_image[] = $cast['profile_path'];
        }

        if(isset($cast_array)){

            $term_taxonomy_ids = wp_set_object_terms($post_id, $cast_array, 'cast_tv');

            for ($casti = 0; $casti <= count($term_taxonomy_ids)-1; $casti++) {

            $term_ex = term_exists($cast_array[$casti], 'cast_tv');

            if (!empty($term_ex)) {

                /*
                if(TR_MOVIES_UPLOAD_IMAGES==1){

                    if($cast_array_image[$casti]!=''){

                        $image_150 = wp_get_image_editor( 'http://image.tmdb.org/t/p/original/'.$cast_array_image[$casti] );

                        if ( ! is_wp_error( $image_150 ) ) {
                            $image_150->resize( 150, 150, true );
                            $image_150->save( $upload_dir['path'].'/cast-'.$term_taxonomy_ids[$casti].'.jpg' );
                        }

                        $image_40 = wp_get_image_editor( 'http://image.tmdb.org/t/p/original/'.$cast_array_image[$casti] );

                        if ( ! is_wp_error( $image_40 ) ) {
                            $image_40->resize( 40, 40, true );
                            $image_40->save( $upload_dir['path'].'/cast-'.$term_taxonomy_ids[$casti].'-40.jpg' );
                        }

                        update_term_meta($term_taxonomy_ids[$casti], 'image', $upload_dir['url'].'/cast-'.$term_taxonomy_ids[$casti].'.jpg');
                        update_term_meta($term_taxonomy_ids[$casti], 'image_40', $upload_dir['url'].'/cast-'.$term_taxonomy_ids[$casti].'-40.jpg');
                    }

                }*/

                if($cast_array_image[$casti]!=''){
                    update_term_meta($term_taxonomy_ids[$casti], 'image_hotlink', $cast_array_image[$casti]);
                }

            }

            }
        }
        
        echo '
            <script type="text/javascript">
                window.location = "'.admin_url('admin.php?page=tr-movies-tv&action=edit&msj=1&id='.$post_id).'";
            </script>
        ';
        
    }

    // API
?>