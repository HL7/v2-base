<?php

// This script must be adjusted to only create strings which can be used 
// for print in the header!

//This script sets some global variables which contain a string for printing into HTML

function SetButtons()
{

global $nButtonParameter;
global $sButtonParameter;
global $gHtmlPath;
global $gHtmlFile;
global $gPath2GeneratedFiles;
global $gPathDelimiter;

//variables to be set:
global $gNav1left; //top left
global $gNav2left; 
global $gNav3left; 
global $gNav4left; //low left

global $gNav3right; // 3. row right
global $gNav4right; // 4. row right

//print "<!-- hl7_buttons.php -->\n";

//row with version selection
//print "<!-- 1. row navigation -->\n";
//print $gVersButtons ;

$gNav2left = "<br/>\n"; 
$gNav3left = "<br/>\n"; 


if (isset($_GET["vVersion"]))
{
	$vVersion = $_GET["vVersion"];
}
else
{
	$vVersion = "";
}



//empty row
//print "<!-- 3.row empty navigation -->\n";

//is set by tables
if ($nButtonParameter > 0)
{
	$gNav3right = "<TABLE class=nav>\n<TR>\n";

	//only provide this overview for tables
	$gNav3right = $gNav3right . "<td>&nbsp;</td>\n";
	if ($nButtonParameter == 2)
	{
		$gNav3right = $gNav3right . "<td nowrap width=\"55\" class=\"tabobjecttabClickable\"
		  OnClick = \"javascript:window.location.href='hl7_table_overview.php?". $sButtonParameter ."'\"
		  onmouseover=\"this.className='tabobjecttabHighlight'\"
		  onmouseout=\"this.className='tabobjecttabClickable'\" > 
		  Table Overview\n</td>\n";

	}

	if ($nButtonParameter == 1)
	{
		$gNav3right = $gNav3right . "<td nowrap width=\"55\" class=\"tabobjecttabClickable\"
		  OnClick = \"javascript:window.location.href='hl7_table_cmp.php?". $sButtonParameter ."'\"
		  onmouseover=\"this.className='tabobjecttabHighlight'\"
		  onmouseout=\"this.className='tabobjecttabClickable'\" > 
		  Compare Tables\n</td>\n";
	}

	$gNav3right = $gNav3right .  "</TR>\n";
	$gNav3right = $gNav3right .  "</TABLE>\n\n";
}
else
{
	$gNav3right = "<br/>\n";
}


//print "\n<!-- version-specific tables (row 4) -->\n";

//version specific selections
$gNav4left =  "<TABLE class=nav>\n";
$gNav4left = $gNav4left . "<TR >\n";

if ($vVersion <> "")
{
//	$gNav4left = $gNav4left . "<td class=nav4 width=\"10\">&nbsp; </td>\n";

	$gNav4left = $gNav4left . "<td nowrap width=\"70\" class=\"tabobjecttabClickable\"
	  OnClick = \"javascript:window.location.href='hl7_version.php?vVersion=". $vVersion ."'\"
	  onmouseover=\"this.className='tabobjecttabHighlight'\"
	  onmouseout=\"this.className='tabobjecttabClickable'\" > 
	  Contents\n</td>\n";

	  $gNav4left = $gNav4left . "<td nowrap width=\"60\" class=\"tabobjecttabClickable\"
	  OnClick = \"javascript:window.location.href='hl7_index.php?vVersion=". $vVersion ."'\"
	  onmouseover=\"this.className='tabobjecttabHighlight'\"
	  onmouseout=\"this.className='tabobjecttabClickable'\" > 
	  Index\n</td>\n";

	  $gNav4left = $gNav4left . "<td nowrap width=\"70\" class=\"tabobjecttabClickable\"
	  OnClick = \"javascript:window.location.href='" . $gPath2GeneratedFiles . $gPathDelimiter . $gHtmlPath. $gPathDelimiter . $gHtmlFile . "'\"
	  onmouseover=\"this.className='tabobjecttabHighlight'\"
	  onmouseout=\"this.className='tabobjecttabClickable'\" > 
	  Standard\n</td>\n";
}
else
{
	$gNav4left = $gNav4left . "<td>&nbsp;</td>\n";
}

//$gNav4left = $gNav4left . "<td class=nav4 width=\"*\">&nbsp; </td>\n";

$gNav4left = $gNav4left . "</TR>\n";
$gNav4left = $gNav4left . "</TABLE>\n\n";

// --------

$gNav4right =  "<TABLE class=nav>\n";
$gNav4right = $gNav4right . "<TR>\n";

if ($vVersion <> "")
{
	$gNav4right = $gNav4right . "<td nowrap width=\"55\" class=\"tabobjecttabClickable\"
	  OnClick = \"javascript:window.location.href='hl7_event.php?vVersion=". $vVersion ."'\"
	  onmouseover=\"this.className='tabobjecttabHighlight'\"
	  onmouseout=\"this.className='tabobjecttabClickable'\" > 
	  Events</td>\n";

	  $gNav4right = $gNav4right . "<td nowrap width=\"70\" class=\"tabobjecttabClickable\"
	  OnClick = \"javascript:window.location.href='hl7_segment.php?vVersion=". $vVersion ."'\"
	  onmouseover=\"this.className='tabobjecttabHighlight'\"
	  onmouseout=\"this.className='tabobjecttabClickable'\" > 
	  Segments\n</td>\n";

	  $gNav4right = $gNav4right . "<td width=\"90\" class=\"tabobjecttabClickable\"
	  OnClick = \"javascript:window.location.href='hl7_msgtype.php?vVersion=". $vVersion ."'\"
	  onmouseover=\"this.className='tabobjecttabHighlight'\"
	  onmouseout=\"this.className='tabobjecttabClickable'\" > 
	  Msg. Types\n</td>\n";

	  $gNav4right = $gNav4right . "<td width=\"95\" class=\"tabobjecttabClickable\"
	  OnClick = \"javascript:window.location.href='hl7_msgstruct.php?vVersion=". $vVersion ."'\"
	  onmouseover=\"this.className='tabobjecttabHighlight'\"
	  onmouseout=\"this.className='tabobjecttabClickable'\" > 
	  Msg. Structs\n</td>\n";

	  $gNav4right = $gNav4right . "<td width=\"110\" class=\"tabobjecttabClickable\"
	  OnClick = \"javascript:window.location.href='hl7_dataelem.php?vVersion=". $vVersion ."'\"
	  onmouseover=\"this.className='tabobjecttabHighlight'\"
	  onmouseout=\"this.className='tabobjecttabClickable'\" > 
	  Data Elements\n</td>";

	  $gNav4right = $gNav4right . "<td nowrap width=\"50\" class=\"tabobjecttabClickable\"
	  OnClick = \"javascript:window.location.href='hl7_table.php?vVersion=". $vVersion ."'\"
	  onmouseover=\"this.className='tabobjecttabHighlight'\"
	  onmouseout=\"this.className='tabobjecttabClickable'\" > 
	  Tables\n</td>\n";

	  $gNav4right = $gNav4right . "<td width=\"90\" class=\"tabobjecttabClickable\"
	  OnClick = \"javascript:window.location.href='hl7_datatype.php?vVersion=". $vVersion ."'\"
	  onmouseover=\"this.className='tabobjecttabHighlight'\"
	  onmouseout=\"this.className='tabobjecttabClickable'\" > 
	  Data Types\n</td>\n";

	  $gNav4right = $gNav4right . "<td width=\"95\" class=\"tabobjecttabClickable\"
	  OnClick = \"javascript:window.location.href='hl7_datastruct.php?vVersion=". $vVersion ."'\"
	  onmouseover=\"this.className='tabobjecttabHighlight'\"
	  onmouseout=\"this.className='tabobjecttabClickable'\" > 
	  Data Structs\n</td>\n";

	  $gNav4right = $gNav4right . "<td nowrap width=\"60\" class=\"tabobjecttabClickable\"
	  OnClick = \"javascript:window.location.href='hl7_query.php?vVersion=". $vVersion ."'\"
	  onmouseover=\"this.className='tabobjecttabHighlight'\"
	  onmouseout=\"this.className='tabobjecttabClickable'\" > 
	  Queries\n</td>\n";
}
else
{
	$gNav4right = $gNav4right . "<td>&nbsp;</td>\n";
}

$gNav4right = $gNav4right . "</TR>\n";
$gNav4right = $gNav4right . "</TABLE>\n\n";

}

?>
