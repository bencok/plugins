<?php
if ( !defined('NONCE_KEY') ) die('Die!');

    $error = ''; $ok = '';

    if(isset($_POST['submit_categories'])){
                        
        if(empty($_POST['name'])){
            
            $error.='<p class="msjadm-error">'.__('The name field is empty.', TRMOVIES).'</p>';
            
        }else{
        
            $parent_term = term_exists( stripslashes( wp_strip_all_tags( $_POST['name'] ) ), 'category' );

            if(!empty($parent_term)){
                
                $error.='<p class="msjadm-error">'.__('The category you introduced already exists.', TRMOVIES).'</p>';

            }
            
        }
                                
        
        if(empty($error)){
            
            wp_insert_term( stripslashes( wp_strip_all_tags( $_POST['name'] ) ), 'category' );
            
            $ok='<p class="msjadm-ok">'.__('The category was added successfully.', TRMOVIES).'</p>';
            
        }
        
    }
?>
<main>
    <form action="admin.php?page=tr-movies-movie&action=categories" method="post" class="Blkcn">
        <p class="Title"><?php _e('Add Category', TRMOVIES); ?></p>

        <label class="Inprgt">
            <span><?php _e('Name'); ?></span>
            <input name="name" type="text" placeholder="<?php _e('Name of category', TRMOVIES); ?>">
        </label>

        <p><button class="BtnSnd BtnStylA BtnFlR" name="submit_categories" type="submit"><?php _e('Add category', TRMOVIES); ?></button></p>

    </form>
</main>