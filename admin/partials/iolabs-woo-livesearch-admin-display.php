<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://www.iolabs.nl
 * @since      1.0.0
 *
 * @package    Iolabs_Woo_Livesearch
 * @subpackage Iolabs_Woo_Livesearch/admin/partials
 */

if(isset($_GET['wools-cache-status']) || isset($_GET['wools-options-status'])) {
  $statusMessage = $_GET['wools-cache-status'] ?? $_GET['wools-options-status'];
  if($statusMessage === 'success') {
    $message = 'Success';
    $class = 'updated';
  } else {
    $message = 'Something went wrong, are you sure the cache file & folder are writable?';
    $class = 'error';
  }
}

if(isset($message) && isset($class)) {
  echo "<div id='message' class='{$class}'>{$message}</div>";
}
?>
<script>
  function toggle(source) {
    checkboxes = document.getElementsByName('keys[]');
    for(var i=0, n=checkboxes.length;i<n;i++) {
      checkboxes[i].checked = source.checked;
    }
  }
</script>
<section class="pattern" id="twocolumnlayout1">
  <div class="wrap">
    <h1>IOlabs Woocommerce Live Search</h1>
    <div id="poststuff">
      <div id="post-body" class="metabox-holder columns-2">
        <div id="post-body-content">
          <div class="meta-box-sortables ui-sortable">
            <div class="postbox">
              <h2>Settings</h2>
              <div class="inside">
                <input type="checkbox" onClick="toggle(this)" /> Toggle All<br/><br />
                <form method="post" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>">
                  <fieldset>
                      <?php foreach($keys as $i) { ?>
                        <label for="<?php echo $i; ?>">
                          <input type="checkbox" name="keys[]" value="<?php echo $i; ?>" id="<?php echo $i; ?>" <?php checked
                          (in_array($i, $options));
                          ?>>
                          <span><?php echo $i; ?></span>
                        </label><br />
                      <?php } ?>
                    <br />
                    <input type="hidden" name="referrer" value="<?php echo $_SERVER['REQUEST_URI']; ?>">
                    <input type="hidden" name="action" value="wools_save_options">
                    <input type="submit" value="Build Product Cache" class="button button-primary">
                  </fieldset>
                </form>
              </div>
            </div>
          </div>
        </div>
        <div id="postbox-container-1" class="postbox-container">
          <div class="meta-box-sortables">
            <div class="postbox">
              <h2><span>Re-generate product cache</span></h2>
              <div class="inside">
                <p>This will generate a new product cache, make sure to use it after updating products.</p>
                <form action="<?php echo esc_url( admin_url('admin-post.php') ); ?>" method="post">
                  <input type="hidden" name="referrer" value="<?php echo $_SERVER['REQUEST_URI']; ?>">
                  <input type="hidden" name="action" value="wools_build_cache">
                  <input type="submit" value="Build Product Cache" class="button button-primary">
                </form>
              </div>
            </div>
          </div>
        </div>
      <br class="clear">
    </div>
  </div>
</section>

