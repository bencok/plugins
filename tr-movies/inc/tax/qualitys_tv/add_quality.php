<?php
if ( !defined('NONCE_KEY') ) die('Die!');

    $error = ''; $ok = '';

    if(isset($_POST['submit_quality'])){
                        
        if(empty($_POST['name'])){
            
            $error.='<p class="msjadm-error">'.__('The name field is empty.', TRMOVIES).'</p>';
            
        }else{
        
            $parent_term = term_exists( stripslashes( wp_strip_all_tags( $_POST['name'] ) ), 'quality' );

            if(!empty($parent_term)){
                
                $error.='<p class="msjadm-error">'.__('The quality you introduced already exists.', TRMOVIES).'</p>';

            }
            
        }
                                
        
        if(empty($error)){
            
            wp_insert_term( stripslashes( wp_strip_all_tags( $_POST['name'] ) ), 'quality' );
            
            $ok='<p class="msjadm-ok">'.__('The quality was added successfully.', TRMOVIES).'</p>';
            
        }
        
    }
?>
<main>
    <form action="admin.php?page=tr-movies-tv&action=links&action2=qualitys" method="post" class="Blkcn">
        <p class="Title"><?php _e('Add Quality', TRMOVIES); ?></p>

        <label class="Inprgt">
            <span><?php _e('Name'); ?></span>
            <input name="name" type="text" placeholder="<?php _e('Name of quality', TRMOVIES); ?>">
        </label>

        <p><button class="BtnSnd BtnStylA BtnFlR" name="submit_quality" type="submit"><?php _e('Add quality', TRMOVIES); ?></button></p>

    </form>
</main>