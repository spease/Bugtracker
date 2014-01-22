<?php include("bugclasses.php")?>
<HTML>
<HEAD>
<?if($CustomCSS){?><LINK REL="Stylesheet" TYPE="Text/CSS" HREF="<?echo$CustomCSS?>"><?}?>
<LINK REL="Stylesheet" TYPE="Text/CSS" HREF="style.css">
<?php

$DaUser = new User($PHP_AUTH_USER);
if($DaUser->IsDeveloper)
{	//1
$DaBug = new Bug;
if($Versions = LoadVersions())
{	//2
if($HTTP_POST_VARS['AddVersion'] && $chVersion)
{
	//*****Show bug saved successfully
	if(AddVersion(StripSlashes($chVersion)))
	{
?>
<meta http-equiv="Refresh" content="1; URL=addversion.php">
<TITLE>Version added</TITLE>
</HEAD>
<BODY>
<TABLE STYLE="height:100%">
<TR STYLE="height:45%"><TD></TD></TR>
<TR><TD STYLE="text-align:center"><BR><A HREF="listbugs.php">Version successfully added, taking you back to the bug info. <BR>Click here if you don't want to wait (Or your browser does not forward you)</A></TD></TR>
<TR STYLE="height:45%"><TD></TD></TR>
</TABLE>
</BODY>
</HTML>
<?	}
	//*****Show error saving bug
	else
	{
?>
<TITLE>Error saving</TITLE>
</HEAD>
<BODY>
<TABLE STYLE="height:100%">
<TR STYLE="height:45%"><TD></TD></TR>
<TR><TD STYLE="text-align:center"><BR><A HREF="addversion.php">There was an error saving the version info; either try re-sending the form or contact the webmaster<BR>Click here to go to the bug fix form</A></TD></TR>
<TR STYLE="height:45%"><TD></TD></TR>
</TABLE>
</BODY>
</HTML>
<?	}
}	//3
//*****Show form entry screen
else
{	//3
if($HTTP_POST_VARS['FixBug'])
{
	if(!$chVersion)		$Error.="No version entered\n<BR>";
}
?>
<TITLE>Adding a version/<?echo$ProgramName?> Bug Tracker</TITLE>
</HEAD>
<BODY>
<?PrintHeader("Adding Version");
if($Error)echo"<SPAN STYLE=\"color:orange\">".$Error."</SPAN>";?>
<H3>All versions</H3>
<HR>
<?
$Versions = LoadVersions();
if($Versions)
{
	echo"<TABLE style=\"width:100%\">\n";
	foreach($Versions as $Key => $Version)
	{
		//Until versions are figured out.
		//echo "<TR><TD>$Version <A HREF=\"remversion.php?version=$Key\">(Remove)</A></TD></TR>\n";
		echo "<TR><TD>$Version</TD></TR>\n";
	}
	echo"</TABLE>";
}
else
{
	echo "No versions have been added";
}
?>
<HR>
<FORM ACTION="addversion.php" METHOD="POST">
<TABLE STYLE="width:100%">
	<TR CLASS="sectionheader"><TD COLSPAN=2 STYLE="text-align:center"><B>Version Info</B></TD></TR>
	<TR>
		<TD><DIV STYLE="border: 1px"><B>Version Name:</B></TD>
		<TD><INPUT TYPE="TEXT" NAME="chVersion"></TD>
	</TR>
</TABLE>
<DIV STYLE="text-align:center"><INPUT TYPE="Submit" VALUE="Add Version" NAME="AddVersion"></DIV></DIV>
</FORM>
</BODY>
</HTML>
<?}	//3
}	//2
//*****Versions file doesn't exist
else
{	//2
?>
<TITLE>Versions file does not exist</TITLE>
</HEAD>
<BODY>
<TABLE STYLE="height:100%">
<TR STYLE="height:45%"><TD></TD></TR>
<TR><TD STYLE="text-align:center"><BR><A HREF="listbugs.php">Version file does not exist.<BR>Click here to go to back to the bugs listing</A></TD></TR>
<TR STYLE="height:45%"><TD></TD></TR>
</TABLE>
</BODY>
</HTML>
<?}	//2
}	//1
//*****User isn't a developer
else
{	//1
?>
<TITLE>Permission error</TITLE>
</HEAD>
<BODY>
<TABLE STYLE="height:100%">
<TR STYLE="height:45%"><TD></TD></TR>
<TR><TD STYLE="text-align:center"><BR><A HREF="listbugs.php">Only developers may add versions<BR>Click here to go back to the bugs listing</A></TD></TR>
<TR STYLE="height:45%"><TD></TD></TR>
</TABLE>
</BODY>
</HTML>
<?}	//1?>