<?php

include("hl7_vinfo.php");
include("hl7_header.php");
include("hl7_buttons.php");
include("hl7_footer.php");

if (isset($_GET["vQuery"]))
{
	$sQuery = $_GET["vQuery"];
}
else
{
	$sQuery = "";
}

$sButtonType = "query";
if ($sQuery != "")
{
	$sButtonParameter = "&vQuery=" . $sQuery;
}
$header1 = "HL7 Comprehensive Database ";

//open database
include("db.php");


$bEven = TRUE;

if ($sQuery == "")
{
	//no event selected => display list of all

	PrintHeader();
	
	print "<H2 align=center>";
	print "List of all Queries";
	print "</H2>\n";

	$SQL = "SELECT * FROM HL7Queries where version_id =" . $_GET["vVersion"] . " order by query_id;";

	$result = odbc_exec($linkID, $SQL);

	$Fields = odbc_num_fields($result); 
	print "<p>\n"; 
	print "<div class=\"tabelle\" align=\"center\">\n";
	print "<p>\n"; 
	print "</p>\n"; 
	print "<table class=\"info\">\n<tr>"; 

	// Build Column Headers 
	print "<tr class=\"info\">"; 
	print "<th class=\"info\">"; 
	print "Query ID\n"; 
	print "</th>"; 
	print "<th class=\"info\">"; 
	print "Name\n"; 
	print "</th>"; 
	print "<th class=\"info\">"; 
	print "Query\n"; 
	print "</th>"; 
	print "<th class=\"info\">"; 
	print "Response\n"; 
	print "</th>"; 
	print "<th class=\"info\">"; 
	print "Sec. Ref.\n"; 
	print "</th>"; 
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
		print "<td class=\"info\">"; 
		printf("<a href=hl7_query.php?vVersion=%s&vQuery=%s>\n", $_GET["vVersion"], odbc_result($result,1)); 
		printf("%s\n", odbc_result( $result, 1 )); 
		printf("</a>\n"); 
		print "</td>"; 
		$sText = odbc_result( $result, 3 );
		if ($sText == "")
			$sText = "<br>";
		print "<td class=\"info\" valign=\"center\">"; 
		printf("%s\n", $sText); 
		print "</td>"; 

		print "<td class=\"info\">"; 
		printf("<a href=hl7_msgtype.php?vVersion=%s&vType=%s>\n", $_GET["vVersion"], odbc_result($result,"query_msg_type")); 
		printf("%s\n", odbc_result( $result, "query_msg_type" )); 
		printf("</a>\n"); 
		print "^"; 
		printf("<a href=hl7_event.php?vVersion=%s&vEvent=%s>\n", $_GET["vVersion"], odbc_result($result,"query_trigger")); 
		printf("%s\n", odbc_result( $result, "query_trigger" )); 
		printf("</a>\n"); 
		print "^"; 
		printf("<a href=hl7_msgstruct.php?vVersion=%s&vStruct=%s>\n", $_GET["vVersion"], odbc_result($result,"query_msg_struct")); 
		printf("%s\n", odbc_result( $result, "query_msg_struct" )); 
		printf("</a>\n"); 
		print "</td>"; 

		print "<td class=\"info\">"; 
		printf("<a href=hl7_msgtype.php?vVersion=%s&vType=%s>\n", $_GET["vVersion"], odbc_result($result,"response_msg_type")); 
		printf("%s\n", odbc_result( $result, "response_msg_type" )); 
		printf("</a>\n"); 
		print "^"; 
		printf("<a href=hl7_event.php?vVersion=%s&vEvent=%s>\n", $_GET["vVersion"], odbc_result($result,"response_trigger")); 
		printf("%s\n", odbc_result( $result, "response_trigger" )); 
		printf("</a>\n"); 
		print "^"; 
		printf("<a href=hl7_msgstruct.php?vVersion=%s&vStruct=%s>\n", $_GET["vVersion"], odbc_result($result,"response_msg_struct")); 
		printf("%s\n", odbc_result( $result, "response_msg_struct" )); 
		printf("</a>\n"); 
		print "</td>"; 

		print "<td class=\"info\">"; 
		$sText = odbc_result( $result, "section" );
		if ($sText == "")
			$sText = "<br>";
		print $sText;
		print "</td>\n"; 

		print "</tr>\n"; 
	} 
	print "</table>\n"; 
	print "<p><b> The database currently maintains $Outer queries for this version!</b></p>"; 

	print "</div>\n"; 

	odbc_free_result($result);
}
else
{
	//just this version
	//no event selected => display list of all

	$SQL = "SELECT * FROM HL7Queries where version_id =" . $_GET["vVersion"] . " and query_id = '". $sQuery ."';";

	$result = odbc_exec($linkID, $SQL);
	if (odbc_fetch_row( $result ))
	{
		$header2 = "Query " . $sQuery;
		$header3 = odbc_result( $result, "description" );
	}

	PrintHeader();

	$sText = odbc_result( $result, "section" );
	if ($sText == "")
		$sText = "Std.";
	if (odbc_result( $result, "anchor" ) == "")
		$sOut = $sText;
	else
		$sOut = "<a href=" . $gPath2GeneratedFiles . "/" . $gHtmlPath. "/" . odbc_result($result,"anchor") . ">" . $sText . "</a>\n"; 
	if ($sOut == "Std.")
		$sOut = "";

	$sHtml = odbc_result( $result, "anchor" );
	$sChapter = odbc_result( $result, "section" );
	odbc_free_result($result);


	print "<H2 align=center>";
	print $header2;
	print "</H2>\n";
	print "<H3 align=center>";
	print $header3;
	print "</H3>\n";

	if ($sOut != "")
		print "<h2>see Chapter: " . $sOut . "</H2>\n";
		
	$SQL = "SELECT * FROM HL7Queries where version_id =" . $_GET["vVersion"] . " and query_id = '". $sQuery ."';";
	$result = odbc_exec($linkID, $SQL);
	odbc_fetch_row( $result );

	print "<H2>Query ID " . odbc_result( $result, "query_id" ) . "</H2>\n"; 

	print "<div class=\"tabelle\" align=\"center\">\n";
	print "<p>\n"; 
	print "</p>\n"; 
	print "<table class=\"info\">\n"; 

	print "<tr class=\"info\">"; 
	print "<th class=\"info\">"; 
	print "Name\n"; 
	print "</th>"; 
	print "<th class=\"info\">"; 
	print odbc_result( $result, "description" ) . "<br>" . odbc_result( $result, "interpretation" ); 
	print "</th>"; 
	print "</tr>\n"; 

	print "<tr class=\"info\">"; 
	print "<td class=\"info\">"; 
	print "Query ID\n"; 
	print "</td>"; 
	print "<td class=\"info\">"; 
	print odbc_result( $result, "query_id" ); 
	print "</td>"; 
	print "</tr>\n"; 

	print "<tr class=\"info\">"; 
	print "<td class=\"info\">"; 
	print "Query Type\n"; 
	print "</td>"; 
	print "<td class=\"info\">"; 
	print odbc_result( $result, "query_type" ); 
	print "</td>"; 
	print "</tr>\n"; 

	print "<tr class=\"info\">"; 
	print "<td class=\"info\">"; 
	print "Query Mode\n"; 
	print "</td>"; 
	print "<td class=\"info\">"; 
	print odbc_result( $result, "query_mode" ); 
	print "</td>"; 
	print "</tr>\n"; 

	print "<tr class=\"info\">"; 
	print "<td class=\"info\">"; 
	print "Query Msg Type\n"; 
	print "</td>"; 
	print "<td class=\"info\">"; 
	printf("<a href=hl7_msgtype.php?vVersion=%s&vType=%s>\n", $_GET["vVersion"], odbc_result($result,"query_msg_type")); 
	printf("%s\n", odbc_result( $result, "query_msg_type" )); 
	printf("</a>\n"); 
	print "</td>"; 
	print "</tr>\n"; 

	print "<tr class=\"info\">"; 
	print "<td class=\"info\">"; 
	print "Query Msg Trigger\n"; 
	print "</td>"; 
	print "<td class=\"info\">"; 
	printf("<a href=hl7_event.php?vVersion=%s&vEvent=%s>", $_GET["vVersion"], odbc_result($result,"query_trigger")); 
	print  odbc_result( $result, "query_trigger" ); 
	printf("</a>"); 
	print "</td>"; 
	print "</tr>\n"; 

	print "<tr class=\"info\">"; 
	print "<td class=\"info\">"; 
	print "Query Msg Struct\n"; 
	print "</td>"; 
	print "<td class=\"info\">"; 
	printf("<a href=hl7_msgstruct.php?vVersion=%s&vStruct=%s>\n", $_GET["vVersion"], odbc_result($result,"query_msg_struct")); 
	printf("%s\n", odbc_result( $result, "query_msg_struct" )); 
	printf("</a>\n"); 
	print "</td>"; 
	print "</tr>\n"; 

	print "<tr class=\"info\">"; 
	print "<td class=\"info\">"; 
	print "Response Msg Type\n"; 
	print "</td>"; 
	print "<td class=\"info\">"; 
	printf("<a href=hl7_msgtype.php?vVersion=%s&vType=%s>\n", $_GET["vVersion"], odbc_result($result,"response_msg_type")); 
	printf("%s\n", odbc_result( $result, "response_msg_type" )); 
	printf("</a>\n"); 
	print "</td>"; 
	print "</tr>\n"; 

	print "<tr class=\"info\">"; 
	print "<td class=\"info\">"; 
	print "Response Msg Trigger\n"; 
	print "</td>"; 
	print "<td class=\"info\">"; 
	printf("<a href=hl7_event.php?vVersion=%s&vEvent=%s>", $_GET["vVersion"], odbc_result($result,"response_trigger")); 
	print  odbc_result( $result, "response_trigger" ); 
	printf("</a>"); 
	print "</td>"; 
	print "</tr>\n"; 

	print "<tr class=\"info\">"; 
	print "<td class=\"info\">"; 
	print "Response Msg Struct\n"; 
	print "</td>"; 
	print "<td class=\"info\">"; 
	printf("<a href=hl7_msgstruct.php?vVersion=%s&vStruct=%s>\n", $_GET["vVersion"], odbc_result($result,"response_msg_struct")); 
	printf("%s\n", odbc_result( $result, "response_msg_struct" )); 
	printf("</a>\n"); 
	print "</td>"; 
	print "</tr>\n"; 

	print "<tr class=\"info\">"; 
	print "<td class=\"info\">"; 
	print "Query Characteristics\n"; 
	print "</td>"; 
	print "<td class=\"info\">"; 
	print odbc_result( $result, "query_characteristics" ); 
	print "</td>"; 
	print "</tr>\n"; 

	print "<tr class=\"info\">"; 
	print "<td class=\"info\">"; 
	print "Response Characteristics\n"; 
	print "</td>"; 
	print "<td class=\"info\">"; 
	print odbc_result( $result, "response_characteristics" ); 
	print "</td>"; 
	print "</tr>\n"; 

	print "<tr class=\"info\">"; 
	print "<td class=\"info\">"; 
	print "Purpose\n"; 
	print "</td>"; 
	print "<td class=\"info\">"; 
	print odbc_result( $result, "purpose" ); 
	print "</td>"; 
	print "</tr>\n"; 

	print "<tr class=\"info\">"; 
	print "<td class=\"info\">"; 
	print "Segment Pattern\n"; 
	print "</td>"; 
	print "<td class=\"info\">"; 
	print odbc_result( $result, "segment_pattern" ); 
	print "</td>"; 
	print "</tr>\n"; 

	print "<tr class=\"info\">"; 
	print "<td class=\"info\">"; 
	print "Priority\n"; 
	print "</td>"; 
	print "<td class=\"info\">"; 
	print odbc_result( $result, "priority" ); 
	print "</td>"; 
	print "</tr>\n"; 

	print "</table>\n"; 

	print "</div>\n"; 


//=======================
//start message structure

	print "<h3>Message Structures as used for this Query</h3>\n";

	//the two SQL statements
	$SQL1 = "SELECT seq_no, seg_code, groupname, repetitional, optional from HL7MsgStructIDSegments where version_id = " . $_GET["vVersion"] . " and message_structure = '" . odbc_result($result,"query_msg_struct") . "' order by seq_no";
	$SQL2 = "SELECT seq_no, seg_code, groupname, repetitional, optional from HL7MsgStructIDSegments where version_id = " . $_GET["vVersion"] . " and message_structure = '" . odbc_result($result,"response_msg_struct") . "' order by seq_no";

	include("hl7_message.php");

	odbc_free_result($result);
// end message structure
//======================
//display information

	$SQL = "SELECT * from HL7QueryDisplay where version_id =" . $_GET["vVersion"] . " and query_id = '". $sQuery ."' order by seq_no";

	$result = odbc_exec($linkID, $SQL);

	$Fields = odbc_num_fields($result); 
	
	if (odbc_result($result, "line") <> "")
	{
		print "<H2>Example Output in Display Form</H2>";
	};
	
	print "<pre>\n"; 
	// Table Body 
	while( odbc_fetch_row( $result ))
	{ 
		print odbc_result( $result, "line") . "\n"; 
	} 
	print "</pre>\n"; 

//======================
//QueryInput (Parameter + Comments)

	for ($j = 1; $j < 7; $j++)
	{
		switch ($j)
		{
		case 1:
			$SQL = "SELECT * FROM HL7QueryInput where version_id =" . $_GET["vVersion"] . " and query_id = '". $sQuery ."' and qry_type ='QPD' order by seq_no;";
			$tab = 1;
			break;
		case 3:
			$SQL = "SELECT * FROM HL7QueryInput where version_id =" . $_GET["vVersion"] . " and query_id = '". $sQuery ."' and qry_type ='QBE' order by seq_no;";
			$tab = 1;
			break;
		case 5:
			$SQL = "SELECT * FROM HL7QueryInput where version_id =" . $_GET["vVersion"] . " and query_id = '". $sQuery ."' and qry_type ='VIRTUAL' order by seq_no;";
			$tab = 1;
			break;
		case 2:
			$SQL = "SELECT * FROM HL7QueryInputParameter where version_id =" . $_GET["vVersion"] . " and query_id = '". $sQuery ."' and qry_type ='QPD' order by seq_no;";
			$tab = 2;
			break;
		case 4:
			$SQL = "SELECT * FROM HL7QueryInputParameter where version_id =" . $_GET["vVersion"] . " and query_id = '". $sQuery ."' and qry_type ='QBE' order by seq_no;";
			$tab = 2;
			break;
		case 6:
			$SQL = "SELECT * FROM HL7QueryInputParameter where version_id =" . $_GET["vVersion"] . " and query_id = '". $sQuery ."' and qry_type ='VIRTUAL' order by seq_no;";
			$tab = 2;
			break;
		}

		$result = odbc_exec($linkID, $SQL);

		$Fields = odbc_num_fields($result); 
		if ($Fields > 0)
		{
			switch ($j)
			{
			case 1:
				print "<H2>QPD Input Parameter</H2>\n";
				break;
			case 3:
				print "<H2>QBE Input Parameter</H2>\n";
				break;
			case 5:
				print "<H2>Virtual Table Input Parameter</H2>\n";
				break;
			case 2:
				print "<H2>QPD Input Parameter Comments</H2>\n";
				break;
			case 4:
				print "<H2>QBE Input Parameter Comments</H2>\n";
				break;
			case 6:
				print "<H2>Virtual Table Input Parameter Comments</H2>\n";
				break;
			}
		
			print "<p>\n"; 
			print "<table class=\"info\">\n<tr>"; 

			// Build Column Headers 
			if ($tab == 1)
			{
				//1st 3 tables (parameter)
				
				for ($i=1; $i <= $Fields; $i++)
				{ 
					switch ($i)
					{
					case 1:
					case 2:
					case 3:
						$sText = "";
						break;
					case 4:
						$sText = "Seq";
						break;
					case 5:
						$sText = "Field";
						break;
					case 6:
						$sText = "Key/ Search";
						break;
					case 7:
						$sText = "Sort";
						break;
					case 8:
						$sText = "Length";
						break;
					case 9:
						$sText = "Data Type";
						break;
					case 10:
						$sText = "opt";
						break;
					case 11:
						$sText = "Rep#";
						break;
					case 12:
						$sText = "Match Op";
						break;
					case 13:
						$sText = "Table#";
						break;
					case 14:
						$sText = "Segment Field Name";
						break;
					case 15:
						$sText = "Loinc";
						break;
					case 16:
						$sText = "Element Name";
						break;
					default:
						$sText = odbc_field_name( $result,$i); 
					}
					if ($sText != "")
						printf("<th class=\"info\">%s</th>\n", $sText); 
			
				} 
				print "</tr>\n"; 
		
				// Table Body 
				while( odbc_fetch_row( $result ))
				{ 
					print "<tr class=\"info\">"; 
					for($i=1; $i <= $Fields; $i++)
					{ 
						switch ($i)
						{
						case 1:
						case 2:
						case 3:
							//hide
							break;
						case 9:
							printf("<td class=\"info\"><a href=hl7_datastruct.php?vVersion=%s&vStruct=%s>%s</a></td>\n", 
								odbc_result( $result, "version_id" ), 
								odbc_result( $result, "qry_type" ),
								odbc_result( $result, "qry_type" )); 
							break;
						case 13:
							if (odbc_result( $result, "table_id" ) == 0)
							{
								printf("<td class=\"info\"><br></td>\n"); 
							}
							else
							{
								printf("<td class=\"info\"><a href=hl7_table.php?vVersion=%s&vTable=%s>%04d</a></td>\n", 
									odbc_result( $result, "version_id" ), 
									odbc_result( $result, "table_id" ),
									odbc_result( $result, "table_id" )); 
							}
							break;
						default:
							if (odbc_result( $result, $i ) == "")
							{
								printf("<td class=\"info\"><BR></td>\n"); 
							}
							else
							{
								printf("<td class=\"info\">%s</td>\n", 
									odbc_result( $result, $i )); 
							}
							break;
						}
					}
					print "</tr>\n"; 
				} 
			}
			else
			{
				//2nd 3 tables (=comments)
				
				// Build Column Headers 
				for ($i=1; $i <= $Fields; $i++)
				{ 
					switch ($i)
					{
					case 1:
					case 2:
					case 3:
						$sText = "";
						break;
					case 4:
						$sText = "Seq";
						break;
					case 5:
						$sText = "Field";
						break;
					case 6:
						$sText = "Component";
						break;
					case 7:
						$sText = "Data Type";
						break;
					case 8:
						$sText = "Comment";
						break;
					default:
						$sText = odbc_field_name( $result,$i); 
					}
					if ($sText != "")
						printf("<th class=\"info\">%s</th>\n", $sText); 
			
				} 
		
				// Table Body 
				while( odbc_fetch_row( $result ))
				{ 
					print "<tr class=\"info\">"; 
					for($i=1; $i <= $Fields; $i++)
					{ 
						switch ($i)
						{
						case 1:
						case 2:
						case 3:
							//hide
							break;
						case 7:
							if (odbc_result( $result, "data_type" ) == "")
							{
								printf("<td class=\"info\"><BR></td>\n"); 
							}
							else
							{
								printf("<td class=\"info\"><a href=hl7_datastruct.php?vVersion=%s&vStruct=%s>%s</a></td>\n", 
									odbc_result( $result, "version_id" ), 
									odbc_result( $result, "data_type" ),
									odbc_result( $result, "data_type" )); 
							}
							break;
						case 8:
							printf("<td class=\"info\">%s</td>\n", 
									odbc_result( $result, "description" )); 
						default:
							if (odbc_result( $result, $i ) == "")
							{
								printf("<td class=\"info\"><BR></td>\n"); 
							}
							else
							{
								printf("<td class=\"info\">%s</td>\n", 
									odbc_result( $result, $i )); 
							}
							break;
						}
					}
					print "</tr>\n"; 
				}
			}
			print "</table>\n"; 
		}

		odbc_free_result($result);
	}


//======================
//response control
	$SQL = "SELECT * FROM HL7QueryRCP where version_id =" . $_GET["vVersion"] . " and query_id = '". $sQuery ."' order by seq_no;";

	print "<H2>Response Control</H2>\n";
	print "<p>\n"; 
	print "<table class=\"info\">\n<tr>"; 

	$result = odbc_exec($linkID, $SQL);

	$Fields = odbc_num_fields($result); 

	// Build Column Headers 
	for ($i=1; $i <= $Fields; $i++)
	{ 
		switch ($i)
		{
		case 1:
		case 2:
		case 3:
		case 4:
			$sText = "";
			break;
		case 5:
			$sText = "Field";
			break;
		case 6:
			$sText = "Component";
			break;
		case 7:
			$sText = "Length";
			break;
		case 8:
			$sText = "Data Type";
			break;
		case 9:
			$sText = "Description";
			break;
		default:
			$sText = odbc_field_name( $result,$i); 
		}
		if ($sText != "")
			printf("<th class=\"info\">%s</th>\n", $sText); 

	} 
	print "</tr>\n"; 
		
	// Table Body 
	while( odbc_fetch_row( $result ))
	{ 
		print "<tr class=\"info\">"; 
		for($i=1; $i <= $Fields; $i++)
		{ 
			switch ($i)
			{
			case 1:
			case 2:
			case 3:
			case 4:
				//hide
				break;
			case 8:
				if (odbc_result( $result, "datatype" ) == "")
				{
					printf("<td class=\"info\"><BR></td>\n"); 
				}
				else
				{
					printf("<td class=\"info\"><a href=hl7_datastruct.php?vVersion=%s&vStruct=%s>%s</a></td>\n", 
						odbc_result( $result, "version_id" ), 
						odbc_result( $result, "datatype" ),
						odbc_result( $result, "datatype" )); 
				}
				break;
//			case 8:
//				printf("<td class=\"info\">%s</td>\n", 
//						odbc_result( $result, "description" )); 
			default:
				if (odbc_result( $result, $i ) == "")
				{
					printf("<td class=\"info\"><BR></td>\n"); 
				}
				else
				{
					printf("<td class=\"info\">%s</td>\n", 
						odbc_result( $result, $i )); 
				}
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
