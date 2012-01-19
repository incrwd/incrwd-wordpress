<?php

require_once(ABSPATH.WPINC . '/http.php');
require_once(dirname(__FILE__) . '/json.php');

class BasicInfoAPI {
  // This always gets incremented by 1.
  var $api_version = '1';

  function BasicInfoAPI($api_url) {
    $this->api_url = $api_url;
  }

  function set_basic_info() {
    $url = $this->api_url . 'get_basic_info/';
    $args = array(
      "api_version" => $this->api_version,
      "domain" => $_SERVER["SERVER_NAME"]);
    $http = new WP_Http();
    $response = $http->request(
      $url,
      array(
        'method' => 'POST',
        'body' => $args));
    $json = new JSON();
    $data = $json->unserialize($response['body']);
    update_option('incrwd_site_id', $data->site_id);
    update_option('incrwd_secret_key', $data->secret_key);
  }
}

?>