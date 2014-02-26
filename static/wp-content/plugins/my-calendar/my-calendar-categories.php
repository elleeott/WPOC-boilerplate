<?php
if (!empty($_SERVER['SCRIPT_FILENAME']) && 'my-calendar-categories.php' == basename($_SERVER['SCRIPT_FILENAME'])) {
	die ('Please do not load this page directly. Thanks!');
}
// Function to handle the management of categories

// This is a hack for people who don't have PHP installed with exif_imagetype
if (!function_exists('exif_imagetype') ) {
    function exif_imagetype ( $filename ) {
        if ( !is_dir( $filename ) && ( list($width, $height, $type, $attr) = getimagesize( $filename ) ) !== false ) {
            return $type;
        }
    return false;
    }
}

function my_dirlist($directory) {
    // create an array to hold directory list
    $results = array();
    // create a handler for the directory
    $handler = opendir($directory);
    // keep going until all files in directory have been read
    while ($file = readdir($handler)) {
        // if $file isn't this directory or its parent, 
        // add it to the results array
		if ( filesize( $directory.'/'.$file ) > 11 ) {
			if ( $file != '.' && $file != '..' && !is_dir($directory.'/'.$file) && (
			exif_imagetype($directory.'/'.$file) == IMAGETYPE_GIF || 
			exif_imagetype($directory.'/'.$file) == IMAGETYPE_PNG ||  
			exif_imagetype($directory.'/'.$file) == IMAGETYPE_JPEG ) ) {
				$results[] = $file;
			}
		}
    }
    // tidy up: close the handler
    closedir($handler);
    // done!
	sort($results,SORT_STRING);
    return $results;
}

function my_csslist($directory) {
    // create an array to hold directory list
    $results = array();
    // create a handler for the directory
    $handler = opendir($directory);
    // keep going until all files in directory have been read
    while ($file = readdir($handler)) {
        // if $file isn't this directory or its parent, 
        // add it to the results array
        if ( $file != '.' && $file != '..' ) {
            $results[] = $file;
		}
    }
    // tidy up: close the handler
    closedir($handler);
    // done!
	sort($results,SORT_STRING);
    return $results;
}

function is_custom_icon() {
	global $wp_plugin_dir;
	if (file_exists( str_replace('/my-calendar','',$wp_plugin_dir ). '/my-calendar-custom/' ) ) {
		$results = my_dirlist(  str_replace('/my-calendar','',$wp_plugin_dir ) . '/my-calendar-custom/' );
		if ( empty($results) ) {
			return false;
		} else {
			return true;
		}
	} else {
		return false;
	}
}

function my_calendar_manage_categories() {
  global $wpdb;
	$mcdb = $wpdb;  
  $formats = array( '%s', '%s', '%s' );
  // My Calendar must be installed and upgraded before this will work
  check_my_calendar();
?>
<div class="wrap jd-my-calendar">
<?php 
my_calendar_check_db();
?>
<?php
  // We do some checking to see what we're doing
	if ( !empty($_POST) ) {
		$nonce=$_REQUEST['_wpnonce'];
		if (! wp_verify_nonce($nonce,'my-calendar-nonce') ) die("Security check failed");
	}
  
	if (isset($_POST['mode']) && $_POST['mode'] == 'add') {
    
		$add = array(
		'category_name'=>$_POST['category_name'],
		'category_color'=>$_POST['category_color'],
		'category_icon'=>$_POST['category_icon'],
		'category_private'=>( (isset( $_POST['category_private'] ))?1:0 )
		);
		$results = $mcdb->insert( my_calendar_categories_table(), $add, $formats );
		if ( isset($_POST['mc_default_category']) ) {
			$cat_ID = $mcdb->insert_id;
			update_option( 'mc_default_category',$cat_ID );
			$append = __('Default category changed.','my-calendar');			
		} else { $append = ''; }
		if ( $results ) {
			echo "<div class=\"updated\"><p><strong>".__('Category added successfully','my-calendar')." $append</strong></p></div>";
		} else {
			echo "<div class=\"updated error\"><p><strong>".__('Category addition failed.','my-calendar')."</strong></p></div>";	  
		}
    } else if ( isset($_GET['mode']) && isset($_GET['category_id']) && $_GET['mode'] == 'delete' ) {
		$cat_ID = (int) $_GET['category_id'];
		$sql = "DELETE FROM " . my_calendar_categories_table() . " WHERE category_id=$cat_ID";
		$results = $mcdb->query($sql);
		if ($results) {
			$sql = "UPDATE " . my_calendar_table() . " SET event_category=1 WHERE event_category=$cat_ID";
			$cal_results = $mcdb->query($sql);
			mc_delete_cache();
		}
		if ( get_option('mc_default_category') == $cat_ID ) { 
			update_option( 'mc_default_category',1 );
		}		
		if ($results && $cal_results) {
			echo "<div class=\"updated\"><p><strong>".__('Category deleted successfully. Categories in calendar updated.','my-calendar')."</strong></p></div>";
		} else if ( $results && !$cal_results ) {
			echo "<div class=\"updated\"><p><strong>".__('Category deleted successfully. Categories in calendar not updated.','my-calendar')."</strong></p></div>";	  
		} else if ( !$results && $cal_results ) {
			echo "<div class=\"updated error\"><p><strong>".__('Category not deleted. Categories in calendar updated.','my-calendar')."</strong></p></div>";  
		}
    } else if (isset($_GET['mode']) && isset($_GET['category_id']) && $_GET['mode'] == 'edit' && !isset($_POST['mode'])) {
		$cur_cat = (int) $_GET['category_id'];
		mc_edit_category_form('edit',$cur_cat);
	} else if (isset($_POST['mode']) && isset($_POST['category_id']) && isset($_POST['category_name']) && isset($_POST['category_color']) && $_POST['mode'] == 'edit') {
		$update = array(
		'category_name'=>$_POST['category_name'],
		'category_color'=>$_POST['category_color'],
		'category_icon'=>$_POST['category_icon'],
		'category_private'=>( (isset( $_POST['category_private'] ))?1:0 )
		);
		$where = array( 'category_id'=>(int) $_POST['category_id'] );	
		$append = '';
		if ( isset($_POST['mc_default_category']) ) {
			update_option( 'mc_default_category',(int) $_POST['category_id'] );
			$append = __('Default category changed.','my-calendar');
		}		
		$results = $mcdb->update( my_calendar_categories_table(), $update, $where, $formats, '%d' );
		mc_delete_cache();
		if ($results) {
			echo "<div class=\"updated\"><p><strong>".__('Category edited successfully.','my-calendar')." $append</strong></p></div>";
		} else {
			echo "<div class=\"updated error\"><p><strong>".__('Category was not edited.','my-calendar')." $append</strong></p></div>";
		}
		$cur_cat = (int) $_POST['category_id'];		
		mc_edit_category_form('edit',$cur_cat);		
    }

	if ( isset($_GET['mode']) && $_GET['mode'] != 'edit' || isset($_POST['mode']) && $_POST['mode'] != 'edit' || !isset($_GET['mode']) && !isset($_POST['mode']) ) {
		mc_edit_category_form('add');
	} 
?></div>
<?php
}

function mc_edit_category_form($view='edit',$catID='') {
global $wpdb;
	$mcdb = $wpdb;
	if ( $catID != '' ) {
		$catID = (int) $catID; 
		$sql = "SELECT * FROM " . my_calendar_categories_table() . " WHERE category_id=$catID";
		$cur_cat = $mcdb->get_row($sql);
	}
global $path, $wp_plugin_dir,$wp_plugin_url;
	if ( is_custom_icon() ) {
		$directory = str_replace('/my-calendar','',$wp_plugin_dir) . '/my-calendar-custom/';
		$path = '/my-calendar-custom';
		$iconlist = my_dirlist($directory);
	} else {
		$directory = dirname(__FILE__).'/icons/';	
		$path = '/'.dirname(plugin_basename(__FILE__)).'/icons';
		$iconlist = my_dirlist($directory);
    }

?>
<?php if ($view == 'add') { ?>
    <h2><?php _e('Add Category','my-calendar'); ?></h2>
<?php } else { ?>
  <h2><?php _e('Edit Category','my-calendar'); ?></h2>
<?php } ?>

<div class="postbox-container" style="width: 70%">
<div class="metabox-holder">

<div class="ui-sortable meta-box-sortables">
<div class="postbox">
<h3><?php _e('Category Editor','my-calendar'); ?></h3>
	<div class="inside">	   
    <form id="my-calendar" method="post" action="<?php echo admin_url('admin.php?page=my-calendar-categories'); ?>">
		<div><input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce('my-calendar-nonce'); ?>" /></div>
		<?php if ($view == 'add') { ?>	
			<div>
			<input type="hidden" name="mode" value="add" />
            <input type="hidden" name="category_id" value="" />
			</div>
		<?php } else { ?>
			<div>
			<input type="hidden" name="mode" value="edit" />
            <input type="hidden" name="category_id" value="<?php if ( is_object($cur_cat) ) echo $cur_cat->category_id ?>" />
			</div>		
		<?php } ?>
			<fieldset>
			<legend><?php if ($view == 'add') { _e('Add Category','my-calendar'); } else { _e('Edit Category','my-calendar'); } ?></legend>
				<label for="category_name"><?php _e('Category Name','my-calendar'); ?>:</label> <input type="text" id="category_name" name="category_name" class="input" size="30" value="<?php if ( !empty($cur_cat) && is_object($cur_cat) ) echo stripslashes( esc_attr( $cur_cat->category_name ) ); ?>" /><br />
				<label for="category_color"><?php _e('Category Color (Hex format)','my-calendar'); ?>:</label> <input type="text" id="category_color" name="category_color" class="input" size="10" maxlength="7" value="<?php if ( !empty($cur_cat) &&  is_object($cur_cat) ) { echo (strpos($cur_cat->category_color,'#') !== 0)?'#':''; echo $cur_cat->category_color; } else { echo '#'; } ?>" /><br />
				<label for="category_icon"><?php _e('Category Icon','my-calendar'); ?>:</label> <select name="category_icon" id="category_icon">
	
		<?php
		foreach ($iconlist as $value) {
			if ( ( !empty($cur_cat) && is_object($cur_cat) ) && $cur_cat->category_icon == $value) {
				$selected = " selected='selected'";
			} else {
				$selected = "";
			}
			echo "<option value='$value'$selected style='background: url(".str_replace('my-calendar/','',$wp_plugin_url)."$path/$value) left 50% no-repeat;'>$value</option>";
		}
		?>			
					</select>
					<?php $checked = ( $view == 'add' )?'':mc_is_checked( 'mc_default_category',$cur_cat->category_id,'',true); ?>
					<input type="checkbox" value="on" name="mc_default_category" id="mc_default_category"<?php echo $checked; ?> /> <label for="mc_default_category"><?php _e('Default category','my-calendar'); ?></label>
					<?php if ( $view == 'add' ) { $checked = ''; } else { if ( !empty($cur_cat) &&  is_object($cur_cat) && $cur_cat->category_private == 1 ) { $checked=' checked="checked"'; } else { $checked = ''; } } ?>
					<p><input type="checkbox" value="on" name="category_private" id="category_private"<?php echo $checked; ?> /> <label for="category_private"><?php _e('Private category (logged-in users only)','my-calendar'); ?></label></p>
					
				</fieldset>
				<p>
					<input type="submit" name="save" class="button-primary" value="<?php if ($view == 'add') {  _e('Add Category','my-calendar'); } else { _e('Save Changes','my-calendar'); } ?> &raquo;" />
				</p>
		</form>
	</div>
</div>
</div>
<?php if ($view == 'edit') { ?>
<p><a href="<?php echo admin_url('admin.php?page=my-calendar-categories'); ?>"><?php _e('Add a New Category','my-calendar'); ?> &raquo;</a></p>
<?php } ?>

<div class="ui-sortable meta-box-sortables">
<div class="postbox">
<h3><?php _e('Category List','my-calendar'); ?></h3>
	<div class="inside">	
<?php mc_manage_categories(); ?>
	</div>
	</div>
</div>

</div>
</div>
	<?php jd_show_support_box(); ?>

<?php
}

function mc_manage_categories() {
	global $wpdb, $path, $wp_plugin_url;
	$mcdb = $wpdb;
?>
 <h2><?php _e('Manage Categories','my-calendar'); ?></h2>
<?php
    $co = ( !isset($_GET['co']) )?1:(int) $_GET['co'];
	switch ( $co ) {
		case 1: $cat_order = 'category_id';break;
		case 2: $cat_order = 'category_name';break;
		default: $cat_order = 'category_id';
	}
    // We pull the categories from the database	
    $categories = $mcdb->get_results("SELECT * FROM " . my_calendar_categories_table() . " ORDER BY $cat_order ASC");

	if ( !empty($categories) ) {
?>
	<table class="widefat page fixed" id="my-calendar-admin-table">
	<thead> 
		<tr>
			<th class="manage-column" scope="col"><?php echo ($co==2)?"<a href='".admin_url("admin.php?page=my-calendar-categories&amp;co=1")."'>":''; ?><?php _e('ID','my-calendar') ?><?php echo ($co==2)?'</a>':''; ?></th>
			<th class="manage-column" scope="col"><?php echo ($co==1)?"<a href='".admin_url("admin.php?page=my-calendar-categories&amp;co=2")."'>":''; ?><?php _e('Category Name','my-calendar') ?><?php echo ($co==1)?'</a>':''; ?></th>
			<th class="manage-column" scope="col"><?php _e('Category Color','my-calendar'); ?></th>
			<th class="manage-column" scope="col"><?php _e('Category Icon','my-calendar'); ?></th>
			<th class="manage-column" scope="col"><?php _e('Private','my-calendar'); ?></th>
			<th class="manage-column" scope="col"><?php _e('Edit','my-calendar'); ?></th>
			<th class="manage-column" scope="col"><?php _e('Delete','my-calendar'); ?></th>
		</tr>
	</thead>
       <?php
       $class = '';
       foreach ( $categories as $category ) {
	   $class = ($class == 'alternate') ? '' : 'alternate';
	   $default = ( $category->category_id == get_option('mc_default_category') )?' default':'';
           ?>
		<tr class="<?php echo $class.$default; ?>">
		<th scope="row"><?php echo $category->category_id; ?></th>
		<td><?php echo stripslashes( esc_attr( $category->category_name ) ); ?></td>
		<td style="background-color:<?php echo (strpos($category->category_color,'#') !== 0)?'#':''; echo $category->category_color; ?>;">&nbsp;</td>
	    <td style="background-color:<?php echo (strpos($category->category_color,'#') !== 0)?'#':''; echo $category->category_color; ?>;"><img src="<?php echo str_replace('my-calendar/','',$wp_plugin_url). $path; ?>/<?php echo stripslashes( esc_attr( $category->category_icon ) ); ?>" alt="" /></td>		 
		<td><?php echo ( $category->category_private == 1 )?'Yes':'No'; ?></td>
		<td><a href="<?php echo admin_url("admin.php?page=my-calendar-categories&amp;mode=edit&amp;category_id=$category->category_id"); ?>" class='edit'><?php _e('Edit','my-calendar'); ?></a></td>
	     <?php
		       if ($category->category_id == 1) {
					echo '<td>'.__('N/A','my-calendar').'</td>';
		       } else {
	               ?>
	               <td><a href="<?php echo admin_url("admin.php?page=my-calendar-categories&amp;mode=delete&amp;category_id=$category->category_id"); ?>" class="delete" onclick="return confirm('<?php _e('Are you sure you want to delete this category?','my-calendar'); ?>')"><?php _e('Delete','my-calendar'); ?></a></td>
	               <?php
		       }
        ?>
              </tr>
                <?php
          }
      ?>
      </table>
      <?php
   } else {
     echo '<p>'.__('There are no categories in the database - something has gone wrong!','my-calendar').'</p>';
   }
?>
<?php
}