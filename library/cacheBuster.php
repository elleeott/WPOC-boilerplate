<?php
function autoVer($url){
	$subDomain ='static';
    $path = pathinfo($url);
    $ver = '.'.filemtime($_SERVER['DOCUMENT_ROOT'].$url).'.';
    echo '//'.$subDomain.'.'.$_SERVER['HTTP_HOST'].str_replace($subDomain.'/','',$path['dirname']).'/'.str_replace('.', $ver, $path['basename']);
}
?>