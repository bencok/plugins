<?php
if ( !defined('NONCE_KEY') ) die('Die!');

    $error = ''; $ok = ''; $content = ''; $content_season = ''; $parent_term_slug = '';

    $upload_dir = wp_upload_dir();

    // API episodes

    if( isset($_GET['autoepisodes']) ) {
        
        $post_id = intval( $_GET['id'] );
        
        $episodes_array_add = array( '' );
        
        $poster_path_episode = array( '' );
        
        $tr_movies_seasons_curl = trmovies_curl('https://api.themoviedb.org/3/tv/'.$_GET['tmdbid'].'/season/'.$_GET['tseason'].'?api_key='.TR_MOVIES_API_KEY.'&language='.get_bloginfo("language"));

        $tr_movies_seasons_curl = json_decode($tr_movies_seasons_curl, true);
                
        $ar_gueststars_comms = array('');

        for ($ise = 1; $ise <= count($tr_movies_seasons_curl['episodes']); $ise++) {

            $tr_movies_episode_curl[$ise] = trmovies_curl('https://api.themoviedb.org/3/tv/'.$_GET['tmdbid'].'/season/'.$_GET['tseason'].'/episode/'.$ise.'?api_key='.TR_MOVIES_API_KEY.'&language='.get_bloginfo("language"));

            $tr_movies_episode_curl[$ise] = json_decode($tr_movies_episode_curl[$ise], true);
                        
            if(isset($tr_movies_episode_curl[$ise]['episode_number'])){
            
            $season_current = $tr_movies_episode_curl[$ise]['season_number'];
            
            $array_guest_stars = $tr_movies_episode_curl[$ise]['guest_stars'];

            foreach ($array_guest_stars as &$value_guest_stars) {
                $ar_gueststars_comms[$ise][]= $value_guest_stars['name'];
            }
                        
            $parent_term_episode = term_exists( trim(sanitize_title(stripslashes(get_the_title($post_id).' '.$tr_movies_episode_curl[$ise]['season_number'].'x'.$tr_movies_episode_curl[$ise]['episode_number']))), 'episodes' );
            $parent_term_id_ep = $parent_term_episode['term_id'];
            
            $parent_term_slugb=sanitize_title(stripslashes(get_the_title($post_id).' '.$tr_movies_episode_curl[$ise]['season_number'].'x'.$tr_movies_episode_curl[$ise]['episode_number'])).',';
            //$term_count[$ise] = get_term( $parent_term_id_ep, 'episodes' );

            //if(isset($term_count[$ise]->count) and $term_count[$ise]->count==0 and isset($term_count[$ise]->term_id)){ $parent_term_id_ep = $term_count[$ise]->term_id; }
                
            if (!in_array($tr_movies_episode_curl[$ise]['season_number'].'x'.$tr_movies_episode_curl[$ise]['episode_number'], $ar_episode_numbers)) {
                $cid_ep = wp_insert_term(stripslashes(get_the_title($post_id)).' '.$tr_movies_episode_curl[$ise]['season_number'].'x'.$tr_movies_episode_curl[$ise]['episode_number'], 'episodes');
                if (!is_wp_error($cid_ep) ) {

                $parent_term_id_ep = $cid_ep['term_id'];
                $termn = get_term( $parent_term_id_ep, 'episodes' );

                $parent_term_slug.= $termn->slug.',';

                if( TR_MOVIES_UPLOAD_IMAGES==1 and $tr_movies_episode_curl[$ise]['still_path']!='' ) {
                    
                    if ( is_rtl() ) { $title_img = md5(sanitize_title(get_the_title($post_id))); }else{ $title_img = sanitize_title(get_the_title($post_id)); }

                    copy('http://image.tmdb.org/t/p/original/'.$tr_movies_episode_curl[$ise]['still_path'], $upload_dir['path'].'/'.$title_img.'-'.$parent_term_id_ep.'-episode-'.$tr_movies_episode_curl[$ise]['episode_number'].'.jpg');

                    $image = wp_get_image_editor( $upload_dir['path'].'/'.$title_img.'-'.$parent_term_id_ep.'-episode-'.$tr_movies_episode_curl[$ise]['episode_number'].'.jpg' );

                    if ( ! is_wp_error( $image ) ) {
                        $image->resize( 800, 562, false );
                        $image->save( $upload_dir['path'].'/'.$title_img.'-'.$parent_term_id_ep.'-episode-'.$tr_movies_episode_curl[$ise]['episode_number'].'.jpg' );
                    }

                    $attachment_episode = array(
                        'guid' => $upload_dir['path'].'/'.$title_img.'-'.$parent_term_id_ep.'-episode-'.$tr_movies_episode_curl[$ise]['episode_number'].'.jpg', 
                        'post_mime_type' => 'image/jpeg',
                        'post_title' => $title_img.'-'.$parent_term_id_ep.'-episode-'.$tr_movies_episode_curl[$ise]['episode_number'].'.jpg',
                        'post_content' => '',
                        'post_status' => 'inherit'
                    );

                    $attach_id_episode = wp_insert_attachment( $attachment_episode, $upload_dir['path'].'/'.$title_img.'-'.$parent_term_id_ep.'-episode-'.$tr_movies_episode_curl[$ise]['episode_number'].'.jpg',$post_id);

                    $attach_data_episode = wp_generate_attachment_metadata( $attach_id_episode, $upload_dir['path'].'/'.$title_img.'-'.$parent_term_id_ep.'-episode-'.$tr_movies_episode_curl[$ise]['episode_number'].'.jpg' );
                    wp_update_attachment_metadata( $attach_id_episode, $attach_data_episode );

                    $poster_path_episode[$ise] = $attach_id_episode;

                }

                if( empty($poster_path_episode[$ise]) ){ $poster_path_episode[$ise] = ''; }
                if( empty($ar_gueststars_comms[$ise]) ){ $ar_gueststars_comms[$ise] = array(''); }
                
                $array_episodes[$ise] = array(

                    'air_date' => $tr_movies_episode_curl[$ise]['air_date'],
                    'episode_number' => $tr_movies_episode_curl[$ise]['episode_number'],
                    'name' => $tr_movies_episode_curl[$ise]['name'],
                    'overview' => $tr_movies_episode_curl[$ise]['overview'],
                    'id' => $tr_movies_episode_curl[$ise]['id'],
                    'season_number' => $tr_movies_episode_curl[$ise]['season_number'],
                    'still_path_hotlink' => $tr_movies_episode_curl[$ise]['still_path'],
                    'still_path' => $poster_path_episode[$ise],
                    'guest_stars' => implode(', ', $ar_gueststars_comms[$ise]),

                );

                if(!empty($array_episodes[$ise]['air_date'])){
                    update_term_meta($parent_term_id_ep, 'air_date', $array_episodes[$ise]['air_date']);
                }

                if(!empty($array_episodes[$ise]['episode_number'])){
                    update_term_meta($parent_term_id_ep, 'episode_number', $array_episodes[$ise]['episode_number']);
                }

                if(!empty($array_episodes[$ise]['name'])){
                    update_term_meta($parent_term_id_ep, 'name', $array_episodes[$ise]['name']);
                }

                if(!empty($array_episodes[$ise]['overview'])){
                    update_term_meta($parent_term_id_ep, 'overview', $array_episodes[$ise]['overview']);
                }

                if(!empty($array_episodes[$ise]['id'])){
                    update_term_meta($parent_term_id_ep, 'id', $array_episodes[$ise]['id']);
                }

                if(!empty($array_episodes[$ise]['season_number'])){
                    update_term_meta($parent_term_id_ep, 'season_number', $array_episodes[$ise]['season_number']);
                }

                if(!empty($array_episodes[$ise]['still_path_hotlink'])){
                    update_term_meta($parent_term_id_ep, 'still_path_hotlink', $array_episodes[$ise]['still_path_hotlink']);
                }

                if(!empty($array_episodes[$ise]['still_path'])){
                    update_term_meta($parent_term_id_ep, 'still_path', $array_episodes[$ise]['still_path']);
                }

                if(!empty($array_episodes[$ise]['guest_stars'])){
                    update_term_meta($parent_term_id_ep, 'guest_stars', $array_episodes[$ise]['guest_stars']);
                }
                
                update_term_meta($parent_term_id_ep, 'tr_id_post', $post_id);

            }
            }
            
            //$episodes_array_add[] = $parent_term_slug;
            
        }
            
        $result = array_unique(array_filter(explode(',', $parent_term_slug)));
            
            //print_r($result);
        wp_set_object_terms($post_id, $result, 'episodes', true);
        
        $term_list_seasons = wp_get_post_terms($post_id, 'seasons', array("fields" => "all"));
        if(!is_wp_error($term_list_seasons) and !empty($term_list_seasons)) {
            
            $season_id = '';
            
            foreach ($term_list_seasons as &$term_list_season) {
                
                if(get_term_meta($term_list_season->term_id, 'season_number', true)==$season_current){
                
                    $season_id = $term_list_season->term_id;
                    
                }
            }
            
        }
            
        }
                
        $term_list_episodes = wp_get_post_terms($post_id, 'episodes', array("fields" => "all"));
        if(!is_wp_error($term_list_episodes) and !empty($term_list_episodes)){

            update_post_meta($post_id, TR_MOVIES_FIELD_NEPISODES, count($term_list_episodes));
            
            if($season_id!=''){
                
                $array_episodes_season[] = '';
                
                foreach ($term_list_episodes as &$count_episode_season) {
                    
                    if(get_term_meta($count_episode_season->term_id, 'season_number', true)==$season_current){

                        $array_episodes_season[] = $count_episode_season->term_id;

                    }
                    
                }

                update_term_meta($season_id, 'number_of_episodes', count($array_episodes_season)-1);
                
            }

        }
        
        
        echo '
            <script type="text/javascript">
                window.location = "'.admin_url('admin.php?page=tr-movies-tv&action=edit&msj=6&id='.$post_id).'";
            </script>
        ';
        
    }

    // End API episodes
?>