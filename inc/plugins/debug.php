<?php
$internal_debug = false;

if(!defined("IN_MYBB"))
{
    die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}

$output = array();
$name_array = array();
$plugins->add_hook("pre_output_page", "debug_output");
$plugins->add_hook("admin_page_output_footer", "debug_admin_output");

if($internal_debug) {
	$plugins->add_hook("global_start", "debug_debug");
	$plugins->add_hook("admin_load", "debug_debug");
}

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

/*
function debug_install()
{
	global $db;
}

function debug_is_installed() {
	global $db;
}

function debug_uninstall()
{
	global $db;
}
*/

function debug_activate()
{
	require MYBB_ROOT."inc/adminfunctions_templates.php";
	find_replace_templatesets("header", "#".preg_quote('<navigation>')."#i", '<debug_output>'."\n".'<navigation>');
}

function debug_deactivate()
{
	require MYBB_ROOT."inc/adminfunctions_templates.php";
	find_replace_templatesets("header", "#".preg_quote("\n".'<debug_output>')."#i", '');
}


function debug($var, $name = "[Undefined]")
{
	global $output, $name_array;
	
	if(!isset($name_array))
	    $name_array = array();
	
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

	$debug = "";
	if(!empty($output)) {
		$debug = "<table border=\"0\" cellspacing=\"{$theme['borderwidth']}\" cellpadding=\"{$theme['tablespace']}\" class=\"tborder\" style=\"clear: both;\">
		<tr>
			<td class=\"thead\"><strong>Debug Output</strong></td>
		</tr>";
		
		foreach($output as $name => $var) {
			$debug .= "<tr>\n
		<td class=\"tcat\">\n
			<div class=\"expcolimage\"><img src=\"{$theme['imgdir']}/collapse_collapsed.gif\" id=\"{$name}_img\" class=\"expander\" alt=\"[+]\" title=\"[+]\" /></div>\n
			<div><span class=\"smalltext\"><strong>{$name}</strong></span></div>\n
		</td>\n
		</tr>\n";
			$debug .= "<tbody style=\"display: none;\" id=\"{$name}_e\">\n";
			$debug .= "<tr class=\"trow1\" style=\"text-align: left;\">\n<td><pre>\n";
			$debug .= print_r($var, true);
			$debug .= "\n</pre></td>\n</tr>\n</tbody>\n";
		}
		
		$debug .= "</table>";
	}
		
	$page = str_replace("<debug_output>", $debug, $page);
	
	return $page;
}

function debug_admin_output($page)
{
	global $mybb, $output, $templates, $theme;

	$content = "";
	if(!empty($output)) {
		echo "            </div>\n";
		echo "        </div>\n";
		echo "    <br style=\"clear: both;\" />";
		echo "    <br style=\"clear: both;\" />";
		echo "    </div>\n";
		
		echo "    <div style=\"background-color: #000000;\">\n";
		echo "        <div style=\"background-color: #000080; color: #FFFFFF; padding: 5px;\">Debug Output</div>\n";

		foreach($output as $name => $var) {
			echo "<div style=\"background-color: #000000; color: #FFFFFF; padding: 5px; height: 15px;\">";
			echo "<div style=\"float: right;\"><img src=\"../images/collapse_collapsed.gif\" id=\"{$name}_img\" class=\"expander\" alt=\"[+]\" title=\"[+]\" /></div>\n";
			echo "<div style=\"float: left;\"><span class=\"smalltext\"><strong>{$name}</strong></span></div>\n";
			echo "</div>";
			echo "<div style=\"display: none; padding: 1px; border: 1px solid black;\" id=\"{$name}_e\">";
			echo "<div style=\"text-align: left; background-color: #FFFFFF;\">\n <br /><pre>\n";
			print_r($var);
			echo "\n</pre></div></div></div>\n";
		}
	
		echo "<div>\n<div>\n<div>\n";
	}
}

function debug_debug()
{
	global $mybb;
	debug($mybb, "MyBB Debug");
}
?>