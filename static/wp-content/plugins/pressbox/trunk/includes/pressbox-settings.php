<?php
// Are we allowed to see this page at all?
if (!current_user_can('manage_options')) {
  wp_die('');
}

// Base plugin directory, escape from our subdir.
$pressbox_basedir = explode('/', __FILE__);
unset($pressbox_basedir[count($pressbox_basedir) - 1]);
unset($pressbox_basedir[count($pressbox_basedir) - 1]);
$pressbox_basedir = implode('/', $pressbox_basedir);

// Constants
$pressbox_file = $pressbox_basedir . "/pressbox.php";
$pressbox_basedir = plugins_url('', $pressbox_file) . "/pressbox.php";
$pressbox_imgdir = plugins_url('images', $pressbox_file);
$pressbox_cssdir = plugins_url('css', $pressbox_file);
$pressbox_incdir = plugins_url('includes', $pressbox_file);

// Handle the DropBox OAuth Callback
if (isset($_GET['uid'])) {
  update_option('lh_pressbox_uid', $_GET['uid']);

  $connection = new PressboxOauth(
                  get_option('lh_pressbox_consumer_key'),
                  get_option('lh_pressbox_consumer_secret')
  );

  // Convert temporary request token into a permanent access token.
  $access_token = $connection->getAccessToken(get_option('lh_pressbox_consumer_key'), get_option('lh_pressbox_consumer_secret'), get_option('lh_pressbox_oauth_token'), get_option('lh_pressbox_oauth_token_secret'));

  if ($connection->http_code == "200") { // We got our token.
    update_option('lh_pressbox_access_token', $access_token['oauth_token']);
    update_option('lh_pressbox_access_token_secret', $access_token['oauth_token_secret']);
  } else {
    $error = "Pressbox could not connect to your DropBox Account. Verify your Key and Secret and try again.";
  }

  $message = "<strong>PressBox is now connected to your DropBox account!</strong>";
}

// Attempt to authenticate with DropBox using OAuth.
if (isset($_POST['submit-type']) && $_POST['submit-type'] == "dropbox-connect") {
  update_option('lh_pressbox_consumer_key', $_POST['consumerkey']);
  update_option('lh_pressbox_consumer_secret', $_POST['consumersecret']);

  $connection = new PressboxOauth(
                  get_option('lh_pressbox_consumer_key'),
                  get_option('lh_pressbox_consumer_secret')
  );

  $token = $connection->getRequestToken();

  update_option('lh_pressbox_oauth_token', $token['oauth_token']);
  update_option('lh_pressbox_oauth_token_secret', $token['oauth_token_secret']);

  $url = $connection->getAuthorizeURL($token, admin_url() . "options-general.php?page=pressbox/pressbox.php");
  echo "<script>location.replace(\"" . $url . "\");</script>";
}

// Disconnect our blog from Dropbox.
if (isset($_POST['submit-type']) && $_POST['submit-type'] == "dropbox-disconnect") {
  delete_option('lh_pressbox_access_token');
  delete_option('lh_pressbox_access_token_secret');
  delete_option('lh_pressbox_oauth_token');
  delete_option('lh_pressbox_oauth_token_secret');

  $message = "<strong>Pressbox disconnected from your DropBox Account.</strong>";
}

// Update General Settings
if (isset($_POST['submit-type']) && $_POST['submit-type'] == "general-settings") {
  if ($_POST['thumbs'] == "1") {
    update_option('lh_pressbox_show_thumbnails', 1);
  } else {
    delete_option('lh_pressbox_show_thumbnails');
  }

  if ($_POST['favorites']) {
    $items = explode("\n", $_POST['favorites']);
    $favorites = array();

    foreach ($items as $favorite) {
      if(strlen(chop($favorite)) != 0) {
      array_push($favorites, chop($favorite));
      }
    }

    update_option('lh_pressbox_favorites', $favorites);
  } else {
    delete_option('lh_pressbox_favorites');
  }

  if ($_POST['defaultpath']) {
    update_option('lh_pressbox_default_path', $_POST['defaultpath']);
  } else {
    delete_option('lh_pressbox_default_path');
  }

  $message = "Pressbox settings saved!";
}

// If we're connected, let's get some account information.
if (get_option('lh_pressbox_access_token')) {
  $connection = new PressboxOauth(
                  get_option('lh_pressbox_consumer_key'),
                  get_option('lh_pressbox_consumer_secret'),
                  get_option('lh_pressbox_access_token'),
                  get_option('lh_pressbox_access_token_secret'));

  $user_info = lh_pressbox_get_account_info($connection);

  update_option('lh_pressbox_display_name', $user_info->{ 'display_name' });
  update_option('lh_pressbox_quota', $user_info->{ 'quota_info' }->{ 'quota' });
  update_option('lh_pressbox_shared', $user_info->{ 'quota_info' }->{ 'shared' });
  update_option('lh_pressbox_normal', $user_info->{ 'quota_info' }->{ 'normal' });
}
?>

<div class="wrap" id="pressbox">
  <?php if ($error) { ?>
    <div class="error"><p><?php echo $error; ?></p></div>
  <?php } ?>
  <?php if ($message) { ?>
    <div class="updated"><p><?php echo $message; ?></p></div>
  <?php } ?>
  <?php screen_icon('plugins'); ?>
  <h2>Pressbox</h2>
  <div id="pressbox-about">
    <img src="<?php echo $pressbox_imgdir . "/pressbox.png" ?>" alt="Pressbox" />
    <p>Version 1.0,  by <a href="http://lesharris.com">Les Harris</a></p>
    <p><a href="http://lesharris.com/pressbox">Pressbox Plugin Page</a></p>
  </div>
  <div id="pressbox-shortcode">
    <p>Pressbox provides one shortcode, 'pressbox', with the following parameters:</p>
    <ul>
      <li><code>path</code>: The path to the file you want to retrieve.</li>
      <li><code>type</code>: The valid options are <code>image</code> and <code>link</code>. The image type will retrieve and display an image, link will output a link to any file in your dropbox.</li>
      <li><code>name</code>: Used only with the link type. Sets the displayed name of the link, the default value is the link text if not set.</li>
      <li>&nbsp;</li>
    </ul>
    <p>Examples:</p>
    <ul>
      <li><code>[pressbox path=/Photos/myphoto.png]</code> -> Displays myphoto.png</li>
      <li><code>[pressbox path=/Photos/myphoto.png type=link name=Download My Picture!]</code> -> Displays a link to myphoto.png.</li>
    </ul>
  </div>
  <div class="pressbox-settings" id="poststuff">

    <div class="ui-sortable meta-box-sortables">
      <div class="postbox">
        <div class="handlediv" title="Click to toggle"><br/></div>
        <h3><img src="<?php echo $pressbox_imgdir; ?>/dropbox.png" alt="DropBox" /> DropBox API Settings</h3>
        <div class="inside">
          <br class="clear" />

          <form method="POST" action="">
            <div>
              <?php if (get_option('lh_pressbox_access_token')) { ?>
                <ul>
                  <li>
                    <div class="pressbox-infolabel">Display Name:</div> 
                    <code><?php echo get_option('lh_pressbox_display_name'); ?></code>
                  </li>
                  <li>
                    <div class="pressbox-infolabel">API Key:</div>
                    <code><?php echo get_option('lh_pressbox_consumer_key'); ?></code>
                  </li>
                  <li>
                    <div class="pressbox-infolabel">API Key Secret:</div>
                    <code><?php echo get_option('lh_pressbox_consumer_secret'); ?></code>
                  </li>
                  <li>
                    <div class="pressbox-infolabel">Access Token:</div>
                    <code><?php echo get_option('lh_pressbox_access_token'); ?></code>
                  </li>
                  <li>
                    <div class="pressbox-infolabel">Access Token Secret:</div>
                    <code><?php echo get_option('lh_pressbox_access_token_secret'); ?></code>
                  </li>
                  <li>
                    <div class="pressbox-infolabel">Quota Usage:</div> <strong>
                      <?php
                      $used = round(( (get_option('lh_pressbox_shared') + get_option('lh_pressbox_normal')) / 1024) / 1024, 2);

                      if ($used < 1024) {
                        echo $used . "MB";
                      } else {
                        echo round($used / 1024, 1) . "GB";
                      }
                      ?>
                    </strong> / <strong>
                      <?php
                      $available = round(( get_option('lh_pressbox_quota') / 1024) / 1024, 2);

                      if ($available < 1024) {
                        echo $available . "MB";
                      } else {
                        echo round($available / 1024, 1) . "GB";
                      }
                      ?>
                    </strong>
                  </li>
                </ul>
                <input type="hidden" name = "submit-type" value="dropbox-disconnect" />
                <p><input type="submit" name="connect" value="Disconnect your Blog from DropBox" class="button-primary" /></p>
              <?php } else { ?>
                <p>The new DropBox Web API forbids clients (like this plugin!) from requesting or storing your username and password.
                  In its place it uses a system called OAuth.  To use OAuth with DropBox you will need two keys: an API key, and a API secret key. 
                  To get these keys you must perform the following steps:</p>
                <div id="pressbox-inscontainer">
                  <ol>
                    <li>Go to <a href="https://www.dropbox.com/developers/apps">https://www.dropbox.com/developers/apps</a>, login if necessary, and click the 'Create an App' button</li>
                    <li>Give your new App whatever name and description you want.</li>
                    <li>You should now see your new app in the list. Click the Options link for your app.</li>
                    <li>Copy the API Keys located on the bottom of the form to the fields in PressBox</li>
                    <li>Click the 'Connect your Blog to Dropbox' button, you will be taken to the DropBox website automatically.</li>
                    <li>DropBox will ask if you want to allow your new app to connect to DropBox. Select 'Allow'</li>
                    <li>You will be brought back to this page and you should see that you have been connected to DropBox!</li>
                  </ol>
                </div>
                <p id="pressbox-insfooter">It's a lot of steps but you'll only have to do this once.</p>
                <ul>
                  <li>
                    <div class="pressbox-label">Key</div>
                    <input size="60" name="consumerkey" value="<?php echo get_option('lh_pressbox_consumer_key'); ?>" />
                  </li>
                  <li>
                    <div class="pressbox-label">Secret</div>
                    <input size="60" name="consumersecret" value="<?php echo get_option('lh_pressbox_consumer_secret'); ?>" />
                  </li>
                </ul>
                <input type="hidden" name = "submit-type" value="dropbox-connect" />
                <p><input type="submit" name="connect" value="Connect your Blog to DropBox" class="button-primary" /></p>
              <?php } ?>
            </div>
          </form>
        </div>
      </div>
    </div>

    <?php if (get_option('lh_pressbox_access_token')) { ?>
      <div class="ui-sortable meta-box-sortables">
        <div class="postbox">
          <div class="handlediv" title="Click to toggle"><br/></div>
          <h3>General Settings</h3>
          <div class="inside">
            <br class="clear" />

            <form method="POST" action="">
              <div>
                <ul>
                  <li>
                    <div class="pressbox-clabel">Show Image Thumbnails in File List?</div>
                    <input type="checkbox" name="thumbs" value="1" <?php
    if (get_option('lh_pressbox_show_thumbnails')) {
      echo "checked";
    }
      ?>/>
                  </li>
                  <li>
                    <div class="pressbox-clabel">Default Path for File List:</div>
                    <input size="60" name="defaultpath" value="<?php echo get_option('lh_pressbox_default_path') ?>" />
                    <p>This setting controls which folder is the default folder in the file list.  If you have a folder called Photos in the root directory you would put in /Photos to have it be the default.  A blank value will make the root the default.</p>
                  </li>
                  <li>
                    <div class="pressbox-clabel">Favorite Folders</div><br/>
                    <textarea name="favorites" rows="10" cols="60">
<?php
                      if (get_option('lh_pressbox_favorites')) {
                        $favorites = get_option('lh_pressbox_favorites');

                        foreach ($favorites as $favorite) {
                          echo $favorite . "\n";
                        }
                      }
?></textarea>
                    <p>Place one path per line and they will show up in the Favorites dropdown in the file list. Remember, Dropbox is case sensitive! It considers /MyFolder and /myfolder to be two different folders.</p>
                  </li>
                </ul>
                <input type="hidden" name = "submit-type" value="general-settings" />
                <p><input type="submit" name="connect" value="Save General Settings" class="button-primary" /></p>
              </div>
            </form>
          </div>
        </div>
      </div>
    <?php } ?>

  </div>
</div>
