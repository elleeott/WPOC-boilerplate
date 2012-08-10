<?php

function lh_pressbox_get_account_info($connection) {
  $data = $connection->get('account/info');
  
    return json_decode($data);
}

function lh_pressbox_get_metadata_for($connection, $path = "") {
  return json_decode($connection->get('metadata/dropbox/' . trim($path,"/")));
}

function lh_pressbox_get_file($connection, $path, $params) {
  return $connection->get("files/dropbox/" . trim($path,"/"), $params, true);
}

function lh_pressbox_get_thumbnail($connection, $path, $size) {
  
  switch ($size) {
    case "small":
    case "medium":
    case "large":
    break;

    default:
      $size = "small";
      break;
  }

  $parameters = array(size => $size);
  
  return $connection->get("thumbnails/dropbox/" . trim($path, "/"), $parameters, true);
}