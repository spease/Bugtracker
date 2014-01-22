<?php
include("bugclasses.php");
$user = stripslashes(rawurldecode($user));
$ViewingUser	= new User($PHP_AUTH_USER);
$DaUser = new User;
if($user && $DaUser->Load($user))
{
?>
<HTML>
<HEAD>
<?if($CustomCSS){?><LINK REL="Stylesheet" TYPE="Text/CSS" HREF="<?echo$CustomCSS?>"><?}?>
<LINK REL="Stylesheet" TYPE="Text/CSS" HREF="style.css">
<TITLE>Viewing <?echo$DaUser->Username?>'s profile/<?echo$ProgramName?> Bug Tracker</TITLE>
</HEAD>
<BODY>
<?PrintHeader("Viewing $DaUser->Username's profile")?>
<TABLE>
	<TR>
		<TD>
			<TABLE CLASS="nofirstborders">
				<TR><TD><B>Alias:</B></TD><TD><A HREF="mailto:<?echo$DaUser->Email?>"><?echo$DaUser->Username?></A></TD></TR>
				<TR><TD><b>Real name:</B></TD><TD><?echo$DaUser->RealName?></TD></TR>
				<TR><TD><b>Processor(s):</B></TD><TD><?echo$DaUser->Processor?></TD></TR>
				<TR><TD><b>Memory:</B></TD><TD><?echo$DaUser->Memory?></TD></TR>
				<TR><TD><b>Operating System:</B></TD><TD><?echo$DaUser->OS?></TD></TR>
				<TR><TD><b>Video Card:</B></TD><TD><?echo$DaUser->VideoCard?></TD></TR>
				<TR><TD><b>Sound Card:</B></TD><TD><?echo$DaUser->SoundCard?></TD></TR>
			</TABLE>
		</TD>
		<TD>
			<B>Vital Info:</B>
			<BR><?echo$DaUser->VitalInfo?>
		</TD>
	</TR>
</TABLE>
<HR>
<TABLE CLASS="noborders">
	<TR>
		<TD STYLE='width:33%'><A HREF="listusers.php"><--Return to users listing</A></TD>
		<TD STYLE='width:33%;text-align:center'><?if($user==$PHP_AUTH_USER){?><A HREF="updateprofile.php">Update user profile</A><?}elseif($ViewingUser->IsAdmin){?><A HREF="updateprofile.php?user=<?echo rawurlencode($DaUser->Username)?>">Update <?echo$DaUser->Username?>'s profile</A><?}?></TD>
		<TD STYLE='width:33%;text-align:right'><?if($DaUser->ClaimedBugs){?><A HREF="listclaims.php?user=<?echo rawurlencode($DaUser->Username)?>">View user's claimed bugs (<?echo(count($DaUser->ClaimedBugs))?>) --></A><?}?></TD>
	</TR>
</TABLE>
<?}elseif($user){?>
User Not Found</TITLE>
<H2>User Not Found</H2>
The specified user was not found
<?}else{?>
No user specified</TITLE>
</HEAD>
<H2>MechWar3D Bug Tracker</H2>
No user specified to view. For a full listing of valid users, click <A HREF="listusers.php">here</A>.
<?}?>
</BODY>
</HTML>