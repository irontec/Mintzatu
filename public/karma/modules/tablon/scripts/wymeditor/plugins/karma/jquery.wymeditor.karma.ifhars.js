/*
 * Plugin de ifhars para poder enlazar ficheros
 *  
 */
//Extend WYMeditor
WYMeditor.editor.prototype.karma_ifhars = function(config, inst) {
    var config = config;
    var wym = this;
    
    config.linkClass = config.linkClass || '';
    
    if (config.karmaSection) {
        var href = window.location.href;
        var karmaUrl = href.substring(0, href.indexOf(window.location.search));
        config.url = karmaUrl + '?op=' + config.karmaSection + '&embed=true';
    }

    // construct the button's html
    var html = "<li class='wym_tools_karma_ifhars'>" + "<a class=" + inst
            + " name='Ficheros' href='#'>" + "Enlace a Ficheros"
            + "</a></li>";
    
    // add the button to the tools box
    jQuery(wym._box).find(
        wym._options.toolsSelector + wym._options.toolsListSelector)
        .append(html);
            
    //handle click event
    jQuery(wym._box).find('li.wym_tools_karma_ifhars a.' + inst).click(function() {
        var $dialog = jQuery('<div>'
                  + '<label style="width:160px;margin:5px;display:block;float:left">Url de descarga:</label>'
                  + '<input type="text" style="width:400px; float:left; margin: 5px;" name="downloadUrl" />'
                  + '<label style="width:160px;margin:5px;display:block;float:left;clear:left">Nombre del fichero:</label>'
                  + '<input type="text" style="width:400px; float:left; margin: 5px;"name="fileName"/>'
                  + '<iframe src="' + config.url + '" width="800" height="600" scrolling="no"/>'
                  + '<p class="loading" style="clear: left; text-align: center">Cargando explorador de archivos...</p>'
                  + '</div>');
        
        $loadingMessage = $dialog.find('p.loading');
        $urlInput = $dialog.find('input[name=downloadUrl]');
        $fileNameInput = $dialog.find('input[name=fileName]');
        $ifharsIframe = $dialog.find('iframe');
        $ifharsIframe.hide();

        $ifharsIframe.load(function(){
            $loadingMessage.hide();
            $(this).show();
            $(this).contents().delegate('a.fileLink:not(.dir)', 'click', function() {
                $urlInput.val(config.downloadUrl.replace('%hash%', $(this).attr('rel')));
                $fileNameInput.val($(this).find('.fileName').text());
            });
        });

        $dialog.dialog({
            height: 670,
            width: 870,
            title: 'Enlace a ficheros',
            buttons:{
                'Ok': function() {
                    wym.insert('<a class="' + config.linkClass + '" href="' + $urlInput.val() + '">' + $fileNameInput.val() + '</a>');
                    wym.update();
                    $(this).dialog('destroy');
                    return;
                }
            }
        });
    });
};
