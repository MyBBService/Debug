<?php
if(!defined("IN_MYBB"))
{
    die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}

$output = array();
$name_array = array();
$plugins->add_hook("pre_output_page", "debug_output");

function debug_info()
{
    return array(
        "name"            => "Debug",
        "description"    => "Einfache Debug Funktionen f&uuml;r MyBBService.de",
        "website"        => "http://mybbservice.de/",
        "author"        => "MyBBService",
        "authorsite"    => "http://mybbservice.de/",
        "version"        => "&infin;",
        "guid"             => "",
        "compatibility" => "*"
    );
}

function debug_install()
{
	global $db;

	$templatearray = array(
        "title" => "debug",
        "template" => "<table border=\"0\" cellspacing=\"{\$theme[\'borderwidth\']}\" cellpadding=\"{\$theme[\'tablespace\']}\" class=\"tborder\" style=\"clear: both;\">
	<tr>
		<td class=\"thead\"><strong>Debug Output</strong></td>
	</tr>
	{\$content}
</table>",
        "sid" => -2
	);
	$db->insert_query("templates", $templatearray);
}

function debug_is_installed() {
	global $db;
	$query = $db->simple_select("templates", "title", "title='debug'");
	if($db->num_rows($query) > 0)
		return true;
	return false;
}

function debug_activate()
{
	require MYBB_ROOT."inc/adminfunctions_templates.php";
	find_replace_templatesets("header", "#".preg_quote('<div id="container">')."#i", '<debug_output>'."\n".'<div id="container">');
}

function debug_deactivate()
{
	require MYBB_ROOT."inc/adminfunctions_templates.php";
	find_replace_templatesets("header", "#".preg_quote('<debug_output>'."\n")."#i", '');
}

function debug_uninstall()
{
	global $db;

	//Delete templates
	$templatearray = array(
		"debug"
    );
    $deltemplates = implode("','", $templatearray);
	$db->delete_query("templates", "title in ('{$deltemplates}')");
}

function debug($var, $name)
{
	global $output, $name_array;
	
	if(!isset($name_array))
	    $name_array = array();
	
	if(substr($name, 0, 1) != "$")
	    $name = "$".$name;
	    
	if(array_key_exists($name, $name_array)) {
	    ++$name_array[$name];
	    $name = $name." (".$name_array[$name].")";
	} else
		$name_array[$name] = 1;
	
	$output[$name] = $var;
}

function debug_output($page)
{
	global $mybb, $output, $templates, $theme;

	$content = "";
	
	foreach($output as $name => $var) {
		$content .= "<tr>\n
	<td class=\"tcat\">\n
		<div class=\"expcolimage\"><img src=\"{$theme['imgdir']}/collapse_collapsed.gif\" id=\"{$name}_img\" class=\"expander\" alt=\"[+]\" title=\"[+]\" /></div>\n
		<div><span class=\"smalltext\"><strong>{$name}</strong></span></div>\n
	</td>\n
</tr>\n";
		$content .= "<tbody style=\"display: none;\" id=\"{$name}_e\">\n";
		$content .= "<tr class=\"trow1\" style=\"text-align: left;\">\n<td><pre>\n";
		$content .= print_r($var, true);
		$content .= "\n</pre></td>\n</tr>\n</tbody>\n";
	}
	
	if($content != "")
		eval("\$debug = \"".$templates->get("debug")."\";");
	else
		$debug = "";
	
	$page = str_replace("<debug_output>", $debug, $page);
	
	return $page;
}
?>