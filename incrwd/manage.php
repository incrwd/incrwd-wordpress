<?php
define("TRUE_STRING", "true");
define("FALSE_STRING", "false");

$in_excerpt = get_option('add_share_widget_excerpt') == TRUE_STRING;
$in_content = get_option('add_share_widget_content') == TRUE_STRING;
$in_content_top = get_option('add_share_widget_content_top') == TRUE_STRING;
?>
<div id="incrwd-dashboard">
   <h2>Incrwd Dashboard</h2>
   <p>Sign in to your <a href="http://www.myincrwd.com/signup/login/?next=/signup/analytics_dashboard/">Dashboard</a> to see analytics and customize your rewards... for free!</p>
</div>
<div id="incrwd-options" class="incrwd-settings">
   <h2>Incrwd Settings</h2>
   <?php
   
  $post_excerpt = $_POST['add_share_widget_excerpt'] == TRUE_STRING;
  $post_content = $_POST['add_share_widget_content'] == TRUE_STRING;
  $post_content_top = $_POST['add_share_widget_content_top'] == TRUE_STRING;

  if ($_POST) {
    $currently_activated = $post_excerpt || $post_content || $post_content_top;   
  }
  else {
    $currently_activated = $in_excerpt || $in_content || $in_content_top;
  }
  if ($currently_activated) {
    echo "<div style='color: #7EB54A;'><p><strong>Share Widget Activated</strong></p></div>";
  }
  else {
    echo "<div style='color: red; text-size: 18px;'><p><strong>Share Widget Deactivated</strong></p></div>";
  }

  if($_POST) {
    $in_excerpt = $post_excerpt;
    update_option('add_share_widget_excerpt', $in_excerpt ? TRUE_STRING : FALSE_STRING);
  
    $in_content = $post_content;
    update_option('add_share_widget_content', $in_content ? TRUE_STRING : FALSE_STRING);
     
    $in_content_top = $post_content_top;
    update_option('add_share_widget_content_top', $in_content_top ? TRUE_STRING : FALSE_STRING);
  }
  
   ?>
   <form method="POST">
    <p><b>Locations to add Share Widget:</b></p>
    <input type="checkbox" name="add_share_widget_excerpt" value="true" tabindex="3" <?php if ($in_excerpt) echo "checked"; ?>/> Add Share Widget to Post Listing (Main Page)<br/>
    <input type="checkbox" name="add_share_widget_content_top" value="true" tabindex="4" <?php if ($in_content_top) echo "checked"; ?>/> Add Share Widget to Top of Post<br/>
    <input type="checkbox" name="add_share_widget_content" value="true" tabindex="4" <?php if ($in_content) echo "checked"; ?>/> Add Share Widget to Bottom of Post
    <p class="submit" style="text-align: left">
      <input name="submit" type="submit" value="Save" class="button-primary button" tabindex="5" />
    </p>
   </form>
</div>