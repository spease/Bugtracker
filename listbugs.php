<?php
include("bugclasses.php");
?>
<HTML>
<HEAD>
<?if($CustomCSS){?><LINK REL="Stylesheet" TYPE="Text/CSS" HREF="<?echo$CustomCSS?>"><?}?>
<LINK REL="Stylesheet" TYPE="Text/CSS" HREF="style.css">
<TITLE>Listing Bugs/<?echo$ProgramName?> Bug Tracker</TITLE>
<BODY>
<?PrintHeader("Listing Bugs");
ListBugs()?>
<A HREF="addbug.php">Add a bug</A>
</BODY>
</HTML>