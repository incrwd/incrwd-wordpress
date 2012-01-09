<?php
if (isset($_POST['incrwd_site_id']) && isset($_POST['incrwd_secret_key'])) {
  update_option('incrwd_site_id', $_POST['incrwd_site_id']);
  update_option('incrwd_secret_key', $_POST['incrwd_secret_key']);
  echo "<div><p><strong>Your settings have been changed.</strong></p></div>";
}

$incrwd_site_id = get_option('incrwd_site_id');
$incrwd_secret_key = get_option('incrwd_secret_key');
?>
<div id="incrwd-options" class="incrwd-settings">
   <h2>Incrwd Settings</h2>
   <form method="POST">
       <table class="form-table">
            <tr>
                <th scope="row" valign="top">Incrwd Site ID</th>
                <td>
                    <input type="text" name="incrwd_site_id" value="<?php echo esc_attr($incrwd_site_id); ?>" tabindex="2">
                </td>
            </tr>
            <tr>
                <th scope="row" valign="top">Incrwd Secret Key</th>
                <td>
                    <input type="text" name="incrwd_secret_key" value="<?php echo esc_attr($incrwd_secret_key); ?>" tabindex="2">
                </td>
            </tr>
       </table>
        <p class="submit" style="text-align: left">
            <input name="submit" type="submit" value="Save" class="button-primary button" tabindex="4">
        </p>
   </form>
</div>
<?php
?>