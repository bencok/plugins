<?php
if ( !defined('NONCE_KEY') ) die('Die!');
global $post;
?>
    <input name="tr_post_id_links" type="hidden" value="<?php echo intval($_GET['id']); ?>">
    <div id="tr_movies_tab_5" class="tr_movies_tab tr-season-tab Blkcn" style="display:none">    
  <div class="Title"><strong><?php _e('SEASONS', TRMOVIES); ?></strong></div>
        <div class="links_options">
            <ul class="AATbNv">
                <?php
                    $iseason=1;
                    foreach ($seasons_list as &$value) {
                ?>
                    <li data-Tab="Season<?php echo $iseason; ?>"<?php if($iseason==1){ echo' class="on"'; } ?>><?php echo get_term_meta($value->term_id, 'season_number', true); ?></li>
                <?php
                        $iseason++;
                    }
                ?>
            </ul>
        </div>
        <div class="Title"><i class="dashicons dashicons-playlist-video"></i><strong><?php _e('Episodes', TRMOVIES); ?></strong> <button type="button" class="BtnLinks tr_link_save"><?php _e('Save Links', TRMOVIES); ?></button></div>
        <?php
            $iseason=1;
                
            $servers = get_categories( array(
                'orderby' => 'name',
                'hide_empty' => 0,
                'taxonomy' => 'server'
            ) );
        
            $languages = get_categories( array(
                'orderby' => 'name',
                'hide_empty' => 0,
                'taxonomy' => 'language'
            ) );
        
            $qualitys = get_categories( array(
                'orderby' => 'name',
                'hide_empty' => 0,
                'taxonomy' => 'quality'
            ) );
        
        
            foreach ($seasons_list as &$value) {
                $eseason_number = get_term_meta( $value->term_id, 'season_number', true );
        ?>
        <div id="Season<?php echo $iseason; ?>" class="Season AATbCn<?php if($iseason==1){ echo' on'; } ?>">
                        
            <?php
                
                        if($iseason==1){
                
                    if(!in_array($eseason_number, $lst_seasons_episodes)){ 
                        echo '<p>'.__('You must add the episodes of this season before.', TRMOVIES).'</p>';
                    }

                
                $iepisodes_list = 1;
                foreach ($episodes_list as &$value_episode) {
                    if(get_term_meta($value_episode->term_id, 'season_number', true)==get_term_meta($value->term_id, 'season_number', true)){
                        
                    $links[$value_episode->term_id]=unserialize(get_term_meta($value_episode->term_id, TR_MOVIES_FIELD_LINK, true));                        
            ?>
            <div class="AACrdn">
                <div class="Title AALink<?php if($iepisodes_list==1){ echo' on'; } ?>">Episode <span><?php echo $iepisodes_list; ?></span> <i class="dashicons dashicons-arrow-down-alt2"></i></div>
                <div class="AAcont">
                    <button type="button" class="Btnmore tr_link_add" data-class="tr-series-row-<?php echo $iseason.'-'.$iepisodes_list; ?>"><i class="dashicons dashicons-plus-alt"></i> <?php _e('Add link', TRMOVIES); ?></button>
                    <div class="ToroPlay-tblcn">
                        <table class="ToroPlay-tbl">
                            <thead>
                                <tr>
                                    <th><?php _e('TYPE', TRMOVIES); ?></th>
                                    <th><?php _e('SERVER', TRMOVIES); ?></th>
                                    <th><?php _e('LANGUAGE', TRMOVIES); ?></th>
                                    <th><?php _e('QUALITY', TRMOVIES); ?></th>
                                    <th><?php _e('LINK', TRMOVIES); ?></th>
                                    <th><?php _e('DATE', TRMOVIES); ?></th>
                                    <th colspan="2"></th>
                                </tr>
                            </thead>
                            <tbody>
                               
                                <?php
                        
                                if(count(unserialize($links[$value_episode->term_id]['type']))>0){

                                    for($ilnk = 0; $ilnk < count(unserialize($links[$value_episode->term_id]['type'])); ++$ilnk) {
                                        
                                ?>                                    

                                <tr class="tr-series-row-<?php echo $iseason.'-'.$iepisodes_list; ?>">
                                    <td>
                                        <select class="trmoviesget" name="trmovies|<?php echo $value_episode->term_id; ?>[type][]">
                                            <option value=""><?php _e('Select', TRMOVIES); ?></option>
                                            <option <?php selected( unserialize($links[$value_episode->term_id]['type'])[$ilnk], 1 ); ?> value="1"><?php _e('Online', TRMOVIES); ?></option>
                                            <option <?php selected( unserialize($links[$value_episode->term_id]['type'])[$ilnk], 2 ); ?> value="2"><?php _e('Download', TRMOVIES); ?></option>
                                        </select>
                                    </td>
                                    <td>
                                        <select class="trmoviesget" name="trmovies|<?php echo $value_episode->term_id; ?>[server][]">
                                            <option value=""><?php _e('Select', TRMOVIES); ?></option>
                                            <?php
                                                foreach ( $servers as $server ) {
                                                    echo '<option '.selected( unserialize($links[$value_episode->term_id]['server'])[$ilnk], $server->term_id, false ).' value="'.$server->term_id.'">'.$server->name.'</option>';
                                                }
                                            ?>
                                        </select>
                                    </td>
                                    <td>
                                        <select class="trmoviesget" name="trmovies|<?php echo $value_episode->term_id; ?>[lang][]">
                                            <option value=""><?php _e('Select', TRMOVIES); ?></option>
                                            <?php
                                            foreach ( $languages as $lang ) {
                                                echo '<option '.selected( unserialize($links[$value_episode->term_id]['lang'])[$ilnk], $lang->term_id, false ).' value="'.$lang->term_id.'">'.$lang->name.'</option>';
                                            }
                                            ?>
                                        </select>
                                    </td>
                                    <td>
                                        <select class="trmoviesget" name="trmovies|<?php echo $value_episode->term_id; ?>[quality][]">
                                            <option value=""><?php _e('Select', TRMOVIES); ?></option>
                                            <?php
                                            foreach ( $qualitys as $quality ) {
                                                echo '<option '.selected( unserialize($links[$value_episode->term_id]['quality'])[$ilnk], $quality->term_id, false ).' value="'.$quality->term_id.'">'.$quality->name.'</option>';
                                            }
                                            ?>
                                        </select>
                                    </td>
                                    <td><input class="trmoviesget" name="trmovies|<?php echo $value_episode->term_id; ?>[link][]" type="text" value="<?php echo unserialize($links[$value_episode->term_id]['link'])[$ilnk]; ?>"></td>
                                    <td><input class="trmoviesget" type="text" name="trmovies|<?php echo $value_episode->term_id; ?>[date][]" value="<?php echo unserialize($links[$value_episode->term_id]['date'])[$ilnk]; ?>"></td>
                                    <td><button data-class="tr-series-row-<?php echo $iseason.'-'.$iepisodes_list; ?>" type="button" class="BtnTrpy tr_link_remove"><i class="dashicons dashicons-dismiss"></i><?php _e('Delete', TRMOVIES); ?></button></td>
                                    <td>
                                        <a href="#" class="BtnTrpy move-up tr-move-up"><i class="dashicons dashicons-arrow-up"></i><?php _e('Up', TRMOVIES); ?></a>
                                        <a href="#" class="BtnTrpy move-down tr-move-down"><i class="dashicons dashicons-arrow-down"></i><?php _e('Down', TRMOVIES); ?></a>
                                    </td>
                                </tr>
                                
                                <?php
                                }
                                
                                }else{
                                ?>
                               
                                <tr class="tr-series-row-<?php echo $iseason.'-'.$iepisodes_list; ?>">
                                    <td>
                                        <select class="trmoviesget" name="trmovies|<?php echo $value_episode->term_id; ?>[type][]">
                                            <option value=""><?php _e('Select', TRMOVIES); ?></option>
                                            <option value="1"><?php _e('Online', TRMOVIES); ?></option>
                                            <option value="2"><?php _e('Download', TRMOVIES); ?></option>
                                        </select>
                                    </td>
                                    <td>
                                        <select class="trmoviesget" name="trmovies|<?php echo $value_episode->term_id; ?>[server][]">
                                            <option value=""><?php _e('Select', TRMOVIES); ?></option>
                                            <?php
                                                foreach ( $servers as $server ) {
                                                    echo '<option '.selected( $trmovieslinks[$i]['server'], $server->term_id, false ).' value="'.$server->term_id.'">'.$server->name.'</option>';
                                                }
                                            ?>
                                        </select>
                                    </td>
                                    <td>
                                        <select class="trmoviesget" name="trmovies|<?php echo $value_episode->term_id; ?>[lang][]">
                                            <option value=""><?php _e('Select', TRMOVIES); ?></option>
                                            <?php
                                            foreach ( $languages as $lang ) {
                                                echo '<option '.selected( $trmovieslinks[$i]['lang'], $lang->term_id, false ).' value="'.$lang->term_id.'">'.$lang->name.'</option>';
                                            }
                                            ?>
                                        </select>
                                    </td>
                                    <td>
                                        <select class="trmoviesget" name="trmovies|<?php echo $value_episode->term_id; ?>[quality][]">
                                            <option value=""><?php _e('Select', TRMOVIES); ?></option>
                                            <?php
                                            foreach ( $qualitys as $quality ) {
                                                echo '<option '.selected( $trmovieslinks[$i]['quality'], $quality->term_id, false ).' value="'.$quality->term_id.'">'.$quality->name.'</option>';
                                            }
                                            ?>
                                        </select>
                                    </td>
                                    <td><input class="trmoviesget" name="trmovies|<?php echo $value_episode->term_id; ?>[link][]" type="text"></td>
                                    <td><input class="trmoviesget" type="text" name="trmovies|<?php echo $value_episode->term_id; ?>[date][]"></td>
                                    <td><button data-class="tr-series-row-<?php echo $iseason.'-'.$iepisodes_list; ?>" type="button" class="BtnTrpy tr_link_remove"><i class="dashicons dashicons-dismiss"></i><?php _e('Delete', TRMOVIES); ?></button></td>
                                    <td>
                                        <a href="#" class="BtnTrpy move-up tr-move-up"><i class="dashicons dashicons-arrow-up"></i><?php _e('Up', TRMOVIES); ?></a>
                                        <a href="#" class="BtnTrpy move-down tr-move-down"><i class="dashicons dashicons-arrow-down"></i><?php _e('Down', TRMOVIES); ?></a>
                                    </td>
                                </tr>
                                
                                <?php } ?>
                                
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php
                $iepisodes_list++;
                    }
                }
                            
                }else{ _e('Loading...', TRMOVIES); }
            ?>

        </div>
        <?php $iseason++; } ?>
    
    </div>