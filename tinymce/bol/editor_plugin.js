(function(){
    tinymce.create('tinymce.plugins.bolproductlink',{
        init:function(ed,url){
            var t=this;
            ed.addCommand('bolproductlink',function(ui,val)
                {ed.windowManager.open(
                    {file:url+'/'+val+'.php',width:t._pluginWidth[val],height:t._pluginHeight[val],inline:1,auto_focus:0},
                    {plugin_url:url})
                }
            );
            ed.addButton('bolproductlink',{
                title:'insert bol.com Products/Widgets',
                cmd:'bolproductlink',
                image:url+'/img/bol.gif'});
                ed.onInit.add(function(){
                    if(ed.settings.content_css!==false){
                        dom=ed.windowManager.createInstance('tinymce.dom.DOMUtils',document);
                        dom.loadCSS(url+'/css/button.css');
                        ed.dom.loadCSS(url+'/css/button.css')
                    }
                })
            },
        _pluginFunctions:{
            'bol-product-link':'Productlink',
            'bol-bestsellers':'Bestsellerslijst',
            'bol-search':'Zoekwidget'},
        _pluginHeight:{
            'bol-product-link':'800',
            'bol-bestsellers':'800',
            'bol-search':'840'},
        _pluginWidth:{
            'bol-product-link':'750',
            'bol-bestsellers':'750',
            'bol-search':'750'},
        getInfo:function(){
            return{
                longname:'bol.com Product Links',
                author:'DAXX',
                authorurl:'http://daxx.com',
                infourl:'http://daxx.com',
                version:'0.1'
            }},
        createControl:function(n,cm){
            var t=this,menu=t._cache.menu,c,ed=tinyMCE.activeEditor,each=tinymce.each;
            if(n!='bolproductlink'){return null}
            c=cm.createSplitButton(n,{cmd:'',scope:t,title:'insert bol.com Product/Widgets'});
            c.onRenderMenu.add(function(c,m){m.add({'class':'mceMenuItemTitle',title:'bol.com promotiematerialen'}).setDisabled(1);
            each(t._pluginFunctions,function(value,key){
                var o={icon:0},mi;
                o.onclick=function(){ed.execCommand('bolproductlink',true,key)};
                o.title=value;
                mi=m.add(o);
                menu[key]=mi});
            t._selectMenu(ed)});
            return c},
        _cache:{menu:{}},
        _selectMenu:function(ed){
            var fe=ed.selection.getNode(),each=tinymce.each,menu=this._cache.menu;
            each(this.shortcodes,function(value,key){
                if(typeof menu[key]=='undefined'||!menu[key]){return}
                menu[key].setSelected(ed.dom.hasClass(fe,key))})}
    });

    tinymce.PluginManager.add('bolproductlink',tinymce.plugins.bolproductlink)
})();
