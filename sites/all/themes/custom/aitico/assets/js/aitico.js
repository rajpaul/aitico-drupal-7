(function($){           
        Drupal.behaviors.aiticoDecoreteFileInput = {
            
            attach: function (context, settings) {
                $(".file-group-file input[type=file]").ace_file_input({
                    style:'well',/** for making it like the larger file input in the example */
                    no_file:'No File ...',
                    btn_choose:'Choose',
                    btn_change:'Change',
                    thumbnail:'large',
                    no_icon:'icon-cloud-upload'
                });
            }
        }
        
        Drupal.behaviors.aiticoDecoreteSearchLog = { 
            
        attach: function (context, settings) { 
            $('#search_logs').attr('disabled','disabled');
           
            var myRanges = {};
            myRanges[Drupal.t("Today")] = ['today', 'today'];
            myRanges[Drupal.t("Yesterday")] = ['yesterday', 'yesterday'];
            myRanges[Drupal.t("Last 7 Days")] = [Date.today().add({
                        days: -6
                    }), 'today'];
                       
            $('#id-date-range-picker-1').daterangepicker({
                ranges: myRanges,
                locale: { 
                     applyLabel: Drupal.t("Apply"),
                     clearLabel: Drupal.t("Clear"),
                     fromLabel: Drupal.t("From"),
                     toLabel: Drupal.t("To"),
                     customRangeLabel: Drupal.t("Custom Range")
                 }                
            },
            function(start, end) {
                $("#invalid-time-range").hide();
                $('#search_logs').removeAttr('disabled');
            }
            );
            }
        }
        
        Drupal.behaviors.aiticoDecoreteSchedule = {
            attach: function (context, settings) {
                $(".schedule-datepicker").datepicker({
                    "format": "dd/mm/yyyy", 
                    "weekStart": 1, 
                    "language": '#{locale}',
                    "autoclose": true
                });
                
              $('.schedule-time').timepicker({
                minuteStep: 15,
                showInputs: true,
                showMeridian: false
            });
            } 
        }      
        
        Drupal.behaviors.aiticoDecoreateFileAdmin = {
          attach: function (context, settings) {           
              widget_boxes();
          }
        }        
        
        Drupal.behaviors.aiticoDecoreateSystemAdmin = {
            
          attach: function (context, settings) {       

            $('#logtable').dataTable({
                "oLanguage": {
                    "sLengthMenu":   Drupal.t("Display _MENU_ records"),
                    "sZeroRecords":  Drupal.t("No data available in table"),
                    "sInfo":         Drupal.t("Showing  _START_ to _END_ of _TOTAL_  entries"),
                    "sInfoEmpty":    Drupal.t("Showing 0 to 0 of 0 entries"),
                    "sSearch":       Drupal.t("Search")
                }
            });
            $('#usertable').dataTable({                
                "oLanguage": {
                    "sLengthMenu":   Drupal.t("Display _MENU_ records"),
                    "sZeroRecords":  Drupal.t("No data available in table"),
                    "sInfo":         Drupal.t("Showing  _START_ to _END_ of _TOTAL_  entries"),
                    "sInfoEmpty":    Drupal.t("Showing 0 to 0 of 0 entries"),
                    "sSearch":       Drupal.t("Search")
                } ,
                "bDestroy":true
            }); 
            
            
            $('#devicestable').dataTable({
                "oLanguage": {
                    "sLengthMenu":   Drupal.t("Display _MENU_ records"),
                    "sZeroRecords":  Drupal.t("No data available in table"),
                    "sInfo":         Drupal.t("Showing  _START_ to _END_ of _TOTAL_  entries"),
                    "sInfoEmpty":    Drupal.t("Showing 0 to 0 of 0 entries"),
                    "sSearch":       Drupal.t("Search")
                } ,
                "bDestroy":true
            });
            
            
          }
        }

})(jQuery);