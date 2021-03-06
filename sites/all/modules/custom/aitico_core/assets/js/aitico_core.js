(function($){
    $(document).ready(function(){
        
        $(window).scroll(function() {
            
            var scrollYpos = $(document).scrollTop();
            scrolllimit = $("#admin-action").height()+scrollYpos;
            if( scrolllimit <= $("#example-basic").height()){
                $("#admin-action").css({
                        'top': scrollYpos,
                        'position': 'relative'
                });
                
            }
        });
        
        $(".link-collapse-all").live('click', function(){
                $("#admin-action").css({
                        'top': 0,
                        'position': 'relative'
                });
        });

        
        $(document).ajaxStart(function() {
            // do something on start
            /*$("html, body").animate({
                scrollTop: 0
            },1000); */
            $("#loader").css("display","block");
        }).ajaxError(function(e, xhr, settings) {
            // do something on error
            $("#loader").css("display","none");
        }).ajaxSuccess(function() {
            // do something on success
            $("#loader").css("display","none");
        });


        /**
         * Handler for the form redirection error.
         */
        Drupal.ajax.prototype.error = function (response, uri) {
           if (window.console) {
                console.log(Drupal.ajaxError(response, uri));
            }
            //alert(Drupal.ajaxError(response, uri));
            // Remove the progress element.
            if (this.progress.element) {
                $(this.progress.element).remove();
            }
            if (this.progress.object) {
                this.progress.object.stopMonitoring();
            }
            // Undo hide.
            $(this.wrapper).show();
            // Re-enable the element.
            $(this.element).removeClass('progress-disabled').removeAttr('disabled');
            // Reattach behaviors, if they were detached in beforeSerialize().
            if (this.form) {
                var settings = response.settings || this.settings || Drupal.settings;
                Drupal.attachBehaviors(this.form, settings);
            }
        };


        var lang = $('html').attr('lang');      

        $("#delete-node").hide();
        $("#tree_url").hide();
        $("#schedule_remove_modal").hide();
        $("#tree_url_device").hide();
        $("#delete-node-device").hide();
      
        $(".tree_table tr").live('click', function(){
            
          
            $("#delete-node").show();               
            $(".tree_table tr").removeClass("active");         
            $(this).addClass("active");
          
            var type = $(this).attr("type");            
            var nid = $(this).attr("nid");
            var company_id = 0;
            var site_id = 0;
            var cst_id = 0;
            if(type == 'company'){
                company_id = nid;
            }else if (type == 'site'){
                site_id = nid;
                company_id = $(this).data('tt-parent-id');
            }else if (type == 'charging_station'){                 
                company_id = $(this).prevAll("tr.company:first").attr("nid");                                
                cst_id = nid;
                site_id = $(this).data('tt-parent-id');
            }

            $(this).find('span.ajax_link a.use-ajax').attr("href", "/administration/info/" + type +"/"+company_id);
            $(this).find('span.ajax_link a.use-ajax').trigger('click');
            $('#admin-action').show();                              
            $("#tree_url").show();
            $("#delete-node").show(); 
            var add_url = "";
            var del_url = "";
            if(type == "company"){                              
                add_url = "/administration/site/add/"+nid;  
                del_url = "/administration/company/remove/"+nid;
                if(lang=='fi'){
                    add_url = "/"+lang+"/administration/site/add/"+nid;
                    del_url = "/"+lang+"/administration/company/remove/"+nid;
                }                
                $("#tree_url").text(Drupal.t("Add Site"));                
                $("#tree_url").attr("href", add_url);
                $("#delete-node").text(Drupal.t("Delete Company"));
                $("#delete-node").attr("href",del_url);
                $("#tree_url_device").hide();
                $("#delete-node-device").hide();
            }
            else if(type == "site"){                
                add_url = "/administration/cst/add/"+nid;  
                del_url = "/administration/site/remove/"+nid;
                if(lang=='fi'){
                    add_url = "/"+lang+"/administration/cst/add/"+nid;
                    del_url = "/"+lang+"/administration/site/remove/"+nid;
                }                
                $("#tree_url").text(Drupal.t("Add Charging Station"));
                $("#tree_url").attr("href", add_url);
                $("#delete-node").text(Drupal.t("Delete Site"));
                $("#delete-node").attr("href", del_url);
                $("#tree_url_device").hide();
                $("#delete-node-device").hide();
            }
            else if(type == "charging_station"){
                add_url = "/administration/device/add/"+nid;  
                del_url = "/administration/cst/remove/"+nid;
                if(lang=='fi'){
                    add_url = "/"+lang+"/administration/device/add/"+nid;  
                    del_url = "/"+lang+"/administration/cst/remove/"+nid;
                }                             
                $("#tree_url_device").show();
                $("#tree_url_device").attr("href", add_url);
                $("#delete-node").text(Drupal.t("Delete Charging Station"));
                $("#delete-node").attr("href", del_url);
                $("#tree_url").hide();
                $("#delete-node-device").hide();
            }
            else if (type == "device"){
                del_url = "/administration/device/remove/"+nid;
                if(lang=='fi'){                  
                    del_url = "/"+lang+"/administration/device/remove/"+nid;
                } 
                $("#tree_url").hide();              
                $("#tree_url_device").hide();
                $("#delete-node").hide();
                $("#delete-node-device").show();
                $("#delete-node-device").attr("href", del_url);
            }
            
            var cookieDate = new Date();
            cookieDate.setTime(cookieDate.getTime() + (30 * 60 * 1000));
            
            $.cookie("aitico_tree_info_selected", nid, { expires: cookieDate});
            
        });
        
        
        //User table js action
        $("#usertable tr:gt(0)").live('click',  function(){
            if($(this).hasClass("active")){
                $(this).removeClass("selected");
                $(this).removeClass("active");
                $('#tab-users-buttons #user-edit').hide();
                $('#tab-users-buttons #user-delete').hide();  
                
                return;
            }
          
            $("#usertable tr").removeClass("active");   
            $("#usertable tr").removeClass("selected");   
            $(this).addClass("active");
            $(this).addClass("selected");
            
            var user_edit_url = '/administration/user/edit/'+$(this).data('user-id');
            var user_del_url = '/user/'+$(this).data('user-id')+'/cancel';
            
            if(lang=='fi'){
                user_edit_url = "/"+lang+'/administration/user/edit/'+$(this).data('user-id');
                user_del_url = "/"+lang+'/user/'+$(this).data('user-id')+'/cancel';
            } 
            
            if(!$('#usertable tr td').is('.dataTables_empty')){
                $('#user-delete').attr('href',user_del_url);
                $('#user-edit').attr('href',user_edit_url);
                if($("#usertable").has('thead')){
                    $('#tab-users-buttons #user-edit').show();
                    $('#tab-users-buttons #user-delete').show();  
                }
            }      
    
        });  
        

        $("#rent-button").bind("click" , function(){
            var description = $("#form-description").val();
            var duration = $("#duration").val();
            var rent_url =$("#rent-info").data("cst-url");
            if(duration == '00:00'){
                $("#duration_error").text(Drupal.t('Duration must be greater than 00:00'));
                $("#duration_error").show();
                return false;
            }else{
                $("#duration_error").hide();
            }
            var all_device = 0;
            $(".cst-device-info").each(function() { 
             var available_device = $(this).data("available-device"); 
             all_device  = all_device + available_device;
             });
             
            if (all_device != 0){
                $.ajax({
                    url:  rent_url,
                    type: 'POST',
                    data: {
                        'description' : description ,
                        'duration' : duration
                    },                   
                    success: function(data) {     
                        $('.modal-body').find('p').show();
                        $("#show-pin").text(data);
                    }
                    
                });
            }else{
              $('.modal-body').find('p').hide();
              $("#show-pin").text(Drupal.t('There is no available device to rent!'));
            }
        });
        
        $(".filegroup-add-item").live('click', function(){
            
            var fileGroupId = $(this).data('groupid');
            var containerId = 'widget-main-filegroup-'+fileGroupId;
            var containerDiv = $('#'+containerId);
            var cloneDiv = $('#slot-item-clone').clone();
            cloneDiv.removeAttr('id');
            cloneDiv.removeAttr('style');
            cloneDiv.appendTo(containerDiv);
        })
        
        $('.action-delete-filegroup').live('click', function(e){
            var fileGroupId = $(this).data('groupid');
            
            $result = confirm(Drupal.t('Are you sure to delete the file group?'));
            if($result){
                $('#aitico-filegroup-remove-form-'+fileGroupId).submit();
            } else {
                e.preventDefault();
            }
        })

        $(".remove-slot").live('click', function(){
            $(this).closest('div').remove();
        })
        
        $(".filegroup-submit-item").live('click', function(){
            var fileGroupId = $(this).data('groupid');
            $('#aitico-filegroup-slots-form-'+fileGroupId).submit();
        })
        
        $('.aitico-remove-filegroup-form').live('submit', function(event) {
            $.ajax({
                type: 'POST',
                data: $(this).serialize(),
                url: $(this).attr('action'),
                success: function(data) {                    
                    location.reload();
                }
            });
            
            return false; 
            
        });
        
        
        $("#filegroup-add-group").live('click', function(){
            if($('#group-add').val() == ''){
                var containerDiv = $('#page-content');
                cloneDiv = $('#filegroup-validation-message').clone();
                cloneDiv.removeAttr('id');
                cloneDiv.removeAttr('style');
                cloneDiv.prependTo(containerDiv);
                return false;
            }
            
        })
        

        $('.aitico-filegroup-forms').live('submit', function(event) {
          
            $.ajax({
                type: 'POST',
                data: $(this).serialize(),
                url: $(this).attr('action'),
                success: function(data) {
                    location.reload();
                }
            });
            
            return false; 
            
        });

        $("a.save-schedule").live('click' , function(){
            var save_tr_id = $(this).attr('id');
            var id_arr = save_tr_id.split('-');                
            var sch_id = id_arr[2];                
            var date_value = $("#schedule-date-"+sch_id).val();
            var time_value = $("#schedule-time-"+sch_id).val();   
            $.ajax({
                type: 'POST',
                data: {
                    date : date_value,
                    time : time_value
                },
                url: $(this).attr('href'),
                success: function(data) {
                        
                }
            });
            
            return false; 
        });
        //schedule row selection
        $("#schedule-table tr").live('click' , function(){
            if($(this).hasClass('selected')){
                $(this).removeClass('selected');
                $("#schedule_remove_url").hide();
                $("#duplicate_schedule_update").hide();
            } else {
                $(this).closest('#schedule-table').find('tbody tr').removeClass('selected');
                $(this).addClass('selected');
                $("#schedule_remove_url").show();
                $("#duplicate_schedule_update").show();
            }
                
        });
        
        $('#duplicate_schedule_update').live('click' , function(){
            var file_group_id = $('#group-select').val();
            var sch_id = $('#schedule-table').find('tr.selected').attr("nid");
            $.ajax({
                type: 'POST',
                url : '/administration/schedule/duplicate/' + file_group_id + '/' + sch_id,
             
                success: function(data) {
                    jQuery("#group-select").change();
                }
            });
            
            return false; 
        });
        //Change device status
        $(".device-change-status").live('click',function(){           
                $.ajax({
                type: 'POST',
                url: $(this).attr('href'),             
                success: function(data) {
                   location.reload();
                }
            });
            
            return false
        });
        
    });
    
    Drupal.behaviors.fileUpload = {
        attach: function (context, settings) {
            $('#tab-files input[type=file], .group-wallpaper input[type=file]',context).on('change', function(){
                var can_upload = false;
                var files = $(this).val();
                var img_type_arr = [ 'png', 'jpg','jpeg', 'gif' , 'tiff' ,'bmp'];  
                
                var file_extension = files.split('.').pop().toLowerCase();                
                var slot_file_type = $(this).closest('form').find('input[name=file_type]').val();                
                if(slot_file_type == 1 && jQuery.inArray(file_extension, img_type_arr) > -1){
                    can_upload = true;
                }else if (slot_file_type == 2 && (file_extension == 'text' || file_extension == 'txt')){
                    can_upload = true;
                }
                if (can_upload){
                    var form_id = $(this).closest("form").attr("id");   
                    var serialize = $("#"+form_id).serialize();
                    var action  = $("#"+form_id).attr("action");          
                    var form  = document.getElementById(form_id);                
                    ajaxUpload(form, action+"?"+serialize, $(this), "slot");  
                }else{
                    alert(Drupal.t('Uploaded type mismatch , please choose another type .'));
                }


            });

        }


    }    
  
    Drupal.behaviors.searchLog = {
        attach: function (context, settings) { 
            $('#search_logs').attr('disabled','disabled');           
            //handle invalid time range in search log    
            $('#search_logs').live('click',function(){
                
                var date_range = $('#id-date-range-picker-1').val();
                if(dateRangeValidation(date_range)){
                    return true;
                }else { 
                    $("#invalid-time-range").show();
                    return false;
                }
                
            })
            //form submit through enter key
            $('#search_logs_frm').keypress(function(e){
                if ( e.which == 13 ) // Enter key = keycode 13
                {  
                    var date_range = $('#id-date-range-picker-1').val();
                    if(dateRangeValidation(date_range)){
                        return true;
                    }else { 
                        $("#invalid-time-range").show();
                        return false;
                    }
                }

            });
            
        }
        
        
    }
    
    
    
    Drupal.behaviors.system = {
        attach: function (context, settings) {
             $("#system-node").click(function(){
                $("#admin-action").show();
                $(".tree_table tr").removeClass("selected");
                $("#delete-node").hide();
                $("#tree_url").hide();
            });
        }
    }  
    
    Drupal.behaviors.schedule = {
        attach: function (context, settings) {   
            
            $('.schedule_file input[type=file]'  , context).on('change', function(){                
                var can_upload = false;
                var files = $(this).val();
                var img_type_arr = [ 'png', 'jpg','jpeg', 'gif' , 'tiff' ,'bmp'];  
                
                var file_extension = files.split('.').pop();                
                var slot_file_type = $(this).closest('form').find('input[name=file_type]').val();                
                if(slot_file_type == 1 && jQuery.inArray(file_extension, img_type_arr) > -1){
                    can_upload = true;
                }else if (slot_file_type == 2 && (file_extension == 'text' || file_extension == 'txt')){
                    can_upload = true;
                }
                if (can_upload){
                    var form_id = $(this).closest("form").attr("id");   
                    var serialize = $("#"+form_id).serialize();
                    var action  = $("#"+form_id).attr("action");          
                    var form  = document.getElementById(form_id);                
                    ajaxUpload(form, action+"?"+serialize, $(this), "schedule");  
                }else{
                    alert('Uploaded type mismatch , please choose another type .');
                } 
            });
            
            
            $("#schedule_remove_url").hide();

            var group_id = '';
            $("#group-select").change(function(){
                group_id = $(this).val();
                $("#update_cst_group_"+group_id).trigger('click');    
            });
            
            $("#add_schedule_update").click(function(){
                var gid = $("#group-select").val();             
                $("#add_schedule_update_"+gid).trigger('click');                               
            });
                    
            $('.schedule-date').on('changeDate', function(ev){  
                var $this = $(this);
                var sch_id = $this.parents('tr').attr("nid");
                $('a#save-schedule-'+sch_id).show();
                var d1=new Date(ev.date);  
                var dd1 = d1.toDateString();    
                var dd2 = d1.toLocaleString();    
                console.log(dd1);
                $(this).attr('value',dd1);
                $(this).datepicker('hide');                
            });
            
            $('.schedule-time').on('changeTime', function() {
                $('#stime').text($(this).val());
            });
            
             
            $("a.save-schedule").live('click' , function(){                 
                var save_tr_id = $(this).attr('id');
                var id_arr = save_tr_id.split('-');                
                var sch_id = id_arr[2];
                
                var date_value = $("#schedule-date-"+sch_id).val();
                var time_value = $("#schedule-time-"+sch_id).val();   
                $.ajax({
                    type: 'POST',
                    data: {
                        date : date_value,
                        time : time_value
                    },
                    url: $(this).attr('href'),
                    success: function(data) {
                        
                    }
                });
            
                return false; 
            });
            
            //schedule deletion modal           
            var schedule_nid = '';
            $(".schedule-delete-btn").bind("click" , function(){
                schedule_nid = $('#schedule-table tr.selected').attr("nid");
                var remove_url = "/administration/schedule/remove/";
         
                $.ajax({
                    url:  remove_url,
                    type: 'POST',
                    data: {
                        schedule_nid:schedule_nid
                    },                   
                    success: function(data) {      
                        $("#group-select").change();
                        $(".schedule-cancel-btn").trigger('click');                   
                    }
                    
                });
           
            });
            

        }
    }  
    
 
   
})(jQuery);


function check_file_type(type , slot){
    
    jQuery.post('/api/keep-alive', {
        data:json, 
        hash:hash
    }, function(data) {
        console.log(data);
    });
}

function test_keep_alive_api(){
    var json = json_string;
    var hash = md5_hash;
    jQuery.post('/api/keep-alive', {
        data:json, 
        hash:hash
    }, function(data) {
        console.log(data);
    });
}

function test_get_permission_api(){
    
    var pin = pin_code;
    var device_id = device;
    var cst_id = cst;
    var hash = md5_hash;
    
    jQuery.get('/api/get-permission', {
        pincode:pin, 
        device:device_id, 
        cstid:cst_id, 
        hash:hash
    }, function(data) {
        console.log(data);
    });
}



function $m(theVar){
    return document.getElementById(theVar);
}
function remove(theVar){
    var theParent = theVar.parentNode;
    theParent.removeChild(theVar);
}
function addEvent(obj, evType, fn){
    if(obj.addEventListener)
        obj.addEventListener(evType, fn, true);
    if(obj.attachEvent)
        obj.attachEvent("on"+evType, fn);
}
function removeEvent(obj, type, fn){
    if(obj.detachEvent){
        obj.detachEvent('on'+type, fn);
    }else{
        obj.removeEventListener(type, fn, false);
    }
}
function isWebKit(){
    return RegExp(" AppleWebKit/").test(navigator.userAgent);
}


function ajaxUpload(form, url_action, currentInput , type){
    
    $("#loader").css("display","block");
    var detectWebKit = isWebKit();
    form = typeof(form)=="string"?$m(form):form;
    var erro="";
    if(form==null || typeof(form)=="undefined"){
        erro += "The form of 1st parameter does not exists.\n";
    }else if(form.nodeName.toLowerCase()!="form"){
        erro += "The form of 1st parameter its not a form.\n";
    }
    if(erro.length>0){
        alert("Error in call ajaxUpload:\n" + erro);
        return;
    }
    var iframe = document.createElement("iframe");
    iframe.setAttribute("id","ajax-temp");
    iframe.setAttribute("name","ajax-temp");
    iframe.setAttribute("width","0");
    iframe.setAttribute("height","0");
    iframe.setAttribute("border","0");
    iframe.setAttribute("style","width: 0; height: 0; border: none;");
    form.parentNode.appendChild(iframe);
    window.frames['ajax-temp'].name="ajax-temp";
    var doUpload = function(){
        var ifhtml = jQuery(document).find("iframe").contents().find("body div#upload_ajax").html();
        if(typeof ifhtml != "undefined") {
            if(type == "slot"){                
                var cst_id = jQuery("#global_cst_id").val();
                jQuery(document).find('span.ajax_link_files_refresh a.use-ajax').trigger('click');
            }
            else {
                jQuery("#group-select").change();
            }
        }
        else {
            var body = jQuery(document).find("iframe").contents().find("body").html();  
            alert("An error occurred while uploading .The specified file could not be uploaded, Please try again !");
            $("#loader").css("display","none");
        }
       
        removeEvent($m('ajax-temp'),"load", doUpload);
        if(detectWebKit){
            remove($m('ajax-temp'));
        }else{
            setTimeout(function(){
                remove($m('ajax-temp'))
            }, 250);
        }
    }
    addEvent($m('ajax-temp'),"load", doUpload);
    form.setAttribute("target","ajax-temp");
    form.setAttribute("action",url_action);
    form.setAttribute("method","post");
    form.setAttribute("enctype","multipart/form-data");
    form.setAttribute("encoding","multipart/form-data");
    form.submit();
}

function dateRangeValidation(date_range){
    var date_regex = /^((0?[1-9]|1[012])[- /.](0?[1-9]|[12][0-9]|3[01])[- /.](19|20)?[0-9]{2})*$/;
                
    if(date_range){ //check empty
        var date_range_arr = date_range.split("-");
                    
        if(date_range_arr.length==2){ //check start date and end date
                        
            var start_date = date_range_arr[0].trim();
            var end_date = date_range_arr[1].trim();
            var chk_start_date = date_regex.test(start_date);
            var chk_end_date = date_regex.test(end_date);
            if(chk_start_date && chk_end_date){ //check date range
                return true;
            } else {
                return false;
            }  
        } else{
            return false;
        }
    } else { 
        return false;
    }
}