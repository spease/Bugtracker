<?php
include("bugclasses.php");
?>
<HTML>
<HEAD>
<?if($CustomCSS){?><LINK REL="Stylesheet" TYPE="Text/CSS" HREF="<?echo$CustomCSS?>"><?}?>
<LINK REL="Stylesheet" TYPE="Text/CSS" HREF="style.css">
<TITLE>Users Listing/<?echo$ProgramName?> Bug Tracker</TITLE>
</HEAD>
<?PrintHeader("Listing Users");
ListUsers()?>
<A HREF="updateprofile.php">Update user profile</A>
</BODY>
</HTML>