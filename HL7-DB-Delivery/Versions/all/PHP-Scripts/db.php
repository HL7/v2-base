<?php

//opens the database
//should be included in all files

$db = "HL7";
$mdbFilename="D:/Eigene Dateien/HL7/Datenbank/hl7_85.mdb";

$db="Driver={Microsoft Access Driver (*.mdb)};Dbq=$mdbFilename";

$linkID = @odbc_connect($db, "", "", SQL_CUR_USE_ODBC);

if ($linkID == FALSE)
{
	die( "no connection to ODBC database " . $db . " possible.\n");
}

?>
