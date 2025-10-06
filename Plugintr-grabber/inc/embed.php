<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$type = intval($_GET['playertype']);

$link = $type == 1 ? unserialize ( get_post_meta( intval(get_query_var('location')), 'trglinks_'.intval(get_query_var('AnimeEmbed')), true ) ) : unserialize ( get_term_meta( intval(get_query_var('location')), 'trglinks_'.intval(get_query_var('AnimeEmbed')), true ) );

$link = base64_decode( $link['link'] );

$a = array( '&amp;amp;lt;', '&amp;amp;quot;', '&amp;amp;gt;', '&amp;lt;', '&amp;quot;', '&amp;gt;', '&lt;', '&quot;', '&gt;' );
$b = array( '<', '"', '>', '<', '"', '>', '<', '"', '>' );
$link = str_replace( $a, $b, $link );

preg_match('#<iframe(.*?)></iframe>#is', html_entity_decode($link), $matches);

preg_match('/\[(.*?)\]/', html_entity_decode($link), $matshortcode);

if( isset($matshortcode[0]) ) {
    $link = do_shortcode( $matshortcode[0] );
}elseif( isset($matches[0]) ) {
    $link = html_entity_decode($link);
    preg_match('/<iframe.*src=\"(.*)\".*><\/iframe>/isU', $link, $matches);
    if( in_array( tr_grabber_get_domain_from_url($matches[1]), tr_grabber_frame_servers() ) ) {
        $link = str_replace($matches[1], esc_url( home_url( '/' ) ).'/?trhide=1&tid='.strrev(bin2hex($matches[1])).'&', $link);
    }
}else{
    if( in_array( tr_grabber_get_domain_from_url($link), tr_grabber_frame_servers() ) ) {
        $link = str_replace($link, esc_url( home_url( '/' ) ).'?trhide=1&tid='.strrev(bin2hex($link)).'&', $link);
    }
    $link = '<iframe width="560" height="315" src="'.$link.'" frameborder="0" allowfullscreen></iframe>';
}
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <title><?php _e('Embed', 'tr-grabber'); ?></title>
    <meta name="viewport" content="width=device-width,minimum-scale=1,initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <style>body,html,.Video,iframe{width: 100%;height: 100%;margin: 0;font-size: 0;}</style>
</head>
<body oncontextmenu="return false;">
    
    <div class="Video">  <?php
   $resposta = false;
   if (isset($_POST['g-recaptcha-response']) && !empty($_POST['g-recaptcha-response'])) {
      $captcha_data = $_POST['g-recaptcha-response'];
      $chave_secreta = 'CHAVE_SECRETA';   
      $resposta = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret=$chave_secreta&response=$captcha_data&remoteip='.$_SERVER['REMOTE_ADDR']);
   }
   if ($resposta) {
   ?><script src='https://content.jwplatform.com/libraries/Jq6HIbgz.js'></script><?php echo $link; ?><?php }?><?php include('popads'); ?>

<!-- 
<script src='https://cdn.jsdelivr.net/npm/js-cookie@2/src/js.cookie.min.js'></script>
<script src='https://demos.jwplayer.com/resume-playback-with-cookies/logger.js'></script> -->

   <?php
   if(!$resposta){
   ?>
   <script type="text/javascript"  src="https://serieson.org/blockad.js"></script>
      <script src='https://www.google.com/recaptcha/api.js'></script><div class='wrap'>
   <form method='post' onsubmit='return validar()'>
      <div class='g-recaptcha' data-callback='recaptchaCallback' data-sitekey='6LcYzEUdAAAAAOKGK4ltK_2g5b51RzDhOCAmiNUJ'></div>
      <br>
  <div id="ninth" class="buttonBox">
    <button>Liberar video</button>
  </div>
</form></div>

<style>

.wrap {
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
}

/* NINTH HOVER */

#ninth>button{
  transition:all .5s ease-in-out;
}

#ninth:hover button{
  background: #121212;
  color: #ffc000;
}
.buttonBox{
  position:relative;
  max-width: 200px;
  min-width: 150px;
  flex: 20%;
}

button{
  width: 300px;
  height:80px;
  position:relative;
  background: #181818;
  text-transform:uppercase;
  color:white;
  font-weight:700;
  letter-spacing:1px;
  border:none;
  font-size:15px;
  outline:none;
  font-family: "Lucida Sans Unicode", "Lucida Grande", sans-serif;
  cursor: pointer;
}
</style>
   </form>

   <script>
   googlerecpchk = false;
   function recaptchaCallback() {
      googlerecpchk = true;
   };

   function validar(){

      console.log(googlerecpchk);
      if(!googlerecpchk){
         alert('reCaptcha inválido!');
         return false;
      }

   }
   </script>

<?php }?>
</div>
<script id="aclib" type="text/javascript" src="//acscdn.com/script/aclib.js"></script>
<script type="text/javascript">
    aclib.runPop({
        zoneId: '7806334',
    });
</script>
<?php

function getAdsGeneratingJs($zoneId, $zoneType)
{
    $ch = curl_init("https://youradexchange.com/ad/s2sadblock.php?zone_id={$zoneId}&zone_type={$zoneType}&v=2");
    curl_setopt_array($ch, array(
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CONNECTTIMEOUT_MS => 100,
        CURLOPT_TIMEOUT_MS => 200,
    ));
    $javascriptCode = curl_exec($ch);
    curl_close($ch);

    // js to render in your web page
    return $javascriptCode;
}

echo getAdsGeneratingJs("7806334", "suv4");
?>
</body>
</html>