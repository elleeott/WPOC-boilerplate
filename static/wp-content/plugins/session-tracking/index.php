<?php
/*
Plugin Name: session tracking
Description: keycode and related unique phone numbers for source tracking.
*/

global $session_tracking_version;
$session_tracking_version = '1.0';

//initial db setup
function session_track_install() {
	global $wpdb;
	global $session_tracking_version;
	$table_name = $wpdb->prefix. 'session_tracking';
	$sql = "CREATE TABLE " . $table_name . " (
	  id mediumint(9) NOT NULL AUTO_INCREMENT,
	  keycode varchar(5) NOT NULL,
	  phone_number varchar(32) NOT NULL,
	  keycode_description text NOT NULL,
	  user_id bigint(20) NOT NULL,
	  last_edit_date timestamp NOT NULL,
	  UNIQUE KEY id (id)
	) ;";
	
   require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
   dbDelta($sql);
   add_option("session_tracking_version", $session_tracking_version);	
}

register_activation_hook(__FILE__,'session_track_install');

//create admin screen
add_action('admin_menu', 'session_tracking_menu');
function session_tracking_menu() {
	add_options_page('Session Tracking', 'Session Tracking', 'manage_options', 'session-tracking-options', 'session_tracking_options');
}

function session_tracking_options() {
	if (!current_user_can('manage_options'))  {
		wp_die( __('You do not have sufficient permissions to access this page.') );
	}
?>
<style>
	.session-tracking table {
		border-collapse: collapse;
	}
	.session-tracking th {
		text-align:left;
		border-bottom:3px solid #efefef;
		padding:5px;
	}
	.session-tracking td {
		text-align:left;
		vertical-align:top;
		padding:5px;
		border-bottom:1px solid #efefef;
	}
	.session-tracking textarea {
		width:300px;
	}

</style>	
<div class="wrap session-tracking">
<h2>Session Tracking</h2>
<?php 
	//print_r($_POST);
	global $wpdb;
	$table_name = $wpdb->prefix. 'session_tracking';

	//delete rows
	if(isset($_POST['remove'])) {
		$key_id = $_POST['id'];
		$wpdb->query($wpdb->prepare("DELETE FROM $table_name WHERE id = %s", $key_id));
	}
	//validate form
	if(isset($_POST['submit'])) {
		$keycode=$_POST['keycode'];
		$phone_number=$_POST['phone_number'];
		$existing_items = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name WHERE keycode = %s OR phone_number = %s",$keycode,$phone_number));
		
		if(empty($_POST['keycode']) || empty($_POST['phone_number'])) {
			echo '<div id="message" class="updated"><p>you must enter a valid keycode and phone number.</p></div>';
		} elseif($existing_items) {
			echo '<div id="message" class="updated"><p>phone number and keycode must be unique.</p></div>';
		} else {
			//insert rows
			$values['keycode']=$_POST['keycode'];
			$values['phone_number']=$_POST['phone_number'];
			if(isset($_POST['keycode_description'])){
				$values['keycode_description']=$_POST['keycode_description'];	
			}
			$wpdb->insert($table_name,$values);
		}
	}
?>
		<table>
			<thead>
				<tr>
					<th>keycode</th>
					<th>phone number</th>
					<th>description</th>
					<th></th>
				</tr>
			</thead>
			<tbody>
			<?php	
				//list existing keycodes		
				$items = $wpdb->get_results("SELECT * FROM $table_name ORDER BY last_edit_date");
				if($items){
					foreach($items as $item) {
						echo '<form name="session-tracking" method="post" action="">';
						echo '<tr>';
						echo '<td>'.$item->keycode.'</td>';
						echo '<td>'.$item->phone_number.'</td>';
						echo '<td>'.$item->keycode_description.'</td>';
						echo '<td><input type="hidden" name="id" value="'.$item->id.'"/><input class="button" type="submit" name="remove" value="remove"></td>';
						echo '</tr>';
						echo '</form>';
					}	
				}
				
			?>	
				<form name="session-tracking" method="post" action="">	
					<tr>
						<td><input type="text" name="keycode"/></td>
						<td><input type="text" name="phone_number" /></td>
						<td><textarea name="keycode_description"></textarea></td>
						<td><input id="submit" name="submit" class="button-primary" type="submit" value="add new keycode" /></td>
					</tr>		
				</form>
			</tbody>
		</table>
</div>
<?php
		
}


//set session tracking variables
function set_key_session() {
	global $wpdb;
	$table_name = $wpdb->prefix. 'session_tracking';
	if(!is_admin()){
		if(!isset($_SESSION)) {
			session_start();
		}

		//if keycode param is in url string and keycode is valid
		if(isset($_GET['keycode'])) {
			$keycode=$_GET['keycode'];
			$keycode_query = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE keycode = %s",$keycode));
			if($keycode_query){
				$_SESSION['keycode']=$keycode;
				$_SESSION['phone_number']=$keycode_query->phone_number;
			}
		//use default keycode otherwise	
		} elseif(!isset($_SESSION['keycode'])) {
			$_SESSION['keycode'] = 'AA999';
			$_SESSION['phone_number'] = '800.555.9999';
		}

	}
}
add_action('init', 'set_key_session');


function get_session_phone() {
	echo $_SESSION['phone_number'];
}