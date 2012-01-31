(function() {
    tinymce.create('tinymce.plugins.ingredient_name', {
 
        init : function(ed, url){
            ed.addButton('ingredient_name', {
            title : 'Tag ingredient',
            onclick : function() {
				var text=ed.selection.getContent({'format':'text'});
				ed.execCommand('mceInsertContent',false,'[ingredient_name]' + text + '[/ingredient_name]');
            },
                image: url + "/name.png"
            });
        }
    });
 
    tinymce.PluginManager.add('ingredient_name', tinymce.plugins.ingredient_name);
 
})();
