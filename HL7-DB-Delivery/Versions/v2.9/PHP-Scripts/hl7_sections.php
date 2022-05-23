<?php

	$SQL = "SELECT * from HL7ChapterParagraphs where version_id = " . $_GET["vVersion"] . " and section = '" . $sSection . "' order by sort";
//print $linkID;
	$result2 = odbc_exec($linkID, $SQL);

	while( odbc_fetch_row( $result2 ))
	{ 
		$contents = odbc_result( $result2, "contents" );
		switch (odbc_result($result2, "style"))
		{
		case "Heading1":
			$style1 = "H1";
			$style2 = "";
			break;
		case "Heading2":
			$style1 = "H2";
			$style2 = "";
			break;
		case "Heading3":
			$style1 = "H3";
			$style2 = "";
			break;
		case "Heading4":
			$style1 = "H4";
			$style2 = "";
			break;
		case "segment":
			$style1 = "p";
			$style2 = "class=" . odbc_result($result2, "style");
			$contents = "<a href=\"hl7_segment.php?vVersion=" . $_GET["vVersion"] . "&vSegment=" . $contents . "\">" . $contents . "</a> \n"; 
			break;	
		default:
			$style1 = "p";
			$style2 = "class=" . odbc_result($result2, "style");
			break;
		}
		print "<" . $style1 . " "  .$style2 . ">\n";
		print $contents . "\n";
		print "</" . $style1 . ">\n";
	}
	odbc_free_result($result2);

?>
