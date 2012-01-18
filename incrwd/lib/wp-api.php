<?php
/**
 * Incrwd API for WordPress
 *
 * @author     Incrwd <team@incrwd.com>
 * @copyright  2011 Incrwd, Inc.
 * @link       http://myincrwd.com/
 * @package    Incrwd
 * @subpackage IncrwdWordPressAPI
 * @version    1.0
 */

require_once(ABSPATH.WPINC . '/http.php');

class IncrwdWordPressAPI {
  // This always gets incremented by 1.
  var $api_version = '2';

  function IncrwdWordPressAPI($site_id, $secret_key, $api_url) {
    $this->site_id = $site_id;
    $this->secret_key = $secret_key;
    $this->api_url = $api_url;
  }

  function approved_comment_left($comment_id) {
    $this->call("approved_comment", 
                array("comment_id" => $comment_id,
                      "remote_cookie" => $_COOKIE["__ic"]));
  }

  /**
   * Makes a call to an Incrwd API method.
   *
   * @param $method
   *   The Incrwd API method to call.
   * @param $args
   *   An associative array of arguments to be passed.
   */
  function call($method, $args=array()) {
    if (!($this->site_id)) {
      return -1;
    }
    $url = $this->api_url . $method . '/';
    $args['api_version'] = $this->api_version;
    $real_args = array(
      "site_id" => $this->site_id,
      "args" => encode_arr($args));
    $http = new WP_Http();
    $response = $http->request(
      $url,
      array(
        'method' => 'POST',
        'body' => $real_args));
    if ($response->errors) {
      return -1;
    }
    $data = unserialize($response['body']);
    return $data;
  }
}

?>