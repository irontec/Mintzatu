window.jQuery&&(function(b,a,c){b.extend(WYMeditor.editor.prototype,{initResize:function(){var k=b.browser.msie?document.body:a,f=false,e,d,j=-1,i=b.browser.msie?"n-resize":"ns-resize",l=function(m){if(f){g();return false}f=true;d=b(e).height()+((m.clientY+b(document).scrollTop())*j);b("head").append('<style class="cursorImportant" type="text/css">body * {cursor: '+i+" !important;}</style>");b(e).parent().css("position","relative").end().css("visibility","hidden").after(b('<div class="wym_iframe_masque" />').css({position:"absolute",top:"1px",width:"100%",height:"100%",background:"#e8f0fa",border:"1px dashed gray"}));b(k).bind("mousemove",h).bind("mouseup",g);return false},h=function(m){b(e).height(d-((m.clientY+b(document).scrollTop())*j));return false},g=function(){b("head").children(".cursorImportant").remove();b(k).unbind("mousemove",h).unbind("mouseup",g);b(e).css("visibility","visible").next(".wym_iframe_masque").remove();f=false;return false};e=b(this._box).find(".wym_iframe > iframe");b(this._box).append(b('<div class="wym_resize" />').css({height:"2px",width:"100%",margin:"0 auto -3px",border:"1px solid silver","border-width":"1px 0 1px",cursor:i}).mousedown(l))}})})(window.jQuery,this);