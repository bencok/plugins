<?php

if ( !defined('NONCE_KEY') ) die('Die!');

    if(isset($_GET['delseason'])){
        
        // delete all episodes
        
        $seasons_list = wp_get_post_terms(intval($_GET['id']), 'episodes', array('order' => 'ASC', 'fields' => 'all', 'meta_query' => [[
            'key' => 'season_number',
            'compare' => '=',
            'value' => get_term_meta(intval($_GET['season']), 'season_number', true)
        ]],) );
        foreach ($seasons_list as &$value) {
            wp_delete_term( $value->term_id, 'episodes' );
        }
        
        // delete season
        
        wp_delete_term( intval($_GET['season']), 'seasons' );
        
        $nseasons = get_post_meta($post->ID, TR_MOVIES_FIELD_NSEASONS, true);

        update_post_meta($post->ID, TR_MOVIES_FIELD_NSEASONS, $nseasons-1);
                
        echo '
            <script type="text/javascript">
                window.location = "'.admin_url('admin.php?page=tr-movies-tv&action=edit&id='.intval($_GET['id'])).'&msj=5";
            </script>
        ';
                
    }

?>

    <div id="tr_movies_tab_3" class="tr_movies_tab"<?php if(!isset($_POST['submit_season']) and !isset($_GET['season'])){ ?> style="display:none"<?php } ?>>
           
            <?php
                $tmdbid = get_post_meta($post->ID, TR_MOVIES_FIELD_IMDBID, true);
                if($tmdbid!=''){
            ?>

            <button type="submit" name="trmovie_api_season" class="Btnmore"><i class="dashicons dashicons-plus-alt"></i> <?php if(empty($seasons_list)){ _e("Add season's (API)", TRMOVIES); }else{ _e("Update season's (API)", TRMOVIES); } ?></button>

            <input type="hidden" name="tmdbid" value="<?php echo $tmdbid; ?>">

            <?php } ?>

        <?php
            if(isset($_GET['type']) and $_GET['type']==1) {

                include( TR_MOVIES_PLUGIN_DIR . 'inc/tvshows/season/edit_season.php');

            }elseif(!isset($_GET['type']) or $_GET['type']==2){

                include( TR_MOVIES_PLUGIN_DIR . 'inc/tvshows/season/add_season.php');

            }
        ?>
        
        <?php if(!empty($seasons_list)){ ?>
       
        <div class="Top">
            <h2><?php _e('Seasons', TRMOVIES); ?></h2>
        </div>
        <div id="tr_list_seasons">
            <div class="Blkcn">
                <table class="ToroPlay-tbl">
                    <thead>
                        <tr>
                            <th><input data-id="sselect" class="select-all" type="checkbox"></th>
                            <th><?php _e('NAME', TRMOVIES); ?></th>
                            <th><?php _e('OPTIONS', TRMOVIES); ?></th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php
                            $tmdbid = get_post_meta($post->ID, TR_MOVIES_FIELD_IMDBID, true);
                            foreach($seasons_list as $season_single) {
                                $eseason_number = get_term_meta( $season_single->term_id, 'season_number', true );
                        ?>
                        <tr>
                            <td><input class="sselect" value="<?php echo $season_single->term_id; ?>" name="del_sel[]" type="checkbox"></td>
                            <td><?php echo __('Season', TRMOVIES).' '.$eseason_number; ?></td>
                            <td><?php if($tmdbid!='' and get_term_meta($season_single->term_id, 'no_api', true)==''){ ?><a class="updtepsd" href="admin.php?page=tr-movies-tv&action=edit&id=<?php echo $post->ID; ?>&tseason=<?php echo $eseason_number; ?>&tmdbid=<?php echo $tmdbid; ?>&autoepisodes=1"><i class="dashicons dashicons-update"></i><?php if(in_array($eseason_number, $lst_seasons_episodes)){ _e("Update episode's (API)", TRMOVIES); }else{ _e("Add episode's (API)", TRMOVIES); } ?></a> <?php } ?><a class="edtlnk" href="admin.php?page=tr-movies-tv&action=edit&id=<?php echo $post->ID; ?>&type=1&season=<?php echo $season_single->term_id; ?>"><i class="dashicons dashicons-edit"></i><?php _e('Edit', TRMOVIES); ?></a><a onclick="return confirm('<?php _e('Are you sure?', TRMOVIES); ?>')" class="dltlnk" href="admin.php?page=tr-movies-tv&action=edit&id=<?php echo $post->ID; ?>&season=<?php echo $season_single->term_id; ?>&type=1&delseason=1"><i class="dashicons dashicons-trash"></i><?php _e('Delete', TRMOVIES); ?></a></td>
                        </tr>
                        <?php
                            }
                        ?>
                    </tbody>
                </table>
                <button onclick="return confirm('<?php _e('Are you sure?', TRMOVIES); ?>');" name="delmult_season" class="BtnSnd BtnStylA BtnFlR" type="submit"><?php _e('Clear Selected', TRMOVIES); ?></button>
           </div>
        </div>
        <?php } ?>

    </div>