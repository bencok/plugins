<?php
if ( !defined('NONCE_KEY') ) die('Die!');

    $error = ''; $ok = ''; $content = '';

    include( plugin_dir_path( __FILE__ ) . 'submit/movie_submit_api.php');
    include( plugin_dir_path( __FILE__ ) . 'submit/movie_submit.php');

    if(isset($_POST['trmovie_api']) and empty($_POST['trmovie_id']) and TR_MOVIES_API_KEY!=''){
        
        $error.= '<p class="msjadm-error">'.__('You must enter ID.', TRMOVIES).'</p>';
        
    }

    if(TR_MOVIES_API_KEY==''){
        
        $error.= '<p class="msjadm-error">'.__('You must set up an API Key, <a href="admin.php?page=tr-movies">click here</a>.', TRMOVIES).'</p>';
        
    }


?>
    
    <section>
        <div class="Top">
            <h1><?php _e('Add Movie', TRMOVIES); ?></h1>
            <?php tr_movies_menu(); ?>
        </div>

        
        <?php echo $ok.$error; ?>
        <form action="<?php echo admin_url('admin.php?page=tr-movies-movie&action=add'); ?>" method="post" enctype="multipart/form-data">
            <div class="AdmCls">
                <main>
                    <div id="tmdb_trmovies" class="tridfrm">
                        <label for="trmovie_id">
                            <span>TMDB <span>#ID</span></span>
                            <input name="trmovie_id" id="trmovie_id" type="text" value="">
                            <button name="trmovie_api" class="tr_movies_go"><i class="dashicons dashicons-yes"></i><?php _e('Go', TRMOVIES); ?></button>
                        </label>
                        <span class="ttp" style="display:inline">
                            <span><span><?php _e('EXAMPLE', TRMOVIES); ?></span>https://www.themoviedb.org/movie/<strong>284052</strong>-doctor-strange</span>
                            <i class="dashicons dashicons-warning"></i>
                        </span>
                    </div>

                    <p class="Blkcn TtlInp"><input<?php if( isset( $_POST['title'] ) and empty( $ok ) ){ echo' value="'.stripslashes( wp_strip_all_tags( $_POST['title'] ) ).'"'; } ?> type="text" name="title" placeholder="<?php _e("Movie's name", TRMOVIES); ?>"></p>

                    <div class="Blkcn Blkcnjs">
                        <p class="Title"><?php _e("Categories", TRMOVIES); ?></p>
                                
                        <ul class="ListCheck Clfx">
                            <?php
                                $categories = get_categories( array(
                                    'orderby' => 'name',
                                    'hide_empty'  => 0,
                                ) );

                                foreach ( $categories as $category ) {
                            ?>

                                <li>
                                    <label><input<?php if (isset($_POST['categories']) and in_array($category->term_id, $_POST['categories'])) { ?> checked<?php } ?> name="categories[]" value="<?php echo $category->term_id; ?>" type="checkbox"> <?php echo $category->name; ?></label>
                                </li>
                            <?php
                                }
                            ?>
                        </ul>
                    </div>            
                    
                    <div class="Blkcn">
                        <p class="Title ttlmbnt"><?php _e('Synopsis', TRMOVIES); ?></p>
                        <?php
                            $settings = array(
                                'textarea_rows' => 15,
                                'tabindex' => 1,
                                'media_buttons' => true
                            );

                            wp_editor($content, 'content', $settings);
                        ?>
                    </div>

                    <div class="Blkcn TrailerBx">
                        <label>
                            <span class="Title"><?php _e('Trailer', TRMOVIES); ?> <i class="dashicons dashicons-format-video"></i></span>
                            <textarea class="txtara" name="trailer" placeholder="<?php _e('Insert code iframe here', TRMOVIES); ?>"><?php if( isset( $_POST['trailer'] ) and empty( $ok ) ){ echo stripslashes( $_POST['trailer'] ); } ?></textarea>
                        </label>
                    </div>
                    
                        
                </main>
                <aside>
                    
                    <div class="Blkcn OrglNm">
                        <p class="Title"><?php _e("Original name", TRMOVIES); ?></p>
                        <input<?php if( isset( $_POST['original_title'] ) and empty( $ok ) ){ echo' value="'.stripslashes( wp_strip_all_tags( $_POST['original_title'] ) ).'"'; } ?> type="text" name="original_title" placeholder="<?php _e("Original name", TRMOVIES); ?>">
                    </div>
                    
                    <div class="Blkcn Blkcnjs">
                        <p class="Title"><?php _e('Cast', TRMOVIES); ?></p>
                        <?php
                            $cast_post = ''; $input_cast_post = '';
                            if( isset( $_POST['trc_cast'] ) and empty( $ok ) ){

                                foreach ($_POST['trc_cast'] as &$value) {
                                    $cast_post.= '<span>'.$value.' <i class="dashicons dashicons-no-alt del_tr_suggest" data-input="'.sanitize_title($value).'"></i></span>';
                                    $input_cast_post.= '<input class="norepeat'.sanitize_title($value).'" type="hidden" value="'.$value.'" name="trc_cast[]">';
                                }
                            }
                            echo $input_cast_post;
                        ?>

                        <div class="Lstslct" id="cnt_cast">
                            <?php echo $cast_post; ?>
                        </div>
                        <input type="text" name="cast" id="trc_cast">
                        <script type="text/javascript">

                            jQuery(document).ready(function($) {
                                $("#trc_cast").suggest(ajaxurl + "?action=ajax-tag-search&tax=cast", {multiple:true, multipleSep: ","});
                            });

                        </script>
                    </div>
                    
                    <div class="Blkcn Blkcnjs">
                        <p class="Title"><?php _e('Directors', TRMOVIES); ?></p>
                        <?php
                            $directors_post = ''; $input_directors_post = '';
                            if( isset( $_POST['trc_directors'] ) and empty( $ok ) ){

                                foreach ($_POST['trc_directors'] as &$value) {
                                    $directors_post.= '<span>'.$value.' <i class="dashicons dashicons-no-alt del_tr_suggest" data-input="'.sanitize_title($value).'"></i></span>';
                                    $input_directors_post.= '<input class="norepeat'.sanitize_title($value).'" type="hidden" value="'.$value.'" name="trc_directors[]">';
                                }
                            }
                            echo $input_directors_post;
                        ?>
                        <div class="Lstslct" id="cnt_directors">
                            <?php echo $directors_post; ?>
                        </div>
                        <input id="trc_directors" type="text" name="directors">
        
                        <script type="text/javascript">

                            jQuery(document).ready(function($) {
                                $("#trc_directors").suggest(ajaxurl + "?action=ajax-tag-search&tax=directors", {multiple:true, multipleSep: ","});
                            });

                        </script>
                    </div>
                    
                    <div class="Blkcn Blkcnjs">
                        <p class="Title"><?php _e('Tags', TRMOVIES); ?></p>
                        <?php
                            $tags_post = ''; $input_tags_post = '';
                            if( isset( $_POST['trc_tags'] ) and empty( $ok ) ){

                                foreach ($_POST['trc_tags'] as &$value) {
                                    $tags_post.= '<span>'.$value.' <i class="dashicons dashicons-no-alt del_tr_suggest" data-input="'.sanitize_title($value).'"></i></span>';
                                    $input_tags_post.= '<input class="norepeat'.sanitize_title($value).'" type="hidden" value="'.$value.'" name="trc_tags[]">';
                                }
                            }
                            echo $input_tags_post;
                        ?>
                        <div class="Lstslct" id="cnt_tags">
                            <?php echo $tags_post; ?>
                        </div>
                        <input id="trc_tags" type="text" name="tags">
                        <script type="text/javascript">

                            jQuery(document).ready(function($) {
                                $("#trc_tags").suggest(ajaxurl + "?action=ajax-tag-search&tax=post_tag", {multiple:true, multipleSep: ","});
                            });

                        </script>
                    </div>
                    
                    <div class="Blkcn Blkcnjs">
                        <p class="Title"><?php _e('Countries', TRMOVIES); ?></p>
                        <?php
                            $countries_post = ''; $input_countries_post = '';
                            if( isset( $_POST['trc_countries'] ) and empty( $ok ) ){

                                foreach ($_POST['trc_countries'] as &$value) {
                                    $countries_post.= '<span>'.$value.' <i class="dashicons dashicons-no-alt del_tr_suggest" data-input="'.sanitize_title($value).'"></i></span>';
                                    $input_countries_post.= '<input class="norepeat'.sanitize_title($value).'" type="hidden" value="'.$value.'" name="trc_countries[]">';
                                }
                            }
                            echo $input_countries_post;
                        ?>
                        <div class="Lstslct" id="cnt_countries">
                            <?php echo $countries_post; ?>
                        </div>
                        <input id="trc_countries" type="text" name="countries">
                        <script type="text/javascript">

                            jQuery(document).ready(function($) {
                                $("#trc_countries").suggest(ajaxurl + "?action=ajax-tag-search&tax=country", {multiple:true, multipleSep: ","});
                            });

                        </script>
                    </div>
                    
                    <div class="Blkcn">
                        <p class="Title"><?php _e("Info", TRMOVIES); ?></p>
                        <label class="Inprgt">
                            <span><?php _e("Duration", TRMOVIES); ?></span>
<input<?php if( isset( $_POST['duration'] ) and empty( $ok ) ){ echo' value="'. wp_strip_all_tags( $_POST['duration'] ) .'"'; } ?> type="text" name="duration" placeholder="<?php _e("Duration", TRMOVIES); ?>">
                        </label>
                                                    
                        <label class="Inprgt">
                            <span><?php _e("Release data", TRMOVIES); ?></span>
                            <input<?php if( isset( $_POST['release'] ) and empty( $ok ) ){ echo' value="'.stripslashes( wp_strip_all_tags( $_POST['release'] ) ).'"'; } ?> type="text" name="release" placeholder="<?php _e("Release data", TRMOVIES); ?>">
                        </label>
                    </div>

                    <div class="Blkcn">
                        <p class="Title"><?php _e('Poster', TRMOVIES); ?></p>
                        <p class="InpFlImg">
                            <img id="img-poster" src="<?php echo TR_MOVIES_PLUGIN_URL; ?>assets/img/noimgb.png" alt="" />
                            <input type="file" name="poster" id="inp-poster">
                            <span><?php _e("Click here for upload image", TRMOVIES); ?></span>
                        </p>
                    </div>

                    <div class="Blkcn BackdropImg">
                        <p class="Title"><?php _e('Backdrop', TRMOVIES); ?></p>
                        <p class="InpFlImg">
                            <img id="img-backdrop" src="<?php echo TR_MOVIES_PLUGIN_URL; ?>assets/img/noimgb.png" alt="" />
                            <input type="file" name="backdrop" id="inp-backdrop">
                            <span><?php _e("Click here for upload image", TRMOVIES); ?></span>
                        </p>                        
                    </div>
                    
                    <button class="BtnSnd" type="submit" name="submit"><i class="dashicons dashicons-plus-alt"></i> <?php _e('Add Movie', TRMOVIES); ?></button>
                </aside>
            </div>

        </form>

    </section>
