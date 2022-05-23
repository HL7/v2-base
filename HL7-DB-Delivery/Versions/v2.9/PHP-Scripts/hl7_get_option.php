<?php

function getOption($nOption) 
{
	global $linkID;

	$SQL = "SELECT * from DBOptions where seq_no = " . $nOption ;
	$result = odbc_exec($linkID, $SQL);
	odbc_fetch_row( $result );
	$sValue = odbc_result( $result, "value");
	odbc_free_result($result);

	return $sValue;
}

?>
