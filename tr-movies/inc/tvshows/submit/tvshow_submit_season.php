<?php
if ( !defined('NONCE_KEY') ) die('Die!');

    $error = ''; $ok = ''; $content = ''; $content_season = '';

    $upload_dir = wp_upload_dir();

    if( isset($_POST['submit_season']) ) { // add season
        
        $post_id = intval( $_POST['tr_post_id_links'] ) ;

        $post_name = get_the_title( $post_id );

        if(empty($_POST['name_season'])){

            $error.='<p class="msjadm-error">'.__("You must enter season's name.", TRMOVIES).'</p>';

        }

        if(empty($_POST['season_number']) and !intval($_POST['season_number'])){

            $error.='<p class="msjadm-error">'.__("You must enter season's number.", TRMOVIES).'</p>';

        }
        
        if(empty($_POST['season_number']) and !intval($_POST['season_number'])){

            $error.='<p class="msjadm-error">'.__("You must enter episodes's number.", TRMOVIES).'</p>';

        }
        
        if(!empty($_POST['name_season']) and !empty($_POST['season_number']) and intval($_POST['season_number']) ){

            $parent_term = term_exists( $post_name.' '.intval($_POST['season_number']), 'seasons' );
            $parent_term_id = $parent_term['term_id'];

            if($parent_term_id==''){
                $cid = wp_insert_term($post_name.' '.intval($_POST['season_number']), 'seasons');
                $parent_term_id = $cid['term_id'];
            }else{

                $error.='<p class="msjadm-error">'.__('This season was already added.', TRMOVIES).'</p>';

            }
            
        }

        if(empty($error)) {

            $poster_path_seasons = '';
            
            if(!empty( $_FILES['poster_season']['tmp_name'] ) ){
                
                if ( is_rtl() ) { $title_img = md5(sanitize_title($post_name)); }else{ $title_img = sanitize_title($post_name); }

                move_uploaded_file($_FILES['poster_season']['tmp_name'], $upload_dir['path'].'/'.$title_img.'-'.$parent_term_id.'-season-'.intval($_POST['season_number']).'.jpg');

                $image = wp_get_image_editor( $upload_dir['path'].'/'.$title_img.'-'.$parent_term_id.'-season-'.intval($_POST['season_number']).'.jpg' );

                if ( ! is_wp_error( $image ) ) {
                    $image->resize( 800, 562, false );
                    $image->save( $upload_dir['path'].'/'.$title_img.'-'.$parent_term_id.'-season-'.intval($_POST['season_number']).'.jpg' );
                }

                $attachment_season = array(
                    'guid' => $upload_dir['path'].'/'.$title_img.'-'.$parent_term_id.'-season-'.intval($_POST['season_number']).'.jpg', 
                    'post_mime_type' => 'image/jpeg',
                    'post_title' => $title_img.'-'.$parent_term_id.'-season-'.intval($_POST['season_number']).'.jpg',
                    'post_content' => '',
                    'post_status' => 'inherit'
                );

                $attach_id_season = wp_insert_attachment( $attachment_season, $upload_dir['path'].'/'.$title_img.'-'.$parent_term_id.'-season-'.intval($_POST['season_number']).'.jpg',$post_id);

                $attach_data_season = wp_generate_attachment_metadata( $attach_id_season, $upload_dir['path'].'/'.$title_img.'-'.$parent_term_id.'-season-'.intval($_POST['season_number']).'.jpg' );
                wp_update_attachment_metadata( $attach_id_season, $attach_data_season );

                $poster_path_seasons = $attach_id_season;

            }

            if(isset($_POST['air_date_season'])){ $_POST['air_date_season'] = $_POST['air_date_season']; }else{ $_POST['air_date_season'] = ''; }

            if(isset($_POST['content_season'])){ $_POST['content_season'] = $_POST['content_season']; }else{ $_POST['content_season'] = ''; }

            //if(isset($_POST['number_of_episodes_season'])){ $_POST['number_of_episodes_season'] = $_POST['number_of_episodes_season']; }else{ $_POST['number_of_episodes_season'] = ''; }

            $array_seasons = array(

                'season_number' => intval($_POST['season_number']),
                'air_date' => stripslashes( wp_strip_all_tags( $_POST['air_date_season'] ) ),
                'name' => stripslashes( wp_strip_all_tags( $_POST['name_season'] ) ),
                'id' => '',
                'overview' => stripslashes( $_POST['content_season'] ),
                'poster_path_hotlink' => '',
                'poster_path' => $poster_path_seasons,
                //'number_of_episodes' => intval($_POST['number_of_episodes_season']),

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
            if(!empty($array_seasons['number_of_episodes'])){
                update_term_meta($parent_term_id, 'number_of_episodes', 0);
            }
            update_term_meta($parent_term_id, 'no_api', 1);
            
            update_term_meta($parent_term_id, 'tr_id_post', $post_id);
            
            $seasons_list = wp_get_post_terms($post_id, 'seasons', array("fields" => "ids"));
            
            if(!empty($seasons_list)){ $terms_ids = array_merge( $seasons_list, array($parent_term_id) );  }else{ $terms_ids = array($parent_term_id); }
            
            wp_set_object_terms($post_id, $terms_ids, 'seasons', true);
            
            $term_list_season = wp_get_post_terms($post_id, 'seasons', array("fields" => "all"));
            if(!is_wp_error($term_list_season) and !empty($term_list_season)){

                update_post_meta($post_id, TR_MOVIES_FIELD_NSEASONS, count($term_list_season));
                
            }
            
            
            echo '
                <script type="text/javascript">
                    window.location = "'.admin_url('admin.php?page=tr-movies-tv&action=edit&msj=3&id='.$post_id).'";
                </script>
            ';
            
        }

    }

    // add season

?>