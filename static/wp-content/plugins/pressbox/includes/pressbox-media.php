<?php
// Base plugin directory, escape from our subdir.
$pressbox_basedir = explode('/', __FILE__);
unset($pressbox_basedir[count($pressbox_basedir) - 1]);
unset($pressbox_basedir[count($pressbox_basedir) - 1]);
$pressbox_basedir = implode('/', $pressbox_basedir);

// Constants
$pressbox_file = $pressbox_basedir . "/pressbox.php";
$pressbox_wbasedir = plugins_url('', $pressbox_file) . "/pressbox.php";
$pressbox_imgdir = plugins_url('images', $pressbox_file);
$pressbox_cssdir = plugins_url('css', $pressbox_file);
$pressbox_incdir = plugins_url('includes', $pressbox_file);
$pressbox_plugurl = plugin_dir_url($pressbox_file);

require_once( plugin_dir_path(__FILE__) . "/pressbox-treeitem.php" );

media_upload_header();
echo "<link rel='stylesheet' href='" . $pressbox_cssdir . "/pressbox.css' type='text/css' media='all' />";

if ($_GET['insert']) {
  // Core function located in wp-admin/includes/media.php

  if ($_GET['type'] == "image") {
    media_send_to_editor("[pressbox path=/" . $_GET['path'] . "]");
  } else {
    media_send_to_editor("[pressbox path=/" . $_GET['path'] . "type=link]");
  }
} else if ($_GET['choose']) {
  // Get file metadata.
  $connection = new PressboxOauth(
                  get_option('lh_pressbox_consumer_key'),
                  get_option('lh_pressbox_consumer_secret'),
                  get_option('lh_pressbox_access_token'),
                  get_option('lh_pressbox_access_token_secret'));

  $metadata = lh_pressbox_get_metadata_for($connection, $_GET['path']);
} else {
  $folders = array();
  $files = array();

  $connection = new PressboxOauth(
                  get_option('lh_pressbox_consumer_key'),
                  get_option('lh_pressbox_consumer_secret'),
                  get_option('lh_pressbox_access_token'),
                  get_option('lh_pressbox_access_token_secret'));

  if ($_GET['path'] && $_GET['path'] != "root") {
    $root = lh_pressbox_get_metadata_for($connection, $_GET['path']);
  } else if ($_GET['favorite']) {
    $root = lh_pressbox_get_metadata_for($connection, $_GET['choice']);
  } else if ($_GET['path'] == "root" || !get_option('lh_pressbox_default_path') || !$_GET['path']) {
    $root = lh_pressbox_get_metadata_for($connection);
  } else {
    $root = lh_pressbox_get_metadata_for($connection, get_option('lh_pressbox_default_path'));
  }

  if ($root->{ 'error' }) {
    $error = "Path not found. Remember, DropBox is cAsE sensitive.";
  } else {
    $items = array();

    foreach ($root->{ 'contents' } as $key => $value) {
      array_push($items, new PressboxTreeitem($_GET['type'], $_GET['tab'], $_GET['post_id'], $value));
    }

    $items = array_reverse($items);

    foreach ($items as $treeitem) {
      if ($treeitem->is_dir) {
        array_push($folders, $treeitem);
      } else {
        array_push($files, $treeitem);
      }
    }

    $folders = array_reverse($folders);
    $files = array_reverse($files);

    if (get_option('lh_pressbox_show_thumbnails')) {
      $show_thumbs = true;
    } else {
      $show_thumbs = false;
    }
  }
}

function generate_breadcrumb($cwd) {
  $counter = 1;
  $path = "";

  $output = "<li><a href=\"./media-upload.php?post_id=" . $_GET['post_id'] .
          "&type=" . $_GET['type'] . "&tab=" . $_GET['tab'] . "&path=root \">Dropbox</a></li>";

  if ($cwd == "root") {
    return $output;
  }

  if (strlen($cwd) == 0 && get_option('lh_pressbox_default_path')) {
    $folders = explode("/", trim(get_option('lh_pressbox_default_path'), "/"));
  } else if (strlen($cwd) == 0 && $_GET['choice']) {
    $folders = explode("/", trim($_GET['choice'], "/"));
  } else if (strlen($cwd) == 0) {
    return $output;
  } else {
    $folders = explode("/", $cwd);
  }

  $len = count($folders);
    
  foreach ($folders as $folder) {
    $path .= "/" . $folder;
    $output .= "<li><a href=\"./media-upload.php?post_id=" . $_GET['post_id'] .
            "&type=" . $_GET['type'] . "&tab=" . $_GET['tab'] . "&path=" . trim($path, "/");

    if ($counter == $len && $_GET['choose']) { // Are we looking at a file?
      $output .= "&choose=1\">" . $folder . "</a></li>";
    } else { // Nope
      $output .= "\">" . $folder . "</a></li>";
    }

    $counter++;
  }

  return $output;
}
?>

<div id="pressbox-breadcrumb">
  <ul>
    <?php echo generate_breadcrumb($_GET['path']); ?>
  </ul>
</div>
<div id="pressbox-favorites">
  <p>Default Path: <?php
    $default_path = "<a href='" . "./media-upload.php?post_id=" . $_GET['post_id'] .
            "&type=" . $_GET['type'] . "&tab=" . $_GET['tab'];
    if (get_option('lh_pressbox_default_path')) {
      $default_path .= "&path=" . trim(get_option('lh_pressbox_default_path'), "/") .
              "'>" . get_option('lh_pressbox_default_path') . "</a>";
    } else {
      $default_path .= "&path=root'>/</a>";
    }

    echo $default_path;
    ?></p>
  <?php if (get_option('lh_pressbox_favorites')) { ?>
    <div id="pressbox-favform">
      <form method="GET" action="">
        <select name="choice"><?php
  $favorites = get_option('lh_pressbox_favorites');
  foreach ($favorites as $path) {
    if ($_GET['choice']) {
      if (trim($path, "/") == trim($_GET['choice'], "/")) {
        echo "<option value='{$path}' selected='selected'>{$path}</option>";
      } else {
        echo "<option value='{$path}'>{$path}</option>";
      }
    } else {
      echo "<option value='{$path}'>{$path}</option>";
    }
  }
    ?></select>
        <input type="hidden" name="favorite" value="1" />
        <input type="hidden" name="post_id" value="<?php echo $_GET['post_id'] ?>" />
        <input type="hidden" name="tab" value="<?php echo $_GET['tab'] ?>" />
        <input type="hidden" name="type" value="<? echo $_GET['type'] ?>" />
        <input type="submit" class="primary" name="submit" value="Go" />
      </form>
    </div>
  <?php } ?>
</div>
<div id="pressbox-wrap">
  <?php if ($error) { ?>
    <div class="error"><?php echo $error; ?></div>
  <?php } else { ?>
    <?php if ($_GET['choose']) { ?>
      <div id="pressbox-display-container">
        <div id="pressbox-display-box">
          <div id="pressbox-display">
            <?php
              $item = new PressboxTreeitem($_GET['type'], $_GET['tab'], $_GET['post_id'], $metadata);
              $item->display_thumbnail("large");
            ?>
          <div id="pressbox-metadata">
            <ul>
              <li>Name: <?php echo basename($metadata->{'path'}); ?></li>
              <li>Date: <?php echo $metadata->{'modified'}; ?></li>
              <li>Size: <?php echo $metadata->{'size'}; ?></li>
              <li>Type: <?php echo $metadata->{'mime_type'}; ?></li>
            </ul>
          </div>
          </div>
          <div id="pressbox-insert">
            <form method="GET" action="">
              <input type="hidden" name="choose" value="1" />
              <input type="hidden" name="insert" value="1" />
              <input type="hidden" name="post_id" value="<?php echo $_GET['post_id'] ?>" />
              <input type="hidden" name="tab" value="<?php echo $_GET['tab'] ?>" />
              <input type="hidden" name="type" value="<? echo $_GET['type'] ?>" />
              <input type="hidden" name="path" value="<? echo $_GET['path'] ?>" />
              <input type="submit" class="primary" name="submit" value="Insert into Post" />
            </form>
          </div>
        </div>
      </div>
    <?php } else { ?>
      <?php if ($folders) { ?>
        <div id="pressbox-folders">
          <ul>
            <?php foreach ($folders as $folder) { ?>
              <li><?php $folder->display(); ?></li>
            <?php } ?>
          </ul>
        </div>
      <?php } ?>
      <div id="pressbox-files">
        <ul id="pressbox-filelist">
          <?php foreach ($files as $file) { ?>
            <li><div class="pressbox-file"><div class="pressbox-container"><?php $file->display($show_thumbs, "large"); ?></div></div></li>
          <?php } ?>
        </ul>
      </div>
    <?php } ?>
  <?php } ?>
</div>