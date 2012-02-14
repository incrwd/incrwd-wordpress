<?php
$activate_share_widget = get_option('activate_share_widget');
$add_share_widget_excerpt = get_option('add_share_widget_excerpt');
$add_share_widget_content = get_option('add_share_widget_content');
?>
<div id="incrwd-options" class="incrwd-settings">
   <h2>Incrwd Settings</h2>
   <?php
    
   if (($_POST['add_share_widget_excerpt'] == 'true' || $_POST['add_share_widget_content'] == 'true' || $add_share_widget_excerpt == 'true' || $add_share_widget_content == 'true' ) && !($_POST['add_share_widget_excerpt'] != 'true' && $_POST['add_share_widget_content'] != 'true' )) {
     $activate_share_widget = 'true';
     echo "<div style='color: #7EB54A;'><p><strong>Share Widget Activated</strong></p></div>";
     update_option('activate_share_widget', 'true');
   }
   else {
     $activate_share_widget = 'false';
     echo "<div style='color: red; text-size: 18px;'><p><strong>Share Widget Deactivated</strong></p></div>";
     update_option('activate_share_widget', 'false');
   }

   if ( $_POST['add_share_widget_excerpt'] == 'true' ) {
     $add_share_widget_excerpt = 'true';
     update_option('add_share_widget_excerpt', 'true');
   }
   else {
     $add_share_widget_excerpt = 'false';
     update_option('add_share_widget_excerpt', 'false');
   }

   if ( $_POST['add_share_widget_content'] == 'true' ) {
     $add_share_widget_content = 'true';
     update_option('add_share_widget_content', 'true');
   }
   else {
     $add_share_widget_content = 'false';
     update_option('add_share_widget_content', 'false');
   }
   ?>
   <form method="POST">
                     <p><b>Locations to add Share Widget:</b></p>
                     <input type="checkbox" name="add_share_widget_excerpt" value="true" tabindex="3" <?php if ($add_share_widget_excerpt == 'true') echo "checked"; ?>/> Add Share Widget to Content
                     <input type="checkbox" name="add_share_widget_content" value="true" tabindex="4" <?php if ($add_share_widget_content == 'true') echo "checked"; ?>/> Add Share Widget to Page
        <p class="submit" style="text-align: left">
            <input name="submit" type="submit" value="Save" class="button-primary button" tabindex="5" />
        </p>
   </form>
</div>