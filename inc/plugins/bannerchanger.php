<?php 
// Disallow direct access to this file for security reasons
if(!defined("IN_MYBB"))
{
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}

$plugins->add_hook("global_start", "load_header");
$plugins->add_hook("admin_config_settings_begin", "load_lang");

function bannerchanger_info()
{
	$info = array(
		"name"			=> "Dynamisches Banner",
		"description"	=> "Ein einfaches Plugin, welches dynamisch das Header Bild anpasst",
		"website"		=> "http://mybbservice.de",
		"author"		=> "MyBBService",
		"authorsite"	=> "http://mybbservice.de",
		"version"		=> "1.0",
		"guid" 			=> "",
		"compatibility" => "*"
	);
	
	return $info;
}

function bannerchanger_install()
{
	global $db, $lang;
	$lang->load("bannerchanger");

	//Einstellungs Gruppe
	$settings_group = array(
        "title"          => $lang->setting_group_bannerchanger,
        "name"           => "bannerchanger",
        "description"    => $lang->setting_group_bannerchanger_desc,
        "disporder"      => "40",
        "isdefault"      => "0",
    );
    $gid = $db->insert_query("settinggroups", $settings_group);


	//Einstellungen
	$setting = array(
        "name"           => "bannerchanger_birthday",
        "title"          => $lang->setting_bannerchanger_birthday,
        "description"    => $lang->setting_bannerchanger_birthday_desc,
        "optionscode"    => "yesno",
        "value"          => '1',
        "disporder"      => '1',
        "gid"            => (int)$gid,
    );
	$db->insert_query("settings", $setting);

	$setting = array(
        "name"           => "bannerchanger_newyear",
        "title"          => $lang->setting_bannerchanger_newyear,
        "description"    => $lang->setting_bannerchanger_newyear_desc,
        "optionscode"    => "yesno",
        "value"          => '1',
        "disporder"      => '2',
        "gid"            => (int)$gid,
    );
	$db->insert_query("settings", $setting);
	
	$setting = array(
        "name"           => "bannerchanger_easter",
        "title"          => $lang->setting_bannerchanger_easter,
        "description"    => $lang->setting_bannerchanger_easter_desc,
        "optionscode"    => "yesno",
        "value"          => '1',
        "disporder"      => '3',
        "gid"            => (int)$gid,
    );
	$db->insert_query("settings", $setting);

	$setting = array(
        "name"           => "bannerchanger_xmas",
        "title"          => $lang->setting_bannerchanger_xmas,
        "description"    => $lang->setting_bannerchanger_xmas_desc,
        "optionscode"    => "yesno",
        "value"          => '1',
        "disporder"      => '4',
        "gid"            => (int)$gid,
    );
	$db->insert_query("settings", $setting);
	rebuild_settings();
}

function bannerchanger_is_installed()
{
	global $db;

	$query = $db->simple_select("settinggroups", "gid", "name='bannerchanger'");
	return ($db->num_rows($query) > 0);	
}

function bannerchanger_uninstall()
{
	global $db;

	$query = $db->simple_select("settinggroups", "gid", "name='bannerchanger'");
    $gid = $db->fetch_field($query, "gid");
	$db->delete_query("settinggroups", "gid='{$gid}'");
	$db->delete_query("settings", "gid='{$gid}'");
	rebuild_settings();
}

function load_lang()
{
	global $lang;
	$lang->load("bannerchanger");
}

function load_header()
{
	global $mybb, $logo, $db;
	
	$day = date("d");
	$month = date("m");
	
	$zusatz = "";
	
	if($mybb->settings['bannerchanger_eastern']) {
		//Osterdatum laden
		$ostern = easter_date();
		
		if($month == date("m", $ostern) && $day == date("d", $ostern)) {
			$zusatz = "easter";
		}
	}
	
	if($mybb->settings['bannerchanger_xmas'] && $month == 12 && $day <= 26) {
		//Wir haben Weihnachten!
		$zusatz = "xmas";
	}
	
	if($mybb->settings['bannerchanger_newyear'] && (($month == 12 && $day == 31) || ($month == 1 && $day == 1))) {
		//Wir haben Silvester!
		$zusatz = "newyear";
	}
	
	if($mybb->settings['bannerchanger_birthday']) {
		//Geburtstag überprüfen
		if(!empty($mybb->user['birthday'])) {
			list($bday, $bmonth) = explode("-", $mybb->user['birthday']);
			if($bday == $day && $bmonth == $month) {
				$zusatz = "bday";
			}
		}
	}

	
	// Select the board theme to use.
	$loadstyle = '';
	$load_from_forum = 0;
	$style = array();
	
	// This user has a custom theme set in their profile
	if(isset($mybb->user['style']) && intval($mybb->user['style']) != 0)
	{
	        $loadstyle = "tid='".$mybb->user['style']."'";
	}
	
	$valid = array(
	        "showthread.php", 
	        "forumdisplay.php",
	        "newthread.php",
	        "newreply.php",
	        "ratethread.php",
	        "editpost.php",
	        "polls.php",
	        "sendthread.php",
	        "printthread.php",
	        "moderation.php"
	);
	
	if(in_array($current_page, $valid))
	{
	        cache_forums();
	
	        // If we're accessing a post, fetch the forum theme for it and if we're overriding it
	        if(!empty($mybb->input['pid']))
	        {
	                $query = $db->simple_select("posts", "fid", "pid = '".intval($mybb->input['pid'])."'", array("limit" => 1));
	                $fid = $db->fetch_field($query, "fid");
	
	                if($fid)
	                {
	                        $style = $forum_cache[$fid];
	                        $load_from_forum = 1;
	                }
	        }
	        // We have a thread id and a forum id, we can easily fetch the theme for this forum
	        else if(!empty($mybb->input['tid']))
	        {
	                $query = $db->simple_select("threads", "fid", "tid = '".intval($mybb->input['tid'])."'", array("limit" => 1));
	                $fid = $db->fetch_field($query, "fid");
	
	                if($fid)
	                {
	                        $style = $forum_cache[$fid];
	                        $load_from_forum = 1;
	                }
	        }
	
	        // We have a forum id - simply load the theme from it
	        else if($mybb->input['fid'])
	        {
	                $style = $forum_cache[intval($mybb->input['fid'])];
	                $load_from_forum = 1;
	        }
	}
	unset($valid);
	
	// From all of the above, a theme was found
	if(isset($style['style']) && $style['style'] > 0)
	{
	        // This theme is forced upon the user, overriding their selection
	        if($style['overridestyle'] == 1 || !isset($mybb->user['style']))
	        {
	                $loadstyle = "tid='".intval($style['style'])."'";
	        }
	}
	
	// After all of that no theme? Load the board default
	if(empty($loadstyle))
	{
	        $loadstyle = "def='1'";
	}
	
	// Fetch the theme to load from the database
	$query = $db->simple_select("themes", "name, tid, properties, stylesheets", $loadstyle, array('limit' => 1));
	$theme = $db->fetch_array($query);
	
	// No theme was found - we attempt to load the master or any other theme
	if(!$theme['tid'])
	{
	        // Missing theme was from a forum, run a query to set any forums using the theme to the default
	        if($load_from_forum == 1)
	        {
	                $db->update_query("forums", array("style" => 0), "style='{$style['style']}'");
	        }
	        // Missing theme was from a user, run a query to set any users using the theme to the default
	        else if($load_from_user == 1)
	        {
	                $db->update_query("users", array("style" => 0), "style='{$style['style']}'");
	        }
	        // Attempt to load the master or any other theme if the master is not available
	        $query = $db->simple_select("themes", "name, tid, properties, stylesheets", "", array("order_by" => "tid", "limit" => 1));
	        $theme = $db->fetch_array($query);
	}
	$theme = @array_merge($theme, unserialize($theme['properties']));

	// Theme logo - is it a relative URL to the forum root? Append bburl
	if(!preg_match("#^(\.\.?(/|$)|([a-z0-9]+)://)#i", $theme['logo']) && substr($theme['logo'], 0, 1) != "/")
	{
	        $theme['logo'] = $mybb->settings['bburl']."/".$theme['logo'];
	}
	
	$logo = $theme['logo'];
	
	if($zusatz == "") 
	    //Schade kein Bannerwechsel
		return;

	//Theme ist mal wieder nicht geladen...
	$dir = substr($logo, 0, strrpos($logo, "."));
	$ext = substr($logo, strrpos($logo, "."));
	
	$nlogo = $dir."_".$zusatz.$ext;
	
	if(file_exists($nlogo))
	    $logo = $nlogo;
}
?>