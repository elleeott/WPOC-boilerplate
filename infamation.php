<?php echo $_SERVER['DOCUMENT_ROOT']; ?><br/>
<?php echo $_SERVER['HTTP_HOST']; ?><br/>
<?php echo $_SERVER['SERVER_NAME']; ?><br/>
<?php echo exec('whoami'); ?><br/>
<?php echo $_SERVER['DOCUMENT_ROOT'] . '/static/wp-content'; ?><br/>
<?php echo '//static.'.str_replace('www.','',$_SERVER['SERVER_NAME']).'/wp-content'; ?>

<?php phpinfo(); ?>
