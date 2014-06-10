<?php

	if(empty($_POST['kento_wp_stats_hidden']))
		{
			$kento_wp_stats_delete_data = get_option( 'kento_wp_stats_delete_data' );	
				
					
		}

	else
		{
		
		if($_POST['kento_wp_stats_hidden'] == 'Y')
			{
			//Form data sent
			
			
			$kento_wp_stats_delete_data = $_POST['kento_wp_stats_delete_data'];
			update_option('kento_wp_stats_delete_data', $kento_wp_stats_delete_data);			

			
			?>
			<div class="updated"><p><strong><?php _e('Changes Saved.' ); ?></strong></p>
            </div>
            
            
            
            
<?php
			}
		} 
?>

 
 
 
 
 
 
 
 <div class="wrap">
 
 
<form  method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
	<input type="hidden" name="kento_wp_stats_hidden" value="Y">
        <?php settings_fields( 'kento_wp_stats_options' );
				do_settings_sections( 'kento_wp_stats_options' );
		?>

<div class="wp-settings-pro">
    <div class="heading"><h2>Kento WP Stats Settings</h2></div>
    
    <div class="settings-saved">
    </div>
    

    
    <!--
    <div class="setting-descriptions"><p></p></div>

     -->


    <div class="option-area">
    <div class="option-title"><strong>Reset Data ?</strong>
    
    </div>
    <div class="option-descriptions">Delete all data on table when uninstall or delete plugin.
    </div>
    
    <div class="option-input">
<label ><input type="radio" name="kento_wp_stats_delete_data"  value="yes" <?php  if($kento_wp_stats_delete_data=='yes') echo "checked"; ?>/><span title="yes" class="kento_wp_stats_delete_data_yes <?php  if($kento_wp_stats_delete_data=='yes') echo "selected"; ?>">Yes</span></label>
            
        	<label ><input type="radio" name="kento_wp_stats_delete_data"  value="no" <?php  if($kento_wp_stats_delete_data=='no') echo "checked"; ?>/><span title="no" class="kento_wp_stats_delete_data_no <?php  if($kento_wp_stats_delete_data=='no') echo "selected"; ?>">No</span></label>
    
        
    </div>

                <p class="submit">
                    <input class="button button-primary" type="submit" name="Submit" value="<?php _e('Save Changes' ) ?>" />
                </p>

</div>

</form>



</div>





<style type="text/css">


.kento-wp-stats-box {
  background: none repeat scroll 0 0 #29D883;
  border-bottom: 2px solid #117042;
  border-top: 2px solid #117042;
  padding-bottom: 30px;
}

.kento-wp-stats-box .box-title {
  font-size: 14px;
  font-weight: bold;
  padding: 10px 0 10px 15px;
}




.wp-settings-pro {
  background: none repeat scroll 0 0 #FFFFFF;
  margin-bottom: 20px;
  padding-bottom: 20px;
  width: 100%;
}

.wp-settings-pro .heading {
  border-bottom: 2px solid #666666;
}




.wp-settings-pro .heading h2 {
  color: #333333;
  font-size: 20px;
  font-weight: bold;
  padding-left: 20px;
}

.wp-settings-pro .heading .updated {
  margin-left: 20px;
}


.wp-settings-pro .submit {
	margin-left: 20px;
}
.wp-settings-pro .setting-descriptions{

}

.wp-settings-pro .setting-descriptions p {
  border-bottom: 1px solid;
  color: #999999;
  font-size: 13px;
  margin-bottom: 15px;
  margin-left: 20px;
  margin-top: 15px;
  padding-bottom: 5px;
}

.wp-settings-pro .option-area {
  margin: 30px 0;
}


.wp-settings-pro .option-area .option-title {
  font-size: 15px;
  margin: 10px 0;
  padding-left: 20px;
}

.wp-settings-pro .option-area .option-descriptions {
  font-size: 13px;
  padding-left: 20px;
}

.wp-settings-pro .option-area .option-input {
  border-bottom: 1px solid #DDDDDD;
  margin-left: 20px;
  padding: 20px 0;
}

</style>








</div>
