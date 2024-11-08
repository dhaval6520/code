jQuery(document).ready(function($) {
	var currentRequest = null;
	jQuery("#get_jt_response a#jt_tracking").click(function(event) {
		var waybill = jQuery("#order-tracking").val();
		currentRequest = jQuery.ajax({
                type: 'POST',
                data: {
                  'action' : 'jt_track_order',
                  'waybill' : waybill,
                },
                url: jt_ajax_object.ajaxurl,
                cache: false,
                beforeSend : function()    {          
                    if(currentRequest != null) {
                        currentRequest.abort();
                    }
                },
                success: function(responce) {
                    jQuery(".jtrespone").html(responce);
                }
        });
	});
});