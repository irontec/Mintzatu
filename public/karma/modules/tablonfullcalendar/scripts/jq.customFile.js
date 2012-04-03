(function($) {
    $.fn.customFile = function(options) {
			var settings = {
            bgImage			: "./icons/hdd_mount.png",
			scrollInterval	: 500
			};
        
        	if(options) {
            $.extend(settings, options);
        	}
        	
        	
      	return this.each(function() {
				var self = this;
				var s = settings;
				$(this).val("");
				
				$.fn.clearInterval = function() {
					window.clearInterval(self.interval);
					window.setTimeout(function() { $(self).siblings("input").val("");	},s.scrollInterval);
					return $(self);
				};
				
				
				$(this).wrap('<span style="display:block;position:relative;width:80%;"></span>');
      		$(this).css("opacity","0").css("z-index",2).css("position","relative").css("cursor","pointer");
      		$(this).parent("span").append('<input type="text" readonly="true" class="uploadField" style="width:'+($(this).width()-15)+'px; margin-right:15px;padding-right:16px;background:#fff url('+settings.bgImage+') right center no-repeat;position:absolute;z-index:1;top:0px;left:0px;" />');
      		
      		$(this).bind("change",function() {
      			$(this).clearInterval();
      			if ($(this).val()=="") return;
					var inp = $(this).siblings("input");
					inp.attr("realVal",$(this).val());
					inp.val($(this).val());
					this.interval = window.setInterval(function() {
						inp.val(inp.val().substr(1));
						if (inp.val()=="") inp.val(inp.attr("realVal"));
					},s.scrollInterval);
      		});
      		return $(this);
      	});
    };
})(jQuery);