<?php
if ( !defined('NONCE_KEY') ) die('Die!');

    $error = ''; $ok = '';

    $term_id = empty($_GET['edit']) ? intval($_POST['edit']) : intval($_GET['edit']);

    $term = get_term( $term_id, 'cast' );

    if(isset($_POST['submit_edit_cast'])){
                        
        if(empty($_POST['name'])){
            
            $error.='<p class="msjadm-error">'.__('The name field is empty.', TRMOVIES).'</p>';
            
        }else{
        
            $parent_term = term_exists( stripslashes( wp_strip_all_tags( $_POST['name'] ) ), 'cast' );
            
            if(!empty($parent_term) and $parent_term['term_id']!=$term->term_id){
                
                $error.='<p class="msjadm-error">'.__('The actor you introduced already exists.', TRMOVIES).'</p>';

            }
            
        }
                                
        if(empty($error)){
            
            wp_update_term($term->term_id, 'cast', array(
              'name' => stripslashes( wp_strip_all_tags( $_POST['name'] )),
              'slug' => stripslashes( wp_strip_all_tags( $_POST['name'] ))
            ));
            
            if(!empty($_FILES['poster']['tmp_name'])){
                                
                $term_id = $term->term_id;

                $upload_dir = wp_upload_dir();
                
                $ext = pathinfo($_FILES['poster']['name'], PATHINFO_EXTENSION);
                                
                $image_150 = wp_get_image_editor( $_FILES['poster']['tmp_name'] );

                if ( ! is_wp_error( $image_150 ) ) {
                    $image_150->resize( 150, 150, true );
                    $image_150->save( $upload_dir['path'].'/cast-'.$term_id.'.'.$ext );
                }

                $image_40 = wp_get_image_editor($_FILES['poster']['tmp_name'] );

                if ( ! is_wp_error( $image_40 ) ) {
                    $image_40->resize( 40, 40, true );
                    $image_40->save( $upload_dir['path'].'/cast-'.$term_id.'-40.'.$ext );
                }

                $check = getimagesize($upload_dir['path'].'/cast-'.$term_id.'.'.$ext);

                if(isset($check[1])){
                    update_term_meta($term_id, 'image', $upload_dir['url'].'/cast-'.$term_id.'.'.$ext);
                    update_term_meta($term_id, 'image_40', $upload_dir['url'].'/cast-'.$term_id.'-40.'.$ext);
                }

            }
                        
            $ok='<p class="msjadm-ok">'.__('The actor was updated successfully.', TRMOVIES).'</p>';
            
        }
        
    }
?>
<main>
    <form action="admin.php?page=tr-movies-movie&action=cast" method="post" class="Blkcn" enctype="multipart/form-data">
        <p class="Title"><?php _e('Edit Actor', TRMOVIES); ?> <span>(<a target="_blank" href="term.php?taxonomy=cast&tag_ID=<?php echo $term->term_id; ?>"><?php _e('Edit term', TRMOVIES); ?></a>)</span></p>

        <label class="Inprgt">
            <span><?php _e('Name'); ?></span>
            <input value="<?php if(isset($_POST['name'])){ echo stripslashes( wp_strip_all_tags( $_POST['name'] )); }else{ echo $term->name; } ?>" name="name" type="text" placeholder="<?php _e('Name of actor', TRMOVIES); ?>">
        </label>
        
        <p class="InpFlImg img_actor">
            <?php
                $image = get_term_meta($term_id, 'image', true);
                $image_hotlink = get_term_meta($term_id, 'image_hotlink', true);
                if($image=='' and $image_hotlink==''){
            ?>
            <img id="img-poster" src="<?php echo TR_MOVIES_PLUGIN_URL; ?>assets/img/noimgb.png" alt="" />
            <?php }elseif($image_hotlink!='' and $image==''){ ?>
                <img id="img-poster" src="<?php echo '//image.tmdb.org/t/p/w185'.$image_hotlink; ?>" alt="" />
            <?php }else{ ?>
            <img id="img-poster" src="<?php echo $image; ?>?cache=<?php echo microtime(); ?>" alt="" />            
            <?php } ?>
            <input type="file" name="poster" id="inp-poster">
            <span><?php _e("Click here for upload image", TRMOVIES); ?></span>
        </p>
        
        <input type="hidden" name="edit" value="<?php echo $term->term_id; ?>">

        <p><button class="BtnSnd BtnStylA BtnFlR" name="submit_edit_cast" type="submit"><?php _e('Save changes', TRMOVIES); ?></button></p>

    </form>
</main>