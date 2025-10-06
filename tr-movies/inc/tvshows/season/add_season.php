<?php
if ( !defined('NONCE_KEY') ) die('Die!');
?>
        <div class="Top">
            <h2><?php _e('Add season', TRMOVIES); ?></h2>
        </div>
        
        <div class="Blkcn">
            <p class="Title"><?php _e("Info", TRMOVIES); ?></p>
            <label class="Inprgt">
                <span><?php _e("Name", TRMOVIES); ?></span>
                <input<?php if( isset( $_POST['name_season'] ) and empty( $ok ) ){ echo' value="'.stripslashes( wp_strip_all_tags( $_POST['name_season'] ) ).'"'; } ?> type="text" name="name_season" placeholder="<?php _e("Name", TRMOVIES); ?>">
            </label>
            <label class="Inprgt">
                <span class="altlbl"><?php _e("Season number", TRMOVIES); ?></span>
                <input<?php if( isset( $_POST['season_number'] ) and empty( $ok ) ){ echo' value="'.stripslashes( wp_strip_all_tags( $_POST['season_number'] ) ).'"'; } ?> type="text" name="season_number" placeholder="<?php _e("Season number", TRMOVIES); ?>">
            </label>
            <label class="Inprgt">
                <span><?php _e("Air date", TRMOVIES); ?></span>
                <input<?php if( isset( $_POST['air_date_season'] ) and empty( $ok ) ){ echo' value="'.stripslashes( wp_strip_all_tags( $_POST['air_date_season'] ) ).'"'; } ?> type="text" name="air_date_season" placeholder="<?php _e("Air date", TRMOVIES); ?>">
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
                <img style="display:none" id="img-poster-season" src="" alt="">
                <input id="inp-season" type="file" name="poster_season">
                <span><?php _e("Click here for upload image", TRMOVIES); ?></span>
            </p>
        </div>
        <button class="BtnSnd Alt" type="submit" name="submit_season"><?php _e('Add season', TRMOVIES); ?></button>