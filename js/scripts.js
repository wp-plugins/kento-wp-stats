
jQuery(document).ready(function()
	{




      var hook = true;
      window.onbeforeunload = function() {
        if (hook) {
			
		  document.cookie="knp_landing=0; path=/";
		  
				var knp_online_count = -1;
				jQuery.ajax(
					{
				type: 'POST',
				url: kento_wp_stats_ajax.kento_wp_stats_ajaxurl,
				data: {"action": "kento_wp_stats_offline_visitors", "knp_online_count":knp_online_count},
				success: function(data)
						{
							
						}
					});	
		  
		  
		  
		  
		  
        }
      }

		
		
		
		
	
	});	







