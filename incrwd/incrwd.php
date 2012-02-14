<?php
/*
Plugin Name: Incrwd Engagement Rewards System
Plugin URI: http://www.myincrwd.com/
Description: The Incrwd Engagement Rewards System adds an awesome rewards widget to your site that will measurably improve the metrics you care about the most.
Author: Incrwd <team@myincrwd.com>
Version: 3
Author URI: http://myincrwd.com/
*/

require_once(dirname(__FILE__) . '/build.php');
require_once(dirname(__FILE__) . '/lib/utils.php');
require_once(dirname(__FILE__) . '/incrwd-embed.php');
require_once(dirname(__FILE__) . '/lib/wp-api.php');
require_once(dirname(__FILE__) . '/lib/basic-info-api.php');

if (defined('INCRWD_LOCAL') && INCRWD_LOCAL) { // Incrwd defines this for local dev
  define('INCRWD_API_URL', 'http://incrwd.example.com/w/api/');
  define('INCRWD_JS_URL', '');
  define('WP_DEBUG', true);
  define('WP_DEBUG_DISPLAY', false);
  define('WP_DEBUG_LOG', true);
} else {
  define('INCRWD_API_URL', 'http://widget.myincrwd.com/w/api/');
  define('INCRWD_JS_URL', 'http://static.widget.myincrwd.com/incrwd.js');
}

function incrwd_options() {
  return array('incrwd_site_id',
               'incrwd_secret_key');
}

function incrwd_output_footer() {
  if (!get_option('incrwd_site_id')) {
    $api = new BasicInfoAPI(INCRWD_API_URL);
    $api->set_basic_info();
  }
  incrwd_embed(get_option('incrwd_site_id'), 
               defined('INCRWD_LOCAL') && INCRWD_LOCAL, 
               INCRWD_JS_URL, incrwd_sso());

  if (get_option('activate_share_widget')) {
    echo '<div id="fb-root"></div><script>(function(d, s, id) {
      var js, fjs = d.getElementsByTagName(s)[0];
      if (d.getElementById(id)) return;
      js = d.createElement(s); js.id = id;
      js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
      fjs.parentNode.insertBefore(js, fjs);
      }(document, "script", "facebook-jssdk"));</script>';
    echo '<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>';
    echo '<script type="text/javascript" src="https://apis.google.com/js/plusone.js" > {parsetags: "explicit"} </script>';
  }
}


function incrwd_manage() {
  // TODO: in future, check for needs update here.
  include_once(dirname(__FILE__) . '/manage.php');
}

function sharewidget_manage() {
  include_once(dirname(__FILE__) . '/manage-share.php');
}

function incrwd_add_pages() {
  add_submenu_page(
    'plugins.php',
    'Manage Incrwd',
    'Manage Incrwd',
    'activate_plugins',
    'incrwd',
    'incrwd_manage');
}

//add_action('admin_menu', 'incrwd_add_pages', 10);

////////////////////////////////////////////////////////////

function incrwd_activate_sharewidget() {
  add_submenu_page(
    'plugins.php',
    'Manage Incrwd',
    'Manage Incrwd',
    'activate_plugins',
    'incrwd',
    'sharewidget_manage');
}


function incrwd_make_share_widget() {
  global $post;
  
  $widget = '<html xmlns:fb="http://ogp.me/ns/fb#"><fb:like href="<?php echo the_permalink($post->ID); ?>" send="false" layout="button_count" width="100" show_faces="false"></fb:like>';
  $widget .= '<a href="https://twitter.com/share" class="twitter-share-button" data-url="<?php echo the_permalink($post->ID); ?>">Tweet</a>';
  $widget .= '<g:plusone size="medium" href="<?php the_permalink($post->ID); ?>"></g:plusone>';
  
  return $widget;
}

function is_content() {
  return !is_page() && !is_feed();
}

function incrwd_add_share_widget_excerpt($content) {
  if (is_content() && get_option('add_share_widget_excerpt') == 'true') {
    return $content.'<p>'.incrwd_make_share_widget().'</p><br/>';
  }
  return $content;
}

function incrwd_add_share_widget_content($content) {
  if (is_content() && get_option('add_share_widget_content') == 'true') {
    return $content.'<p>'.incrwd_make_share_widget().'</p><br/>';
  }
  return $content;
}

function incrwd_remove_widget($content) {
	remove_action('the_content', 'incrwd_add_share_widget_content');
	return $content;
}

function incrwd_add_script() {
    echo '<script type="text/javascript" src="https://apis.google.com/js/plusone.js" > {parsetags: "explicit"} </script>';
}

if (get_option('add_share_widget_content') != 'false' || get_option('add_share_widget_excerpt') != 'false') {
    add_filter('wp_head', 'incrwd_add_script');
    if (get_option('add_share_widget_content') != 'false') {
      update_option('add_share_widget_content', 'true');
      add_filter('the_content', 'incrwd_add_share_widget_content');
    }
    if (get_option('add_share_widget_excerpt') != 'false') {
      update_option('add_share_widget_excerpt', 'true');
      //add_filter('get_the_excerpt', 'incrwd_remove_widget', 9);
      add_filter('the_excerpt', 'incrwd_add_share_widget_excerpt');
    }
}

/////////////////////////////////////////////////////////////


function incrwd_get_api() {
  return new IncrwdWordPressAPI(
    get_option('incrwd_site_id'),
    get_option('incrwd_secret_key'),
    INCRWD_API_URL);
}

function incrwd_new_comment($comment_id, $approval_status) {
  if ($approval_status == 1) {
    incrwd_get_api()->approved_comment_left($comment_id);
  }
}

function incrwd_comment_approved($comment) {
  $comment_id = $comment->comment_ID;
  incrwd_get_api()->approved_comment_left($comment_id);
}


/**
 * Encodes an associative array using hmac/time method.
 *
 * @param $arr
 *   An associative array of arguments to be passed.
 */
function encode_arr($arr) {
  if (count($arr) == 0) {
    $json = "{}";
  }
  else {
    $json = cf_json_encode($arr);
  }
  $data = base64_encode($json);
  $key = get_option('incrwd_secret_key');
  $time = time();
  $hmac = incrwd_hmacsha1($data . ' ' . $time, $key);
  $payload = $data . ' ' . $hmac . ' '. $time;
  return $payload;
}

// Single sign-on integration
function incrwd_sso() {
  global $current_user;
  if ($current_user->ID) {
    $avatar_tag = get_avatar($current_user->ID);
    $avatar_data = array();
    preg_match('/(src)=((\'|")[^(\'|")]*(\'|"))/i', $avatar_tag, $avatar_data);
    $avatar = str_replace(array('"', "'"), '', $avatar_data[2]);
    $user_data = array(
      'username' => $current_user->display_name,
      'id' => intval($current_user->ID),
      'avatar' => $avatar,
      'email' => $current_user->user_email,);
  }
  else {
    $user_data = array();
  }
  return encode_arr($user_data);
}

add_action('wp_footer', 'incrwd_output_footer');
add_action('admin_menu', 'incrwd_activate_sharewidget', 10);
add_action('comment_post', 'incrwd_new_comment', 10, 2);
add_action('comment_unapproved_to_approved', 'incrwd_comment_approved');
