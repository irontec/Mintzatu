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
				
				
			$(self).wrap('<span style="display:block;position:relative;width:80%;"></span>');
      		$(self).css("opacity","0").css("z-index",2).css("position","relative").css("cursor","pointer");
      		$(self).parent("span").append('<input type="text" id="file_' + (new Date()).getTime() + '" readonly="true" class="uploadField" style="width:'+($(this).width()-15)+'px; margin-right:15px;padding-right:16px;background:#fff url('+settings.bgImage+') right center no-repeat;position:absolute;z-index:1;top:0px;left:0px;" />');
      		
      		$(self).change(function(e) {
      			var actual = e.target;
  				if(actual!=self) return false;
      			$(actual).clearInterval();
      			if ($(actual).val()=="") return;
      			var inp = $(actual).siblings("input");
      			if ($(actual).val()==inp.attr("realVal")) return;
				inp.attr("realVal",$(actual).val());
				inp.val($(actual).val());
				this.interval = window.setInterval(function() {
					inp.val(inp.val().substr(1));
					if (inp.val()=="") inp.val(inp.attr("realVal"));
				},s.scrollInterval);
      		});
      		
      		return $(this);
      	});
    };
})(jQuery);
