<?php
//expects two SQL-statements in $SQL1 and $SQL2

	print "<table border=0>\n";
	print "<tr>\n";
	$sMax = "                                    ";
	for ($i = 1; $i < 3; $i++)
	{
		print "<td valign=top width=25%>\n";
		if ($i == 1)
		{
			print "<h3>Sending Application</h3>\n";
			$SQL = $SQL1;
		}
		else
		{
			print "<h3>Receiving Application</h3>\n";
			$SQL = $SQL2;
		}

		$result2 = odbc_exec($linkID, $SQL);
		$Fields2 = odbc_num_fields($result2); 

		print "<pre>\n"; 
		$sIndent = "";
		// Table Body 
		while( odbc_fetch_row( $result2 ))
		{ 
			switch (odbc_result( $result2, "seg_code" ))
			{
			case "[":
			case "{":
			case "<":
			case "[{":
				$sPrint = false;
				break;
			case "}]":
			case "]":
			case "}":
			case ">":
				$sPrint = false;
				$sIndent = substr($sIndent, 0, strlen($sIndent)-3);
				break;
			case "|":
				$sPrint = false;
				break;
			default:
				$sPrint = true;
				break;
			}
			$sLine = $sIndent;
			if (odbc_result( $result2, "repetitional" ) == 1)
				$sLine = $sLine .  "{  ";
			if (odbc_result( $result2, "optional" ) == 1)
				$sLine = $sLine .  "[  ";
			if ($sPrint == true)
			{
				$sLine = $sLine . "<a href=hl7_segment.php?vVersion=" . $_GET["vVersion"] . "&vSegment=" . odbc_result($result2,"seg_code") . ">"; 
				$sLine = $sLine . odbc_result( $result2, "seg_code" ); 
				$sLine = $sLine . "</a>"; 
			}
			else
			{
				$sLine = $sLine .  odbc_result( $result2, "seg_code" ); 
			}

			if (odbc_result( $result2, "groupname" ) <> "")
			{
				$sLine = $sLine . substr($sMax,strlen($sLine)) . odbc_result( $result2, "groupname" ). " ";
			}

			if (odbc_result( $result2, "optional" ) == 1)
				$sLine = $sLine . "  ]";
			if (odbc_result( $result2, "repetitional" ) == 1)
				$sLine = $sLine . "  }";
			print $sLine . "\n"; 
			switch (odbc_result( $result2, "seg_code" ))
			{
			case "[{":
			case "[":
			case "{":
			case "<":
				$sIndent = $sIndent . "   ";
				break;
			}
		} 
		print "</pre>\n"; 
		odbc_free_result($result2);
		print "</td>\n";
	}
	print "</table>\n";

?>
