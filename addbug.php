<?php
include("bugclasses.php");
if($PHP_AUTH_USER)
{
	$DaUser = new User($PHP_AUTH_USER);
}
if($HTTP_POST_VARS['AddBug'] && $chTitle && $chTitle < 64 && $chDescription && ($chName || $DaUser))
{
	$DaBug=new Bug;
	if($chName)
	{
		$DaBug->ReporterName=$chName;
		$DaBug->ReporterEmail=$chEmail;
	}
	else
	{
		$DaBug->ReporterUN=$PHP_AUTH_USER;
	}
	$DaBug->Title		=StripSlashes($chTitle);
	$DaBug->Version		=StripSlashes($chVersion);
	$DaBug->Level		=StripSlashes($chLevel);
	$DaBug->OS			=StripSlashes($chOS);
	$DaBug->Processor	=StripSlashes($chProcessor);
	$DaBug->Memory		=StripSlashes($chMemory);
	$DaBug->VideoCard	=StripSlashes($chVideoCard);
	$DaBug->SoundCard	=StripSlashes($chSoundCard);
	$DaBug->Description	=StripSlashes($chDescription);
	$DaBug->Save();
?>
<HTML>
<HEAD>
<?if($CustomCSS){?><LINK REL="Stylesheet" TYPE="Text/CSS" HREF="<?echo$CustomCSS?>"><?}?>
<LINK REL="Stylesheet" TYPE="Text/CSS" HREF="style.css">
<meta http-equiv="Refresh" content="1; URL=listbugs.php">
<TITLE>Bug successfully added</TITLE>
</HEAD>
<BODY>
<TABLE STYLE="height:100%">
<TR STYLE="height:45%"><TD></TD></TR>
<TR><TD STYLE="text-align:center"><BR><A HREF="listbugs.php">Bug successfully added, taking you back to the bugs listing. <BR>Click here if you don't want to wait (Or your browser does not forward you)</A></TD></TR>
<TR STYLE="height:45%"><TD></TD></TR>
</TABLE>
</BODY>
</HTML>
<?
}
//Add bug form
else
{
if($HTTP_POST_VARS['AddBug'])
{
	if(!$chTitle)					$Error.="No title specified for bug; enter a title and try re-sending\n<BR>";
	if($chTitle > 64)				$Error.="Title must be less than 64 characters long";
	if(!$chDescription)				$Error.="No description specified for bug; enter a description of the bug and try re-sending\n<BR>";
	if(!$DaUser && !$chName)		$Error.="No name entered; enter one and try re-sending the form\n<BR>";
}
?>
<HTML>
<HEAD>
<?if($CustomCSS){?><LINK REL="Stylesheet" TYPE="Text/CSS" HREF="<?echo$CustomCSS?>"><?}?>
<LINK REL="Stylesheet" TYPE="Text/CSS" HREF="style.css">
<TITLE>Adding Bug/<?echo$ProgramName?> Bug Tracker</TITLE>
<STYLE TYPE="Text/CSS">
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
<?PrintHeader("Adding a Bug");
ListBugs(3);
if($Error)echo"<SPAN STYLE=\"color:orange\">".$Error."</SPAN>";
?>
<TABLE><FORM ACTION="addbug.php" METHOD="post">
	<TR>
		<TD>
			<TABLE CLASS="noborders">
				<TR CLASS="sectionheader"><TD STYLE='text-align:center' COLSPAN="2"><B>Bug Information</B></TD></TR>
				<TR>
					<TD><B>Bug Title:</B></TD>
					<TD><INPUT TYPE="TEXT" NAME="chTitle" VALUE="<?echo$chTitle?>" STYLE="width:400"></TD>
				</TR>
				<TR>
					<TD><B><?echo$ProgramName?> Version:</B></TD>
					<TD>
						<SELECT NAME="chVersion">
<?$Versions = LoadVersions();
foreach($Versions as $Version)
{
	echo"\t\t\t\t\t\t<OPTION VALUE=\"$Version\"";
	if($chVersion==$Version)	echo" SELECTED";
	echo">$Version</OPTION>\n";
}?>
						</SELECT></TD>
				</TR>
				<TR>
					<TD><B>Bug Severity:</B></TD>
					<TD>
						<SELECT NAME="chLevel">
						<OPTION VALUE="Class A"<?if($chLevel=="Class A")echo" SELECTED"?>>Class A</OPTION>
						<OPTION VALUE="Class B"<?if($chLevel=="Class B")echo" SELECTED"?>>Class B</OPTION>
						<OPTION VALUE="Class C"<?if($chLevel=="Class C")echo" SELECTED"?>>Class C</OPTION>
						<OPTION VALUE="Class D"<?if($chLevel=="Class D")echo" SELECTED"?>>Class D</OPTION>
						</SELECT> <A HREF="" OnClick="window.open('levels.html',null,'width=320,height=300,directories=no,location=no,menubar=no,resizable=yes,status=no,toolbars=no'); return false;" STYLE="font-size:10pt">(Class descriptions)</A>
					</TD>
				</TR>
				<TR>
					<TD><B>Description:</B></TD>
					<TD><TEXTAREA NAME="chDescription" ROWS="10" COLS="50"><?echo$chDescription?></TEXTAREA></TD>
				</TR>
			</TABLE>
		</TD>
		<TD STYLE="width:390">
			<TABLE CLASS="AddBugTable">
				<TR CLASS="sectionheader"><TD STYLE='text-align:center' COLSPAN="2"><B>Bug Reporter Information</B></TD></TR>
				<TR>
					<TD style="width:150"><B>Name:</B></TD>
				<?if(!$DaUser->Username){?>
					<TD><INPUT TYPE="TEXT" NAME="chName"></TD>
				</TR>
				<TR>
					<TD><B>Email:</B></TD>
					<TD><INPUT TYPE="TEXT" NAME="chEmail"></TD>
				<?}else{?>
				<TD><?echo $DaUser->Username?></TD>
				<?}?>
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
			</TABLE>
		</TD>
	</TR>
</TABLE>
<BR>
<DIV STYLE="text-align:center"><INPUT TYPE="Submit" VALUE="Add Bug" NAME="AddBug"></DIV>
</FORM>
</BODY>
</HTML>
<?}?>
