 jQuery(document).ready(function(){
    jQuery("#story_search_number").hide();
    jQuery('select#filter_by').on('change', function (e) {
        var optionSelected = jQuery("option:selected", this);
        var valueSelected = this.value;
        var language = jQuery("#page_languag").val();
        if(language != 'ar'){
            if(valueSelected == 'number'){
                jQuery("#story_search_number").show();
                jQuery("#story_search").hide();
            }
            else{
                jQuery("#story_search").show();
                jQuery("#story_search_number").hide();
            }
        }
    });
    var currentRequest = null;
    jQuery('#story_search,#story_search_number').on('input',function(e){
            var search_text  = jQuery(this).val();
            jQuery("#overlay").fadeIn(300);　
            var language = jQuery("#page_languag").val();
            var filter_by = jQuery("select#filter_by").val();
            var filter_by_text = jQuery( "select#filter_by option:selected" ).text();
            currentRequest = jQuery.ajax({
                type: 'POST',
                data: {
                  'action' : 'story_search',
                  'search_text' : search_text,
                  'language' : language,
                  'filter_by' : filter_by,
                },
                url: stories_ajax_object.ajaxurl,
                cache: false,
                beforeSend : function()    {          
                    if(currentRequest != null) {
                        currentRequest.abort();
                    }
                },
                success: function(responce) {
                  jQuery("#overlay").fadeOut(300);
                  if (responce != '') {
                    console.log('kk'+filter_by_text);
                    jQuery(".sfound .search_result_for").show();
                    jQuery(".sfound .search_result_for span.result").text(' '+filter_by_text);
                    if(search_text != ''){
                        jQuery(".sfound .search_result_for span.result").text(' '+filter_by_text +' - '+ search_text);
                    }
                    jQuery(".nofound .story_not_found").hide();
                    jQuery("#current_story").html(responce);
                    jQuery('#current_story .wp-audio-shortcode').mediaelementplayer({ success: function (player, node) {} });
                  }
                  else{
                    jQuery(".nofound .story_not_found").show();
                    jQuery("#current_story").html('');
                  }
                }
            });
    });
    jQuery('select#filter_by').on('change', function() {
        var selected_val = this.value;
            jQuery("#overlay").fadeIn(300);　
            var language = jQuery("#page_languag").val();
            var filter_by = this.value;
            var search_text  = jQuery("input#story_search").val();
            var filter_by_text = jQuery( "select#filter_by option:selected" ).text();
            currentRequest = jQuery.ajax({
                type: 'POST',
                data: {
                  'action' : 'story_search',
                  'search_text' : search_text,
                  'language' : language,
                  'filter_by' : filter_by,
                },
                url: stories_ajax_object.ajaxurl,
                cache: false,
                beforeSend : function()    {          
                    if(currentRequest != null) {
                        currentRequest.abort();
                    }
                },
                success: function(responce) {
                  jQuery("#overlay").fadeOut(300);
                  if (responce != '') {
                    console.log('kk'+filter_by_text);
                    jQuery(".sfound .search_result_for").show();
                    jQuery(".sfound .search_result_for span.result").text(' '+filter_by_text);
                    if(search_text != ''){
                        jQuery(".sfound .search_result_for span.result").text(' '+filter_by_text +' - '+ search_text);
                    }                    jQuery(".nofound .story_not_found").hide();
                    jQuery("#current_story").html(responce);
                    jQuery('#current_story .wp-audio-shortcode').mediaelementplayer({ success: function (player, node) {} });
                  }
                  else{
                    jQuery(".nofound .story_not_found").show();
                    jQuery("#current_story").html('');
                  }
                }
            });
    });
    
});