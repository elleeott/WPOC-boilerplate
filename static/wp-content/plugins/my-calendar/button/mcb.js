// pass the window to the object
var myCalQT = window.myCalQT || {};
// set the function
(myCalQT.Tag = function() {
	return {
		// set the function
		embed : function() {
			// check if the URL is a string  and if the function tb_show works, if not, return
			if (typeof this.configUrl !== 'string' || typeof tb_show !== 'function') {
				return;
			}	
			// pepare the url
			var url = this.configUrl + ((this.configUrl.match(/\?/)) ? "&" : "?") + "TB_iframe=true";	
			// call lightbox to show the embed
			tb_show('My Calendar Shortcode Generator', url , false);
		}
	};
}());
/*  Generator specific script */
(myCalQT.Tag.Generator = function(){
	// tags to find
	var tags = 'category,ltype,lvalue,format,showkey,shownav,showjump,toggle,time'.split(',');
	// to validate and generate the tag
	var vt = function(id){
		var form = jQuery('#'+id).val();
		if(form==''){
			return '';
		}else{
			return ' '+id+'="'+form+'"';
		}
	};
	// to build tag
	var buildTag = function() {
		var r = '[my_calendar'; 
		for (i=0;i<tags.length;i++){
			r += vt(tags[i]);
		}
		return r + ']';
	};
	
	// get the selected text in the box
	var getSel = function(){
		var win = window.parent || window;
		var ret = '';
		if ( typeof win.tinyMCE !== 'undefined' && ( win.ed = win.tinyMCE.activeEditor ) && !win.ed.isHidden() ) {
			win.ed.focus();
			if (win.tinymce.isIE){
				win.ed.selection.moveToBookmark(win.tinymce.EditorManager.activeEditor.windowManager.bookmark);
			}
			ret = win.tinymce.EditorManager.activeEditor.selection.getContent({format : 'text'});
		} else {
			var myField = win.edCanvas;
			// IE
			if (document.selection) {
				myField.focus();
				ret = (win.document.all) ? win.document.selection.createRange().text : win.document.getSelection();
			}
			// Mozilla, Netscape
			else if (myField.selectionStart || myField.selectionStart == '0') {
				ret = myField.value.substring(myField.selectionStart, myField.selectionEnd);
			} 
		}
		return (ret=='')?false:ret;
	};
	
	// Tag parser
	var parseTagEdit = function(){
		// get selection
		var selec = getSel();
		if(selec != false){
			// trim
			selec = selec.replace(/^\s+|\s+$/g,'');
			// look for the endpoint
			var endp = selec.lastIndexOf(']');
			// if no endpoint is found
			if(endp == -1){
				return;
			}
			// look for the starting point
			if(selec.substring(0,13) != '[my_calendar '){
				return;
			}
			// only params
			selec = selec.substring(13, endp);
			// remove more than two white spaces
			selec = selec.replace(/\s+/g, ' ')
			// get params separated by space
			var params = selec.split(' ');
			// modify values
			for (i=0;i<params.length;i++){
				var parval = params[i].split('=');
				jQuery('#'+parval[0]).val(parval[1]);
			}
		}	
	};
	// to insert tag
	var insertTag = function() {
		var tag = buildTag() || "";
		var win = window.parent || window;
				
		if ( typeof win.tinyMCE !== 'undefined' && ( win.ed = win.tinyMCE.activeEditor ) && !win.ed.isHidden() ) {
			win.ed.focus();
			if (win.tinymce.isIE){
				win.ed.selection.moveToBookmark(win.tinymce.EditorManager.activeEditor.windowManager.bookmark);
			}
			win.ed.execCommand('mceInsertContent', false, tag);
		} else {
			win.edInsertContent(win.edCanvas, tag);
		}	
		// Close Lightbox
		win.tb_remove();
	};
	return {
		initialize : function() {
			if (typeof jQuery === 'undefined') {
				return;
			}
			jQuery("#mycalendar").click(function(e) {
				e.preventDefault();
				insertTag();
			});
			parseTagEdit();
		}
	};
}());