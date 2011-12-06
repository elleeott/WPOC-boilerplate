<?php

class acf_Wysiwyg extends acf_Field
{
	
	/*--------------------------------------------------------------------------------------
	*
	*	Constructor
	*
	*	@author Elliot Condon
	*	@since 1.0.0
	*	@updated 2.2.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function __construct($parent)
	{
    	parent::__construct($parent);
    	
    	$this->name = 'wysiwyg';
		$this->title = __("Wysiwyg Editor",'acf');
		
		add_action('admin_head', 'wp_tiny_mce');
		
   	}
   	
   	
   	/*--------------------------------------------------------------------------------------
	*
	*	admin_print_scripts / admin_print_styles
	*
	*	@author Elliot Condon
	*	@since 3.0.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function admin_print_scripts()
	{
		wp_enqueue_script(array(
		
			'jquery',
			'jquery-ui-core',
			'jquery-ui-tabs',

			// wysiwyg
			'editor',
			'thickbox',
			'media-upload',
			'word-count',
			'post',
			'editor-functions',
			'tiny_mce',
						
		));
	}
	
	function admin_print_styles()
	{
  		wp_enqueue_style(array(
			'thickbox',		
		));
	}
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	admin_head
	*
	*	@author Elliot Condon
	*	@since 2.0.6
	* 
	*-------------------------------------------------------------------------------------*/
	
	function admin_head()
	{
		?>
		<script type="text/javascript">
		(function($){
			
			// store wysiwyg buttons
			$.acf_wysiwyg_buttons = {};
			

			$.fn.acf_deactivate_wysiwyg = function(){

				$(this).find('.acf_wysiwyg textarea').each(function(){

					tinyMCE.execCommand("mceRemoveControl", false, $(this).attr('id'));
					
				});
				
			};
			
			
			$.fn.acf_activate_wysiwyg = function(){
				
				// tinymce must exist
				if(!typeof(tinyMCE) == "object")
				{
					return false;
				}
	
				
				
					
				// add tinymce to all wysiwyg fields
				$(this).find('.acf_wysiwyg textarea').each(function(){
					
					// reset buttons
					tinyMCE.settings.theme_advanced_buttons1 = $.acf_wysiwyg_buttons.theme_advanced_buttons1;
					tinyMCE.settings.theme_advanced_buttons2 = $.acf_wysiwyg_buttons.theme_advanced_buttons2;
				
					var toolbar = $(this).closest('.acf_wysiwyg').attr('data-toolbar');
					
					if(toolbar == 'basic')
					{
						tinyMCE.settings.theme_advanced_buttons1 = "bold,italic,formatselect,|,link,unlink,|,bullist,numlist,|,undo,redo";
						tinyMCE.settings.theme_advanced_buttons2 = "";
					}
					else
					{
						// add images + code buttons
						tinyMCE.settings.theme_advanced_buttons2 += ",code";
					}
					

					//console.log( $(this).attr('id') + ': before: ' + tinyMCE.settings.theme_advanced_buttons1);
					//tinyMCE.execCommand("mceRemoveControl", false, $(this).attr('id'));
					tinyMCE.execCommand('mceAddControl', false, $(this).attr('id'));


				});

				
			};
			
			
			$(window).load(function(){
				
				// store variables
				$.acf_wysiwyg_buttons.theme_advanced_buttons1 = tinyMCE.settings.theme_advanced_buttons1;
				$.acf_wysiwyg_buttons.theme_advanced_buttons2 = tinyMCE.settings.theme_advanced_buttons2;
				
				$('#poststuff').acf_activate_wysiwyg();
				
			});
			
			// Sortable: Start
			$('#poststuff .repeater > table > tbody, #poststuff .acf_flexible_content > .values').live( "sortstart", function(event, ui) {
				
				$(ui.item).find('.acf_wysiwyg textarea').each(function(){
					tinyMCE.execCommand("mceRemoveControl", false, $(this).attr('id'));
				});
				
			});
			
			// Sortable: End
			$('#poststuff .repeater > table > tbody, #poststuff .acf_flexible_content > .values').live( "sortstop", function(event, ui) {
				
				$(ui.item).find('.acf_wysiwyg textarea').each(function(){
					tinyMCE.execCommand("mceAddControl", false, $(this).attr('id'));
				});
				
			});
			
			// Delete
			$('#poststuff .repeater a.remove_field').live('click', function(event){
				
				var tr = $(event.target).closest('tr').find('.acf_wysiwyg textarea').each(function(){
					tinyMCE.execCommand("mceRemoveControl", false, $(this).attr('id'));
				});				
			});
			
			
		})(jQuery);
		</script>
		<style type="text/css">
			.acf_wysiwyg iframe{ 
				min-height: 250px;
			}
			
			#post-body .acf_wysiwyg .wp_themeSkin .mceStatusbar a.mceResize {
				top: -2px !important;
			}
			
			.acf_wysiwyg .editor-toolbar {
				display: none;
			}
			
			.acf_wysiwyg #editorcontainer {
				background: #fff;
			}
		</style>
		<?php
	}
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	create_options
	*
	*	@author Elliot Condon
	*	@since 2.0.6
	*	@updated 2.2.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function create_options($key, $field)
	{	
		// vars
		$field['toolbar'] = isset($field['toolbar']) ? $field['toolbar'] : 'full';
		$field['media_upload'] = isset($field['media_upload']) ? $field['media_upload'] : 'yes';
		
		?>
		<tr class="field_option field_option_<?php echo $this->name; ?>">
			<td class="label">
				<label><?php _e("Toolbar",'acf'); ?></label>
			</td>
			<td>
				<?php 
				$this->parent->create_field(array(
					'type'	=>	'radio',
					'name'	=>	'fields['.$key.'][toolbar]',
					'value'	=>	$field['toolbar'],
					'layout'	=>	'horizontal',
					'choices' => array(
						'full'	=>	'Full',
						'basic'	=>	'Basic'
					)
				));
				?>
			</td>
		</tr>
		<tr class="field_option field_option_<?php echo $this->name; ?>">
			<td class="label">
				<label><?php _e("Show Media Upload Buttons?",'acf'); ?></label>
			</td>
			<td>
				<?php 
				$this->parent->create_field(array(
					'type'	=>	'radio',
					'name'	=>	'fields['.$key.'][media_upload]',
					'value'	=>	$field['media_upload'],
					'layout'	=>	'horizontal',
					'choices' => array(
						'yes'	=>	'Yes',
						'no'	=>	'No',
					)
				));
				?>
			</td>
		</tr>
		<?php
	}
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	create_field
	*
	*	@author Elliot Condon
	*	@since 2.0.5
	*	@updated 2.2.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function create_field($field)
	{
		// vars
		$field['toolbar'] = isset($field['toolbar']) ? $field['toolbar'] : 'full';
		$field['media_upload'] = isset($field['media_upload']) ? $field['media_upload'] : 'yes';
		
		$id = 'wysiwyg_' . uniqid();
		
		
		?>
		<div class="acf_wysiwyg" data-toolbar="<?php echo $field['toolbar']; ?>">
			<?php if($field['media_upload'] == 'yes'): ?>
			<div id="editor-toolbar" class="hide-if-no-js">	
				<div id="media-buttons" class="hide-if-no-js">
					Upload/Insert 
					<a title="Add an Image" class="thickbox" id="add_image" href="media-upload.php?post_id=1802&amp;type=image&amp;TB_iframe=1&amp;width=640&amp;height=314">
						<img onclick="return false;" alt="Add an Image" src="<?php echo $this->parent->wpadminurl; ?>images/media-button-image.gif?ver=20100531">
					</a>
					<a title="Add Video" class="thickbox" id="add_video" href="media-upload.php?post_id=1802&amp;type=video&amp;TB_iframe=1&amp;width=640&amp;height=314">
						<img onclick="return false;" alt="Add Video" src="<?php echo $this->parent->wpadminurl; ?>images/media-button-video.gif?ver=20100531">
					</a>
					<a title="Add Audio" class="thickbox" id="add_audio" href="media-upload.php?post_id=1802&amp;type=audio&amp;TB_iframe=1&amp;width=640&amp;height=314">
						<img onclick="return false;" alt="Add Audio" src="<?php echo $this->parent->wpadminurl; ?>images/media-button-music.gif?ver=20100531">
					</a>
					<a title="Add Media" class="thickbox" id="add_media" href="media-upload.php?post_id=1802&amp;TB_iframe=1&amp;width=640&amp;height=314">
						<img onclick="return false;" alt="Add Media" src="<?php echo $this->parent->wpadminurl; ?>images/media-button-other.gif?ver=20100531">
					</a>
				</div>
			</div>
			<?php endif; ?>
			<div id="editorcontainer">
				<textarea id="<?php echo $field['name']; ?>" name="<?php echo $field['name']; ?>" ><?php echo wp_richedit_pre($field['value']); ?></textarea>
			</div>
		</div>
		<?php

	}
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	get_value_for_api
	*
	*	@author Elliot Condon
	*	@since 3.0.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function get_value_for_api($post_id, $field)
	{
		// vars
		$value = parent::get_value($post_id, $field);
		
		$value = apply_filters('the_content',$value); 
		
		return $value;
	}
	

}

?>