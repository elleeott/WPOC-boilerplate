<?php
function autoVer($url){
	$subDomain ='static';
    $path = pathinfo($url);
    $ver = '.'.filemtime($_SERVER['DOCUMENT_ROOT'].$url).'.';
    echo '//'.$subDomain.'.'.$_SERVER['SERVER_NAME'].str_replace($subDomain.'/','',$path['dirname']).'/'.str_replace('.', $ver, $path['basename']);
}
?>