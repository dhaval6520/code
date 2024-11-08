jQuery(document).ready(function() {
      var currentRequest = null;
      jQuery(".js-select2").select2({
        closeOnSelect: true
      }).on('change', function(e){
            var order_id = jQuery(this).val();
            currentRequest = jQuery.ajax({
                type: 'POST',
                data: {
                  'action' : 'jt_track_order_account',
                  'order_id' : order_id,
                },
                url: jt_ajax_object_account.ajaxurl,
                cache: false,
                beforeSend : function()    {          
                    if(currentRequest != null) {
                        currentRequest.abort();
                    }
                },
                success: function(responce) {
                    jQuery(".ajax_response").html(responce);
                }
            });
        });
});
