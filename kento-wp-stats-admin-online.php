<div class="wrap">

<div class="kento-wp-stats-admin">
<script>		
	jQuery(document).ready(function()
		{

			setInterval(function(){
				jQuery.ajax(
						{
					type: 'POST',
					url: kento_wp_stats_ajax.kento_wp_stats_ajaxurl,
					data: {"action": "kento_wp_stats_ajax_online_total"},
					success: function(data)
							{
								jQuery(".onlinecount .count").html(data);
							}
						});	
			}, 30000)
					});
			
</script>
<div class="onlinecount">
Total User Online<br />
<span class="count"></span>
</div>


<script>		
	jQuery(document).ready(function()
		{

			setInterval(function(){
				jQuery.ajax(
						{
					type: 'POST',
					url: kento_wp_stats_ajax.kento_wp_stats_ajaxurl,
					data: {"action": "kento_wp_stats_visitors_page"},
					success: function(data)
							{
								jQuery(".visitors").html(data);
							}
						});	
			}, 30000)
					});
			
</script>


<div class="visitors">
</div>


</div>



</div>
