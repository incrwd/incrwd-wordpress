<?php 
function incrwd_embed($site_id, $incrwd_local, $compiled_js_url, $sso) {
?>
  <script type="text/javascript">
    incrwd_config = {
      site_id: <?php echo $site_id; ?>,
      remote_auth: '<?php echo $sso; ?>'
    };
  </script>
<?php if ($incrwd_local) {
      echo '<script type="text/javascript" src="http://incrwd.example.com:8000/w/bootloader.js"></script>';
  }
  elseif ($site_id) {
    echo '<script type="text/javascript" src="' . $compiled_js_url . '"></script>';
  }
}
?>