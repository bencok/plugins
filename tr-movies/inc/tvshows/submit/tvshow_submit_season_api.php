<?php
if ( !defined('NONCE_KEY') ) die('Die!');

    $error = ''; $ok = ''; $content = ''; $content_season = '';

    $upload_dir = wp_upload_dir();

    if( isset($_POST['trmovie_api_season']) ) {
            
        if(isset($_GET['id'])){
            $post_id = intval( $_GET['id'] );    
        }else{
            $post_id = intval( $_POST['id_post'] );
        }
        
        $tr_movies_curl = trmovies_curl('https://api.themoviedb.org/3/tv/'.$_POST['tmdbid'].'?api_key='.TR_MOVIES_API_KEY.'&language='.get_bloginfo("language"));

        $tr_movies_curl = json_decode($tr_movies_curl, true);
                
        $seasons_array_add = array( '' );
        $episodes_array_add = array( '' );
        
        $term_count = ''; $parent_term_id_array = '';
        
        if(isset($tr_movies_curl['number_of_seasons'])){

            for ($ise = 1; $ise <= $tr_movies_curl['number_of_seasons']; $ise++) {

                $tr_movies_seasons_curl = trmovies_curl('https://api.themoviedb.org/3/tv/'.$_POST['tmdbid'].'/season/'.$ise.'?api_key='.TR_MOVIES_API_KEY.'&language='.get_bloginfo("language"));

                $tr_movies_seasons_curl = json_decode($tr_movies_seasons_curl, true);
                
                $tr_episodes_array[$ise] = $tr_movies_seasons_curl['episodes'];                        

                if (!in_array($tr_movies_seasons_curl['season_number'], $ar_season_numbers)) {
                
                    $cid = wp_insert_term(stripslashes($tr_movies_curl['name']).' '.$ise, 'seasons');
                
                if (!is_wp_error($cid) ) {

                    $parent_term_id = $cid['term_id'];

                    $termn = get_term( $parent_term_id, 'seasons' );

                    $parent_term_slug .= $termn->slug.',';
                    
                    $poster_path_seasons = '';

                    if(TR_MOVIES_UPLOAD_IMAGES==1 and $tr_movies_seasons_curl['poster_path']!=''){
                        
                        if ( is_rtl() ) { $title_img = md5(sanitize_title($tr_movies_curl['name'])); }else{ $title_img = sanitize_title($tr_movies_curl['name']); }

                        copy('http://image.tmdb.org/t/p/original/'.$tr_movies_seasons_curl['poster_path'], $upload_dir['path'].'/'.$title_img.'-'.$parent_term_id.'-season-'.$ise.'.jpg');

                        $image = wp_get_image_editor( $upload_dir['path'].'/'.$title_img.'-'.$parent_term_id.'-season-'.$ise.'.jpg' );

                        if ( ! is_wp_error( $image ) ) {
                            $image->resize( 800, 562, false );
                            $image->save( $upload_dir['path'].'/'.$title_img.'-'.$parent_term_id.'-season-'.$ise.'.jpg' );
                        }

                        $attachment_season = array(
                            'guid' => $upload_dir['path'].'/'.$title_img.'-'.$parent_term_id.'-season-'.$ise.'.jpg', 
                            'post_mime_type' => 'image/jpeg',
                            'post_title' => $title_img.'-'.$parent_term_id.'-season-'.$ise.'.jpg',
                            'post_content' => '',
                            'post_status' => 'inherit'
                        );

                        $attach_id_season = wp_insert_attachment( $attachment_season, $upload_dir['path'].'/'.$title_img.'-'.$parent_term_id.'-season-'.$ise.'.jpg',$post_id);

                        $attach_data_season = wp_generate_attachment_metadata( $attach_id_season, $upload_dir['path'].'/'.$title_img.'-'.$parent_term_id.'-season-'.$ise.'.jpg' );
                        wp_update_attachment_metadata( $attach_id_season, $attach_data_season );

                        $poster_path_seasons = $attach_id_season;

                    }

                    $array_seasons = array(

                        'season_number' => $tr_movies_seasons_curl['season_number'],
                        'air_date' => $tr_movies_seasons_curl['air_date'],
                        'name' => $tr_movies_seasons_curl['name'],
                        'id' => $tr_movies_seasons_curl['id'],
                        'overview' => $tr_movies_seasons_curl['overview'],
                        'poster_path_hotlink' => $tr_movies_seasons_curl['poster_path'],
                        'poster_path' => $poster_path_seasons,
                        'number_of_episodes' => count($tr_movies_seasons_curl['episodes']),

                    );
                                        
                    if(!empty($array_seasons['season_number'])){
                        update_term_meta($parent_term_id, 'season_number', $array_seasons['season_number']);
                    }
                    if(!empty($array_seasons['air_date'])){
                        update_term_meta($parent_term_id, 'air_date', $array_seasons['air_date']);
                    }
                    if(!empty($array_seasons['name'])){
                        update_term_meta($parent_term_id, 'name', $array_seasons['name']);
                    }
                    if(!empty($array_seasons['overview'])){
                        update_term_meta($parent_term_id, 'overview', $array_seasons['overview']);
                    }
                    if(!empty($array_seasons['poster_path'])){
                        update_term_meta($parent_term_id, 'poster_path', $array_seasons['poster_path']);
                    }
                    if(!empty($array_seasons['poster_path_hotlink'])){
                        update_term_meta($parent_term_id, 'poster_path_hotlink', $array_seasons['poster_path_hotlink']);
                    }
                    if(!empty($array_seasons['number_of_episodes'])){
                        update_term_meta($parent_term_id, 'number_of_episodes', 0);
                    }
                    
                    update_term_meta($parent_term_id, 'tr_id_post', $post_id);

                }
                $seasons_array_add[] = $parent_term_id_array;
            }
            }
            }

            $result = array_unique(array_filter(explode(',', $parent_term_slug)));

            wp_set_object_terms($post_id, $result, 'seasons', true);
        
            $term_list_season = wp_get_post_terms($post_id, 'seasons', array("fields" => "all"));
            if(!is_wp_error($term_list_season) and !empty($term_list_season)){

                update_post_meta($post_id, TR_MOVIES_FIELD_NSEASONS, count($term_list_season));
                
            }
        
        echo '
            <script type="text/javascript">
                window.location = "'.admin_url('admin.php?page=tr-movies-tv&action=edit&msj=5&id='.$post_id).'";
            </script>
        ';
        
    }
?>