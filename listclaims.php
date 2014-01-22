<?php
include("bugclasses.php");
$DaUser = new User;
if(!$DaUser->Load($user))	$DaUser->Load($PHP_AUTH_USER);
?>
<HTML>
<HEAD>
<?if($CustomCSS){?><LINK REL="Stylesheet" TYPE="Text/CSS" HREF="<?echo$CustomCSS?>"><?}?>
<LINK REL="Stylesheet" TYPE="Text/CSS" HREF="style.css">
<TITLE>Listing Claimed Bugs for <?echo$DaUser->Username?>/<?echo$ProgramName?> Bug Tracker</TITLE>
<BODY>
<?PrintHeader("Listing Claimed Bugs for $DaUser->Username");
ListClaimedBugs($DaUser)?>
<A HREF="viewuser.php?user=<?echo$DaUser->Username?>"><-- Return to <?echo$DaUser->Username?>'s user information</A>
</BODY>
</HTML>