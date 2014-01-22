<?php
include("bugclasses.php");
$PHP_AUTH_USER = stripslashes($PHP_AUTH_USER);
$user = stripslashes(rawurldecode($user));
$UpdatingUser = new User($PHP_AUTH_USER);
if($user && ($user == $UpdatingUser->Username || $UpdatingUser->IsAdmin))	$DaUser = new User($user);
else
{
	$DaUser = $UpdatingUser;
	$user	= $PHP_AUTH_USER;
}
if($HTTP_POST_VARS['UpdateUser'])
{
	$DaUser->Username	=$user;
	$DaUser->RealName	=StripSlashes($chRealName);
	$DaUser->Email		=StripSlashes($chEmail);
	$DaUser->Title		=StripSlashes($chTitle);
	$DaUser->Version	=StripSlashes($chVersion);
	$DaUser->OS			=StripSlashes($chOS);
	$DaUser->Processor	=StripSlashes($chProcessor);
	$DaUser->Memory		=StripSlashes($chMemory);
	$DaUser->VideoCard	=StripSlashes($chVideoCard);
	$DaUser->SoundCard	=StripSlashes($chSoundCard);
	$DaUser->VitalInfo	=StripSlashes($chVitalInfo);
	$DaUser->Stylesheet	=StripSlashes($chUserStylesheet);
	if($UpdatingUser->IsAdmin)	$DaUser->IsDeveloper	=	$chIsDeveloper;
	$DaUser->Save();
	ResponsePage("$user's profile successfully updated, taking you back to user information.","viewuser.php?user=".rawurlencode($user),"viewuser.php?user=".rawurlencode($user), NULL, "Profile for $user successfully updated");
}
else
{ ?>
<HTML>
<HEAD>
<?if($CustomCSS){?><LINK REL="Stylesheet" TYPE="Text/CSS" HREF="<?echo$CustomCSS?>"><?}?>
<LINK REL="Stylesheet" TYPE="Text/CSS" HREF="style.css">
<TITLE>Updating User Profile for <?echo $user?>/<?echo $ProgramName?> Bug Tracker</TITLE>
<STYLE TYPE="TEXT/CSS">
SELECT
{
	width:200;
}
INPUT
{
	width:200;
}
</STYLE>
</HEAD>
<BODY>
<?PrintHeader("Updating User Profile for $user")?>
<TABLE><FORM ACTION="updateprofile.php<?if($user){echo"?user=".rawurlencode($user);}?>" METHOD="post">
	<TR>
		<TD>
			<TABLE CLASS="noborders">
				<TR>
					<TD style="width:150"><B>Username:</B></TD>
					<TD><?echo$user?></TD>
				</TR>
				<TR>
					<TD><B>Real name:</B></TD>
					<TD><INPUT TYPE="TEXT" NAME="chRealName" VALUE="<?echo htmlspecialchars($DaUser->RealName)?>"></TD>
				</TR>
				<TR>
					<TD><B>Email:</B></TD>
					<TD><INPUT TYPE="TEXT" NAME="chEmail" VALUE="<?echo htmlspecialchars($DaUser->Email)?>"></TD>
				</TR>
				<TR>
					<TD><B>OS:</B></TD>
					<TD>
						<SELECT NAME="chOS">
						<OPTION VALUE="Windows 95"<?if($DaUser->OS=="Windows 95")echo" SELECTED"?>>Windows 95</OPTION>
						<OPTION VALUE="Windows 98"<?if($DaUser->OS=="Windows 98")echo" SELECTED"?>>Windows 98</OPTION>
						<OPTION VALUE="Windows 98SE"<?if($DaUser->OS=="Windows 98SE")echo" SELECTED"?>>Windows 98 SE</OPTION>
						<OPTION VALUE="Windows 2000 Pro"<?if($DaUser->OS=="Windows 2000 Pro")echo" SELECTED"?>>Windows 2000 Professional</OPTION>
						<OPTION VALUE="Windows XP Home"<?if($DaUser->OS=="Windows XP Home")echo" SELECTED"?>>Windows XP Home Edition</OPTION>
						<OPTION VALUE="Windows XP Professional"<?if($DaUser->OS=="Windows XP Professional")echo" SELECTED"?>>Windows XP Professional</OPTION>
						<OPTION VALUE="Linux"<?if($DaUser->OS=="Linux")echo" SELECTED"?>>Linux</OPTION>
						</SELECT>
					</TD>
				</TR>
				<TR>
					<TD><B>Processor(s):</B></TD>
					<TD><INPUT TYPE="TEXT" NAME="chProcessor" VALUE="<?echo htmlspecialchars($DaUser->Processor)?>"></TD>
				</TR>
				<TR>
					<TD><B>Memory:</B></TD>
					<TD><INPUT TYPE="TEXT" NAME="chMemory" VALUE="<?echo htmlspecialchars($DaUser->Memory)?>"> MB</TD>
				</TR>
				<TR>
					<TD><B>Video Card:</B></TD>
					<TD><INPUT TYPE="TEXT" NAME="chVideoCard" VALUE="<?echo htmlspecialchars($DaUser->VideoCard)?>"></TD>
				</TR>
				<TR>
					<TD><B>Sound Card:</B></TD>
					<TD><INPUT TYPE="TEXT" NAME="chSoundCard" VALUE="<?echo htmlspecialchars($DaUser->SoundCard)?>"></TD>
				</TR>
<?if($UpdatingUser->IsAdmin){?>
				<TR>
					<TD><B>Is Developer:</B></TD>
					<TD><INPUT STYLE="border-width:0px;" TYPE="CHECKBOX" NAME="chIsDeveloper"<?if($DaUser->IsDeveloper){?> CHECKED<?}?>></TD>
				</TR>
<?}?>
			</TABLE>
		</TD>
		<TD STYLE="vertical-align:middle;text-align:center">
			<B>Vital Info:</B>
			<BR><TEXTAREA NAME="chVitalInfo" ROWS="10" COLS="50" CLASS="scrollie"><?echo htmlspecialchars($DaUser->VitalInfo)?></TEXTAREA>
		</TD>
	</TR>
	<TR>
		<TD><B>Personal Stylesheet:</B></TD>
		<TD><INPUT TYPE="TEXT" NAME="chUserStylesheet" STYLE="width:90%" VALUE="<?echo htmlspecialchars($DaUser->Stylesheet)?>"></TD>
	</TR>
</TABLE>
<BR>
<DIV STYLE="text-align:center"><INPUT TYPE="Submit" VALUE="Update Profile" NAME="UpdateUser"></DIV>
</FORM>
</BODY>
</HTML>
<?}?>
