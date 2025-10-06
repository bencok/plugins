<?php
if ( !defined('NONCE_KEY') ) die('Die!');

    $error = ''; $ok = '';

    $term_id = empty($_GET['edit']) ? intval($_POST['edit']) : intval($_GET['edit']);

    $term = get_term( $term_id, 'directors_tv' );

    if(isset($_POST['submit_edit_director'])){
                        
        if(empty($_POST['name'])){
            
            $error.='<p class="msjadm-error">'.__('The name field is empty.', TRMOVIES).'</p>';
            
        }else{
        
            $parent_term = term_exists( stripslashes( wp_strip_all_tags( $_POST['name'] ) ), 'directors_tv' );

            if(!empty($parent_term)){
                
                $error.='<p class="msjadm-error">'.__('The director you introduced already exists.', TRMOVIES).'</p>';

            }
            
        }
                                
        if(empty($error)){
            
            wp_update_term($term->term_id, 'directors_tv', array(
              'name' => stripslashes( wp_strip_all_tags( $_POST['name'] )),
              'slug' => stripslashes( wp_strip_all_tags( $_POST['name'] ))
            ));
                        
            $ok='<p class="msjadm-ok">'.__('The director was updated successfully.', TRMOVIES).'</p>';
            
        }
        
    }
?>
<main>
    <form action="admin.php?page=tr-movies-tv&action=directors" method="post" class="Blkcn">
        <p class="Title"><?php _e('Edit Director', TRMOVIES); ?> <span>(<a target="_blank" href="term.php?taxonomy=directors_tv&tag_ID=<?php echo $term->term_id; ?>"><?php _e('Edit term', TRMOVIES); ?></a>)</span></p>

        <label class="Inprgt">
            <span><?php _e('Name'); ?></span>
            <input value="<?php if(isset($_POST['name'])){ echo stripslashes( wp_strip_all_tags( $_POST['name'] )); }else{ echo $term->name; } ?>" name="name" type="text" placeholder="<?php _e('Name of director', TRMOVIES); ?>">
        </label>
        
        <input type="hidden" name="edit" value="<?php echo $term->term_id; ?>">

        <p><button class="BtnSnd BtnStylA BtnFlR" name="submit_edit_director" type="submit"><?php _e('Save changes', TRMOVIES); ?></button></p>

    </form>
</main>