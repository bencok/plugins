<?php
if ( !defined('NONCE_KEY') ) die('Die!');
    $episode_info = get_term( intval($_GET['episode']), 'episodes' );
    $path_img = ''; $img_episode = ''; $error = ''; $ok = '';
    $name = get_term_meta( $episode_info->term_id, 'name', true );
    if(isset($_GET['submit_edit_episode'])){ 
        $content_episode = wp_strip_all_tags($_POST['content_episode']); 
    }else{ 
        $content_episode = get_term_meta( $episode_info->term_id, 'overview', true ); 
    }

    if(isset($_POST['season_episode'])){ $season_episode = intval($_POST['season_episode']); }else{ $season_episode = get_term_meta( $episode_info->term_id, 'season_number', true ); }
    $episode_number = get_term_meta( $episode_info->term_id, 'episode_number', true );
    $air_date = get_term_meta( $episode_info->term_id, 'air_date', true );
    $guest_stars = get_term_meta( $episode_info->term_id, 'guest_stars', true );
    if(get_term_meta( $episode_info->term_id, 'still_path', true )!=''){ 
        $path_img = wp_get_attachment_url(get_term_meta( $episode_info->term_id, 'still_path', true ));
    }elseif(get_term_meta( $episode_info->term_id, 'still_path_hotlink', true )!=''){
        $path_img = '//image.tmdb.org/t/p/w300'.get_term_meta( $episode_info->term_id, 'still_path_hotlink', true );
    }

    $options = '';

    foreach ($seasons_list as &$val_episode_seasons) {
            
        $options .= '<option '.selected( $season_episode, get_term_meta($val_episode_seasons->term_id, 'season_number', true), false).' value="'.get_term_meta($val_episode_seasons->term_id, 'season_number', true).'">'.get_term_meta($val_episode_seasons->term_id, 'season_number', true).'</option>';
        
    }

    if(isset($_POST['submit_edit_episode'])){

        if(empty($_POST['name_episode'])){

            $error.='<p class="msjadm-error">'.__("You must enter episode's name.", TRMOVIES).'</p>';

        }
        
        if(empty($_POST['number_episode']) and !intval($_POST['number_episode'])){

            $error.='<p class="msjadm-error">'.__("You must enter episodes's number.", TRMOVIES).'</p>';

        }
        
        if(empty($_POST['season_episode']) and !intval($_POST['season_episode'])){

            $error.='<p class="msjadm-error">'.__("You must enter season's number.", TRMOVIES).'</p>';

        }
        
        if(empty($error)){
        
                $upload_dir = wp_upload_dir();

                $parent_term_id_ep = intval($_POST['term_id']);

                if( !empty( $_FILES['poster_episode']['tmp_name'] ) ) {

                    move_uploaded_file($_FILES['poster_episode']['tmp_name'], $upload_dir['path'].'/'.sanitize_title(get_the_title($post->ID)).'-'.$parent_term_id_ep.'-episode-'.intval($_POST['number_episode']).'.jpg');

                    $image = wp_get_image_editor( $upload_dir['path'].'/'.sanitize_title(get_the_title($post->ID)).'-'.$parent_term_id_ep.'-episode-'.intval($_POST['number_episode']).'.jpg' );

                    if ( ! is_wp_error( $image ) ) {
                        $image->resize( 800, 562, false );
                        $image->save( $upload_dir['path'].'/'.sanitize_title(get_the_title($post->ID)).'-'.$parent_term_id_ep.'-episode-'.intval($_POST['number_episode']).'.jpg' );
                    }

                    $attachment_episode = array(
                        'guid' => $upload_dir['path'].'/'.sanitize_title(get_the_title($post->ID)).'-'.$parent_term_id_ep.'-episode-'.intval($_POST['number_episode']).'.jpg', 
                        'post_mime_type' => 'image/jpeg',
                        'post_title' => sanitize_title(get_the_title($post->ID)).'-'.$parent_term_id_ep.'-episode-'.intval($_POST['number_episode']).'.jpg',
                        'post_content' => '',
                        'post_status' => 'inherit'
                    );

                    $attach_id_episode = wp_insert_attachment( $attachment_episode, $upload_dir['path'].'/'.sanitize_title(get_the_title($post->ID)).'-'.$parent_term_id_ep.'-episode-'.intval($_POST['number_episode']).'.jpg','');

                    $attach_data_episode = wp_generate_attachment_metadata( $attach_id_episode, $upload_dir['path'].'/'.sanitize_title(get_the_title($post->ID)).'-'.$parent_term_id_ep.'-episode-'.intval($_POST['number_episode']).'.jpg' );
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


                if(!empty($array_episodes_single['still_path'])){
                    update_term_meta($parent_term_id_ep, 'still_path', $array_episodes_single['still_path']);
                }

                if(!empty($array_episodes_single['guest_stars'])){
                    update_term_meta($parent_term_id_ep, 'guest_stars', $array_episodes_single['guest_stars']);
                }
            
            /*
            $object = array_push($ar_episode_ids_object, $parent_term_id_ep);
                        
            wp_set_object_terms($post->ID, array_filter($ar_episode_ids_object), 'episodes', true);
            */
            
            $episodes_list = wp_get_post_terms($post->ID, 'episodes', array("fields" => "ids"));
            
            if(!empty($episodes_list)){ $terms_ids = array_merge( $episodes_list, array($parent_term_id_ep) );  }else{ $terms_ids = array($parent_term_id_ep); }
            
            wp_set_object_terms($post->ID, $terms_ids, 'episodes', true);
            
            echo '
                <script type="text/javascript">
                    window.location = "'.admin_url('admin.php?page=tr-movies-tv&action=edit&msj=7&id='.$post->ID).'";
                </script>
            ';
            
        }
        
    }
    echo $error.$ok;
?>
        <div class="Top">
            <h2><?php _e('Edit episode', TRMOVIES); ?><span><a target="_blank" href="term.php?taxonomy=episodes&tag_ID=<?php echo $episode_info->term_id; ?>&post_type=post"><?php _e('Edit Term', TRMOVIES); ?></a></span></h2>
        </div>
        
        <div class="Blkcn">
            <p class="Title"><?php _e("Info", TRMOVIES); ?></p>
            <label class="Inprgt">
                <span><?php _e("Name", TRMOVIES); ?></span>
                <input value="<?php if(isset($_POST['name_episode'])){ echo wp_strip_all_tags($_POST['name_episode']); }else{ echo $name; } ?>" type="text" name="name_episode" placeholder="<?php _e("Name", TRMOVIES); ?>">
            </label>
            <input type="hidden" name="season_episode" value="<?php echo $season_episode; ?>">
            <input type="hidden" name="number_episode" value="<?php echo $episode_number; ?>">
            <label class="Inprgt">
                <span><?php _e("Air date", TRMOVIES); ?></span>
                <input value="<?php if(isset($_POST['air_date_episode'])){ echo wp_strip_all_tags($_POST['air_date_episode']); }else{ echo $air_date; } ?>" type="text" name="air_date_episode" placeholder="<?php _e("Air date", TRMOVIES); ?>">
            </label>
            <label class="Inprgt">
                <span><?php _e("Guest stars", TRMOVIES); ?></span>
                <input value="<?php if(isset($_POST['guest_stars_episode'])){ echo wp_strip_all_tags($_POST['guest_stars_episode']); }else{ echo $guest_stars; } ?>" type="text" name="guest_stars_episode" placeholder="<?php _e("Guest stars", TRMOVIES); ?>">
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
                <?php if($path_img!=''){ ?>
                <img id="img-poster-episode" src="<?php echo $path_img.'?nocache='.microtime(); ?>" alt="">                
                <?php }else{ ?>
                <img style="display:none" id="img-poster-episode" src="" alt="">
                <?php } ?>
                <input id="inp-episode" type="file" name="poster_episode">
                <span><?php _e("Click here for upload image", TRMOVIES); ?></span>
            </p>
        </div>
        <input type="hidden" name="term_id" value="<?php echo intval($_GET['episode']); ?>">
        <button class="BtnSnd Alt" type="submit" name="submit_edit_episode"><?php _e('Save changes', TRMOVIES); ?></button>