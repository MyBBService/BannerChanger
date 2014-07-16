<?php 
// Disallow direct access to this file for security reasons
if(!defined("IN_MYBB"))
{
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}

$plugins->add_hook("global_intermediate", "load_header");
$plugins->add_hook("admin_config_settings_begin", "load_lang");

function bannerchanger_info()
{
	$info = array(
		"name"			=> "Dynamisches Banner",
		"description"	=> "Ein einfaches Plugin, welches dynamisch das Header Bild anpasst",
		"website"		=> "http://mybbservice.de",
		"author"		=> "MyBBService",
		"authorsite"	=> "http://mybbservice.de",
		"version"		=> "1.2",
		"guid" 			=> "",
		"compatibility" => "17*,18*",
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
		"title"			=> $db->escape_string($lang->setting_group_bannerchanger),
		"name"			=> "bannerchanger",
		"description"	=> $db->escape_string($lang->setting_group_bannerchanger_desc),
		"disporder"		=> "40",
		"isdefault"		=> "0",
	);
	$gid = $db->insert_query("settinggroups", $settings_group);


	//Einstellungen
	$setting = array(
		"name"			=> "bannerchanger_birthday",
		"title"			=> $db->escape_string($lang->setting_bannerchanger_birthday),
		"description"	=> $db->escape_string($lang->setting_bannerchanger_birthday_desc),
		"optionscode"	=> "yesno",
		"value"			=> '1',
		"disporder"		=> '1',
		"gid"			=> (int)$gid,
	);
	$db->insert_query("settings", $setting);

	$setting = array(
		"name"			=> "bannerchanger_newyear",
		"title"			=> $db->escape_string($lang->setting_bannerchanger_newyear),
		"description"	=> $db->escape_string($lang->setting_bannerchanger_newyear_desc),
		"optionscode"	=> "yesno",
		"value"			=> '1',
		"disporder"		=> '2',
		"gid"			=> (int)$gid,
	);
	$db->insert_query("settings", $setting);
	
	$setting = array(
		"name"			=> "bannerchanger_easter",
		"title"			=> $db->escape_string($lang->setting_bannerchanger_easter),
		"description"	=> $db->escape_string($lang->setting_bannerchanger_easter_desc),
		"optionscode"	=> "yesno",
		"value" 		=> '1',
		"disporder"		=> '3',
		"gid"			=> (int)$gid,
	);
	$db->insert_query("settings", $setting);

	$setting = array(
		"name"			=> "bannerchanger_xmas",
		"title"			=> $db->escape_string($lang->setting_bannerchanger_xmas),
		"description"	=> $db->escape_string($lang->setting_bannerchanger_xmas_desc),
		"optionscode"	=> "yesno",
		"value"			=> '1',
		"disporder"		=> '4',
		"gid"			=> (int)$gid,
	);
	$db->insert_query("settings", $setting);

	//Ab Version 1.1
	$setting = array(
		"name"			=> "bannerchanger_night",
		"title"			=> $db->escape_string($lang->setting_bannerchanger_night),
		"description"	=> $db->escape_string($lang->setting_bannerchanger_night_desc),
		"optionscode"	=> "yesno",
		"value"			=> '1',
		"disporder"		=> '5',
		"gid"			=> (int)$gid,
	);
	$db->insert_query("settings", $setting);

	$setting = array(
		"name"			=> "bannerchanger_sundown",
		"title"			=> $db->escape_string($lang->setting_bannerchanger_sundown),
		"description"	=> $db->escape_string($lang->setting_bannerchanger_sundown_desc),
		"optionscode"	=> "text",
		"value"			=> '19',
		"disporder"		=> '6',
		"gid"			=> (int)$gid,
	);
	$db->insert_query("settings", $setting);

	$setting = array(
		"name"			=> "bannerchanger_sunup",
		"title"			=> $db->escape_string($lang->setting_bannerchanger_sunup),
		"description"	=> $db->escape_string($lang->setting_bannerchanger_sunup_desc),
		"optionscode"	=> "text",
		"value"			=> '7',
		"disporder"		=> '7',
		"gid"			=> (int)$gid,
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
	 "1.1.1",
	 "1.1.2",
	 "1.2"
	);
}

function bannerchanger_update_1_1()
{
	global $db;

	$query = $db->simple_select("settinggroups", "gid", "name='bannerchanger'");
	$gid = $db->fetch_field($query, "gid");

	$setting = array(
		"name"			=> "bannerchanger_night",
		"title"			=> $db->escape_string($lang->setting_bannerchanger_night),
		"description"	=> $db->escape_string($lang->setting_bannerchanger_night_desc),
		"optionscode"	=> "yesno",
		"value"			=> '1',
		"disporder"		=> '5',
		"gid"			=> (int)$gid,
	);
	$db->insert_query("settings", $setting);

	$setting = array(
		"name"			=> "bannerchanger_sundown",
		"title"			=> $db->escape_string($lang->setting_bannerchanger_sundown),
		"description"	=> $db->escape_string($lang->setting_bannerchanger_sundown_desc),
		"optionscode"	=> "text",
		"value"			=> '19',
		"disporder"		=> '6',
		"gid"			=> (int)$gid,
	);
	$db->insert_query("settings", $setting);

	$setting = array(
		"name"			=> "bannerchanger_sunup",
		"title"			=> $db->escape_string($lang->setting_bannerchanger_sunup),
		"description"	=> $db->escape_string($lang->setting_bannerchanger_sunup_desc),
		"optionscode"	=> "text",
		"value"			=> '7',
		"disporder"		=> '7',
		"gid"			=> (int)$gid,
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
	global $mybb, $logo, $db, $theme;

	$day = my_date("d");
	$month = my_date("m");

	$zusatz = "";

	$logo = $theme['logo'];

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

		if($month == my_date("m", $ostern) && $day == my_date("d", $ostern)) {
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