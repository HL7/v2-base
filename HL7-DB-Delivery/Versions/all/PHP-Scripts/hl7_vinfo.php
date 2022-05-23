<?php

//Collect information about the different versions
//and prepare top two navigation rows.
//The result is placed in the global variable $gVersButtons.
//This function is called by including this file before hl7_buttons.php.

function SetVersionInfo()
{
global $linkID;
global $sButtonType;
global $sButtonParameter;

global $gNav1left; //top left
global $gNav1right; //top right
global $gNav2right; //top right

global $gPath2GeneratedFiles;
global $gPathDelimiter;

global $gHtmlPath;
global $gHtmlFile;


print "<!-- hl7_vinfo.php -->\n";
$ghl7_version="";

//declare arrays to store information
//(version_id is index)
$arrVersionID = array(); 
$arrHL7Version = array();  //printname
$arrBaseStandard = array();
$arrFileNamePrefix = array();
$arrHtmlPath = array();
$arrHtmlFile = array();

//which versions are available
$SQL = "SELECT * from HL7Versions where display=true order by sort";

$result = odbc_exec($linkID, $SQL);

$Fields = odbc_num_fields($result); 

//INIT:
//collect output
$gNav1left =  "<TABLE class=nav>\n" 
. "<TR>\n"
. "<td nowrap width=\"80\" class=\"tabobjecttabClickable\"
  OnClick = \"javascript:window.location.href = 'hl7_contents.php'\"
  onmouseover=\"this.className='tabobjecttabHighlight'\"
  onmouseout=\"this.className='tabobjecttabClickable'\">
  All Versions</td>\n"
. "<td>&nbsp;</td>\n"
. "</TR>\n</TABLE>\n\n";


$gNav1right = "<!-- 1. row navigation -->\n<TABLE class=nav>\n<TR>\n<td>&nbsp;</td>\n";
$gNav2right = "<!-- 2. row navigation -->\n<TABLE class=nav>\n<TR>\n<td>&nbsp;</td>\n";


$t1 = "<td class='nav1' width=\"10\">&nbsp; </td>\n";
//$t1 = "";
//$t2a = "<td nowrap width=\"50\" class=\"tabobjecttabClickable";
$t2a = "<td nowrap class=\"tabobjecttabClickable";
$t2b = "\"\n";
$t3a = "OnClick = \"javascript:window.location.href = 'hl7_";
//$t3b = ".php'\" \n";
$t4 = "\n onmouseover=\"this.className='tabobjecttabHighlight'\" \n";
$t5a = " onmouseout=\"this.className='tabobjecttabClickable"; 
$t5b = "'\" >\n";
//$t6 =  2.2;
//$t7 = "</td>\n" . "<td class='nav4' width=\"5\">&nbsp; </td>\n";
$t7 = "</td>\n";

if (isset($_GET["vVersion"]))
{
	$x = $_GET["vVersion"];
}
else
{
	$x = "";
}

//may provide wrong link in hl7_buttons.php?
$gHtmlPath = "";
$gHtmlFile = "";

//store all information
while( odbc_fetch_row( $result ))
{
	$arrVersionID[odbc_result( $result, "version_id" )] = odbc_result( $result, "version_id" );
	$arrHL7Version[odbc_result( $result, "version_id" )] = odbc_result( $result, "hl7_version" );
	$arrBaseStandard[odbc_result( $result, "version_id" )] = odbc_result( $result, "base_standard" );
	$arrPreviousVersion[odbc_result( $result, "version_id" )] = odbc_result( $result, "previous_version" );
	$arrFileNamePrefix[odbc_result( $result, "version_id" )] = odbc_result( $result, "filename_prefix" );
	$arrHtmlPath[odbc_result( $result, "version_id" )] = odbc_result( $result, "HtmlPath" );
	$arrHtmlFile[odbc_result( $result, "version_id" )] = odbc_result( $result, "HtmlFile" );
}
odbc_free_result($result);


$nBaseStandard = 0;
//find base standard
if ($x != "")
{
	$nBaseStandard = $arrBaseStandard[$x];
}
if ($nBaseStandard == 0)
{
	//reset to a valid entry
	$nBaseStandard = $x;
}
//$gVersButtons2 = "";

//process rows
foreach( $arrVersionID as $nVersionId)
{ 
	//what is the base standard for row1?
	if ($nVersionId == $nBaseStandard)
	{
		$selected1 = "Selected";
	}
	else
	{
		$selected1 = "";
	}

	//what is to be selected for row2?
	if ($nVersionId == $x)
	{
		$selected2 = "Selected";
	}
	else
	{
		$selected2 = "";
	}
	
	//nicht setzen, da Konflikt mit Versionsübersicht...
//	$ghl7_version = $arrHL7Version[$nVersionId];
	if ($nVersionId == $x)
	{
		$gHtmlPath = $arrFileNamePrefix[$nVersionId] . "/" . $arrHtmlPath[$nVersionId];
		$gHtmlFile = $arrHtmlFile[$nVersionId];
	}
	
	//calculate field width
	if ($arrBaseStandard[$nVersionId] == 0)
	{
		$t2b = "\" width=\"" . strlen($arrHL7Version[$nVersionId])*10 . "\"\n";
		$gNav1right = $gNav1right . $t2a . $selected1 . $t2b . $t3a . $sButtonType . ".php?vVersion=" . $nVersionId . $sButtonParameter . "'\"" . $t4 . $t5a . $selected1 . $t5b . $arrHL7Version[$nVersionId] . $t7;
	}

	if (($arrBaseStandard[$nVersionId] == $nBaseStandard) or ($arrVersionID[$nVersionId] == $nBaseStandard))
	{
		//does this entry have the appropriate base standard or is this base standard?
		$gNav2right = $gNav2right . $t2a . $selected2 . $t2b . $t3a . $sButtonType . ".php?vVersion=" . $nVersionId . $sButtonParameter . "'\"" . $t4 . $t5a . $selected2 . $t5b . $arrHL7Version[$nVersionId] . $t7;
	}
} 

//$gNav1right = $gNav1right . "<td id=corner_tr class='nav1' width=\"10\">&nbsp; </td>\n";
//$gNav1right = $gNav1right . "</TR>\n</TABLE>\n\n<!-- 2. row navigation -->\n<TABLE class='nav'>\n<TR class='nav2'>\n<td class='nav2'>&nbsp;</td>\n";
//$gNav1right = $gNav1right . $gNav2right . "<td class='nav2' width=\"10\">&nbsp; </td>\n";

$gNav1right = $gNav1right . "</TR>\n</TABLE>\n\n";
$gNav2right = $gNav2right . "</TR>\n</TABLE>\n\n";


//where are the generated HTML files
$SQL = "SELECT * from DBOptions where seq_no=18";
$result = odbc_exec($linkID, $SQL);
$gPath2GeneratedFiles = odbc_result( $result, "value" );
odbc_free_result($result);

//what is the delimiter for pathnames
$SQL = "SELECT * from DBOptions where seq_no=2";
$result = odbc_exec($linkID, $SQL);
$gPathDelimiter = odbc_result( $result, "value" );
odbc_free_result($result);
}

?>