<?php

function PrintFooter()
{
print "\n\n";
print "<p>\n";
print "</p>\n";

//print "<div bgcolor=\"#aaaaee\">\n";

//print "<table bgcolor=\"#aaaacc\" width=\"100%\">\n";
print "<table width=\"100%\" class=nav>\n";
print "<tr class=nav3>\n";
print "<td class=nav3 colspan=3 align=left><b>Links to Other/Further HL7 Information</b></td>";
print "<td class=nav3 width=\"*\">&nbsp;</td>";
print "<td class=nav3 align=right>\n";
//heutiges Datum
printf("<i>Generated: %s (FO)</i>\n",  strftime("%d.%m.%Y", time())); 
print "</td>\n";
print "</tr>\n";
print "<tr class=nav2>\n";
print "<td class=nav2 valign=top width=\"250\"><a href=\"http://www.hl7.org\">Health Level Seven, Inc. (HQ)</a></td>";
print "<td class=nav2 valign=top width=\"120\"><a href=\"http://www.hl7.de\">HL7 Germany</a><br/><a href=\"http://www.hl7.eu\">HL7 Europe</a></td>";
print "<td class=nav2 valign=top width=\"200\"><a href=\"http://www.oemig.de/HL7\">Frank Oemig's HL7 Site</a> <br/>(Infos about the <a href=\"http://www.oemig.de/HL7/hl7db.htm\">database</a>)</td>";
print "<td class=nav2 valign=top width=\"*\">&nbsp;</td>";
print "<td class=nav2 valign=top width=\"240\" align=right><a href=\"http://www.hl7-experts.info\">HL7 Experts Network</a></td>";
print "</tr>\n";
print "</table>\n";

//print "</div>\n";

print "</body>\n";
print "</html>\n";
}

?>
