<?php
if ( !defined('NONCE_KEY') ) die('Die!');
    if(isset($_GET['type'])){ $_GET['type'] = $_GET['type']; }else{ $_GET['type'] = ''; }

    if(isset($_GET['delepisode'])){
        
        $nepisodes = get_post_meta(intval($_GET['id']), TR_MOVIES_FIELD_NEPISODES, true);

        update_post_meta(intval($_GET['id']), TR_MOVIES_FIELD_NEPISODES, $nepisodes-1);
        
        $season_epid_del = get_term_meta(intval($_GET['delepisode']), 'season_number', true);
                
        $term_list_seasons = wp_get_post_terms($post->ID, 'seasons', array("fields" => "all"));
        if(!is_wp_error($term_list_seasons) and !empty($term_list_seasons)) {

            $season_id = '';

            foreach ($term_list_seasons as &$term_list_season) {

                if(get_term_meta($term_list_season->term_id, 'season_number', true)==$season_epid_del){

                    $season_id = $term_list_season->term_id;

                }
            }
            
        }
        
        $term_list_episodes = wp_get_post_terms($post->ID, 'episodes', array("fields" => "all"));
        if(!is_wp_error($term_list_episodes) and !empty($term_list_episodes)){

            update_post_meta($post->ID, TR_MOVIES_FIELD_NEPISODES, count($term_list_episodes));

            if($season_id!=''){

                $array_episodes_season[] = '';

                foreach ($term_list_episodes as &$count_episode_season) {

                    if(get_term_meta($count_episode_season->term_id, 'season_number', true)==$season_epid_del){

                        $array_episodes_season[] = $count_episode_season->term_id;

                    }

                }

                update_term_meta($season_id, 'number_of_episodes', count($array_episodes_season)-2);

            }

        }
        
        wp_delete_term( intval($_GET['delepisode']), 'episodes' );
        
        
        echo '
            <script type="text/javascript">
                window.location = "'.admin_url('admin.php?page=tr-movies-tv&action=edit&id='.intval($_GET['id'])).'&msj=8";
            </script>
        ';
                
    }
?>
    <div id="tr_movies_tab_4" class="tr_movies_tab" <?php if(!isset($_POST['submit_episode']) and $_GET['type']!=2){ ?>style="display:none"<?php } ?>>               
            <?php

                if(isset($_GET['type']) and $_GET['type']==2) {

                    include( TR_MOVIES_PLUGIN_DIR . 'inc/tvshows/episode/edit_episode.php');

                }else{

                    include( TR_MOVIES_PLUGIN_DIR . 'inc/tvshows/episode/add_episode.php');

                }

            ?>
        
        <div id="tr_list_episodes">
            <?php
                        
                foreach($episodes_list as $ep_s) {

                        $seasons_avaible[] = get_term_meta($ep_s->term_id, 'season_number', true);
                        
                }
            
                if(empty($seasons_avaible)){ $seasons_avaible = array(); }
                        
                if(empty($episodes_list)){
                    echo '<p class="trnocontent msjadm-error">'.__('You must add episodes this season.', TRMOVIES).'</p>';
                }else{
                foreach($seasons_list as $season_single) {
                                                            
                if (in_array(get_term_meta($season_single->term_id, 'season_number', true), array_unique($seasons_avaible))) {
                    
            ?>
            <div class="Blkcn AACrdn EpsdAcdn">
                <h2 class="AALink"><?php echo $season_single->name; ?> <i class="dashicons dashicons-arrow-down-alt2"></i></h2>
                <table class="ToroPlay-tbl">
                    <thead>
                        <tr>
                            <th><input data-id="select-all-episode<?php echo $season_single->term_id; ?>" class="select-all select-all-episode<?php echo $season_single->term_id; ?>" type="checkbox"></th>
                            <th><?php _e('NAME', TRMOVIES); ?></th>
                            <th><?php _e('OPTIONS', TRMOVIES); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            foreach($episodes_list as $episode_single) {
                                if(get_term_meta($episode_single->term_id, 'season_number', true)==get_term_meta($season_single->term_id, 'season_number', true)){
                        ?>
                        <tr>
                            <td><input name="del_sel_episodes[]" value="<?php echo $episode_single->term_id; ?>" class="select-all-episode<?php echo $season_single->term_id; ?>" type="checkbox"></td>
                            <td><?php echo __('Episode', TRMOVIES).' '.get_term_meta( $episode_single->term_id, 'episode_number', true ); ?></td>
                            <td><a class="edtlnk" href="admin.php?page=tr-movies-tv&action=edit&id=<?php echo $post->ID; ?>&type=2&episode=<?php echo $episode_single->term_id; ?>"><i class="dashicons dashicons-edit"></i><?php _e('Edit', TRMOVIES); ?></a><a onclick="return confirm('<?php _e('Are you sure?', TRMOVIES); ?>')" class="dltlnk" href="admin.php?page=tr-movies-tv&action=edit&id=<?php echo intval($_GET['id']); ?>&delepisode=<?php echo $episode_single->term_id; ?>"><i class="dashicons dashicons-trash"></i><?php _e('Delete', TRMOVIES); ?></a></td>
                        </tr>
                        <?php
                                }
                            }
                        ?>
                    </tbody>
                </table>
            </div>
            <?php } ?>          
            <?php } } ?>            
        </div>          
        <button onclick="return confirm('<?php _e('Are you sure?', TRMOVIES); ?>');" name="delmult_episode" class="BtnSnd BtnStylA BtnFlR" type="submit"><?php _e('Clear Selected', TRMOVIES); ?></button>      
    </div>