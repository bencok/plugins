<?php
if ( !defined('NONCE_KEY') ) die('Die!');
    $ok = ''; $error = '';
?>
<section>

    <div class="Top">
        <h1><?php _e('Languages', TRMOVIES); ?></h1>
        <?php tr_movies_menu(2); ?>
    </div>
        
    <div class="AdmCls AltClBx">
        <?php
            if(empty($_GET['edit']) and empty($_POST['edit'])){
                include( plugin_dir_path( __FILE__ ) . '/add_language.php');
            }else{
                include( plugin_dir_path( __FILE__ ) . '/edit_language.php');                
            }
        ?>
        <?php include( plugin_dir_path( __FILE__ ) . '/del_language.php'); ?>
        <aside>
            <?php echo $error.$ok; ?>
            <form action="<?php echo admin_url('admin.php?page=tr-movies-tv&action=links&action2=languages'); ?>" method="post">
                <div class="TPTblCn TblCnAdm">
                    <?php
                        $paged = isset( $_GET['paged'] ) ? intval($_GET['paged']) : 1;

                        $per_page = 10;

                        $number = count(get_terms('language', array( 'hide_empty' => false )));
                        $offset = ( $paged - 1 ) * $per_page;

                        $term_args = array(
                            'number' => $per_page,
                            'offset' => $offset,
                            'hide_empty' => false
                        );
                        $terms = get_terms('language', $term_args);

                        if ($terms) {
                    ?>
                    <table>
                        <thead>
                            <tr>
                                <th><input id="select-all" value="" type="checkbox"></th>
                                <th class="ThClA"><?php echo $number.' '; _e('Results', TRMOVIES); ?></th>
                                <th><?php _e('Options', TRMOVIES); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                foreach ($terms as $term) {
                                    echo '
                                        <tr>
                                            <td><input value="'.$term->term_id.'" type="checkbox" name="del[]"></td>
                                            <td class="TdClB">'.$term->name.'</td>
                                            <td class="td1ln"><a class="edtlnk" href="admin.php?page=tr-movies-tv&action=links&action2=languages&edit='.$term->term_id.'"><i class="dashicons dashicons-edit"></i>'.__('Edit', TRMOVIES).'</a><a onclick="return confirm(\''.__('Are you sure?', TRMOVIES).'\');" class="dltlnk" href="admin.php?page=tr-movies-tv&action=links&action2=languages&del='.$term->term_id.'"><i class="dashicons dashicons-trash"></i>'.__('Delete', TRMOVIES).'</a></td>
                                        </tr>
                                    ';
                                }                  
                            ?>
                        </tbody>
                    </table>
                    <?php
                        }else{
                            _e('Add your first language :)', TRMOVIES);
                        }
                    ?>
                </div>
                <?php
                    $big = 999999999;

                    echo '<div class="wp-pagenavi">'.paginate_links(array(
                        'base' => 'admin.php?paged='.str_replace($big, '%#%', $big),
                        'format' => '?paged=%#%',
                        'current' => $paged,
                        'total' => ceil($number / $per_page)
                    )).'</div>';
                if ($terms) {
                ?>
                <button onclick="return confirm('<?php _e('Are you sure?', TRMOVIES); ?>');" name="delmult" class="BtnSnd BtnStylA BtnFlR" type="submit"><?php _e('Clear Selected', TRMOVIES); ?></button>
                <?php } ?>

            </form>
        </aside>
    </div>
        
    
</section>