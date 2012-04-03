(function() {

var load = function(){
      var selectTable = new Object();
      $('#busy').hide();
      $('#paso2').hide();
      $('#preview').hide();
      $('#rollback').hide();


      $('#csv_upload').bind('click', function() {
            $.ajaxFileUpload({
                url: 'modules/csvimport/ajax/ops_csvimport.php?upload',
                secureuri:false,
                fileElementId: 'file_csv',
                dataType: 'json',
                beforeSubmit: function(data) {
                    $('#busy').show();
                },
                success: function (data) {
                    $('#busy').hide();
                    $('#paso1').hide();
                    var html = data.map.replace(/%OPENHTMLTAG%/gi,'<');
                    html = html.replace(/%CLOSEHTMLTAG%/gi,'>');
                    $('#csvimport').append(html);
                    $('#paso2').show();
                },
                error: function (data, status, e) {
                    alert(e);
                }
            });
            return false;
    });





    $('#button_submit_conf').bind('click', function() {
               $.ajax({
                      type:  'POST',
                      dataType: 'json',
                      data: $('#csvimport').serialize(),
                      url: 'modules/csvimport/ajax/ops_csvimport.php',
                      beforeSend: function(data) {
                        $('#paso2').hide();
                        $('#paso1').hide();
                        $('#busy').show();
                      },
                      complete: function(data) {
                        $('#busy').hide();
                      },
                      success: function(data) {
                        if (data.error == false) {

                        /*$('#paso2').hide();*/
                           var html;
                                html = data.html.replace(/%OPENHTMLTAG%/gi,'<');
                                html = html.replace(/%CLOSEHTMLTAG%/gi,'>');
                                data.html = html;
                                $('#preview').html(data.html);
                                $('#button_submit_map').bind('click', onButtonSubmitMapClick);
                                $('#preview').show();
                          $("select").bind("change",onSelectChange);

                       } else {
                            $.prompt("Error");
                       }
                        $('#add_field_button').click(onAddFieldButtonClicked);
                        return false;

                    }
               });
               return false;
        });


        var onButtonSubmitMapClick = function() {
          $.ajax({
                      type:  'POST',
                      dataType: 'json',
                      data: $('#csvimport').serialize(),
                      url: 'modules/csvimport/ajax/ops_csvimport.php',
                      beforeSend: function(data) {
                        $('#paso1').hide();
                        $('#paso2').hide();
                        $('#preview').hide();
                        $('#busy').show();
                      },
                      complete: function(data) {
                        $('#busy').hide();
                      },
                      success: function(data) { /* TODO vaya lio, consecuencia de haber usado antes el wizardplugin */
                            if (data.error == false) {
                                var html = data.preview.new_table.replace(/%OPENHTMLTAG%/gi,'<');
                                html = html.replace(/%CLOSEHTMLTAG%/gi,'>');
                                if (html == '') {
                                    $.prompt('Error: Revise la configuración.');
                                } else {
                                   $('#rollback').html(html);

                                   $('#button_submit_final').bind('click', onButtonSubmitFinalClick);
                                   $('#rollback').show();
                                }
                            }
                        }

            });

            return false;
        };

        var onButtonSubmitFinalClick = function() {
            $.ajax({
                        type:   'POST',
                        dataType: 'json',
                        data: $('#csvimport').serialize(),
                        url: 'modules/csvimport/ajax/ops_csvimport.php',
                        beforeSend: function(data) {
                          $('#paso1').hide();
                          $('#paso2').hide();
                          $('#preview').hide();
                          $('#rollback').hide();
                          $('#busy').show();
                        },
                        complete: function(data) {
                          $('#busy').hide();
                        },
                        success: function(data) {
                            if (data.error == false) {
                                $('#paso1').hide();
                                $('#paso2').hide();
                                $('#preview').hide();
                                $('#rollback').hide();
                                var html = data.confirmed.new_table.replace(/%OPENHTMLTAG%/gi,'<');
                                html = html.replace(/%CLOSEHTMLTAG%/gi,'>');
                                if (html == '') {
                                    $.prompt('Error');

                                } else {
                                    $('#do_commit').html(html);
                                    $('#do_commit').show();
                                }

                            }

                        }

                    });
                return false;
            };



              var onSelectChange = function() {
                            /*console.log(selectTable);*/
                                if ($(this).val() != "dont") {
                                    // El anterior vuelve a estar libre
                                    //$('select option[value='+selectTable[$(this).attr('id')]+']').removeAttr('disabled');

                                    $('select option[value='+selectTable[$(this).attr('id')]+']').removeAttr('disabled');

                                    // El nuevo ya no está libre
                                    $('select option[value='+$(this).val()+']').attr("disabled","disabled");
                                    selectTable[$(this).attr('id')] = $(this).val();
                                } else {
                                    $('select option[value='+selectTable[$(this).attr('id')]+']').removeAttr('disabled');

                                    selectTable[$(this).attr('id')] = undefined;
                                }
                };

               var onAddFieldButtonClicked = function() {
                         var currentVal = $('#last_index').val();
                         var tipo = "par";
                         var last_tipo = "impar";
                         var lastRow = $('#row_'+currentVal);
                         if ( lastRow.hasClass('par') ) {
                            tipo = "impar";
                            last_tipo = "par";
                         }
                         var tdsLastRow = lastRow.find('td');
                         var content = '';

                        $('#import_table').find('tbody').append('<tr id="row_'+(parseInt(currentVal)+1)+'">'+lastRow.html()+'</tr>');
                        var newRow = $('#row_'+(parseInt(currentVal)+1));
                        newRow.removeClass(last_tipo);
                        newRow.addClass(tipo);
                        $('#last_index').val(parseInt(currentVal)+1 );
                        currentVal = parseInt(currentVal) + 1;
                        var i = 0;
                        newRow.find('td').each( function() {
                            $(this).attr('id','row_'+currentVal+'_col_'+(i));
                            switch(i) {
                                case 0:
                                $(this).find('select').each (function() {
                                    $(this).attr('id','field_'+currentVal);
                                    $(this).attr('name','field_'+currentVal);
                            		$(this).bind("change",onSelectChange);
                                    });
                                break;
                                case 1:
                                $(this).find('input').each(function() {
                                    $(this).attr('id','field_'+currentVal);
                                    $(this).attr('name','default_'+currentVal);
                                });
                                break;
                                default:
                                    $(this).html('');
                                break;
                            }
                            i++;
                        });




               };

};


jQuery(document).ready(load);

})();
