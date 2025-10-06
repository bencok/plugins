<?php
if ( !defined('NONCE_KEY') ) die('Die!');

    $post = get_post( intval($_GET['id']) );

    $error = ''; $ok = ''; $content = ''; $content_season = ''; $get = '';

    $upload_dir = wp_upload_dir();

    $seasons_list = wp_get_post_terms($post->ID, 'seasons', array('orderby' => 'meta_value_num', 'order' => 'ASC', 'fields' => 'all', 'meta_query' => [[
    'key' => 'season_number',
    'type' => 'NUMERIC',
  ]],) );

    $episodes_list = wp_get_post_terms($post->ID, 'episodes', array('orderby' => 'meta_value_num', 'order' => 'ASC', 'fields' => 'all', 'meta_query' => [[
    'key' => 'episode_number',
    'type' => 'NUMERIC',
  ]],) );

    $ar_episode_create = array(); $ar_episode_ids_object = array(); $ar_season_ids_object = array();

    foreach ($episodes_list as &$array_create_episode) {
        preg_match('/'.sanitize_title($post->post_title).'-(\d+)[x{1}]/', $array_create_episode->slug, $matches);
        $ar_episode_ids_object[] = $array_create_episode->term_id;
        $ar_episode_create[] = get_term_meta($array_create_episode->term_id, 'season_number', true);
        $ar_episode_numbers[$array_create_episode->slug] = get_term_meta( $array_create_episode->term_id, 'season_number', true ).'x'.get_term_meta( $array_create_episode->term_id, 'episode_number', true );
    }

    foreach ($seasons_list as &$array_create_season) {
        $ar_season_ids_object[] = $array_create_season->term_id;
        $ar_season_numbers[$array_create_season->slug] = get_term_meta( $array_create_season->term_id, 'season_number', true );
    }

    $lst_seasons_episodes = array_unique($ar_episode_create);

    if(isset($_GET['season'])){ $get.='&season='.intval($_GET['season']); }
    if(isset($_GET['type'])){ $get.='&type='.intval($_GET['type']); }
    if(isset($_GET['episode'])){ $get.='&episode='.intval($_GET['episode']); }

    include( plugin_dir_path( __FILE__ ) . 'submit/tvshow_submit_episode_api.php');

    include( plugin_dir_path( __FILE__ ) . 'submit/tvshow_submit_season_api.php');    

    include( plugin_dir_path( __FILE__ ) . 'submit/tvshow_submit_edit.php');

    include( plugin_dir_path( __FILE__ ) . 'submit/tvshow_submit_season.php');

    if(isset($_GET['msj']) and $_GET['msj']==1){

        $ok='<p class="msjadm-ok">'.__('The tv show was added.', TRMOVIES).'</p>';

    }

    if(isset($_GET['msj']) and $_GET['msj']==2){

        $ok='<p class="msjadm-ok">'.__('The tv show was updated.', TRMOVIES).'</p>';

    }

    if(isset($_GET['msj']) and $_GET['msj']==3){
        
        $ok='<p class="msjadm-ok">'.__('The season was added.', TRMOVIES).'</p>';
                    
    }

    if(isset($_GET['msj']) and $_GET['msj']==4){
        
        $ok='<p class="msjadm-ok">'.__('The season was updated.', TRMOVIES).'</p>';
                    
    }

    if(isset($_GET['msj']) and $_GET['msj']==5){
        
        $ok='<p class="msjadm-ok">'.__('The season was deleted.', TRMOVIES).'</p>';
                    
    }

    if(isset($_GET['msj']) and $_GET['msj']==6){
        
        $ok='<p class="msjadm-ok">'.__('The episode was added.', TRMOVIES).'</p>';
        
    }

    if(isset($_GET['msj']) and $_GET['msj']==7){
        
        $ok='<p class="msjadm-ok">'.__('The episode was updated.', TRMOVIES).'</p>';
        
    }

    if(isset($_GET['msj']) and $_GET['msj']==8){
        
        $ok='<p class="msjadm-ok">'.__('The episode was deleted.', TRMOVIES).'</p>';
        
    }

    if(isset($_POST['delmult_season'])){

        if(empty($_POST['del_sel'])){
            
            $error.='<p class="msjadm-error">'.__('You have not selected any items.', TRMOVIES).'</p>';
            
        }
        
        if(empty($error)){
                        
            foreach ($_POST['del_sel'] as &$value) {
                wp_delete_term( intval($value), 'seasons' );
            }
            
            $nseasons = get_post_meta($post->ID, TR_MOVIES_FIELD_NSEASONS, true);
            
            update_post_meta($post->ID, TR_MOVIES_FIELD_NSEASONS, $nseasons-count($_POST['del_sel']));
            
            echo '
                <script type="text/javascript">
                    window.location = "'.admin_url('admin.php?page=tr-movies-tv&action=edit&id='.$post->ID.'&delok=1').'";
                </script>
            ';
            
        }
        
    }

    if(isset($_POST['delmult_episode'])){

        if(empty($_POST['del_sel_episodes'])){
            
            $error.='<p class="msjadm-error">'.__('You have not selected any items.', TRMOVIES).'</p>';
            
        }
        
        if(empty($error)){
                        
            foreach ($_POST['del_sel_episodes'] as &$value) {
                wp_delete_term( intval($value), 'episodes' );
            }
            
            $nepisodes = get_post_meta($post->ID, TR_MOVIES_FIELD_NEPISODES, true);
            
            update_post_meta($post->ID, TR_MOVIES_FIELD_NEPISODES, $nepisodes-count($_POST['del_sel_episodes']));
            
            echo '
                <script type="text/javascript">
                    window.location = "'.admin_url('admin.php?page=tr-movies-tv&action=edit&id='.$post->ID.'&delok=1').'";
                </script>
            ';
            
        }
        
    }

    if(isset($_GET['delok'])){
        
        $ok = '<p class="msjadm-ok">'.__('It was successfully deleted.', TRMOVIES).'</p>';
        
    }

    if( isset($_POST['content_season']) and empty($ok) ){

        $content_season = $_POST['content_season'];

    }

    $post_categories = wp_get_post_categories( $post->ID );
    $content = $post->post_content;
    $term_cast = wp_get_post_terms($post->ID, 'cast_tv', array("fields" => "names"));
    $term_directors = wp_get_post_terms($post->ID, 'directors_tv', array("fields" => "names"));
    $term_tags = wp_get_post_terms($post->ID, 'post_tag', array("fields" => "names"));
    $term_cats = wp_get_post_terms($post->ID, 'category', array("fields" => "ids"));
    $in_production = get_post_meta($post->ID, TR_MOVIES_FIELD_INPRODUCTION, true);

?>

    <section>

        <div class="Top">
            <h1><?php _e("Edit Tv show", TRMOVIES); ?></h1>
            <?php tr_movies_menu(2); ?>
        </div>
        
        <?php /*
        <div id="lgtbx-seo" class="lgtbx">
            <div class="lgtbxcn">
                <span class="lgtbclose dashicons dashicons-no-alt"></span>
                <iframe width="100%" height="800px" src="trhide=1"></iframe>
            </div>
            <div class="lgtblyr"></div>
        </div>*/?>

        <ul class="Blkcn TbsBxCn">

            <li class="tr_tab<?php if(!isset($_POST['submit_season']) and !isset($_GET['type']) and !isset($_POST['submit_episode'])){ ?> current<?php } ?>" data-tab="tr_movies_tab_1"><button type="button"><?php _e('Edit Details', TRMOVIES); ?></button></li>

            <li class="tr_tab<?php if(isset($_POST['submit_season']) or isset($_GET['season'])){ ?> current<?php } ?>" data-tab="tr_movies_tab_3"><button type="button"><?php _e('Seasons', TRMOVIES); ?></button></li>

            <?php if(!empty($seasons_list)){ ?>

            <li class="tr_tab<?php if(isset($_POST['submit_episode']) or isset($_GET['type']) and $_GET['type']==2){ ?> current<?php } ?>" data-tab="tr_movies_tab_4"><button type="button"><?php _e('Episodes', TRMOVIES); ?></button></li>

            <?php } ?>
            
            <?php if(!empty($episodes_list)){ ?>
                        
            <li class="tr_tab" data-tab="tr_movies_tab_5"><button type="button"><?php _e('Links', TRMOVIES); ?></button></li>
            
            <?php } ?>

            <li class="EdtPstLnk">
                <a target="_blank" href="<?php echo admin_url('post.php?post='.$post->ID.'&action=edit'); ?>"><?php _e('Edit Tv Show', TRMOVIES); ?></a>
            </li>
            <?php if($post->post_status=='publish'){ ?>
            <li class="EdtPstLnk ViewPstLnk">
                <a target="_blank" href="<?php echo get_permalink($post->ID); ?>"><?php _e('View', TRMOVIES); ?></a>
            </li>
            <?php } ?>
        </ul>

        <?php echo $ok.$error; ?>

        <form action="<?php echo admin_url('admin.php?page=tr-movies-tv&action=edit&id='.$post->ID.$get); ?>" method="post" enctype="multipart/form-data">

           

            <div id="tr_movies_tab_1" class="tr_movies_tab AdmCls"<?php if(isset($_POST['submit_season']) or isset($_GET['type']) or isset($_POST['submit_episode'])){ ?> style="display:none"<?php } ?>>

                <main>

                    <p class="Blkcn TtlInp">

                        <input value="<?php if( isset( $_POST['title'] ) and empty( $ok ) ){ echo stripslashes( wp_strip_all_tags( $_POST['title'] ) ); }else{ echo $post->post_title; } ?>" type="text" name="title" placeholder="<?php _e("Tv show name", TRMOVIES); ?>">

                    </p>

                    <div class="Blkcn">

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

                                <label><input<?php if (isset($term_cats) and in_array($category->term_id, $term_cats)) { ?> checked<?php } ?> name="categories[]" value="<?php echo $category->term_id; ?>" type="checkbox"> <?php echo $category->name; ?></label>

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

                            <span class="Title"><?php _e('Trailer', TRMOVIES); ?><i class="dashicons dashicons-format-video"></i></span>

                            <textarea class="txtara" name="trailer" placeholder="<?php _e('Insert code iframe here', TRMOVIES); ?>"><?php if( isset( $_POST['trailer'] ) and empty( $ok ) ){ echo stripslashes( $_POST['trailer'] );  }elseif( get_post_meta($post->ID, TR_MOVIES_FIELD_TRAILER, true)!='' ){ echo stripslashes( get_post_meta($post->ID, TR_MOVIES_FIELD_TRAILER, true) ); } ?></textarea>

                        </label>

                    </div>

                </main>

                <aside>

                   

                    <div class="Blkcn PrdctnBx">

                        <p class="Title"><?php _e('In production', TRMOVIES); ?></p>

                        <ul class="StsOptns">

                            <li><input <?php if(isset($_POST['in_production'])){ checked( $_POST['in_production'], 1 ); }else{ checked($in_production, 1); } ?> type="radio" name="in_production" value="1"><span><?php _e('Yes', TRMOVIES); ?></span></li>

                            <li><input <?php if(isset($_POST['in_production'])){ checked( $_POST['in_production'], 2 ); }else{ checked($in_production, 2); } ?> type="radio" name="in_production" value="2"><span><?php _e('No', TRMOVIES); ?></span></li>

                        </ul>

                    </div>

                   

                    <div class="Blkcn">

                        <ul class="StsOptns">

                            <li><input <?php checked( $post->post_status, 'publish' ); ?> type="radio" name="status_post" value="publish"><span><?php _e('Published', TRMOVIES); ?></span></li>

                            <li><input <?php checked( $post->post_status, 'pending' ); ?> type="radio" name="status_post" value="pending"><span><?php _e('Pending', TRMOVIES); ?></span></li>

                            <li><input <?php checked( $post->post_status, 'draft' ); ?> type="radio" name="status_post" value="draft"><span><?php _e('Draft', TRMOVIES); ?></span></li>

                        </ul>

                    </div>

                    <div class="Blkcn">

                        <p class="Title"><?php _e("Original name", TRMOVIES); ?></p>

                        <input value="<?php if( isset( $_POST['original_title'] ) and empty( $ok ) ){ echo stripslashes( wp_strip_all_tags( $_POST['original_title'] ) ); }elseif( get_post_meta($post->ID, TR_MOVIES_FIELD_ORIGINALTITLE, true)!='' ){ echo get_post_meta($post->ID, TR_MOVIES_FIELD_ORIGINALTITLE, true); } ?>" type="text" name="original_title" placeholder="<?php _e("Original name", TRMOVIES); ?>">

                    </div>

                    <div class="Blkcn Blkcnjs">

                        <p class="Title"><?php _e('Cast', TRMOVIES); ?></p>

                        

                        <?php

                            $lst_ar_cast = array();

                            if($term_cast!=''){

                                foreach ($term_cast as &$valor) {

                                    echo'<input class="norepeat'.sanitize_title($valor).'" type="hidden" value="'.$valor.'" name="trc_cast[]">';

                                    $lst_ar_cast[]= '<span>'.$valor.' <i class="dashicons dashicons-no-alt del_tr_suggest" data-input="'.sanitize_title($valor).'"></i></span>';

                                }

                            }

                        ?>

                        

                        <div class="Lstslct" id="cnt_cast">

                        

                           <?php if($lst_ar_cast!=''){ echo implode(',', $lst_ar_cast); } ?>

                        

                        </div>

                        

                        <input value="" id="trc_cast" type="text" name="cast" autocomplete="off">

                        

                        <script type="text/javascript">



                            jQuery(document).ready(function($) {

                                $("#trc_cast").suggest(ajaxurl + "?action=ajax-tag-search&tax=cast_tv", {multiple:true, multipleSep: ","});

                            });



                        </script>

                    </div>

                    <div class="Blkcn Blkcnjs">

                        <p class="Title"><?php _e('Directors', TRMOVIES); ?></p>                        

                        

                        <?php

                            $lst_ar_directors = array();

                            if($term_directors!=''){

                                foreach ($term_directors as &$valor) {

                                    echo'<input class="norepeat'.sanitize_title($valor).'" type="hidden" value="'.$valor.'" name="trc_directors[]">';

                                    $lst_ar_directors[]= '<span>'.$valor.' <i class="dashicons dashicons-no-alt del_tr_suggest" data-input="'.sanitize_title($valor).'"></i></span>';

                                }

                            }

                        ?>

                        

                        <div class="Lstslct" id="cnt_directors">

                        

                           <?php if($lst_ar_directors!=''){ echo implode(',', $lst_ar_directors); } ?>

                        

                        </div>

                        

                        <input value="" id="trc_directors" type="text" name="directors" autocomplete="off">

                        

                        <script type="text/javascript">



                            jQuery(document).ready(function($) {

                                $("#trc_directors").suggest(ajaxurl + "?action=ajax-tag-search&tax=directors_tv", {multiple:true, multipleSep: ","});

                            });



                        </script>

                    </div>

                    <div class="Blkcn Blkcnjs">

                        <p class="Title"><?php _e('Tags', TRMOVIES); ?></p>

                                                

                        <?php

                            $lst_ar_tags = array();

                            if($term_tags!=''){

                                foreach ($term_tags as &$valor) {

                                    echo'<input class="norepeat'.sanitize_title($valor).'" type="hidden" value="'.$valor.'" name="trc_tags[]">';

                                    $lst_ar_tags[]= '<span>'.$valor.' <i class="dashicons dashicons-no-alt del_tr_suggest" data-input="'.sanitize_title($valor).'"></i></span>';

                                }

                            }

                        ?>

                        

                        <div class="Lstslct" id="cnt_tags">

                        

                           <?php if($lst_ar_tags!=''){ echo implode(',', $lst_ar_tags); } ?>

                        

                        </div>

                        

                        <input value="" id="trc_tags" type="text" name="tags" autocomplete="off">

                        

                        <script type="text/javascript">



                            jQuery(document).ready(function($) {

                                $("#trc_tags").suggest(ajaxurl + "?action=ajax-tag-search&tax=post_tag", {multiple:true, multipleSep: ","});

                            });



                        </script>

                    </div>

                    <div class="Blkcn">

                        <p class="Title"><?php _e("Info", TRMOVIES); ?></p>

                        <label class="Inprgt">

                            <span><?php _e("Duration", TRMOVIES); ?></span>

                            <input value="<?php if( isset( $_POST['duration'] ) and empty( $ok ) ){ echo stripslashes( wp_strip_all_tags( $_POST['duration'] ) ); }elseif( get_post_meta($post->ID, TR_MOVIES_FIELD_RUNTIME, true)!='' ){ if(is_array(get_post_meta($post->ID, TR_MOVIES_FIELD_RUNTIME, true))){ echo implode(', ', get_post_meta($post->ID, TR_MOVIES_FIELD_RUNTIME, true)); }else{ echo get_post_meta($post->ID, TR_MOVIES_FIELD_RUNTIME, true); } } ?>" type="text" name="duration" placeholder="<?php _e("Duration", TRMOVIES); ?>">

                        </label>

                        <label class="Inprgt">

                            <span><?php _e("First air date", TRMOVIES); ?></span>

                            <input value="<?php if( isset( $_POST['first_air_date'] ) and empty( $ok ) ){ echo stripslashes( wp_strip_all_tags( $_POST['first_air_date'] ) ); }elseif( get_post_meta($post->ID, TR_MOVIES_FIELD_DATE, true)!='' ){ echo get_post_meta($post->ID, TR_MOVIES_FIELD_DATE, true); } ?>" type="text" name="first_air_date" placeholder="<?php _e("First air date", TRMOVIES); ?>">

                        </label>

                        <label class="Inprgt">

                            <span><?php _e("Last air date", TRMOVIES); ?></span>

                            <input value="<?php if( isset( $_POST['last_air_date'] ) and empty( $ok ) ){ echo stripslashes( wp_strip_all_tags( $_POST['last_air_date'] ) ); }elseif( get_post_meta($post->ID, TR_MOVIES_FIELD_DATE_LAST, true)!='' ){ echo get_post_meta($post->ID, TR_MOVIES_FIELD_DATE_LAST, true); } ?>" type="text" name="last_air_date" placeholder="<?php _e("Last air date", TRMOVIES); ?>">

                        </label>

                       <?php /*
                        <label class="Inprgt">

                            <span class="altlbl"><?php _e("Number of episodes", TRMOVIES); ?></span>

                            <input value="<?php if( isset( $_POST['number_of_episodes'] ) and empty( $ok ) ){ echo stripslashes( wp_strip_all_tags( $_POST['number_of_episodes'] ) ); }elseif( get_post_meta($post->ID, TR_MOVIES_FIELD_NEPISODES, true)!='' ){ echo get_post_meta($post->ID, TR_MOVIES_FIELD_NEPISODES, true); } ?>" type="text" name="number_of_episodes" placeholder="<?php _e("Number of episodes", TRMOVIES); ?>">

                        </label>*/ ?>

                        <label class="Inprgt">

                            <span><?php _e("Status", TRMOVIES); ?></span>

                            <input value="<?php if( isset( $_POST['status'] ) and empty( $ok ) ){ echo stripslashes( wp_strip_all_tags( $_POST['status'] ) ); }elseif( get_post_meta($post->ID, TR_MOVIES_FIELD_STATUS, true)!='' ){ echo get_post_meta($post->ID, TR_MOVIES_FIELD_STATUS, true); } ?>" type="text" name="status" placeholder="<?php _e("Status", TRMOVIES); ?>">

                        </label>

                    </div>

                    <div class="Blkcn">
                        <p class="Title"><?php _e('Poster', TRMOVIES); ?></p>
                        <p class="InpFlImg" id="tr_poster">
                            <?php if(get_the_post_thumbnail_url( $post->ID, 'thumbnail' )!=''){ ?>
                            <img id="img-poster" src="<?php echo get_the_post_thumbnail_url( $post->ID, 'thumbnail' ); ?>" alt="">
                            <?php }elseif(get_post_meta($post->ID, TR_MOVIES_POSTER_HOTLINK, true)!=''){ ?>
                            <img id="img-poster" src="<?php echo '//image.tmdb.org/t/p/w342'.get_post_meta($post->ID, TR_MOVIES_POSTER_HOTLINK, true); ?>" alt="">                            
                            <?php }else{ ?>
                            <img id="img-poster" src="<?php echo TR_MOVIES_PLUGIN_URL; ?>assets/img/noimgb.png" alt="" />
                            <?php } ?>
                            <input id="inp-poster" type="file" name="poster">
                            <span><?php _e("Click here for upload image", TRMOVIES); ?></span>
                        </p>
                    </div>
                    <div class="Blkcn BackdropImg">
                        <p class="Title"><?php _e('Backdrop', TRMOVIES); ?></p>
                        <p class="InpFlImg" id="tr_backdrop">
                            <?php
                            $backdrop = wp_get_attachment_image_src(get_post_meta( $post->ID, TR_MOVIES_FIELD_BACKDROP, true ), 'full');
                            if($backdrop[0]!=''){
                                echo '<img id="img-backdrop" src="'.$backdrop[0].'" alt="backdrop">';
                            }elseif(get_post_meta($post->ID, 'backdrop_hotlink', true)!=''){
                                echo '<img id="img-backdrop" src="//image.tmdb.org/t/p/w780'.get_post_meta($post->ID, 'backdrop_hotlink', true).'" alt="backdrop">';                     
                            }else{
                            ?>
                            <img id="img-backdrop" src="<?php echo TR_MOVIES_PLUGIN_URL; ?>assets/img/noimgb.png" alt="" />
                            <?php
                            }
                            ?>
                            <input type="file" name="backdrop" id="inp-backdrop">
                            <span><?php _e("Click here for upload image", TRMOVIES); ?></span>
                        </p>
                    </div>

                    <button class="BtnSnd" type="submit" name="submit"><?php _e('Save changes', TRMOVIES); ?></button>

                </aside>

            </div>

            

            <?php include( TR_MOVIES_PLUGIN_DIR . 'inc/tvshows/season/show_season.php'); ?>

            <?php include( TR_MOVIES_PLUGIN_DIR . 'inc/tvshows/episode/show_episode.php'); ?>

            <?php include( TR_MOVIES_PLUGIN_DIR . 'inc/tvshows/links.php'); ?>
           
            <input type="hidden" name="id_post" value="<?php echo $post->ID; ?>">

        </form>

    </section>