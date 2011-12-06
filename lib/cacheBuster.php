<?php
function autoVer($url){
	$subDomain ='static';
    $path = pathinfo($url);
    $ext = $path['extension'];
    $ver = '.'.filemtime($_SERVER['DOCUMENT_ROOT'].$url).'.'.$ext;
    echo '//'.$subDomain.'.'.str_replace('www.','',$_SERVER['SERVER_NAME']).str_replace($subDomain.'/','',$path['dirname']).'/'.str_replace('.'.$ext, $ver, $path['basename']);
}
?>