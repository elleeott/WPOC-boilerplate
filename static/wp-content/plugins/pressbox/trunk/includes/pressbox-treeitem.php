<?php

if (!class_exists('PressboxTreeitem')) {

  class PressboxTreeitem {

    public $revision;
    public $thumb_exists;
    public $modified;
    public $path;
    public $is_dir;
    public $icon;
    public $size;
    public $imgdir;
    public $plugdir;
    public $tab;
    public $type;
    public $postid;

    /**
     * construct PRessboxTreeitem object
     */
    function __construct($type, $tab, $postid, $item = FALSE) {
      if (!$item) {
        $this->revision = 0;
        $this->thumb_exists = false;
        $this->modified = 0;
        $this->path = "";
        $this->is_dir = false;
        $this->icon = "";
        $this->size = 0;
      } else {
        $this->revision = $item->{ 'revision' };
        $this->thumb_exists = $item->{ 'thumb_exists' };
        $this->modified = $item->{ 'modified' };
        $this->path = $item->{ 'path' };
        $this->is_dir = $item->{ 'is_dir' };
        $this->icon = $item->{ 'icon' };
        $this->size = $item->{ 'size' };
      }

      // Base plugin directory, escape from our subdir.
      $pressbox_basedir = explode('/', __FILE__);
      unset($pressbox_basedir[count($pressbox_basedir) - 1]);
      unset($pressbox_basedir[count($pressbox_basedir) - 1]);
      $pressbox_basedir = implode('/', $pressbox_basedir);

      $pressbox_file = $pressbox_basedir . "/pressbox.php";

      $this->imgdir = plugins_url('images', $pressbox_file);
      $this->plugdir = plugin_dir_path($pressbox_file);

      $this->tab = $tab;
      $this->type = $type;
      $this->postid = $postid;
    }

    function display($show_thumb = FALSE, $size = FALSE) {
      if ($this->is_dir) {
        $output = "./media-upload.php?post_id=" . $this->postid .
                "&type=" . $this->type . "&tab=" . $this->tab . "&path=" .
                trim($this->path, "/");
      } else {
        $output = "./media-upload.php?post_id=" . $this->postid .
                "&type=" . $this->type . "&tab=" . $this->tab . "&path=" .
                trim($this->path, "/") . "&choose=1";
      }

      if ($show_thumb && $this->thumb_exists) {
        $code = wp_create_nonce();

        $source = plugin_dir_url($this->plugdir . "pressbox.php") . "pressbox.php?display_thumb=" . urlencode(trim($this->path, "/")) .
                "&size=" . $size . "&_wpnonce=" . $code;
      } else {
        $source = $this->item_icon();
      }

      echo "<a href=\"{$output}\"><img src='{$source}' alt='{$this->path}'/></a>" .
      "<a href=\"{$output}\">" . basename($this->path) . "</a> ";

      if (!$this->is_dir) {
        $output = "./media-upload.php?post_id=" . $this->postid .
                "&type=" . $this->type . "&tab=" . $this->tab . "&path=" .
                trim($this->path, "/") . "&choose=1&insert=1";
        echo "<a href='{$output}' alt='Insert into Post'>(+)</a>";
      }
    }

    public function display_thumbnail($size = "small") {
      if ($this->thumb_exists) {
        $code = wp_create_nonce();

        $source = plugin_dir_url($this->plugdir . "pressbox.php") . "pressbox.php?display_thumb=" . urlencode(trim($this->path, "/")) .
                "&size=" . $size . "&_wpnonce=" . $code;
      } else {
        $source = $this->item_icon();
      }

      echo "<img src='{$source}' alt='{$this->path}'/>";     
    }
    
    private function item_icon() {
      return $this->imgdir . "/" . $this->icon . ".gif";
    }

    function dump() {
      echo $this->revision . "<br/>";
      // print_r($thumb_exists); echo "<br/>";
      echo $this->modified . "<br/>";
      echo $this->path . "<br/>";
      echo $this->is_dir . "<br/>";
      echo $this->icon . "<br/>";
      echo $this->size . "<br/>";
      echo $this->imgdir . "<br/>";
      echo $this->plugdir . "<br/>";
      echo $this->tab . "<br/>";
      echo $this->type . "<br/>";
      echo $this->postid . "<br/>";
      echo "<br/>";
    }

  }

} // PressboxTreeitem Exists
?>