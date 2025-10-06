<?php
if ( !defined('NONCE_KEY') ) die('Die!');

    $error = ''; $ok = ''; $content = ''; $content_season = '';

    if( isset($_POST['edit_season']) ) {

        $upload_dir = wp_upload_dir();
        
        $post_id = intval( $_POST['tr_post_id_links'] ) ;

        $post_name = get_the_title( $post_id );
        
        $parent_term_id = intval($_POST['season_id']);

        if(empty($_POST['name_season'])){

            $error.='<p class="msjadm-error">'.__("You must enter season's name.", TRMOVIES).'</p>';

        }
        
        if(empty($error)) {

            $poster_path_seasons = '';
            
            if(!empty( $_FILES['poster_season']['tmp_name'] ) ){

                move_uploaded_file($_FILES['poster_season']['tmp_name'], $upload_dir['path'].'/'.sanitize_title($post_name).'-'.$parent_term_id.'-season-'.intval($_POST['season_number']).'.jpg');

                $image = wp_get_image_editor( $upload_dir['path'].'/'.sanitize_title($post_name).'-'.$parent_term_id.'-season-'.intval($_POST['season_number']).'.jpg' );

                if ( ! is_wp_error( $image ) ) {
                    $image->resize( 800, 562, false );
                    $image->save( $upload_dir['path'].'/'.sanitize_title($post_name).'-'.$parent_term_id.'-season-'.intval($_POST['season_number']).'.jpg' );
                }

                $attachment_season = array(
                    'guid' => $upload_dir['path'].'/'.sanitize_title($post_name).'-'.$parent_term_id.'-season-'.intval($_POST['season_number']).'.jpg', 
                    'post_mime_type' => 'image/jpeg',
                    'post_title' => sanitize_title($post_name).'-'.$parent_term_id.'-season-'.intval($_POST['season_number']).'.jpg',
                    'post_content' => '',
                    'post_status' => 'inherit'
                );

                $attach_id_season = wp_insert_attachment( $attachment_season, $upload_dir['path'].'/'.sanitize_title($post_name).'-'.$parent_term_id.'-season-'.intval($_POST['season_number']).'.jpg',$post_id);

                $attach_data_season = wp_generate_attachment_metadata( $attach_id_season, $upload_dir['path'].'/'.sanitize_title($post_name).'-'.$parent_term_id.'-season-'.intval($_POST['season_number']).'.jpg' );
                wp_update_attachment_metadata( $attach_id_season, $attach_data_season );

                $poster_path_seasons = $attach_id_season;

            }

            if(isset($_POST['air_date_season'])){ $_POST['air_date_season'] = $_POST['air_date_season']; }else{ $_POST['air_date_season'] = ''; }

            if(isset($_POST['content_season'])){ $_POST['content_season'] = $_POST['content_season']; }else{ $_POST['content_season'] = ''; }

            if(isset($_POST['number_of_episodes_season'])){ $_POST['number_of_episodes_season'] = $_POST['number_of_episodes_season']; }else{ $_POST['number_of_episodes_season'] = ''; }

            $array_seasons = array(

                'air_date' => stripslashes( wp_strip_all_tags( $_POST['air_date_season'] ) ),
                'name' => stripslashes( wp_strip_all_tags( $_POST['name_season'] ) ),
                'id' => '',
                'overview' => stripslashes( $_POST['content_season'] ),
                'poster_path_hotlink' => '',
                'poster_path' => $poster_path_seasons,
                'number_of_episodes' => intval($_POST['number_of_episodes_season']),

            );

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
            if(!empty($array_seasons['number_of_episodes'])){
                update_term_meta($parent_term_id, 'number_of_episodes', $array_seasons['number_of_episodes']);
            }
            
            wp_update_term($parent_term_id, 'seasons', array(
              'name' => $post_name.' '.intval($_POST['season_number']),
              'slug' => $post_name.' '.intval($_POST['season_number'])
            ));
            
            
            echo '
                <script type="text/javascript">
                    window.location = "'.admin_url('admin.php?page=tr-movies-tv&action=edit&msj=4&id='.$post_id).'";
                </script>
            ';
            
        }
        
    }

    $img = '';
    $season_info = get_term( intval($_GET['season']), 'seasons' );
    $season_name = get_term_meta( $season_info->term_id, 'name', true );
    $season_number = get_term_meta( $season_info->term_id, 'season_number', true );
    $air_date = get_term_meta( $season_info->term_id, 'air_date', true );
    $number_of_episodes = get_term_meta( $season_info->term_id, 'number_of_episodes', true );
    $content_season = get_term_meta( $season_info->term_id, 'overview', true );
    $image = get_term_meta( $season_info->term_id, 'poster_path', true );
    $image_hotlink = get_term_meta( $season_info->term_id, 'poster_path_hotlink', true );
    if($image=='' and $image_hotlink!=''){ $img = '//image.tmdb.org/t/p/w300'.$image_hotlink; }elseif($image!=''){ $img = wp_get_attachment_url($image); }
?>
        
        <div class="Top">
            <h2><?php _e('Edit season', TRMOVIES); ?> <span><a target="_blank" href="term.php?taxonomy=seasons&tag_ID=<?php echo $season_info->term_id; ?>&post_type=post"><?php _e('Edit Term', TRMOVIES); ?></a></span></h2>
        </div>
        
        <?php echo $error.$ok; ?>
        
        <div class="Blkcn">
            <input type="hidden" name="season_number" value="<?php echo $season_number; ?>">
            <input type="hidden" name="number_of_episodes_season" value="<?php echo $number_of_episodes; ?>">
            <p class="Title"><?php _e("Info", TRMOVIES); ?></p>
            <label class="Inprgt">
                <span><?php _e("Name", TRMOVIES); ?></span>
                <input value="<?php if( isset( $_POST['name_season'] ) and empty( $ok ) ){ echo stripslashes( wp_strip_all_tags( $_POST['name_season'] ) ); }else{ echo $season_name;  } ?>" type="text" name="name_season" placeholder="<?php _e("Name", TRMOVIES); ?>">
            </label>
            <label class="Inprgt">
                <span><?php _e("Air date", TRMOVIES); ?></span>
                <input value="<?php if( isset( $_POST['air_date_season'] ) and empty( $ok ) ){ echo stripslashes( wp_strip_all_tags( $_POST['air_date_season'] ) ); }elseif($air_date!=''){ echo $air_date; } ?>" type="text" name="air_date_season" placeholder="<?php _e("Air date", TRMOVIES); ?>">
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

                wp_editor($content_season, 'content_season', $settings);
            ?>
        </div>
        
        <div class="Blkcn">
            <p class="Title"><?php _e('Poster', TRMOVIES); ?></p>
            <p class="InpFlImg" id="tr_poster">
                <?php if($img==''){ ?>
                <img style="display:none" id="img-poster-season" alt="">
                <?php }else{ ?>
                <img id="img-poster-season" src="<?php echo $img; ?>?nocache=<?php echo microtime(); ?>" alt="">                
                <?php } ?>
                <input id="inp-season" type="file" name="poster_season">
                <span><?php _e("Click here for upload image", TRMOVIES); ?></span>
            </p>
        </div>
          
        <input type="hidden" name="season_id" value="<?php echo $season_info->term_id; ?>">
        <button class="BtnSnd Alt" type="submit" name="edit_season"><?php _e('Save changes', TRMOVIES); ?></button>