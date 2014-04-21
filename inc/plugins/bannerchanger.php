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
		"version"		=> "1.1.1",
		"guid" 			=> "",
		"compatibility" => "*",
		"dlcid"			=> "34"
	);
	
	return $info;
}

function bannerchanger_install()
{
	global $db, $lang;
	$lang->load("bannerchanger");

	//Einstellungs Gruppe
	$settings_group = array(
        "title"          => $db->escape_string($lang->setting_group_bannerchanger),
        "name"           => "bannerchanger",
        "description"    => $db->escape_string($lang->setting_group_bannerchanger_desc),
        "disporder"      => "40",
        "isdefault"      => "0",
    );
    $gid = $db->insert_query("settinggroups", $settings_group);


	//Einstellungen
	$setting = array(
        "name"           => "bannerchanger_birthday",
        "title"          => $db->escape_string($lang->setting_bannerchanger_birthday),
        "description"    => $db->escape_string($lang->setting_bannerchanger_birthday_desc),
        "optionscode"    => "yesno",
        "value"          => '1',
        "disporder"      => '1',
        "gid"            => (int)$gid,
    );
	$db->insert_query("settings", $setting);

	$setting = array(
        "name"           => "bannerchanger_newyear",
        "title"          => $db->escape_string($lang->setting_bannerchanger_newyear),
        "description"    => $db->escape_string($lang->setting_bannerchanger_newyear_desc),
        "optionscode"    => "yesno",
        "value"          => '1',
        "disporder"      => '2',
        "gid"            => (int)$gid,
    );
	$db->insert_query("settings", $setting);
	
	$setting = array(
        "name"           => "bannerchanger_easter",
        "title"          => $db->escape_string($lang->setting_bannerchanger_easter),
        "description"    => $db->escape_string($lang->setting_bannerchanger_easter_desc),
        "optionscode"    => "yesno",
        "value"          => '1',
        "disporder"      => '3',
        "gid"            => (int)$gid,
    );
	$db->insert_query("settings", $setting);

	$setting = array(
        "name"           => "bannerchanger_xmas",
        "title"          => $db->escape_string($lang->setting_bannerchanger_xmas),
        "description"    => $db->escape_string($lang->setting_bannerchanger_xmas_desc),
        "optionscode"    => "yesno",
        "value"          => '1',
        "disporder"      => '4',
        "gid"            => (int)$gid,
    );
	$db->insert_query("settings", $setting);

	//Ab Version 1.1
	$setting = array(
        "name"           => "bannerchanger_night",
        "title"          => $db->escape_string($lang->setting_bannerchanger_night),
        "description"    => $db->escape_string($lang->setting_bannerchanger_night_desc),
        "optionscode"    => "yesno",
        "value"          => '1',
        "disporder"      => '5',
        "gid"            => (int)$gid,
    );
	$db->insert_query("settings", $setting);

	$setting = array(
        "name"           => "bannerchanger_sundown",
        "title"          => $db->escape_string($lang->setting_bannerchanger_sundown),
        "description"    => $db->escape_string($lang->setting_bannerchanger_sundown_desc),
        "optionscode"    => "text",
        "value"          => '19',
        "disporder"      => '6',
        "gid"            => (int)$gid,
    );
	$db->insert_query("settings", $setting);

		$setting = array(
        "name"           => "bannerchanger_sunup",
        "title"          => $db->escape_string($lang->setting_bannerchanger_sunup),
        "description"    => $db->escape_string($lang->setting_bannerchanger_sunup_desc),
        "optionscode"    => "text",
        "value"          => '7',
        "disporder"      => '7',
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

function bannerchanger_activate()
{
	require MYBB_ROOT."inc/adminfunctions_templates.php";
	find_replace_templatesets("header", "#".preg_quote('{$theme[\'logo\']}')."#i", '{\$logo}');
}

function bannerchanger_deactivate()
{
	require MYBB_ROOT."inc/adminfunctions_templates.php";
	find_replace_templatesets("header", "#".preg_quote('{$logo}')."#i", '{\$theme[\'logo\']}');
}

function bannerchanger_versions()
{
	return array(
	 "1.0",
	 "1.1",
	 "1.1.1"
	);
}

function bannerchanger_update_1_1()
{
	global $db;

	$query = $db->simple_select("settinggroups", "gid", "name='bannerchanger'");
    $gid = $db->fetch_field($query, "gid");

	$setting = array(
        "name"           => "bannerchanger_night",
        "title"          => $db->escape_string($lang->setting_bannerchanger_night),
        "description"    => $db->escape_string($lang->setting_bannerchanger_night_desc),
        "optionscode"    => "yesno",
        "value"          => '1',
        "disporder"      => '5',
        "gid"            => (int)$gid,
    );
	$db->insert_query("settings", $setting);

	$setting = array(
        "name"           => "bannerchanger_sundown",
        "title"          => $db->escape_string($lang->setting_bannerchanger_sundown),
        "description"    => $db->escape_string($lang->setting_bannerchanger_sundown_desc),
        "optionscode"    => "text",
        "value"          => '19',
        "disporder"      => '6',
        "gid"            => (int)$gid,
    );
	$db->insert_query("settings", $setting);

		$setting = array(
        "name"           => "bannerchanger_sunup",
        "title"          => $db->escape_string($lang->setting_bannerchanger_sunup),
        "description"    => $db->escape_string($lang->setting_bannerchanger_sunup_desc),
        "optionscode"    => "text",
        "value"          => '7',
        "disporder"      => '7',
        "gid"            => (int)$gid,
    );
	$db->insert_query("settings", $setting);

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
	
	//Theme ist mal wieder nicht geladen...
	$dir = substr($logo, 0, strrpos($logo, "."));
	$ext = substr($logo, strrpos($logo, "."));

	
	
	if($mybb->settings['bannerchanger_birthday'] && check_image($dir."_bday".$ext)) {
		//Geburtstag überprüfen
		if(!empty($mybb->user['birthday'])) {
			list($bday, $bmonth) = explode("-", $mybb->user['birthday']);
			if($bday == $day && $bmonth == $month) {
				$zusatz = "bday";
			}
		}
	}

	if($mybb->settings['bannerchanger_easter'] && $zusatz == "") {
		//Osterdatum laden
		$ostern = easter_date();
		
		if($month == date("m", $ostern) && $day == date("d", $ostern)) {
			$zusatz = "easter";
		}
	}
	
	if($mybb->settings['bannerchanger_xmas'] && $month == 12 && $day <= 26 && $zusatz == "") {
		//Wir haben Weihnachten!
		$zusatz = "xmas";
	}
	
	if($mybb->settings['bannerchanger_newyear'] && (($month == 12 && $day == 31) || ($month == 1 && $day == 1)) && $zusatz == "") {
		//Wir haben Silvester!
		$zusatz = "newyear";
	}

	//Nachtüberprufung = Drecksarbeit
	$hour = my_date("G");
	
	//Möglichen Unterstrich anhängen
	if($zusatz != "")
	    $zusatz .= "_";

	//Aktiviert und nachts und bild vorhanden?
	if($mybb->settings['bannerchanger_night'] && ($hour >= $mybb->settings['bannerchanger_sundown'] || $hour < $mybb->settings['bannerchanger_sunup']) && check_image($dir."_".$zusatz."night".$ext)) {
   	    //Nachtzusatz
		$zusatz .= "night";
	}

	//Mögliche anfangs/end unterstriche entfernen
	$zusatz = trim($zusatz, "_");
	
	if($zusatz == "") 
	    //Schade kein Bannerwechsel
		return;
	
	$nlogo = $dir."_".$zusatz.$ext;
	
	if(check_image($nlogo))
	    $logo = $nlogo;
}

function check_image($img)
{
	$file_headers = @get_headers($img);
	if($file_headers[0] == 'HTTP/1.1 404 Not Found') {
		return false;
	}
	return true;
}
?>