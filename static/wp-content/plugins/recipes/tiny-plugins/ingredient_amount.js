(function() {
    tinymce.create('tinymce.plugins.ingredient_amount', {
        init : function(ed, url){
            ed.addButton('ingredient_amount', {
            title : 'ingredient amount',
            onclick : function() {
				var text=ed.selection.getContent({'format':'text'});
				ed.execCommand('mceInsertContent',false,'[ingredient_amount]' + text + '[/ingredient_amount]');
            },
            image: url + "/amt.png"
            });
        }
    });
 
    tinymce.PluginManager.add('ingredient_amount', tinymce.plugins.ingredient_amount);
 
})();
