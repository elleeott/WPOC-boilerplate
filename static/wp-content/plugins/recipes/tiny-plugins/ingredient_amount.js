function tiny_plugin() {
    return "[tiny-plugin]";
}
 
(function() {
    tinymce.create('tinymce.plugins.ingredients', {
 
        init : function(ed, url){
            ed.addButton('ingredients', {
            title : 'Insert ingredients',
                onclick : function() {
                    ed.execCommand(
                    'mceInsertContent',
                    false,
                    tiny_plugin()
                    );
                },
                image: url + "/wand.png"
            });
        }
    });
 
    tinymce.PluginManager.add('ingredients', tinymce.plugins.ingredients);
 
})();