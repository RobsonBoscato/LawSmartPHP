<?php 
	ini_set('display_errors', 0);
	mysqli_report(MYSQLI_REPORT_OFF);
	include('includes/config.php');
	include('includes/helpers/locale.php');
	
	//--------------------------------------------------------------//
	function dbConnect() { //Connect to database
	//--------------------------------------------------------------//
	    // Access global variables
	    global $mysqli;
	    global $dbHost;
	    global $dbUser;
	    global $dbPass;
	    global $dbName;
	    global $dbPort;
	    
	    // Attempt to connect to database server
	    if(isset($dbPort)) $mysqli = new mysqli($dbHost, $dbUser, $dbPass, $dbName, $dbPort);
	    else $mysqli = new mysqli($dbHost, $dbUser, $dbPass, $dbName);
	
	    // If connection failed...
	    if ($mysqli->connect_error) {
	        fail("<!DOCTYPE html><html><head><meta http-equiv=\"Content-Type\" content=\"text/html;charset=utf-8\"/><link rel=\"Shortcut Icon\" type=\"image/ico\" href=\"/img/favicon.png\"><title>"._('Can\'t connect to database')."</title></head><style type=\"text/css\">body{background: #ffffff;font-family: Helvetica, Arial;}#wrapper{background: #f2f2f2;width: 300px;height: 110px;margin: -140px 0 0 -150px;position: absolute;top: 50%;left: 50%;-webkit-border-radius: 5px;-moz-border-radius: 5px;border-radius: 5px;}p{text-align: center;line-height: 18px;font-size: 12px;padding: 0 30px;}h2{font-weight: normal;text-align: center;font-size: 20px;}a{color: #000;}a:hover{text-decoration: none;}</style><body><div id=\"wrapper\"><p><h2>"._('Can\'t connect to database')."</h2></p><p>"._('There is a problem connecting to the database. Please try again later.')."</p></div></body></html>");
	    }
	    
	    global $charset; mysqli_set_charset($mysqli, isset($charset) ? $charset : "utf8");
	    
	    return $mysqli;
	}
	//--------------------------------------------------------------//
	function fail($errorMsg) { //Database connection fails
	//--------------------------------------------------------------//
	    echo $errorMsg;
	    exit;
	}
	// connect to database
	dbConnect();
?>
<?php 
	include_once('includes/helpers/short.php');
	
	//init
	$i = is_numeric($_GET['i']) ? $_GET['i'] : exit;
	$a = isset($_GET['a']) ? mysqli_real_escape_string($mysqli, $_GET['a']) : '';
	if($i=='') exit;
	if($a=='') exit;
	
	//Get brand name
	$q = "SELECT app_key, app_name, custom_domain, custom_domain_protocol, custom_domain_enabled FROM apps WHERE id = '$i'";
	$r = mysqli_query($mysqli, $q);
	if ($r && mysqli_num_rows($r) > 0) 
	{
		while($row = mysqli_fetch_array($r)) 
		{
			$app_key = $row['app_key'];
			$brand_name = $row['app_name'];
			$custom_domain = $row['custom_domain'];
			$custom_domain_protocol = $row['custom_domain_protocol'];
			$custom_domain_enabled = $row['custom_domain_enabled'];
			if($custom_domain!='' && $custom_domain_enabled)
			{
				$parse = parse_url(APP_PATH);
				$domain = $parse['host'];
				$protocol = $parse['scheme'];
				$app_path = str_replace($domain, $custom_domain, APP_PATH);
				$app_path = str_replace($protocol, $custom_domain_protocol, $app_path);
			}
			else $app_path = APP_PATH;
		}
	}
	
	//Exit if app_key is incorrect
	if($app_key != $a) exit;
	
	//spit out RSS
	header("Content-Type: application/xml; charset=UTF-8"); 
	echo GetFeed(); 
	
	function GetFeed()
	{
		return getDetails() . getItems();
	}
	
	function getDetails()
	{
		global $brand_name;
		global $a;
		global $i;
		global $app_path;
		
		$details = '<?xml version="1.0" encoding="utf-8"?>
					<rss xmlns:atom="http://www.w3.org/2005/Atom" version="2.0">
					<channel>
					<title>'.$brand_name.' - '._('Campaigns RSS').'</title>
					<description>RSS feed archive of the last 100 campaigns sent by '.$brand_name.'.</description>
					<language>en</language>
				    <copyright></copyright>
				    <webMaster></webMaster>
				    <atom:link href="'.$app_path.'" rel="self" type="application/rss+xml"/>
					';
		return $details;
	}
	
	function getItems()
	{
		global $i;
		global $mysqli;
		global $app_path;
		
		$items = '';
		
		$q = 'SELECT campaigns.id, campaigns.sent, campaigns.title, campaigns.html_text, campaigns.from_name, campaigns.from_email, login.timezone FROM campaigns, login WHERE campaigns.userID = login.id AND campaigns.app = '.$i.' AND campaigns.sent != "" ORDER BY campaigns.sent DESC LIMIT 100';
		$r = mysqli_query($mysqli, $q);
		if (mysqli_num_rows($r) > 0)
		{
		    while($row = mysqli_fetch_array($r))
		    {
				$campaign_id = $row['id'];
				$date = date('D, M d, Y, h:iA', $row['sent']);
				$from_name = $row['from_name'];
				$from_email = $row['from_email'];
				$title = htmlspecialchars(stripslashes($row['title']), ENT_QUOTES, "UTF-8");
				$h2t = html_entity_decode(
	            trim(strip_tags(preg_replace('/<(head|title|style|script)[^>]*>.*?<\/\\1>/si', '', $row['html_text']))),
		            ENT_QUOTES,
		            'UTF-8'
		        );
				$html_text = substr(preg_replace("/\s+/", ' ', $h2t), 0, 500).'... <a href="'.$app_path.'/w/'.encrypt_val($campaign_id).'">Read more</a>';
				
				//tags for subject
				preg_match_all('/\[([a-zA-Z0-9!#%^&*()+=$@._\-\:|\/?<>~`"\'\s]+),\s*fallback=/i', $title, $matches_var, PREG_PATTERN_ORDER);
				preg_match_all('/,\s*fallback=([a-zA-Z0-9!,#%^&*()+=$@._\-\:|\/?<>~`"\'\s]*)\]/i', $title, $matches_val, PREG_PATTERN_ORDER);
				preg_match_all('/(\[[a-zA-Z0-9!#%^&*()+=$@._\-\:|\/?<>~`"\'\s]+,\s*fallback=[a-zA-Z0-9!,#%^&*()+=$@._\-\:|\/?<>~`"\'\s]*\])/i', $title, $matches_all, PREG_PATTERN_ORDER);
				preg_match_all('/\[([^\]]+),\s*fallback=/i', $title, $matches_var, PREG_PATTERN_ORDER);
				preg_match_all('/,\s*fallback=([^\]]*)\]/i', $title, $matches_val, PREG_PATTERN_ORDER);
				preg_match_all('/(\[[^\]]+,\s*fallback=[^\]]*\])/i', $title, $matches_all, PREG_PATTERN_ORDER);
				$matches_var = $matches_var[1];
				$matches_val = $matches_val[1];
				$matches_all = $matches_all[1];
				for($i=0;$i<count($matches_var);$i++)
				{		
					$field = $matches_var[$i];
					$fallback = $matches_val[$i];
					$tag = $matches_all[$i];
					//for each match, replace tag with fallback
					$title = str_replace($tag, $fallback, $title);
				}
				
				//tags for HTML
				preg_match_all('/\[([a-zA-Z0-9!#%^&*()+=$@._\-\:|\/?<>~`"\'\s]+),\s*fallback=/i', $html_text, $matches_var, PREG_PATTERN_ORDER);
				preg_match_all('/,\s*fallback=([a-zA-Z0-9!,#%^&*()+=$@._\-\:|\/?<>~`"\'\s]*)\]/i', $html_text, $matches_val, PREG_PATTERN_ORDER);
				preg_match_all('/(\[[a-zA-Z0-9!#%^&*()+=$@._\-\:|\/?<>~`"\'\s]+,\s*fallback=[a-zA-Z0-9!,#%^&*()+=$@._\-\:|\/?<>~`"\'\s]*\])/i', $html_text, $matches_all, PREG_PATTERN_ORDER);
				preg_match_all('/\[([^\]]+),\s*fallback=/i', $html_text, $matches_var, PREG_PATTERN_ORDER);
				preg_match_all('/,\s*fallback=([^\]]*)\]/i', $html_text, $matches_val, PREG_PATTERN_ORDER);
				preg_match_all('/(\[[^\]]+,\s*fallback=[^\]]*\])/i', $html_text, $matches_all, PREG_PATTERN_ORDER);
				$matches_var = $matches_var[1];
				$matches_val = $matches_val[1];
				$matches_all = $matches_all[1];
				for($i=0;$i<count($matches_var);$i++)
				{   
					$field = $matches_var[$i];
					$fallback = $matches_val[$i];
					$tag = $matches_all[$i];
					//for each match, replace tag with fallback
					$html_text = str_replace($tag, $fallback, $html_text);
				}
				
				//Name tag
				$title = str_replace('[Name]', $from_name, $title);
				$html_text = str_replace('[Name]', $from_name, $html_text);
				
				//Email tag
				$title = str_replace('[Email]', $from_email, $title);
				$html_text = str_replace('[Email]', $from_email, $html_text);
				
				//convert date
				$today = $row['sent'];
				$currentdaynumber = date('d', $today);
				$currentday = date('l', $today);
				$currentmonthnumber = date('m', $today);
				$currentmonth = date('F', $today);
				$currentyear = date('Y', $today);
				$unconverted_date = array('[currentdaynumber]', '[currentday]', '[currentmonthnumber]', '[currentmonth]', '[currentyear]');
				$converted_date = array($currentdaynumber, $currentday, $currentmonthnumber, $currentmonth, $currentyear);
				$title = str_replace($unconverted_date, $converted_date, $title);
								
				$items .= '<item>
					<title>'.$title.'</title>
					<link>'.$app_path.'/w/'.encrypt_val($campaign_id).'</link>
					<description><![CDATA['.$html_text.']]></description>
					<pubDate>'.$date.'</pubDate>
					</item>
				';
		    }  
		    $items .= '</channel></rss>';
			return $items;
		}
		else
		{
			$items .= '<item><title>No campaigns yet.</title></item>';
			$items .= '</channel></rss>';
			return $items;
		}
	}
?>