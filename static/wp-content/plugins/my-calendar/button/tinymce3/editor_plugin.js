(function() {
	// Load plugin specific language pack
	tinymce.PluginManager.requireLangPack('mcqt');
	tinymce.create('tinymce.plugins.myCalendar', {
		/**
		 * Initializes the plugin, this will be executed after the plugin has been created.
		 * This call is done before the editor instance has finished it's initialization so use the onInit event
		 * of the editor instance to intercept that event.
		 *
		 * @param {tinymce.Editor} ed Editor instance that the plugin is initialized in.
		 * @param {string} url Absolute URL to where the plugin is located.
		 */
		init : function(ed, url) {
			
			if (typeof myCalQT === 'undefined' || 
				typeof myCalQT.Tag === 'undefined' ||
				typeof myCalQT.Tag.embed !== 'function'
			) {
				return;
			}
			ed.addCommand('createMCTag', function() {
				myCalQT.Tag.embed.apply(myCalQT.Tag);
			});
			ed.addButton('myCalendar', {
				title : 'mcqt.description',
				image : url + '/mcb.png',
				cmd : 'createMCTag'
			});
		},
		/**
		 * Creates control instances based in the incomming name. This method is normally not
		 * needed since the addButton method of the tinymce.Editor class is a more easy way of adding buttons
		 * but you sometimes need to create more complex controls like listboxes, split buttons etc then this
		 * method can be used to create those.
		 *
		 * @param {String} n Name of the control to create.
		 * @param {tinymce.ControlManager} cm Control manager to use inorder to create new control.
		 * @return {tinymce.ui.Control} New control instance or null if no control was created.
		 */
		createControl : function(n, cm) {
			return null;
		},
		/**
		 * Returns information about the plugin as a name/value array.
		 * The current keys are longname, author, authorurl, infourl and version.
		 *
		 * @return {Object} Name/value array containing information about the plugin.
		 */
		getInfo : function() {
			return {
				longname : 'My Calendar TinyMCE Plugin',  
				author : 'Joseph C Dolson',  
				authorurl : 'http://www.joedolson.com',  
				infourl : 'http://http://www.joedolson.com/articles/my-calendar/', 
				version : "1.7.9"
			};
		}
	});
	// Register plugin
	tinymce.PluginManager.add('mcqt', tinymce.plugins.myCalendar);
})();