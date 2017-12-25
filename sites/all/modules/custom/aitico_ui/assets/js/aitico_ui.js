/**
 * Put jQuery back into the global $ object.
 * This is necessary because Aitico Theme uses some JS that assumes global $ is jQuery
 */
$ = jQuery;

/**
 * Reload the page whenever any ajax call gives 403 error.
 * Home page will show login form if user is not logged in.
 */
(function($) {
    $(document).ready(function() {

        $('body').bind('ajaxSuccess', function(event, request, settings) {
            if (403 == request.status) {
                location.reload(true);
            }
        }).bind('ajaxError', function(event, request, settings) {
            if (403 == request.status){
                location.reload(true);
            }
        });

    });
})(jQuery);
