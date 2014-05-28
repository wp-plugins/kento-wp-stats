<?php
/*
Plugin Name: Kento Wordpress Stats
Plugin URI: http://kentothemes.com
Description: 
Version: 1.0
Author: KentoThemes
Author URI: http://kentothemes.com
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

require_once( plugin_dir_path( __FILE__ ) . 'includes/Browser.php');
require_once( plugin_dir_path( __FILE__ ) . 'includes/geoplugin.class.php');

define('KENTO_WORDPRESS_STATS_PLUGIN_PATH', WP_PLUGIN_URL . '/' . plugin_basename( dirname(__FILE__) ) . '/' );
function kento_wp_stats_init_scripts()
	{
		wp_enqueue_script('jquery');
		wp_enqueue_style('kento-wp-stats-style', KENTO_WORDPRESS_STATS_PLUGIN_PATH.'css/style.css');
		wp_enqueue_style('kento-wp-stats-flags', KENTO_WORDPRESS_STATS_PLUGIN_PATH.'css/flags.css');
		wp_enqueue_script('kento-wp-stats-js', plugins_url( '/js/scripts.js' , __FILE__ ) , array( 'jquery' ));
		wp_localize_script( 'kento-wp-stats-js', 'kento_wp_stats_ajax', array( 'kento_wp_stats_ajaxurl' => admin_url( 'admin-ajax.php')));


		
		
	}
add_action("init","kento_wp_stats_init_scripts");







register_activation_hook(__FILE__, 'kento_wp_stats_install');


function kento_wp_stats_install()
	{
	global $wpdb;
        $sql = "CREATE TABLE IF NOT EXISTS " . $wpdb->prefix . "kento_wp_stats"
                 ."( UNIQUE KEY id (id),
					id int(100) NOT NULL AUTO_INCREMENT,
					session_id	VARCHAR( 255 )	NOT NULL,
					knp_date	DATE NOT NULL,
					knp_time	TIME NOT NULL,
					duration	TIME NOT NULL,
					userid	VARCHAR( 50 )	NOT NULL,
					event	VARCHAR( 50 )	NOT NULL,
					browser	VARCHAR( 50 )	NOT NULL,
					platform	VARCHAR( 50 )	NOT NULL,
					ip	VARCHAR( 20 )	NOT NULL,
					city	VARCHAR( 50 )	NOT NULL,
					region	VARCHAR( 50 )	NOT NULL,
					countryName	VARCHAR( 50 )	NOT NULL,
					url_id	VARCHAR( 255 )	NOT NULL,
					referer_doamin	VARCHAR( 255 )	NOT NULL,
					referer_url	TEXT NOT NULL,
					screensize	VARCHAR( 50 ) NOT NULL,
					isunique	VARCHAR( 50 ) NOT NULL,
					landing	VARCHAR( 10 ) NOT NULL
					
					
					)";
		$wpdb->query($sql);
		
		
		
        $sql2 = "CREATE TABLE IF NOT EXISTS " . $wpdb->prefix . "kento_wp_stats_online"
                 ."( UNIQUE KEY id (id),
					id int(100) NOT NULL AUTO_INCREMENT,
					session_id VARCHAR( 255 ) NOT NULL,
					knp_time TIME NOT NULL,
					userid	VARCHAR( 50 )	NOT NULL,
					url_id	VARCHAR( 255 )	NOT NULL,
					city	VARCHAR( 50 )	NOT NULL,
					region	VARCHAR( 50 )	NOT NULL,
					countryName	VARCHAR( 50 )	NOT NULL,
					browser	VARCHAR( 50 )	NOT NULL,
					platform	VARCHAR( 50 )	NOT NULL,
					referer_doamin	VARCHAR( 255 )	NOT NULL,
					referer_url	TEXT NOT NULL
				
					
					
					)";
		$wpdb->query($sql2);		
		
		
		
		
		
		
		
		

		}


	



function kento_wp_stats()
	{
	$knp_date = kento_wp_stats_get_date();
	$knp_time = kento_wp_stats_get_time();
	$knp_datetime = kento_wp_stats_get_datetime();	
	$duration = $knp_datetime;
	
	$browser = new Browser_KNP();
	$platform = $browser->getPlatform();
	$browser = $browser->getBrowser();
	
	$ip = $_SERVER['REMOTE_ADDR'];
	
	
	$geoplugin = new geoPlugin();
	$geoplugin->locate();
	$city = $geoplugin->city;
	$region = $geoplugin->region;
	$countryName = $geoplugin->countryCode;

	$referer = kento_wp_stats_get_referer();
	$referer = explode(',',$referer);
	$referer_doamin = $referer[0];
	$referer_url = $referer[1];

	$screensize = kento_wp_stats_get_screensize();

	$userid = kento_wp_stats_getuser();
	$url_id = kento_wp_stats_geturl_id();
	$event = "visit";
	
	$isunique = kento_wp_stats_get_unique();
	$landing = kento_wp_stats_landing();
	$knp_session_id = kento_wp_stats_session();
	
	
	global $wpdb;
	$table = $wpdb->prefix . "kento_wp_stats";
		
	$wpdb->query( $wpdb->prepare("INSERT INTO $table 
								( id, session_id, knp_date, knp_time, duration, userid, event, browser, platform, ip, city, region, countryName, url_id, referer_doamin, referer_url, screensize, isunique, landing )
			VALUES	( %d, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s )",
						array	( '', $knp_session_id, $knp_date, $knp_time, $duration, $userid, $event, $browser, $platform, $ip, $city, $region, $countryName, $url_id, $referer_doamin, $referer_url, $screensize, $isunique, $landing )
								));
		
		


$table = $wpdb->prefix . "kento_wp_stats_online";	
$result = $wpdb->get_results("SELECT * FROM $table WHERE session_id='$knp_session_id'", ARRAY_A);
$count = $wpdb->num_rows;




	if($count==NULL)
		{
	$wpdb->query( $wpdb->prepare("INSERT INTO $table 
								( id, session_id, knp_time, userid, url_id, city, region, countryName, browser, platform, referer_doamin, referer_url) VALUES	(%d, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
							array( '', $knp_session_id, $knp_time, $userid, $url_id, $city, $region, $countryName, $browser, $platform, $referer_doamin, $referer_url)
								));
		}
	else
		{
			$wpdb->query("UPDATE $table SET knp_time='$knp_time', url_id='$url_id', referer_doamin='$referer_doamin', referer_url='$referer_url' WHERE session_id='$knp_session_id'");
		}


					
	}

add_action('wp_head', 'kento_wp_stats');




function kento_wp_stats_register_session(){
    if( !session_id() )
        session_start();
}
add_action('init','kento_wp_stats_register_session');


function kento_wp_stats_session(){

	$knp_session_id = session_id();
	return $knp_session_id;

}


function kento_wp_stats_ajax_online_total()
	{	
		global $wpdb;
		$table = $wpdb->prefix . "kento_wp_stats_online";	
		$count_online = $wpdb->get_results("SELECT * FROM $table", ARRAY_A);
		$count_online = $wpdb->num_rows;
		
		echo $count_online;
		
		$time = date("H:i:s", strtotime(kento_wp_stats_get_datetime()." -120 seconds"));
		$wpdb->query("DELETE FROM $table WHERE knp_time < '$time' ");


		die();
	}
add_action('wp_ajax_kento_wp_stats_ajax_online_total', 'kento_wp_stats_ajax_online_total');
add_action('wp_ajax_nopriv_kento_wp_stats_ajax_online_total', 'kento_wp_stats_ajax_online_total');



function kento_wp_stats_offline_visitors()
	{
		$knp_session_id = kento_wp_stats_session();
		$last_time = kento_wp_stats_get_time();


		global $wpdb;
		$table = $wpdb->prefix."kento_wp_stats";
		
		
		$wpdb->query("UPDATE $table SET duration = '$last_time' WHERE session_id='$knp_session_id' ORDER BY id DESC LIMIT 1");

		$table = $wpdb->prefix . "kento_wp_stats_online";
		
		$wpdb->delete( $table, array( 'session_id' => $knp_session_id ) );




	}

add_action('wp_ajax_kento_wp_stats_offline_visitors', 'kento_wp_stats_offline_visitors');
add_action('wp_ajax_nopriv_kento_wp_stats_offline_visitors', 'kento_wp_stats_offline_visitors');

















function kento_wp_stats_visitors_page()
	{	
		global $wpdb;
		$table = $wpdb->prefix . "kento_wp_stats_online";
		$entries = $wpdb->get_results( "SELECT * FROM $table ORDER BY knp_time DESC" );
		

		

 		echo "<br /><br />";
		echo "<table class='widefat' >";
		echo "<thead><tr>";
		echo "<th scope='col' class='manage-column column-name' style=''><strong>Page</strong></th>";
		echo "<th scope='col' class='manage-column column-name' style=''><strong>User</strong></th>";
		echo "<th scope='col' class='manage-column column-name' style=''><strong>Time</strong></th>";		
		echo "<th scope='col' class='manage-column column-name' style=''><strong>Duration</strong></th>";		
		echo "<th scope='col' class='manage-column column-name' style=''><strong>City</strong></th>";
		echo "<th scope='col' class='manage-column column-name' style=''><strong>Country</strong></th>";
		echo "<th scope='col' class='manage-column column-name' style=''><strong>Browser</strong></th>";	
		echo "<th scope='col' class='manage-column column-name' style=''><strong>Platform</strong></th>";
		echo "<th scope='col' class='manage-column column-name' style=''><strong>Referer</strong></th>";
		
		echo "</tr></thead>";
		echo "<tr class='no-online' style='text-align:center;'>";
				echo "<td colspan='8' style='color:#f00;'>";
				
				if($entries ==NULL)
					{
					echo "No User online";
					
					}
				
				echo "</td>";
		
		echo "</tr>";

		
		
		
		
		 $count = 1;
		foreach( $entries as $entry )
			{
				

				
				
				
				
				
				
				
				$class = ( $count % 2 == 0 ) ? ' class="alternate"' : '';
				
				
				echo "<tr $class>";
				echo "<td>";
				$url_id = $entry->url_id;
				if(is_numeric($url_id))
					{	
						echo "<a href='".get_permalink($url_id)."'>".get_the_title($url_id)."</a>";

					}
				else
					{
						
						echo "<a href='".$url_id."'>Home</a>";

					}
				echo "</td>";				
				


				echo "<td>";
				$userid = $entry->userid;
				if(is_numeric($userid))
					{	
						$user_info = get_userdata($userid);

						echo "<span title='".$user_info->display_name."' class='avatar'>".get_avatar( $userid, 32 )."</span>";
					}
				else
					{
						echo "<span title='Guest' class='avatar'>".get_avatar( 0, 32 )."</span>";
					}
				echo "</td>";



				
				echo "<td>";
				$knp_time = $entry->knp_time;
				echo "<span class='time'>".$knp_time."</span>";
				echo "</td>";				
				
				
				echo "<td>";
				$current_time = strtotime(kento_wp_stats_get_time());
				$knp_time = strtotime($entry->knp_time);
				$duration = ($current_time - $knp_time);

				echo "<span class='duration'>".gmdate("H:i:s", $duration)."</span>";
				echo "</td>";				
				
				echo "<td>";
				$city = $entry->city;
				echo "<span title='".$city."' class='city'>".$city."</span>";
				echo "</td>";				
				
				echo "<td>";
				$countryName = $entry->countryName;
				echo "<span title='".$countryName."' class='flag flag-".strtolower($countryName)."'></span>";
				echo "</td>";
				
				echo "<td>";
				$browser = $entry->browser;			
				echo "<span  title='".$browser."' class='browser ".$browser."'></span>";			
				echo "</td>";				
				
				echo "<td>";
				$platform = $entry->platform;				
				echo "<span  title='".$platform."' class='platform ".$platform."'></span>";				
				echo "</td>";				
				
				
				echo "<td>";
				$referer_doamin = $entry->referer_doamin;
				
				if($referer_doamin==NULL)
					{
						echo "<span title='Referer Doamin'  class='referer_doamin'>Unknown</span>";
						
					}
				elseif($referer_doamin=='direct')
					{
					echo "<span title='Referer Doamin'  class='referer_doamin'>Direct Visit</span>";
					}	
					
				elseif($referer_doamin=='none')
					{
					echo "<span title='Referer Doamin'  class='referer_doamin'>Unknown</span>";
					}
				else
					{
						echo "<span title='Referer Doamin'  class='referer_doamin'><a href='http://".$referer_doamin."'>Domain</a></span> - ";
					}
					
					
				$referer_url = $entry->referer_url;
				
				if($referer_url==NULL || $referer_url=='none' || $referer_url=='direct')
					{
						echo "<span title='Referer URL' class='referer_url'></span>";
						
					}
				else
					{
						// echo "<span title='Referer URL' class='referer_url'><a href='http://".$referer_url."'>URL</a></span>";
					}				

				echo "</td>";				
				
				
				
				
				
				
								
				echo "</tr>";
				
				
			$count++;
			}
		
		
		echo "</table>";

		die();
	}


add_action('wp_ajax_kento_wp_stats_visitors_page', 'kento_wp_stats_visitors_page');
add_action('wp_ajax_nopriv_kento_wp_stats_visitors_page', 'kento_wp_stats_visitors_page');









function kento_wp_stats_getuser()
	{
		if ( is_user_logged_in() ) 
			{
				$userid = get_current_user_id();
			}
		else
			{
				$userid = "guest";
			}
			
		return $userid;
	}











function kento_wp_stats_geturl_id()
	{	
		global $post;
		
		
		
		if(is_home())
			{
				$url_id = $_SERVER['PHP_SELF'];
			}
		elseif(  is_singular())
			{
				$url_id = get_the_ID();
			}
		elseif( is_404())
			{
				$url_id = 'err_404';
			}			
		elseif( is_admin())
			{
				$url_id = admin_url();
			}	

		else
			{
				$url_id = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
			}
					
		return $url_id;
		
	}


function kento_wp_stats_get_referer()
	{	
		if(isset($_SERVER["HTTP_REFERER"]))
			{
				$referer = $_SERVER["HTTP_REFERER"];
				$pieces = parse_url($referer);
				$domain = isset($pieces['host']) ? $pieces['host'] : '';
					if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $regs))
						{
							$referer = $regs['domain'];
						}
					else
						{
							$referer = "none";
						}
				
				$referurl = $_SERVER["HTTP_REFERER"];
			
			}
		else
			{
				$referer = "direct";
				$referurl = "none";
			}
		return $referer.",".$referurl;
	}









	function kento_wp_stats_get_screensize()
		{
	
		if(!isset($_COOKIE["knp_screensize"]))
			{
				
			?>
			<script>
		var exdate=new Date();
		exdate.setDate(exdate.getDate() + 365);    
		var screen_width =  screen.width +"x"+ screen.height;  
		var c_value=screen_width + "; expires="+exdate.toUTCString()+"; path=/";
		document.cookie= 'knp_screensize=' + c_value;
			
			
			</script>
            
            <?php
				$knp_screensize = "unknown";
				
				
			}
		else 
			{
				$knp_screensize = $_COOKIE["knp_screensize"];
			}
		
		
		return $knp_screensize;  
		} 




	function kento_wp_stats_landing()
		{
			if (!isset($_COOKIE['knp_landing']))
				{	

					?>
					<script>
						var exdate=new Date();
						exdate.setDate(exdate.getDate() + 365);    
						knp_landing = 1;
						var c_value=knp_landing + "; expires="+exdate.toUTCString()+"; path=/";
						document.cookie= 'knp_landing=' + c_value;
					
					</script>
					
					<?php
					
					$knp_landing = 1;
					
				}
			else
				{

					$knp_landing = $_COOKIE['knp_landing'];
					$knp_landing += 1;

					?>
					<script>
						var exdate=new Date();
						exdate.setDate(exdate.getDate() + 365);    
						knp_landing = <?php echo $knp_landing; ?>;
						var c_value=knp_landing + "; expires="+exdate.toUTCString()+"; path=/";
						document.cookie= 'knp_landing=' + c_value;
					
					</script>
					
					<?php
					
					
					
					
					
					
					
				}
				

			return $knp_landing;
			
		}


















	function kento_wp_stats_get_date()
		{	
			$gmt_offset = get_option('gmt_offset');
			$knp_datetime = date('Y-m-d', strtotime('+'.$gmt_offset.' hour'));
			
			return $knp_datetime;
		
		}
		

	function kento_wp_stats_get_time()
		{	
			$gmt_offset = get_option('gmt_offset');
			$knp_time = date('H:i:s', strtotime('+'.$gmt_offset.' hour'));
			
			return $knp_time;
		
		}		
		
	function kento_wp_stats_get_datetime()
		{	
			$gmt_offset = get_option('gmt_offset');
			$knp_datetime = date('Y-m-d H:i:s', strtotime('+'.$gmt_offset.' hour'));
			
			return $knp_datetime;
		
		}		
		
		
		


	function kento_wp_stats_get_unique()
		{	

			$cookie_site = md5($_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);

			$cookie_nam = 'knp_page_'.$cookie_site;

			if (isset($_COOKIE[$cookie_nam]))
				{	
					
					$visited = "yes";
		
				}
			else
				{
					
					?>
					<script>
					document.cookie="<?php echo $cookie_nam ?>=yes";
					</script>
					
					<?php
					
					$visited = "no";
				}
		
		
		
		
		
		
			if(empty($_COOKIE[$cookie_nam]))
				{
					$isunique ="yes";
				}
			else 
				{
					$isunique ="no";
				}
				
			return $isunique;
		
		}














function login_with_email_address($username) {
        $user = get_user_by('email',$username);
        if(!empty($user->user_login))
                $username = $user->user_login;
        return $username;
}
add_action('wp_authenticate','login_with_email_address');

function change_username_wps_text($text){
       if(in_array($GLOBALS['pagenow'], array('wp-login.php'))){
         if ($text == 'Username'){$text = 'Username / Email';}
            }
                return $text;
         }
add_filter( 'gettext', 'change_username_wps_text' );





























add_action('admin_init', 'kento_wp_stats_options_init' );
add_action('admin_menu', 'kento_wp_stats_menu_init');


function kento_wp_stats_options_init(){
	register_setting('kento_wp_stats_options', 'kento_wp_stats_total_online');	
	
    }

	
function kento_wp_stats_settings(){
	include('kento-wp-stats-admin-online.php');
}

function kento_wp_stats_menu_init() {

	add_submenu_page('options-general.php', __('Kento WP Stats','menu-kento_wp_stats'), __('Kento WP Stats','menu-kento_wp_stats'), 'manage_options', 'kento_wp_stats_settings', 'kento_wp_stats_settings');
}

?>