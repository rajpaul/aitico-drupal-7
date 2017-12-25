$(document).ready ( function(){

    $(".alert").alert();

    $('[data-rel=tooltip]').tooltip();

    $('textarea').autosize();

    $('#duration').timepicker({
        minuteStep: 15,
        showInputs: true,
        showMeridian: false,
        defaultTime: '00:15 AM'
    });

//    $('#logtable').dataTable();
//    $('#usertable').dataTable();

    /*
    Manipulate charging stations
    */


  /*  $('.charging-stations').on('click','.charging-info', function(){

        if($(this).hasClass('selected')){
            $(this).removeClass('selected');
            //remove link from rent button that modal won't fire
            $('#rent-button').addClass('disabled').attr('href', '');
        } else {
            $(this).closest('.charging-stations').find('.charging-info').removeClass('selected');
            $(this).addClass('selected');

            //add link to rent button that fires the modal pin code window
            $('#rent-button').removeClass('disabled').attr('href', '#pinCode');
        }
    });*/


    /*
    Admin view
    */

    //Tree table start

    $('#example-basic').treetable({
        expandable: true,
        onNodeExpand: function() {writeTreeCookie( $(this).attr("id")  )},
        onNodeCollapse: function() {removeTreeCookie( $(this).attr("id")  )},
        
    });

    $("#example-basic").on('mousedown','tbody tr', function() {
      $("tr.selected").removeClass("selected");
      $(this).addClass("selected");
    });

    $('#delete-node').on('click', function(){
        selected = $('tr.selected').data("ttId");
        node = $("#example-basic").treetable("node", selected);
        $("#example-basic").treetable("unloadBranch", node);
        $('tr.selected').remove();
    });

    $('#add-node').on('click', function(){
        rows = '<tr><td><span></span>Added item</td><td>75%</td><td>345112</td><td>1234</td><td></td></tr>';
        selected = $('tr.selected').data("ttId");
        node = $("#example-basic").treetable("node", selected);
        $("#example-basic").treetable("loadBranch", node, rows);
    });


    //Tree table end

    function writeTreeCookie(id){
        type = $("#row-marker-"+id).attr("type")
        treeInfo = 0;
        treeInfoText = $.cookie("aitico_tree_info")
        treeInfo = JSON.parse(treeInfoText)
        if(!treeInfo){
            var treeInfo ={"tree":[]}
        }
        var rows = treeInfo.tree;
        if(type == 'company'){
            treeInfo.tree[rows.length] = { "company": id, "site":0, "cst":0}
        }
        else if(type == 'site')
        {
            companyId = $("#row-marker-"+id).attr("data-tt-parent-id");
            treeInfo.tree[rows.length] = { "company": companyId, "site": id, "cst":0}
        }
        else if(type == 'charging_station')
        {
            siteId = $("#row-marker-"+id).attr("data-tt-parent-id");
            companyId = $("#row-marker-"+siteId).attr("data-tt-parent-id");
            treeInfo.tree[rows.length] = { "company": companyId, "site": siteId, "cst":id}
        }
        
        var cookieDate = new Date();
        cookieDate.setTime(cookieDate.getTime() + (30 * 60 * 1000));
        
        $.cookie("aitico_tree_info", JSON.stringify(treeInfo), { expires: cookieDate});
        
    }

    function removeTreeCookie(id){
        type = $("#row-marker-"+id).attr("type")
        treeInfo = 0;
        treeInfoText = $.cookie("aitico_tree_info")
        treeInfo = JSON.parse(treeInfoText)
        if(!treeInfo){
            var treeInfo ={"tree":[]}
        }
        var rows = treeInfo.tree;
        if(type == 'company'){
            matches = findTreeCompany(rows,id);
            rows = matches
        }
        else if(type == 'site')
        {
            matches = findTreeSite(rows,id);
            rows = matches
        }
        else if(type == 'charging_station')
        {
            matches = findTreeCst(rows,id);
            rows = matches
        }
        treeInfo.tree = rows

        var cookieDate = new Date();
        cookieDate.setTime(cookieDate.getTime() + (30 * 60 * 1000));
        
        $.cookie("aitico_tree_info", JSON.stringify(treeInfo), { expires: cookieDate});
        
    }
    
    function findTreeCompany(rows,id){
        var matches = $.grep(rows, function (elt)
        {
            return elt.company !== id;
        });
        return matches;
    }
    function findTreeSite(rows,id){
        var matches = $.grep(rows, function (elt)
        {
            return elt.site !== id;
        });
        return matches;
    }
    function findTreeCst(rows,id){
        var matches = $.grep(rows, function (elt)
        {
            return elt.cst !== id;
        });
        return matches;
    }
    
    $('#tab-logos input[type=file]').ace_file_input({
        no_file:'Select image...',
        btn_choose:'Choose',
        btn_change:'Change'
    }).on('change', function(){
        var files = $(this).data('ace_input_files');
        //upload files, etc ...
    });

    $('#tab-files input[type=file]').ace_file_input({
        style:'well',/** for making it like the larger file input in the example */
        no_file:'No File ...',
        btn_choose:'Choose',
        btn_change:'Change',
        thumbnail:'large',
        no_icon:'icon-cloud-upload',
    }).on('change', function(){
        var files = $(this).data('ace_input_files');
        //upload files, etc ...
    });

    $('#id-date-range-picker-1').daterangepicker({
        ranges: {
            'Today': ['today', 'today'],
            'Yesterday': ['yesterday', 'yesterday'],
            'Last 7 Days': [Date.today().add({ days: -6 }), 'today'],
            'Last 30 Days': [Date.today().add({ days: -29 }), 'today'],
            'This Month': [Date.today().moveToFirstDayOfMonth(), Date.today().moveToLastDayOfMonth()],
            'Last Month': [Date.today().moveToFirstDayOfMonth().add({ months: -1 }), Date.today().moveToFirstDayOfMonth().add({ days: -1 })]
        }
    });

    //User table

    $('#usertable').on('mousedown','tbody tr', function(){

        if($(this).hasClass('selected')){
            $(this).removeClass('selected');
            //remove link from rent button that modal won't fire
            $('#user-delete, #user-edit').addClass('disabled').attr('href', '');
        } else {
            $(this).closest('#usertable').find('tbody tr').removeClass('selected');
            $(this).addClass('selected');

            //add link to rent button that fires the modal pin code window
            $('#user-delete').removeClass('disabled').attr('href', '#deleteUser');
            $('#user-edit').removeClass('disabled').attr('href', '#userModal');
        }
    });

    //End user table

    /*
    Schedule file update functions
    */

   /* $('#schedule-table').on('mousedown','tbody tr', function(){

        if($(this).hasClass('selected')){
            $(this).removeClass('selected');
            //remove link from rent button that modal won't fire
            $('#user-delete, #user-edit').addClass('disabled').attr('href', '');
        } else {
            $(this).closest('#schedule-table').find('tbody tr').removeClass('selected');
            $(this).addClass('selected');

            //add link to rent button that fires the modal pin code window
            $('#user-delete').removeClass('disabled').attr('href', '#deleteUser');
            $('#user-edit').removeClass('disabled').attr('href', '#userModal');
        }
    });

    $('#schedule-time').timepicker({
        minuteStep: 15,
        showInputs: true,
        showMeridian: false,
    });

    $('#schedule-date').datepicker({
        autoclose : true
    });*/

//    $('.schedule-view input[type=file]').ace_file_input({
//        style:'well',/** for making it like the larger file input in the example */
//        no_file:'No File ...',
//        btn_choose:'Choose',
//        btn_change:'Change',
//        thumbnail:'large',
//        no_icon:'icon-cloud-upload',
//    }).on('change', function(){
//        var files = $(this).data('ace_input_files');
//        //upload files, etc ...
//    });

    /*
    Login and Forgot Password boxes
     */

    $('#login-box .forgot-password-link').on('click', function(){
        $('.widget-box.visible').removeClass('visible');
        $('#forgot-box').addClass('visible');
    })
    $('#forgot-box .back-to-login-link').on('click', function(){
        $('.widget-box.visible').removeClass('visible');
        $('#login-box').addClass('visible');
    })

});
