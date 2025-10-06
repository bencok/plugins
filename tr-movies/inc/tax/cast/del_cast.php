<?php
if ( !defined('NONCE_KEY') ) die('Die!');

    if(isset($_GET['del'])){
        
        wp_delete_term( intval($_GET['del']), 'cast' );
        
        echo '
            <script type="text/javascript">
                window.location = "'.admin_url('admin.php?page=tr-movies-movie&action=cast&delok=1').'";
            </script>
        ';
        
    }

    if(isset($_POST['delmult'])){

        if(empty($_POST['del'])){
            
            $error.='<p class="msjadm-error">'.__('You have not selected any items.', TRMOVIES).'</p>';
            
        }
        
        if(empty($error)){
                        
            foreach ($_POST['del'] as &$value) {
                wp_delete_term( intval($value), 'cast' );
            }
            
            echo '
                <script type="text/javascript">
                    window.location = "'.admin_url('admin.php?page=tr-movies-movie&action=cast&delok=1').'";
                </script>
            ';
            
        }
        
    }

    if(isset($_GET['delok'])){
        
        $ok = '<p class="msjadm-ok">'.__('It was successfully deleted.', TRMOVIES).'</p>';
        
    }

?>