/*
 * WYMeditor : what you see is What You Mean web-based editor
 * Copyright (c) 2005 - 2009 Jean-Francois Hovinne, http://www.wymeditor.org/
 * Dual licensed under the MIT (MIT-license.txt)
 * and GPL (GPL-license.txt) licenses.
 *
 * For further information visit:
 *        http://www.wymeditor.org/
 *
 * File Name:
 *        jquery.wymeditor.fullscreen.js
 *        Fullscreen plugin for WYMeditor
 *
 * File Authors:
 *        Luis Santos (luis.santos a-t openquest dotpt)
 */

//Extend WYMeditor

WYMeditor.editor.prototype.karma_full = function() {
 
  var wym = this;
  
  //construct the button's html
  var html = "<li class='wym_tools_karma_full'>"
         + "<a name='Full screen' href='#'>"
         + "Full Screen"
         + "</a></li>";

  //add the button to the tools box
  jQuery(wym._box)
    .find(wym._options.toolsSelector + wym._options.toolsListSelector)
    .append(html);

  //handle click event
  jQuery(wym._box).find('li.wym_tools_karma_full a').click(function() {
	  if ( typeof(jQuery(wym._box).attr('style')) == 'undefined' || jQuery(wym._box).css('position') != 'fixed') {
		  console.log($(window).height());
		  jQuery(wym._box).css({position:'fixed',top:'0px',left:'0px',width:$(window).width()+19,height:$(window).height(),'z-index':'800'});
		  jQuery(wym._box).find('iframe').css('height',$(window).height()-39);
	  } else {
		  jQuery(wym._box).css({position:'relative',top:'0px',left:'0px',width:'',height:'','z-index':''});
	  }
	  
	  return;
  });
    
    
};