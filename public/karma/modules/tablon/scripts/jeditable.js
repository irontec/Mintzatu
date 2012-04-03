function customRange(input) {
    var otherDate =  $(".date-pick[name='" + $(input).attr('coupledate') + "']");
    var pos = $(input).attr('couplepos');
    var minidate = null;
    var maxidate = null;

    if (typeof($(input).attr('coupledate')) != 'undefined' && $(input).attr('coupledate') && typeof($(input).attr('couplepos')) !== 'undefined' && $(input).attr('couplepos')) {
        if (typeof($(input).attr('mindate')) != 'undefined' && $(input).attr('mindate')) {
            minidate = $(input).attr('mindate');
        }
        if (typeof($(input).attr('maxdate')) != 'undefined' && $(input).attr('maxdate')) {
            maxidate = $(input).attr('maxdate');
        }
        return {minDate: ((pos === "max" && otherDate.val() !== "") ? otherDate.datepicker("getDate") : minidate),
            maxDate: ((pos === "min" && otherDate.val() !== "") ? otherDate.datepicker("getDate") : maxidate)
        };
    } else {
        return;
    }
}

function dodate(llamador) {
    var contenedor;
    if (llamador !== undefined) {
        contenedor = llamador;
    } else {
        contenedor = $('body');
    }
    $('.date-pickESP', contenedor).each(function () {
        var val = $(this).val();
        var aDateTime = val.split(" ");
        var defaultDate = aDateTime[0];
        var defaultTime = aDateTime[1];
        var box = $(this);

        box.datepicker({
            showOn: "both",
            buttonImage: "icons/calendar.gif",
            yearRange: "-100:+20",
            dateFormat: $(this).attr('datef'),
            buttonImageOnly: true,
            defaultDate: defaultDate,
            onClose: function () {
                var id = $(this).attr('id');
                $('.dateTimeButton').hide();
                box.parent().css('overflow', '');
                box.parent().append('<input style="width: 60px;" type="text" name="tmptime" id="tmp' + id + '" class="otime" value="' + defaultTime + '" />');
                $('#tmp' + id).timepicker({
                    divId: "mytimepicker", 
                    onClose: function (element) {
                        var hora = element.val();
                        if (hora === "undefined") {
                            hora = "12:00";
                        }
                        val = box.val();
                        val = val + ' ' + hora;
                        box.val(val);
                        defaultTime = element.val();
                        element.fadeOut('slow', function () {
                            element.remove();
                            $('.dateTimeButton').show();
                        });
                    }
                });
                $('#tmp' + id).focus();
            }
        }).addClass("embed");
    });

    $('.date-pick', contenedor).each(function () {
        var defaultDate = null;
        switch ($(this).attr('datef')) {
        case "ymd":
            if ($(this).val() === "1970/01/01") {
                Date.format = "yyyy/mm/dd";
                defaultDate  = new Date().asString();
            }
            break;
        case "mdy":
            if ($(this).val() === "01/01/1970") {
                Date.format = "mm/dd/yyyy";
                defaultDate  = new Date().asString();
            }
            break;
//        case "dmy":
        default:
            if ($(this).val() === "01/01/1970") {
                Date.format = "dd/mm/yyyy";
                defaultDate  = new Date().asString();
            }
            break;
        }

        $(this).datepicker({
            showOn: "both",
            buttonImage: "icons/calendar.gif",
            yearRange: "-100:+20",
            dateFormat: $(this).attr('datef'),
            buttonImageOnly: true,
            defaultDate: defaultDate,
            beforeShow: customRange
        }).addClass("embed");
    });
}

/*
 * Jeditable - jQuery in place edit plugin
 *
 * Copyright (c) 2006-2009 Mika Tuupola, Dylan Verheul
 *
 * Licensed under the MIT license:
 *   http://www.opensource.org/licenses/mit-license.php
 *
 * Project home:
 *   http://www.appelsiini.net/projects/jeditable
 *
 * Based on editable by Dylan Verheul <dylan_at_dyve.net>:
 *    http://www.dyve.net/jquery/?editable
 *
 */

/**
  * Version 1.7.0
  *
  * ** means there is basic unit tests for this parameter.
  *
  * @name  Jeditable
  * @type  jQuery
  * @param String      target                    (POST) URL or function to send edited content to **
  * @param Hash        options                    additional options
  * @param String      options[method]            method to use to send edited content (POST or PUT) **
  * @param Function options[callback]         Function to run after submitting edited content **
  * @param String      options[name]            POST parameter name of edited content
  * @param String      options[id]                POST parameter name of edited div id
  * @param Hash        options[submitdata]        Extra parameters to send when submitting edited content.
  * @param String      options[type]            text, textarea or select (or any 3rd party input type) **
  * @param Integer     options[rows]            number of rows if using textarea **
  * @param Integer     options[cols]            number of columns if using textarea **
  * @param Mixed       options[height]            'auto', 'none' or height in pixels **
  * @param Mixed       options[width]            'auto', 'none' or width in pixels **
  * @param String      options[loadurl]        URL to fetch input content before editing **
  * @param String      options[loadtype]        Request type for load url. Should be GET or POST.
  * @param String      options[loadtext]        Text to display while loading external content.
  * @param Mixed       options[loaddata]        Extra parameters to pass when fetching content before editing.
  * @param Mixed       options[data]            Or content given as paramameter. String or function.**
  * @param String      options[indicator]        indicator html to show when saving
  * @param String      options[tooltip]        optional tooltip text via title attribute **
  * @param String      options[event]            jQuery event such as 'click' of 'dblclick' **
  * @param String      options[submit]            submit button value, empty means no button **
  * @param String      options[cancel]            cancel button value, empty means no button **
  * @param String     options[cssclass]        CSS class to apply to input form. 'inherit' to copy from parent. **
  * @param String      options[style]            Style to apply to input form 'inherit' to copy from parent. **
  * @param String      options[select]            true or false, when true text is highlighted ??
  * @param String      options[placeholder]    Placeholder text or html to insert when element is empty. **
  * @param String      options[onblur]            'cancel', 'submit', 'ignore' or function ??
  *
  * @param Function options[onsubmit] function(settings, original) { ... } called before submit
  * @param Function options[onreset]  function(settings, original) { ... } called before reset
  * @param Function options[onerror]  function(settings, original, xhr) { ... } called on error
  *
  * @param Hash    options[ajaxoptions]  jQuery Ajax options. See docs.jquery.com.
  *
  */

(function($) {

    $.fn.editable = function(target, options) {

        if ('disable' == target) {
            $(this).data('disabled.editable', true);
            return;
        }
        if ('enable' == target) {
            $(this).data('disabled.editable', false);
            return;
        }
        if ('destroy' == target) {
            $(this)
                .unbind($(this).data('event.editable'))
                .removeData('disabled.editable')
                .removeData('event.editable');
            return;
        }

        var settings = {
            target     : target,
            required   : false,
            name       : 'value',
            id         : 'id',
            type       : 'text',
            width      : 'auto',
            height     : 'auto',
            event      : 'click',
            onblur     : 'cancel',
            onblurlook : false,
            loadtype   : 'GET',
            loadtext   : 'Cargando...',
            placeholder: '',
            select       : true,
            loaddata   : {},
            submitdata : {},
            submit  : " ",
            cancel  : " ",
            style   : "inherit",
            tooltip : _('dobleclick'),
            indicator : "<img alt='cargando...' src='icons/loader.gif'>",
            ajaxoptions: {}
        };

        if (options) {
            $.extend(settings, options);
        }

        /* setup some functions */

        var plugin   = $.editable.types[settings.type].plugin || function() { };
        var submit   = $.editable.types[settings.type].submit || function() { };
        var buttons  = $.editable.types[settings.type].buttons
                    || $.editable.types['defaults'].buttons;
        var content  = $.editable.types[settings.type].content
                    || $.editable.types['defaults'].content;
        var element  = $.editable.types[settings.type].element
                    || $.editable.types['defaults'].element;
        var elementBlur = $.editable.types[settings.type].elementBlur
                    || $.editable.types['defaults'].elementBlur;
        var reset    = $.editable.types[settings.type].reset
                    || $.editable.types['defaults'].reset;
        var callback = settings.callback || function() { };
        var onedit   = settings.onedit   || function() { };
        var onsubmit = settings.onsubmit || function() { };
        var onreset  = settings.onreset  || function() { };
        var onerror  = settings.onerror  || reset;

        /* show tooltip */
        if (settings.tooltip) {
            $(this).attr('title', settings.tooltip);
        }

        settings.autowidth  = 'auto' == settings.width;
        settings.autoheight = 'auto' == settings.height;

        return this.each(function() {
            
            /* save this to self because this changes when scope changes */
            var self = this;

            var td = $(this);

            if ($(this).hasClass('required')) {
                settings.required = true;
            } else {
                settings.required = false;
            }
            if ($(this).hasClass('condicionante')) {
                settings.condicionante = true;
            } else {
                settings.condicionante = false;
            }

            var th  = $(this).parents('table').find('th:eq('+$(this).attr('index')+')');

            /* inlined block elements lose their width and height after first edit */
            /* save them for later use as workaround */
            var savedwidth  = $(self).width();
            
            var savedheight = $(self).height();

            /* save so it can be later used by $.editable('destroy') */
            $(this).data('event.editable', settings.event);

            /* if element is empty add something clickable (if requested) */
            if (!$.trim($(this).html())) {
                $(this).html(settings.placeholder);
            }


            $(this).bind(settings.event, function(e) {

                /* abort if disabled for this element */
                if (true === $(this).data('disabled.editable')) {
                    return;
                }

                /* prevent throwing an exeption if edit field is clicked again */
                if (self.editing) {
                    return;
                }

                /* abort if onedit hook returns false */
                if (false === onedit.apply(this, [settings, self])) {
                   return;
                }

                /* remove tooltip */
                if (settings.tooltip) {
                    $(self).removeAttr('title');
                }

                /* figure out how wide and tall we are, saved width and height */
                /* are workaround for http://dev.jquery.com/ticket/2190 */
                if (0 == $(self).width()) {
                	
                    //$(self).css('visibility', 'hidden');
                    settings.width  = savedwidth;
                    settings.height = savedheight;
                } else {
                	
                    if (settings.width != 'none') {
                        settings.width =
                            settings.autowidth ? $(self).width()  : settings.width;
                    }
                    if (settings.height != 'none') {
                        settings.height =
                            settings.autoheight ? $(self).height() : settings.height;
                    }
                }
                //$(this).css('visibility', '');
                
                /* remove placeholder text, replace is here because of IE */
                if ($(this).html().toLowerCase().replace(/(;|")/g, '') ==
                    settings.placeholder.toLowerCase().replace(/(;|")/g, '')) {
                        $(this).html('');
                }

                self.editing    = true;
                self.revert     = $(self).html();
                
                $(self).html('');

                /* create the form object */
                var form = $('<form />');

                /* apply css or style or both */
                if (settings.cssclass) {
                    if ('inherit' == settings.cssclass) {
                        form.attr('class', $(self).attr('class'));
                    } else {
                        form.attr('class', settings.cssclass);
                    }
                }


                if (settings.style) {
                    if ('inherit' == settings.style) {
                        form.attr('style', $(self).attr('style'));
                        /* IE needs the second line or display wont be inherited */
                        form.css('display', $(self).css('display'));
                    } else {
                        form.attr('style', settings.style);
                    }
                }

                /* add main input element to form and store it in input */
                var input = element.apply(form, [settings, self]);
                

                /* set input content via POST, GET, given data or existing value */
                var input_content;

                if (settings.loadurl) {
                    var t = setTimeout(function() {
                        input.disabled = true;
                        content.apply(form, [settings.loadtext, settings, self]);
                    }, 100);

                    var loaddata = {};
                    loaddata[settings.id] = self.id;
                    if ($.isFunction(settings.loaddata)) {
                        $.extend(loaddata, settings.loaddata.apply(self, [self.revert, settings]));
                    } else {
                        $.extend(loaddata, settings.loaddata);
                    }
                    $.ajax({
                       type : settings.loadtype,
                       url  : settings.loadurl,
                       data : loaddata,
                       async : false,
                       success: function(result) {
                          window.clearTimeout(t);
                          input_content = result;
                          input.disabled = false;
                       }
                    });
                } else if (settings.data) {
                    input_content = settings.data;
                    if ($.isFunction(settings.data)) {
                        input_content = settings.data.apply(self, [self.revert, settings]);
                    }
                } else {
                	
                    input_content = self.revert;
                }
                content.apply(form, [input_content, settings, self]);

                input.attr('name', settings.name);

                /* add buttons to the form */
                buttons.apply(form, [settings, self]);

                /* add created form to self */
                $(self).append(form);

                /* attach 3rd party plugin if requested */
                plugin.apply(form, [settings, self]);

                /* focus to first visible form element */
                $(':input:visible:enabled:first', form).focus();

                /* highlight input contents when requested */
                if (settings.select) {
                    input.select();
                }

                var th  = $(this).parents('table').find('th:eq('+$(this).attr('index')+')');
                var tdW = th.width();

                if (settings.type=="select") {
                    
                	var tdo = td.find('select');
                    if (tdo.length > 0) {
                        if (tdo.width() > 200) {
                            tdo.css('width', '200px');
                        }
                    	var tdW = tdo.width();
                    	if (tdW > td.width()) {
                    		if (!th.hasClass('resized')) { th.addClass('resized'); th.css('width',(tdW+20)+'px');    }	
                    	}
                        
                    }
                    
                }
                if (settings.type=="text") {
                    var tdo = td.find('input');
                    if (tdo.length>0) {
                        var tdW = tdo.width();
                        if (!th.hasClass('resized')) { th.addClass('resized'); th.css('width',(tdW+100)+'px');    }
                    }

                }

                /* discard changes if pressing esc */
                input.keydown(function(e) {
                    if (e.keyCode == 27) {
                        e.preventDefault();
                        //self.reset();
                        reset.apply(form, [settings, self]);
                    }
                });

                /* discard, submit or nothing with changes when clicking outside */
                /* do nothing is usable when navigating with tab */
                var t;
                if ('cancel' == settings.onblur) {
                    //input.blur(function(e) {
                    var inputBB = elementBlur.apply(form, [settings, self]);
                    if (inputBB !== false) {
                        var inputBlur = inputBB;
                    } else {
                        inputBlur = input;
                    }
                
                    inputBlur.bind("focusout",function(e) {
                        /* prevent canceling if submit was clicked */
                        if (typeof settings.onblurlook == "function") {
                            a = settings.onblurlook();
                            if (!a) {
                                t = setTimeout(function() {
                                    reset.apply(form, [settings, self]);
                                }, 500);
                            }
                        }else {
                            t = setTimeout(function() {
                                reset.apply(form, [settings, self]);
                            }, 500);
                        }
                        window.setTimeout(function() {th.removeClass('resized'); th.css('width','auto');},600);
                    });
                } else if ('submit' == settings.onblur) {
                    input.blur(function(e) {
                        /* prevent double submit if submit was clicked */
                        t = setTimeout(function() {
                            form.submit();
                        }, 200);
                        th.removeClass('resized');
                        th.css('width','auto');
                    });
                } else if ($.isFunction(settings.onblur)) {
                    input.bind("focusout",function(e) {
                        settings.onblur.apply(self, [input.val(), settings]);
                    });
                } else {
                    input.blur(function(e) {
                      /* TODO: maybe something here */
                    });
                }

                form.submit(function(e) {
                     if (t) {
                        clearTimeout(t);
                    }

                    if (settings.required == true && $.trim(input.val())=="") {
                        alert("Valor requerido");
                        return false;
                    }

                    /* do no submit */
                    e.preventDefault();

                    /* call before submit hook. */
                    /* if it returns false abort submitting */
                    if (false !== onsubmit.apply(form, [settings, self])) {
                        /* custom inputs call before submit hook. */
                        /* if it returns false abort submitting */
                        if (false !== submit.apply(form, [settings, self])) {

                          /* check if given target is function */
                          if ($.isFunction(settings.target)) {
                              var str = settings.target.apply(self, [input.val(), settings]);
                              $(self).html(str);
                              self.editing = false;
                              callback.apply(self, [self.innerHTML, settings]);
                              /* TODO: this is not dry */
                              if (!$.trim($(self).html())) {
                                  $(self).html(settings.placeholder);
                              }
                          } else {
                              /* add edited content and id of edited element to POST */
                              var submitdata = {};
                              submitdata[settings.name] = input.val();
                              submitdata[settings.id] = self.id;
                              /* add extra data to be POST:ed */
                              if ($.isFunction(settings.submitdata)) {
                                  $.extend(submitdata, settings.submitdata.apply(self, [self.revert, settings]));
                              } else {
                                  $.extend(submitdata, settings.submitdata);
                              }

                              /* quick and dirty PUT support */
                              if ('PUT' == settings.method) {
                                  submitdata['_method'] = 'put';
                              }

                              /* show the saving indicator */
                              $(self).html(settings.indicator);

                              /* defaults for ajaxoptions */
                              var ajaxoptions = {
                                  type    : 'POST',
                                  data    : submitdata,
                                  url     : settings.target,
                                  success : function(result, status) {
                                      $(self).html(result);
                                      self.editing = false;
                                      callback.apply(self, [self.innerHTML, settings]);
                                      if (!$.trim($(self).html())) {
                                          $(self).html(settings.placeholder);
                                      }
                                  },
                                  error   : function(xhr, status, error) {
                                      onerror.apply(form, [settings, self, xhr]);
                                  }
                              };

                              /* override with what is given in settings.ajaxoptions */
                              $.extend(ajaxoptions, settings.ajaxoptions);
                              $.ajax(ajaxoptions);

                            }
                        }
                    }

                    /* show tooltip again */
                    $(self).attr('title', settings.tooltip);
                    return false;
                });
            });

            /* privileged methods */
            this.reset = function(form) {
                /* prevent calling reset twice when blurring */
                if (this.editing) {
                    /* before reset hook, if it returns false abort reseting */
                    if (false !== onreset.apply(form, [settings, self])) {
                        $(self).html(self.revert);
                        self.editing   = false;
                        if (!$.trim($(self).html())) {
                            $(self).html(settings.placeholder);
                        }
                        /* show tooltip again */
                        if (settings.tooltip) {
                            $(self).attr('title', settings.tooltip);
                        }
                    }
                }
            };
        });


    };


    $.editable = {
        types: {
            defaults: {
                element : function(settings, original) {
                    var input = $('<input type="hidden"></input>');
                    $(this).append(input);
                    return(input);
                },
                elementBlur : function(settings, original) {
                    return false;
                },
                content : function(string, settings, original) {
                    $(':input:first', this).val(string);
                },
                reset : function(settings, original) {
                  original.reset(this);
                },
                buttons : function(settings, original) {
                    var form = this;
                    if (settings.submit) {
                        var submit = $('<input class="button" type="submit">');
                        submit.val(settings.submit);
                        $(this).append(submit);
                    }
                    if (settings.cancel) {
                        var cancel = $('<input class="button undo" type="button">');
                        cancel.val(settings.cancel);
                        $(this).append(cancel);

                        $(cancel).click(function(event) {
                            //original.reset();
                            if ($.isFunction($.editable.types[settings.type].reset)) {
                                var reset = $.editable.types[settings.type].reset;
                            } else {
                                var reset = $.editable.types['defaults'].reset;
                            }
                            reset.apply(form, [settings, original]);
                            return false;
                        });
                    }
                }
            },
            text: {
                element : function(settings, original) {
                    var input = $('<input >');
                    if (settings.width  != 'none') { input.width(settings.width);  }
                    if (settings.height != 'none') { input.height(settings.height); }
                    input.attr('autocomplete','off');
                    var $o = $(original);
                    if ($o.attr('maxlength')) {
                    	input.attr('maxlength', $o.attr('maxlength'));
                    }
                    $(this).append(input);
                    return(input);
                }
            },
            date: {
                element : function(settings, original) {
                    var input = $('<input />');
                    if (settings.width  != 'none') { input.width(settings.width);  }
                    if (settings.height != 'none') { input.height(settings.height); }
                    input.attr('autocomplete','off');
                    $(this).append(input);
                    return(input);
                },
                content : function(string, settings, original) {
                    eval ('var json = ' + string);
                    $(':input:first', this).attr('class',json.theclass).attr('name',json.name).attr('datef',json.datef);
                    $(':input:first', this).val(original.revert);
                    dodate($(this));
                },
                buttons : function(settings, original) {
                    var form = this;
                    if (settings.submit) {
                        /* if given html string use that */
                        if (settings.submit.match(/>$/)) {
                            var submit = $(settings.submit).click(function() {
                                if (submit.attr("type") != "submit") {
                                    form.submit();
                                }
                            });
                        /* otherwise use button with given string as text */
                        } else {
                            var submit = $('<input class="button" type="submit">');
                            submit.html(settings.submit);
                        }
                        submit.val(settings.submit);
                        $(this).append(submit);
                    }
                    if (settings.cancel) {
                        /* if given html string use that */
                        if (settings.cancel.match(/>$/)) {
                            var cancel = $(settings.cancel);
                        /* otherwise use button with given string as text */
                        } else {
                            var cancel = $('<input class="button undo" type="button">');
                            cancel.html(settings.cancel);
                        }
                        cancel.val(settings.cancel);
                        $(this).append(cancel);

                        $(cancel).click(function(event) {
                            if ($.isFunction($.editable.types[settings.type].reset)) {
                                var reset = $.editable.types[settings.type].reset;
                            } else {
                                var reset = $.editable.types['defaults'].reset;
                            }
                            reset.apply(form, [settings, original]);
                            return false;
                        });
                    }
                }

            },
            dateTime: {
                element : function(settings, original) {
                    var input = $('<input />');
                    if (settings.width  != 'none') { input.width(settings.width);  }
                    if (settings.height != 'none') { input.height(settings.height); }
                    /* https://bugzilla.mozilla.org/show_bug.cgi?id=236791 */
                    //input[0].setAttribute('autocomplete','off');
                    input.attr('autocomplete','off');
                    $(this).append(input);
                    return(input);
                },
                content : function(string, settings, original) {
                    eval ('var json = ' + string);
                    $(':input:first', this).attr('class',json.theclass).attr('name',json.name).attr('datef',json.datef);
                    $(':input:first', this).val(original.revert);
                    dodate($(this));
                },
                buttons : function(settings, original) {
                    if (settings.submit) {
                        var submit = $('<input class="button dateTimeButton" type="submit">');
                        submit.val(settings.submit);
                        $(this).append(submit);
                    }
                    if (settings.cancel) {
                        var cancel = $('<input class="button undo" type="button">');
                        cancel.val(settings.cancel);
                        $(this).append(cancel);

                        $(cancel).click(function() {
                            $(original).html(original.revert);
                            original.editing = false;
                        });
                    }
                }

            },
            color: {
                element : function(settings, original) {
            	
                    var input = $('<input >');
                    if (settings.width  != 'none') { input.width(settings.width);  }
                    if (settings.height != 'none') { input.height(settings.height); }
                    input.attr('autocomplete','off');
                    input.addClass('hexcolor');
                    input.addClass('focus');
                    
                    $(this).append(input);
                    
                    return(input);
                },
                content : function(string, settings, original) {
                    eval ('var json = ' + string);
                    $(':input:first', this).attr('class',json.theclass).attr('name',json.name);
                    $(':input:first', this).val(original.revert);
                   docolor($(this));
                }
            },
            textarea: {
                element : function(settings, original) {
                    var textarea = $('<textarea />');
                    if (settings.rows) {
                        textarea.attr('rows', settings.rows);
                    } else if (settings.height != "none") {
                        textarea.height(settings.height);
                    }
                    if (settings.cols) {
                        textarea.attr('cols', settings.cols);
                    } else if (settings.width != "none") {
                        textarea.width(settings.width);
                    }
                    var $o = $(original);
                    if ($o.attr('maxlength')) {
                    	textarea.attr('maxlength', $o.attr('maxlength'));
                    }
                    $(this).append(textarea);
                    return(textarea);
                }
            },
            time: {
                element : function(settings, original) {
                    var input = $('<input />');
                    if (settings.width  != 'none') { input.width(settings.width);  }
                    if (settings.height != 'none') { input.height(settings.height); }
                    input.attr('autocomplete','off');
                    $(this).append(input);
                    return(input);
                },
                content : function(string, settings, original) {
                    $(':input:first', this).val(string);
                    $(':input:first', this).timepicker({divId: "mytimepicker"});
                },
                buttons : function(settings, original) {
                    if (settings.submit) {
                        var submit = $('<input class="button" type="submit" value="">');
                        submit.val(settings.submit);
                        $(this).append(submit);
                    }
                }

            },
            mappoint: {
                element : function(settings, original) {
            	//console.log(original);
            		settings.onblur = 'ignore';
            		//console.log($(this));
                    var input2 = $('<img class="mapButton" src="./icons/network.png" />');
                    var input = $('<input class="mapInput" type="hidden" />');
                    if (settings.width  != 'none') { input.width(settings.width);  }
                    if (settings.height != 'none') { input.height(settings.height); }
                    input.css({
                    	'height':'auto',
                    	'margin':'0 20px 0 0',
                    	'width':'auto'
                    });
                    input.attr('autocomplete','off');
                    
                    $(this).append(input);
                    $(this).append(input2);
                    return(input);
                },
                content : function(string, settings, original) {
                	val = $(string).attr('value');
                    $(':input:first', this).val(val);
                    //$(':input:first', this).timepicker({divId: "mytimepicker"});
                },
                buttons : function(settings, original) {
                    if (settings.submit) {
                        var submit = $('<input class="button" type="submit" value="">');
                        submit.val(settings.submit);
                        $(this).append(submit);
                    }
                }
                

            },
            textareaTags: {
                element : function(settings, original) {
                    var textareaTags = $('<textarea />');
                    textareaTags.attr('class', 'tags');

                    if (settings.rows) {
                        textareaTags.attr('rows', settings.rows);
                    } else {
                        textareaTags.height(settings.height);
                    }
                    if (settings.cols) {
                        textareaTags.attr('cols', settings.cols);
                    } else {
                          textareaTags.width(settings.width);
                    }

                    textareaTags.css('font-size','10px');
                    $(this).append(textareaTags);
                    return(textareaTags);
                },
                content : function(string, settings, original) {
                    eval ('var json = ' + string);
                    $(this).children('textarea').val(json.values);
                    $(this).children("textarea.tags").autocomplete_old('./modules/tablon/ajax/ops_tablon.php?op=tag_autocomplete&acc=load&autocompletetab='+json.autocompletetab+'&autocompletefield='+json.autocompletefield+'&autocompletetabconds='+json.autocompletetabconds, {
                        multiple: true,
                        mustMatch: false,
                        autoFill: true,
                        multipleSeparator : json.separator
                    });
                }
            },
            multiselect: {
                element : function(settings, original) {
                    /*var select = $('<select  multiple="multiple" size="1"  style="visibility:hidden;" />');
                    var hidden = $('<input type="hidden" class="multivals"  />');
                    $(this).append(select).append(hidden);
                    return(hidden);*/
                    var contenedor = $('<div class="contMultiselect"/>');
                    var select = $('<select  multiple="multiple" size="1"  style="display: none;"  >');
                    var hidden = $('<input type="hidden" class="multivals"  >');
                    contenedor.append(select).append(hidden);
                    $(this).append(contenedor);
                    return(hidden);
                },
                elementBlur : function(settings, original) {
                    var elemBlur = $(this).find('.multiSelect');
                    if (elemBlur) return elemBlur;
                    else return false;
                },
                content : function(string, settings, original) {
                    eval ('var json = ' + string);
                        ares = json.values;
                        for (var key in ares) {
                            var option = $('<option>').val(key).append(key);
                            if (ares[key]=="selected") {
                                option.attr('selected', 'selected');
                            }
                            $('select', this).append(option);
                        }
                        if (json.multiselectOnlyOne != undefined && json.multiselectOnlyOne == true) {
                            $('select', this).multiSelect({ onlyOneSelected: true, selectAllText:"*", oneOrMoreSelected: "*" });
                        } else {
                            $('select', this).multiSelect({ oneOrMoreSelected: '*' });
                        }
                }
            },
            multiselectnoreal: {
                element : function(settings, original) {
                    /*var select = $('<select  multiple="multiple" size="1"  style="visibility:hidden;"  >');
                    var hidden = $('<input type="hidden" class="multivals"  >');
                    $(this).append(select).append(hidden);
                    return(hidden);*/
                    var contenedor = $('<div class="contMultiselect"/>');
                    var select = $('<select  multiple="multiple" size="1"  style="display: none;"  >');
                    var hidden = $('<input type="hidden" class="multivals"  >');
                    contenedor.append(select).append(hidden);
                    $(this).append(contenedor);
                    return(hidden);
                },
                elementBlur : function(settings, original) {
                    var elemBlur = $(this).find('.multiSelect');
                    if (elemBlur) return elemBlur;
                    else return false;
                },
                content : function(string, settings, original) {
                    eval ('var json = ' + string);
                    ares = json.values;
                    for (var key in ares) {
                        var option = $('<option>').val(key).append(key);
                        if (ares[key]=="selected") {
                            option.attr('selected', 'selected');
                        }
                        $('select', this).append(option);
                    }
                    if (json.multiselectOnlyOne != undefined && json.multiselectOnlyOne == true) {
                        $('select', this).multiSelect({ onlyOneSelected: true, selectAllText:"*", oneOrMoreSelected: "*" });
                    } else {
                        $('select', this).multiSelect({ oneOrMoreSelected: '*' });
                    }
                }
            },
            multiselectng: {
                element : function(settings, original) {
                    /*var select = $('<select  multiple="multiple" size="1"  style="visibility:hidden;" />');
                    var hidden = $('<input type="hidden" class="multivals"  />');
                    $(this).append(select).append(hidden);
                    return(hidden);*/
                    var contenedor = $('<div class="contMultiselect"/>');
                    var select = $('<select  multiple="multiple" size="1"  style="display: none;"  >');
                    var hidden = $('<input type="hidden" class="multivals"  >');
                    contenedor.append(select).append(hidden);
                    $(this).append(contenedor);
                    return(hidden);
                },
                elementBlur : function(settings, original) {
                    var elemBlur = $(this).find('.multiSelect');
                    if (elemBlur) return elemBlur;
                    else return false;
                },
                content : function(string, settings, original) {
                    eval ('var json = ' + string);
                        ares = json.values;
                        for (var key in ares) {
                            var option = $('<option>').val(key).append(key);
                            if (ares[key]=="selected") {
                                option.attr('selected', 'selected');
                            }
                            $('select', this).append(option);
                        }
                        if (json.multiselectOnlyOne != undefined && json.multiselectOnlyOne == true) {
                            $('select', this).multiSelect({ onlyOneSelected: true, selectAllText:"*", oneOrMoreSelected: "*" });
                        } else {
                            $('select', this).multiSelect({ oneOrMoreSelected: '*' });
                        }
                }
            },
            select: {
               element : function(settings, original) {
                    var select = $('<select>');
                    $(this).append(select);
                    return(select);
                },
                content : function(data, settings, original) {
                    /* If it is string assume it is json. */
                    if (String == data.constructor) {
                        eval ('var json = ' + data);
                    } else {
                    /* Otherwise assume it is a hash already. */
                        var json = data;
                    }
                    if (typeof(json['dependencia']) != 'undefined' && json['dependencia']!=false) {
                        var grupos = false;
                        if (typeof(json["grupo_dep"]) != 'undefined') {
                            eval ('var grupos = ' + json["grupo_dep"]);
                        }
                        for (var key in json) {
                            if ('selected' == key || 'req' == key || 'dependencia' == key || 'grupo_dep' == key) {
                                continue;
                            }
                            if (grupos != false && typeof(grupos[key]) != 'undefined') {
                                var option = $('<option id="'+grupos[key]+'">').val(key).append(json[key]);
                            } else {
                                var option = $('<option>').val(key).append(json[key]);
                            }
                            $('select', this).append(option);
                        }
                    } else {
                        for (var key in json) {
                            if (!json.hasOwnProperty(key)) {
                                continue;
                            }
                            if ('selected' == key || 'req' == key) {
                                continue;
                            }
                            var option = $('<option />').val(key).append(json[key]);
                            $('select', this).append(option);
                        }
                    }
                    /* Loop option again to set selected. IE needed this... */
                    $('select', this).children().each(function() {
                        if ($(this).val() == json['selected'] ||
                            $(this).text() == original.revert) {
                                $(this).attr('selected', 'selected');
                        };
                    });
                }
            }
        },

        /* Add new input type */
        addInputType: function(name, input) {
            $.editable.types[name] = input;
        }
    };

})(jQuery);

$.editable.addInputType('ajaxupload', {
    /* create input element */
    element : function(settings) {
        settings.onblur = 'ignore';
        var input = $('<input type="file" id="upload" name="upload" size="1">');
        $(this).append(input);
        $(input).customFile();
        return(input);
    },
    content : function(string, settings, original) {
        /* do nothing */
    },
    plugin : function(settings, original) {
        $("input:submit", this)
        .bind('click', function() {
            $.ajaxFileUpload({
                url: settings.target+$(original).attr("id"),
                secureuri:false,
                fileElementId: 'upload',
                dataType: 'json',
                success: function (j) {
                    settings.processSaving(j,original);
                        original.editing = false;

                },
                error: function (data, status, e) {
                    alert(e);
                }
            });
            $(original).html(settings.indicator);
            return(false);
        });
    }
});


$.editable.addInputType('fsfilefs', {
    element : function(settings, original) {
            settings.onblur = 'ignore';
            settings.submit = '';
            var fldID = $(original).attr("id");

            var o = this;
            var selected = false;
            $.prompt('<div id="fileTreeDemo" />',{ loaded : function() {
                var fileroot = $("#fileTreeDemo",this).bind("mousemove",function(e) {e.stopPropagation();}).css("height","300px").css("overflow","auto");
                
                fileroot.fileTree({ root: '/',extraparams:{id:fldID},script: './modules/tablon/ajax/ops_tablon.php?op=fsfilefs&acc=list' }, function(file) {
                    // Funcion al seleccionar fichero
                    var aFile = file.split(";");
                    $(".tmp_name",o).val(aFile[0]);
                    $(".name",o).val(aFile[1]);
                    $(".size",o).val(aFile[2]);
                    $(".type",o).val(aFile[3]);
                    selected = true;

                });
            },buttons:{Seleccionar:'seleccionar',Cerrar:true},submit : function(ret) {
                    if ((ret == "seleccionar") && (selected)) {
                        $(o).submit();
                    } else {
                        $(original).html(original.revert);
                        original.editing = false;
                    }
                    return true;
            }});

            var input = $('<input type="hidden" class="tmp_name">');
            $(o).append(input);
            var input = $('<input type="hidden" class="size">');
            $(o).append(input);
            var input = $('<input type="hidden" class="type">');$(o).append(input);
            var input = $('<input type="hidden" class="name">');$(o).append(input);
            return(input);
    },
    content : function(string, settings, original) {
            $(original).append(settings.indicator);
    }
});


$.editable.addInputType('glocation', {
		
		element : function(settings, original) {
			v = $(original.revert).val();
			if ($.trim(v) == "") v = " ";
			settings.data=v;
	        var input = $('<input >');
	        input.attr('autocomplete','off');
	        input.addClass('glocationField');
	        $(this).append(input);
	        doGlocation(input);
	        
	        return(input);
	        
	    },
	    
	    
	    elementBlur: function(){return false;}
});
