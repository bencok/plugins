<?php
if ( !defined('NONCE_KEY') ) die('Die!');

    $error = ''; $ok = ''; $content = '';

    // API
    if( !empty($_POST['trmovie_id']) ){

       $tr_movies_curl = trmovies_curl('https://api.themoviedb.org/3/movie/'.$_POST['trmovie_id'].'?api_key='.TR_MOVIES_API_KEY.'&language='.get_bloginfo("language"));

            $tr_movies_curl = json_decode($tr_movies_curl, true);

        
            $tr_movies_credits_curl = trmovies_curl('https://api.themoviedb.org/3/movie/'.$_POST['trmovie_id'].'/credits?api_key='.TR_MOVIES_API_KEY.'&language='.get_bloginfo("language"));

            $tr_movies_credits_curl = json_decode($tr_movies_credits_curl, true);

            $tr_movies_videos_curl = trmovies_curl('https://api.themoviedb.org/3/movie/'.$_POST['trmovie_id'].'/videos?api_key='.TR_MOVIES_API_KEY.'&language='.get_bloginfo("language"));

            $tr_movies_videos_curl = json_decode($tr_movies_videos_curl, true);

            if(!empty($_POST['post_id_act'])){

                $my_post = array(
                  'ID' => intval($_POST['post_id_act']),
                  'post_title'    => stripslashes($tr_movies_curl['title']),
                  'post_content'  => stripslashes($tr_movies_curl['overview']),
                  'post_status'   => 'draft',
                );

                $post_id=wp_update_post( $my_post );

            }else{

                $my_post = array(
                  'post_title'    => stripslashes($tr_movies_curl['title']),
                  'post_content'  => stripslashes($tr_movies_curl['overview']),
                  'post_status'   => 'draft',
                );

                $post_id=wp_insert_post( $my_post );

            }

            if( !isset( $tr_movies_videos_curl['results'][0]['site'] ) ){ $tr_movies_videos_curl['results'][0]['site'] = ''; }
            if($tr_movies_videos_curl['results'][0]['site']=='YouTube'){
                update_post_meta($post_id, TR_MOVIES_FIELD_TRAILER, '<iframe width="560" height="315" src="https://www.youtube.com/embed/'.$tr_movies_videos_curl['results'][0]['key'].'" frameborder="0" allowfullscreen></iframe>');
            }

            if(!empty($tr_movies_curl['original_title'])){
                update_post_meta($post_id, TR_MOVIES_FIELD_ORIGINALTITLE, $tr_movies_curl['original_title']);
            }

            update_post_meta($post_id, TR_MOVIES_FIELD_ID, $tr_movies_curl['id']);

            update_post_meta($post_id, TR_MOVIES_FIELD_IMDBID, $tr_movies_curl['imdb_id']);

            update_post_meta($post_id, TR_MOVIES_FIELD_DATE, $tr_movies_curl['release_date']);

            $hours = ltrim(gmdate("i", $tr_movies_curl['field_runtime']), 0);
            $minutes = ltrim(gmdate("s", $tr_movies_curl['field_runtime']), 0);

            $hours = empty($hours) ? 0 : $hours;
            $minutes = empty($minutes) ? 0 : $minutes;

            update_post_meta($post_id, TR_MOVIES_FIELD_RUNTIME, $hours.'h '.$minutes.'m');

            foreach ($tr_movies_curl['genres'] as $geners) {
                $geners_array[] = $geners['name'];
            }

            wp_set_object_terms($post_id, $geners_array, 'category');

            if($tr_movies_curl['poster_path']!=''){
                update_post_meta($post_id, TR_MOVIES_POSTER_HOTLINK, $tr_movies_curl['poster_path']);
            }

            if(TR_MOVIES_UPLOAD_IMAGES==1){

                // upload image post

                $upload_dir = wp_upload_dir();

                if($tr_movies_curl['poster_path']!=''){
                    
                    if ( is_rtl() ) { $title_img = md5(sanitize_title($tr_movies_curl['title'])); }else{ $title_img = sanitize_title($tr_movies_curl['title']); }
                    
                    copy('http://image.tmdb.org/t/p/original/'.$tr_movies_curl['poster_path'], $upload_dir['path'].'/'.$title_img.'-'.$post_id.'-poster.jpg');

                    $attachment = array(
                        'guid' => $upload_dir['path'].'/'.sanitize_title($tr_movies_curl['title']).'-'.$post_id.'-poster.jpg', 
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
                    
                    if ( is_rtl() ) { $title_backdrop = md5(sanitize_title($tr_movies_curl['title'])); }else{ $title_backdrop = sanitize_title($tr_movies_curl['title']); }

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

            foreach ($tr_movies_credits_curl['crew'] as $crew) {
                if($crew['department']=='Directing'){ $crew_array[] = $crew['name']; }
            }

            wp_set_object_terms($post_id, $crew_array, 'directors');

            foreach ($tr_movies_curl['production_countries'] as $country) {
                $countries_array[] = $country['name'];
            }

            wp_set_object_terms($post_id, $countries_array, 'country');                

            foreach ($tr_movies_credits_curl['cast'] as $cast) {
                $cast_array[] = $cast['name'];
                $cast_array_image[] = $cast['profile_path'];
            }

            if(isset($cast_array)){

                    $term_taxonomy_ids = wp_set_object_terms($post_id, $cast_array, 'cast');

                    for ($casti = 0; $casti <= count($term_taxonomy_ids)-1; $casti++) {

                    $term_ex = term_exists($cast_array[$casti], 'cast');

                    if (!empty($term_ex)) {

                        if($cast_array_image[$casti]!=''){
                            update_term_meta($term_taxonomy_ids[$casti], 'image_hotlink', $cast_array_image[$casti]);
                        }

                    }

                }
            }
        
            echo '
                <script type="text/javascript">
                    window.location = "'.admin_url('admin.php?page=tr-movies-movie&action=edit&msj=1&id='.$post_id).'";
                </script>
            ';
        
    }
?>