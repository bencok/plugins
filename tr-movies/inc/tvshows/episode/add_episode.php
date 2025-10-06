<?php
if ( !defined('NONCE_KEY') ) die('Die!');

    $options = ''; $img_episode = ''; $error = ''; $ok = '';

    if(isset($_POST['season_episode'])){ $_POST['season_episode'] = intval($_POST['season_episode']); }else{ $_POST['season_episode'] = ''; }
    if(isset($_POST['content_episode'])){ $content_episode = wp_strip_all_tags($_POST['content_episode']); }else{ $content_episode = ''; }


    if(isset($_POST['submit_episode'])){
        
        $post_id = intval( $_POST['tr_post_id_links'] ) ;

        if(empty($_POST['name_episode'])){

            $error.='<p class="msjadm-error">'.__("You must enter episode's name.", TRMOVIES).'</p>';

        }
        
        if(empty($_POST['number_episode']) and !intval($_POST['number_episode'])){

            $error.='<p class="msjadm-error">'.__("You must enter episodes's number.", TRMOVIES).'</p>';

        }
        
        if(empty($_POST['season_episode']) and !intval($_POST['season_episode'])){

            $error.='<p class="msjadm-error">'.__("You must enter season's number.", TRMOVIES).'</p>';

        }

        $parent_term_episode = term_exists( sanitize_title(stripslashes(get_the_title($post_id).' '.intval($_POST['season_episode']).'x'.intval($_POST['number_episode']))), 'episodes' );
        $parent_term_id_ep = $parent_term_episode['term_id'];
        
        if($parent_term_id_ep!=''){
            
            $error.='<p class="msjadm-error">'.__("This episode was already added before.", TRMOVIES).'</p>';
            
        }
        
        if(empty($error)){
        
            $upload_dir = wp_upload_dir();

                $cid_ep = wp_insert_term(stripslashes(get_the_title($post_id)).' '.intval($_POST['season_episode']).'x'.intval($_POST['number_episode']), 'episodes');
                $parent_term_id_ep = $cid_ep['term_id'];


                if( !empty( $_FILES['poster_episode']['tmp_name'] ) ) {

                    move_uploaded_file($_FILES['poster_episode']['tmp_name'], $upload_dir['path'].'/'.sanitize_title(get_the_title($post_id)).'-'.$parent_term_id_ep.'-episode-'.intval($_POST['number_episode']).'.jpg');

                    $image = wp_get_image_editor( $upload_dir['path'].'/'.sanitize_title(get_the_title($post_id)).'-'.$parent_term_id_ep.'-episode-'.intval($_POST['number_episode']).'.jpg' );

                    if ( ! is_wp_error( $image ) ) {
                        $image->resize( 800, 562, false );
                        $image->save( $upload_dir['path'].'/'.sanitize_title(get_the_title($post_id)).'-'.$parent_term_id_ep.'-episode-'.intval($_POST['number_episode']).'.jpg' );
                    }

                    $attachment_episode = array(
                        'guid' => $upload_dir['path'].'/'.sanitize_title(get_the_title($post_id)).'-'.$parent_term_id_ep.'-episode-'.intval($_POST['number_episode']).'.jpg', 
                        'post_mime_type' => 'image/jpeg',
                        'post_title' => sanitize_title(get_the_title($post_id)).'-'.$parent_term_id_ep.'-episode-'.intval($_POST['number_episode']).'.jpg',
                        'post_content' => '',
                        'post_status' => 'inherit'
                    );

                    $attach_id_episode = wp_insert_attachment( $attachment_episode, $upload_dir['path'].'/'.sanitize_title(get_the_title($post_id)).'-'.$parent_term_id_ep.'-episode-'.intval($_POST['number_episode']).'.jpg','');

                    $attach_data_episode = wp_generate_attachment_metadata( $attach_id_episode, $upload_dir['path'].'/'.sanitize_title(get_the_title($post_id)).'-'.$parent_term_id_ep.'-episode-'.intval($_POST['number_episode']).'.jpg' );
                    wp_update_attachment_metadata( $attach_id_episode, $attach_data_episode );

                    $img_episode = $attach_id_episode;

                }

                $array_episodes_single = array(

                    'air_date' => wp_strip_all_tags($_POST['air_date_episode']),
                    'episode_number' => intval($_POST['number_episode']),
                    'name' => wp_strip_all_tags($_POST['name_episode']),
                    'overview' => wp_strip_all_tags($_POST['content_episode']),
                    'season_number' => intval($_POST['season_episode']),
                    'still_path' => $img_episode,
                    'guest_stars' => wp_strip_all_tags($_POST['guest_stars_episode']),

                );
            
                update_term_meta($parent_term_id_ep, 'tr_id_post', $post_id);
                
                if(!empty($array_episodes_single['air_date'])){
                    update_term_meta($parent_term_id_ep, 'air_date', $array_episodes_single['air_date']);
                }

                if(!empty($array_episodes_single['episode_number'])){
                    update_term_meta($parent_term_id_ep, 'episode_number', $array_episodes_single['episode_number']);
                }

                if(!empty($array_episodes_single['name'])){
                    update_term_meta($parent_term_id_ep, 'name', $array_episodes_single['name']);
                }

                if(!empty($array_episodes_single['overview'])){
                    update_term_meta($parent_term_id_ep, 'overview', $array_episodes_single['overview']);
                }

                if(!empty($array_episodes_single['id'])){
                    update_term_meta($parent_term_id_ep, 'id', $array_episodes_single['id']);
                }

                if(!empty($array_episodes_single['season_number'])){
                    update_term_meta($parent_term_id_ep, 'season_number', $array_episodes_single['season_number']);
                }
            
                $season_current = $array_episodes_single['season_number'];


                if(!empty($array_episodes_single['still_path'])){
                    update_term_meta($parent_term_id_ep, 'still_path', $array_episodes_single['still_path']);
                }

                if(!empty($array_episodes_single['guest_stars'])){
                    update_term_meta($parent_term_id_ep, 'guest_stars', $array_episodes_single['guest_stars']);
                }
                
            /*
            $object = array_push($ar_episode_ids_object, $parent_term_id_ep);
                        
            wp_set_object_terms($post_id, array_filter($ar_episode_ids_object), 'episodes', true);
            */
            $episodes_list = wp_get_post_terms($post_id, 'episodes', array("fields" => "ids"));
            
            if(!empty($episodes_list)){ $terms_ids = array_merge( $episodes_list, array($parent_term_id_ep) );  }else{ $terms_ids = array($parent_term_id_ep); }
            
            wp_set_object_terms($post_id, $terms_ids, 'episodes', true);
            
            $term_list_seasons = wp_get_post_terms($post_id, 'seasons', array("fields" => "all"));
            if(!is_wp_error($term_list_seasons) and !empty($term_list_seasons)) {

                $season_id = '';

                foreach ($term_list_seasons as &$term_list_season) {

                    if(get_term_meta($term_list_season->term_id, 'season_number', true)==$season_current){

                        $season_id = $term_list_season->term_id;

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
        
    }

    foreach ($seasons_list as &$val_episode_seasons) {
            
        $options .= '<option '.selected( $_POST['season_episode'], get_term_meta($val_episode_seasons->term_id, 'season_number', true), false).' value="'.get_term_meta($val_episode_seasons->term_id, 'season_number', true).'">'.get_term_meta($val_episode_seasons->term_id, 'season_number', true).'</option>';
        
    }
    echo $error.$ok;
?>
        <div class="Top">
            <h2><?php _e('Add episode', TRMOVIES); ?></h2>
        </div>
        
        <div class="Blkcn">
            <p class="Title"><?php _e("Info", TRMOVIES); ?></p>
            <label class="Inprgt">
                <span><?php _e("Name", TRMOVIES); ?></span>
                <input value="<?php if(isset($_POST['name_episode'])){ echo wp_strip_all_tags($_POST['name_episode']); } ?>" type="text" name="name_episode" placeholder="<?php _e("Name", TRMOVIES); ?>">
            </label>
            <label class="Inprgt">
                <span class="altlbl"><?php _e("Season number", TRMOVIES); ?></span>
                <select name="season_episode">
                    <option value=""><?php _e('Select', TRMOVIES); ?></option>
                    <?php echo $options; ?>
                </select>
            </label>
            <label class="Inprgt">                
                <span class="altlbl"><?php _e("Episode number", TRMOVIES); ?></span>
                <input value="<?php if(isset($_POST['number_episode'])){ echo intval($_POST['number_episode']); } ?>" type="text" name="number_episode" placeholder="<?php _e("Episode number", TRMOVIES); ?>">
            </label>
            <label class="Inprgt">
                <span><?php _e("Air date", TRMOVIES); ?></span>
                <input value="<?php if(isset($_POST['air_date_episode'])){ echo wp_strip_all_tags($_POST['air_date_episode']); } ?>" type="text" name="air_date_episode" placeholder="<?php _e("Air date", TRMOVIES); ?>">
            </label>
            <label class="Inprgt">
                <span><?php _e("Guest stars", TRMOVIES); ?></span>
                <input value="<?php if(isset($_POST['guest_stars_episode'])){ echo wp_strip_all_tags($_POST['guest_stars_episode']); } ?>" type="text" name="guest_stars_episode" placeholder="<?php _e("Guest stars", TRMOVIES); ?>">
            </label>
        </div>
        
        <div class="Blkcn">
            <p class="Title"><?php _e('Synopsis', TRMOVIES); ?></p>
            <?php
                $settings = array(
                    'textarea_rows' => 15,
                    'tabindex' => 1,
                    'media_buttons' => true
                );

                wp_editor($content_episode, 'content_episode', $settings);
            ?>
        </div>
        
        <div class="Blkcn">
            <p class="Title"><?php _e('Poster', TRMOVIES); ?></p>
            <p class="InpFlImg" id="tr_poster_episode">
                <img style="display:none" id="img-poster-episode" src="" alt="">
                <input id="inp-episode" type="file" name="poster_episode">
                <span><?php _e("Click here for upload image", TRMOVIES); ?></span>
            </p>
        </div>
        <button class="BtnSnd Alt" type="submit" name="submit_episode"><?php _e('Add episode', TRMOVIES); ?></button>