<?php

function PrintHeader() 
{
global $ghl7_version;

global $gNav1left; //top left
global $gNav2left; 
global $gNav3left; 
global $gNav4left; //low left

global $gNav1right; 
global $gNav2right; 
global $gNav3right; // 3. row right
global $gNav4right; // 4. row right

	SetVersionInfo();
	SetButtons();

//This script will print the header information including the logos.

print "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.0 //EN\">\n";
print "<html>\n";
print "<head>\n";
print "<meta name=\"author\" content=\"Frank Oemig\">\n";
print "<link rel=\"stylesheet\" href=\"formate.css\" type=\"text/css\">\n";
print "<link rel=\"SHORTCUT ICON\" href=\"hl7.ico\">\n";
print "<title>HL7 Database</title>\n";
print "</head>\n\n";

print "<body>\n\n";

print "<!-- Logos -->\n";
print "<TABLE class=top cellpadding=4 width=100%>\n";
print "<TR class=top>\n";
print "<TD class=top>\n";
print "<IMG SRC = 'images/3dhl7_usa.jpg' border=0 width=60>\n";
print "</TD>\n";
print "<TD>\n";
//left (main) navigation
print $gNav1left;
print $gNav2left;
print $gNav3left;
print $gNav4left;

print "</TD>\n";
print "<TD align=center>\n";
print "<H1>";
if ($ghl7_version != "")
{
	print  "Version " . $ghl7_version ;
}
else
{
	print  "HL7 - Comprehensive Database";
}
print "</H1>\n";
print "</TD>\n";
print "<TD>\n";
//right (version-specific) navigation
print $gNav1right;
print $gNav2right;
print $gNav3right;
print $gNav4right;

print "</TD>\n";
print "<TD>\n";
print "<IMG SRC = \"images/3dhl7_deu.gif\" border=1 align=right width=60>\n";
print "</TD>\n";
print "</TR>\n";
print "</TABLE>\n\n";

}

?>
