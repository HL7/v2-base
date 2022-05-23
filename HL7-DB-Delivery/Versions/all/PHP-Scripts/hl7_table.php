<?php

include("hl7_vinfo.php");
include("hl7_header.php");
include("hl7_buttons.php");
include("hl7_footer.php");

if (isset($_GET["vTable"]))
{
	$sTable = $_GET["vTable"];
	$nButtonParameter=1;
	$sButtonParameter = "&vTable=" . $sTable;
}
else
{
	$sTable = "";
	$nButtonParameter=2;
	$sButtonParameter = "&vTable=" . $sTable;
}

$sButtonType = "table";

$ghl7_version="";
//$header1 = "HL7 Comprehensive Database ";

//open database
include("db.php");

$bEven = TRUE;

if ($sTable == "")
{
	//no table selected => display list of all

	$header2 = "List of Tables";
	$header3 = "";

	PrintHeader();

	print "<H2 align=center>";
	print "List of all Tables";
	print "</H2>\n";

	$SQL = "SELECT HL7Tables.table_id, HL7Tables.description_as_pub, HL7Tables.interpretation, HL7TableTypes.description, HL7Tables.section, HL7Tables.anchor FROM HL7TableTypes INNER JOIN Hl7Tables ON HL7TableTypes.table_type = HL7Tables.table_type where version_id =" . $_GET["vVersion"] . " and table_id > 0 order by table_id";

	$result = odbc_exec($linkID, $SQL);

	$Fields = odbc_num_fields($result); 
	print "<p>\n"; 
	print "<table class=\"info\">\n"; 

	// Build Column Headers 
	print "<tr class=\"info\">"; 
	print "<th class=\"info\">"; 
	print "Table"; 
	print "</th>\n"; 
	print "<th class=\"info\">"; 
	print "Description\n"; 
	print "</th>\n"; 
	print "<th class=\"info\">"; 
	print "German Interpretation\n"; 
	print "</th>\n"; 
	print "<th class=\"info\">"; 
	print "Type\n"; 
	print "</th>\n"; 
	print "<th class=\"info\">"; 
	print "Sec. Ref.\n"; 
	print "</th>\n"; 
	print "</tr>\n"; 

	$Outer=0;
	// Table Body 
	while( odbc_fetch_row( $result ))
	{ 
		$Outer++;
		if ($bEven == FALSE)
		{
			$bEven = TRUE;
			print "<tr class=\"info-even\">"; 
		}
		else
		{
			$bEven = FALSE;
			print "<tr class=\"info-odd\">"; 
		}
		print "<td>"; 
		printf("<a href=\"hl7_table.php?vVersion=%s&vTable=%s\">\n", $_GET["vVersion"], odbc_result($result,1)); 
		printf("%04d\n", odbc_result( $result, 1 )); 
		printf("</a>\n"); 
		print "</td>"; 
		print "<td valign=\"center\">"; 
		printf("%s\n", odbc_result( $result, 2 )); 
		print "</td>\n"; 
		print "<td>"; 
		printf("%s\n", odbc_result( $result, 3 )); 
		print "</td>\n"; 
		print "<td>"; 
		printf("%s\n", odbc_result( $result, 4 )); 
		print "</td>\n"; 
		print "<td>"; 
		printf("<a href=\"" .  $gPath2GeneratedFiles . "/" . $gHtmlPath . "/%s\">\n",odbc_result($result,6)); 
		printf("%s\n", odbc_result( $result, 5 )); 
		printf("</a>\n"); 
		print "</td>\n"; 
		print "</tr>\n"; 
	} 
	print "</table>\n"; 
	print "<p>\n<b> The database currently maintains $Outer table(s) for this version!</b>\n</p>\n\n"; 

	odbc_free_result($result);
}
else
{
	//just this version
	//no event selected => display list of all

	$SQL = "SELECT * from Hl7Tables where version_id = " . $_GET["vVersion"] . " and table_id = " . $sTable;

	$result = odbc_exec($linkID, $SQL);
	if (odbc_fetch_row( $result ))
	{
		$header2 = "Table " . sprintf("%04d", $sTable);
		$header3 = odbc_result( $result, "description_as_pub" ) . "<br>" . odbc_result( $result, "interpretation" );
	}
//	$sHtml = odbc_result( $result, "anchor" );
	$sTableDisplayName = odbc_result( $result, "display_name" );
	$sTableDisplayNameAsPub = odbc_result( $result, "description_as_pub" );
	$sTableOid = odbc_result( $result, "oid_table");
	$sCodesystemOid = odbc_result( $result, "cs_oid");
	$sCodesystemVers = odbc_result( $result, "cs_version");
	//$sCodesystemName = odbc_result( $result, "cs_symbolicname");
	$sValueSetOid = odbc_result( $result, "vs_oid");
	//$sValueSetName = odbc_result( $result, "vs_symbolicname");
	//$sObjectDesc = odbc_result( $result, "object_description");
	$sVersDesc = odbc_result( $result, "version_description");
	$sBinding = odbc_result( $result, "binding");
	$sSteward = odbc_result( $result, "steward");
	$sChapter = odbc_result( $result, "section" );
	odbc_free_result($result);

	//now: fetch details for OID
	if ($sCodesystemOid == "")
	{
		$sCodesystemName = "";
		$sCodesystemDesc = "";
	}
	else
	{
		$SQL = "SELECT * from Hl7Objects where oid = '" . $sCodesystemOid . "'"; 
		$result = odbc_exec($linkID, $SQL);
		$sCodesystemName = odbc_result( $result, "symbolicname");
		$sCodesystemDesc = odbc_result( $result, "object_description");
		odbc_free_result($result);
	}
	
	if ($sValueSetOid == "")
	{
		$sValueSetName = "";
		$sValueSetDesc = "";
	}
	else
	{
		$SQL = "SELECT * from Hl7Objects where oid = '" . $sValueSetOid . "'"; 
		$result = odbc_exec($linkID, $SQL);
		$sValueSetName = odbc_result( $result, "symbolicname");
		$sValueSetDesc = odbc_result( $result, "object_description");
		odbc_free_result($result);
	}
	
	PrintHeader();
	
	print "<H2 align=center>";
	print $header2;
	print "</H2>\n";
	print "<H3 align=center>";
	print $header3;
	print "</H3>\n";

	print "<table>\n"; 
	print "<tr>\n"; 
	print "<td><b>Display Name</b></td>\n<td>" . $sTableDisplayName . "</td><td>(taken from original description, but modified)</td>\n"; 
	print "</tr>\n"; 
	print "<tr>\n"; 
	print "<td></td>\n<td>" . $sTableDisplayNameAsPub . "</td><td>(original/as published) </td>\n"; 
	print "</tr>\n"; 
	print "<tr>\n"; 
	print "<td><b>Table</b></td>\n<td>OID: </td><td>" . $sTableOid . "</td>\n"; 
	print "</tr>\n"; 
	print "<tr>\n"; 
	print "<td><b>Codesystem</b></td>\n<td>OID (+version):  </td><td>" . $sCodesystemOid . " (" . $sCodesystemVers . ")" . "</td>\n"; 
	print "</tr>\n"; 
	print "<tr>\n"; 
	print "<td></td>\n<td>Symbolic Name:  </td><td>" . $sCodesystemName . "</td>\n"; 
	print "</tr>\n"; 
	print "<tr>\n"; 
	print "<td></td>\n<td valign=top>Description:  </td><td>" . $sCodesystemDesc . "</td>\n"; 
	print "</tr>\n"; 
	print "<tr>\n"; 
	print "<td><b>Value Set</b></td>\n<td>OID: </td><td>" . $sValueSetOid . "</td>\n"; 
	print "</tr>\n"; 
	print "<tr>\n"; 
	print "<td></td>\n<td>Symbolic Name:  </td><td>" . $sValueSetName . "</td>\n"; 
	print "</tr>\n"; 
	print "<tr>\n"; 
	print "<td></td>\n<td valign=top>Description:</td><td>" . $sValueSetDesc . "</td>\n"; 
	print "</tr>\n"; 
	print "<tr>\n"; 
	print "<td><b>Version Description</b></td>\n<td colspan=2>" . $sVersDesc . "</td>\n"; 
	print "</tr>\n"; 
	print "<tr>\n"; 
	print "<td><b>Binding</b></td>\n<td>" . $sBinding . "</td><td></td>\n"; 
	print "</tr>\n"; 
	print "<tr>\n"; 
	print "<td><b>Steward</b></td>\n<td>" . $sSteward . "</td><td></td>\n"; 
	print "</tr>\n"; 
	print "<tr>\n"; 
	print "<td><b>Chapter</b></td>\n<td>" . $sChapter . "</td><td></td>\n"; 
	print "</tr>\n"; 
	print "</table>\n"; 

	$SQL = "SELECT table_value,display_name,description_as_pub,interpretation,comment_as_pub,usage_note,section from HL7TableValues where version_id = " . $_GET["vVersion"] . " and table_id = " . $sTable . " order by sort_no";
	
	$result = odbc_exec($linkID, $SQL);

	$Fields = odbc_num_fields($result); 
	print "<p>\n"; 
	print "<table class=\"info\">\n<tr class=\"info\">\n"; 

	// Build Column Headers 
	for ($i=1; $i <= $Fields; $i++){ 
		switch ($i)
		{
		case 1:
			$sText = "Value";
			break;
		case 2:
			$sText = "Display Name";
			break;
		case 3:
			$sText = "Description (as published)";
			break;
		case 4:
			$sText = "German Interpretation";
			break;
		case 5:
			$sText = "Comment";
			break;
		case 6:
			$sText = "Usage Notes";
			break;
		case 7:
			$sText = "Sec. Ref.";
			break;
		default:
			$sText = odbc_field_name( $result,$i); 
		}
		if ($sText != "")
			printf("<th class=\"info\">%s</th>\n", $sText); 
	} 
	// Table Body 
	$Outer=0; 
	while( odbc_fetch_row( $result ))
	{ 
		$Outer++; 
		if ($bEven == FALSE)
		{
			$bEven = TRUE;
			print "<tr class=\"info-even\">\n"; 
		}
		else
		{
			$bEven = FALSE;
			print "<tr class=\"info-odd\">\n"; 
		}
		for($i=1; $i <= $Fields; $i++)
		{ 
			switch ($i)
			{
			case 3:
				if (odbc_result( $result, $i ) ==  odbc_result( $result, 2 ))
				{
					//do not display identical entries
					print "<td class=\"info\"><i>SEE DISPLAY NAME!!</i></td>\n"; 
				}
				break;
			default:
				$sText = odbc_result( $result, $i );
				if ($sText == "")
				{
					$sText = "<br>";
				}
				else
				{
					$sText = str_replace("&", "&amp;", $sText);
					$sText = str_replace("<", "&lt;", $sText);
					$sText = str_replace(">", "&gt;", $sText);
				}
				printf("<td class=\"info\">%s</td>\n", $sText); 
				break;
			}
		}
		print "</tr>\n"; 
	} 
	print "</table>\n"; 
	odbc_free_result($result);


	//list of all references

//======================
	print "<H2>List all Data Elements using this table</H2>\n";
	
	$SQL = "SELECT data_item, description, interpretation, section, anchor FROM HL7DataElements where version_id = " . $_GET["vVersion"] . " and table_id = " . $sTable;

	$result = odbc_exec($linkID, $SQL);

	$Fields = odbc_num_fields($result); 
	print "<p>\n"; 
	print "<table class=\"info\">\n<tr>"; 

	// Build Column Headers 
	for ($i=1; $i <= $Fields; $i++){ 
		switch ($i)
		{
		case 4:
			$sText = "";
			break;
		case 1:
			$sText = "Item";
			break;
		case 2:
			$sText = "Description";
			break;
		case 3:
			$sText = "German Interpretation";
			break;
		case 5:
			$sText = "Sec. Ref.";
			break;
		default:
			$sText = odbc_field_name( $result,$i); 
		}
		if ($sText != "")
			printf("<th class=\"info\">%s</th>\n", $sText); 
	} 
	// Table Body 
	$Outer=0; 
	while( odbc_fetch_row( $result ))
	{ 
		$Outer++; 
		if ($bEven == FALSE)
		{
			$bEven = TRUE;
			print "<tr class=\"info-even\">"; 
		}
		else
		{
			$bEven = FALSE;
			print "<tr class=\"info-odd\">"; 
		}
		for($i=1; $i <= $Fields; $i++)
		{ 
			switch ($i)
			{
			case 5:
//			case 2:
				//hide
				break;
			case 1:
				print "<td class=\"info\">"; 
				printf("<a href=hl7_dataelem.php?vVersion=%s&vData=%s>\n", $_GET["vVersion"], odbc_result($result,1)); 
				printf("%05d\n", odbc_result( $result, 1 )); 
				printf("</a>\n"); 
				print "</td>\n"; 

				break;
			case 4:
				print "<td class=\"info\">"; 
				$sText = odbc_result( $result, "section" );
				if ($sText == "")
					$sText = "Std.";
				if (odbc_result( $result, "anchor" ) == "")
					$sOut = $sText;
				else
					$sOut = "<a href=" . $gPath2GeneratedFiles . "/" . $gHtmlPath. "/" . odbc_result($result,"anchor") . ">" . $sText . "</a>\n"; 
				if ($sOut == "Std.")
					$sOut = "<br>\n";
				print $sOut;
				print "</td>\n"; 
				break;
			default:
				$sText = odbc_result( $result, $i );
				if ($sText == "")
					$sText = "<br>";
				printf("<td class=\"info\">%s</td>\n", 
					$sText); 
				break;
			}
		}
		print "</tr>\n"; 
	} 
	print "</table>\n"; 

	odbc_free_result($result);

//===========================
	print "<H2>List all Data Structures using this Table</H2>\n";
	
	$SQL = "SELECT HL7DataStructureComponents.data_structure, HL7DataStructures.description, HL7DataStructures.interpretation, HL7DataStructures.section, HL7DataStructures.anchor FROM HL7DataStructures INNER JOIN HL7DataStructureComponents ON (HL7DataStructures.version_id = HL7DataStructureComponents.version_id) AND (HL7DataStructures.data_structure = HL7DataStructureComponents.data_structure) where HL7DataStructures.version_id = " . $_GET["vVersion"] . " and HL7DataStructureComponents.table_id = " . $sTable;


	$result = odbc_exec($linkID, $SQL);

	$Fields = odbc_num_fields($result); 
	print "<p>\n"; 
	print "<table class=\"info\">\n<tr class=\"info\">"; 

	// Build Column Headers 
	for ($i=1; $i <= $Fields; $i++){ 
		switch ($i)
		{
		case 4:
			$sText = "";
			break;
		case 1:
			$sText = "Data Structure";
			break;
		case 2:
			$sText = "Description";
			break;
		case 3:
			$sText = "German Interpretation";
			break;
		case 5:
			$sText = "Sec. Ref.";
			break;
		default:
			$sText = odbc_field_name( $result,$i); 
		}
		if ($sText != "")
			printf("<th class=\"info\">%s</th>\n", $sText); 
	} 
	// Table Body 
	$Outer=0; 
	while( odbc_fetch_row( $result ))
	{ 
		$Outer++; 
		print "<tr class=\"info\">"; 
		for($i=1; $i <= $Fields; $i++)
		{ 
			switch ($i)
			{
			case 5:
//			case 2:
				//hide
				break;
			case 4:
				print "<td class=\"info\">"; 
				$sText = odbc_result( $result, "section" );
				if ($sText == "")
					$sText = "Std.";
				if (odbc_result( $result, "anchor" ) == "")
					$sOut = $sText;
				else
					$sOut = "<a href=" . $gPath2GeneratedFiles . "\\" . $gHtmlPath. "/" . odbc_result($result,"anchor") . ">" . $sText . "</a>\n"; 
				if ($sOut == "Std.")
					$sOut = "<br>\n";
				print $sOut;
				print "</td>\n"; 
				break;
			default:
				$sText = odbc_result( $result, $i );
				if ($sText == "")
					$sText = "<br>";
				printf("<td class=\"info\">%s</td>\n", 
					$sText); 
				break;
			}
		}
		print "</tr>\n"; 
	} 
	print "</table>\n"; 

	odbc_free_result($result);

	print "<H2>List all Components using this table</H2>\n";
	
	$SQL = "SELECT comp_no, description, interpretation, section FROM HL7Components where version_id = " . $_GET["vVersion"] . " and table_id = " . $sTable;

	$result = odbc_exec($linkID, $SQL);

	$Fields = odbc_num_fields($result); 
	print "<p>\n"; 
	print "<table class=\"info\">\n<tr class=\"info\">"; 

	// Build Column Headers 
	for ($i=1; $i <= $Fields; $i++){ 
		switch ($i)
		{
		case 1:
			$sText = "Comp.";
			break;
		case 2:
			$sText = "Description";
			break;
		case 3:
			$sText = "German Interpretation";
			break;
		case 4:
			$sText = "Sec. Ref.";
			break;
		default:
			$sText = odbc_field_name( $result,$i); 
		}
		if ($sText != "")
			printf("<th bgcolor='silver'>%s</th>\n", $sText); 
	} 
	// Table Body 
	$Outer=0; 
	while( odbc_fetch_row( $result ))
	{ 
		$Outer++; 
		print "<tr class=\"info\">"; 
		for($i=1; $i <= $Fields; $i++)
		{ 
			switch ($i)
			{
//			case 1:
//			case 2:
//				//hide
//				break;
			default:
				$sText = odbc_result( $result, $i );
				if ($sText == "")
					$sText = "<br>";
				printf("<td class=\"info\">%s</td>\n", 
					$sText); 
				break;
			}
		}
		print "</tr>\n"; 
	} 
	print "</table>\n"; 

	odbc_free_result($result);

}

odbc_close($linkID);

PrintFooter();

?>
