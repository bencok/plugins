<?php
if ( !defined('NONCE_KEY') ) die('Die!');

    $error = ''; $ok = '';

    $term_id = empty($_GET['edit']) ? intval($_POST['edit']) : intval($_GET['edit']);

    $term = get_term( $term_id, 'language' );

    if(isset($_POST['submit_edit_language'])){
                        
        if(empty($_POST['name'])){
            
            $error.='<p class="msjadm-error">'.__('The name field is empty.', TRMOVIES).'</p>';
            
        }else{
        
            $parent_term = term_exists( stripslashes( wp_strip_all_tags( $_POST['name'] ) ), 'language' );
            
            if(!empty($parent_term) and $parent_term['term_id']!=$term->term_id){
                
                $error.='<p class="msjadm-error">'.__('The language you introduced already exists.', TRMOVIES).'</p>';

            }
            
        }
                                
        if(empty($error)){
            
            wp_update_term($term->term_id, 'language', array(
              'name' => stripslashes( wp_strip_all_tags( $_POST['name'] )),
              'slug' => stripslashes( wp_strip_all_tags( $_POST['name'] ))
            ));
            
            if(!empty($_FILES['poster']['tmp_name'])){
                
                $upload_dir = wp_upload_dir();
                $ext = pathinfo($_FILES['poster']['name'], PATHINFO_EXTENSION);

                move_uploaded_file($_FILES["poster"]["tmp_name"], $upload_dir['path'].'/language-'.$term->term_id.$ext);

                $filetype=wp_check_filetype($_FILES["poster"]["name"]);

                $attachment = array(
                    'guid' => $upload_dir['url'].'/language-'.$term->term_id.$ext, 
                    'post_mime_type' => $filetype['type'],
                    'post_title' => '',
                    'post_content' => '',
                    'post_status' => 'inherit',
                );
                
                $attach_id = wp_insert_attachment( $attachment, $upload_dir['path'].'/language-'.$term->term_id.$ext, '');

                require_once(ABSPATH . 'wp-admin/includes/image.php');
                
                $attach_data = wp_generate_attachment_metadata( $attach_id, $upload_dir['path'].'/language-'.$term->term_id.$ext);
                wp_update_attachment_metadata( $attach_id, $attach_data );
                
                update_term_meta($term_id, 'image', $attach_id);

            }
                        
            $ok='<p class="msjadm-ok">'.__('The language was updated successfully.', TRMOVIES).'</p>';
            
        }
        
    }
?>
<main>
    <form action="admin.php?page=tr-movies-tv&action=links&action2=languages" method="post" class="Blkcn" enctype="multipart/form-data">
        <p class="Title"><?php _e('Edit Language', TRMOVIES); ?> <span>(<a target="_blank" href="term.php?taxonomy=language&tag_ID=<?php echo $term->term_id; ?>"><?php _e('Edit term', TRMOVIES); ?></a>)</span></p>

        <label class="Inprgt">
            <span><?php _e('Name'); ?></span>
            <input value="<?php if(isset($_POST['name'])){ echo stripslashes( wp_strip_all_tags( $_POST['name'] )); }else{ echo $term->name; } ?>" name="name" type="text" placeholder="<?php _e('Name of server', TRMOVIES); ?>">
        </label>
        
        <p class="InpFlImg img_actor">
            <?php
                $image = get_term_meta($term_id, 'image', true);
                if($image==''){
            ?>
            <img id="img-poster" src="<?php echo TR_MOVIES_PLUGIN_URL; ?>assets/img/noimgb.png" alt="" />
            <?php }else{ ?>
            <img id="img-poster" src="<?php echo wp_get_attachment_url($image); ?>?cache=<?php echo microtime(); ?>" alt="" />            
            <?php } ?>
            <input type="file" name="poster" id="inp-poster">
            <span><?php _e("Click here for upload image", TRMOVIES); ?></span>
        </p>
        
        <input type="hidden" name="edit" value="<?php echo $term->term_id; ?>">

        <p><button class="BtnSnd BtnStylA BtnFlR" name="submit_edit_language" type="submit"><?php _e('Save changes', TRMOVIES); ?></button></p>

    </form>
</main>