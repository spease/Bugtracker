<?php include("bugclasses.php"); ?>
<HTML>
<HEAD>
<?if($CustomCSS){?><LINK REL="Stylesheet" TYPE="Text/CSS" HREF="<?echo$CustomCSS?>"><?}?>
<LINK REL="Stylesheet" TYPE="Text/CSS" HREF="style.css">
<?php

$DaUser = new User($PHP_AUTH_USER);
if($DaUser->IsDeveloper)
{	//1
$DaBug = new Bug;
if($bug && $DaBug->Load($bug) && !$DaBug->FixerUN && $DaBug->Status != "Fixed")
{	//2
if($HTTP_POST_VARS['FixBug'] && $chFixDescription && ($chName || $PHP_AUTH_USER))
{	//3
	if(!$chFixerName)	$DaBug->FixerUN 	= $PHP_AUTH_USER;
	else
	{
		$DaBug->FixerName	= StripSlashes($chFixerName);
		$DaBug->FixerEmail	= StripSlashes($chFixerEmail);
	}
	
	$DaBug->FixVersion		=	StripSlashes($chFixVersion);
	$DaBug->FixDescription	=	StripSlashes($chFixDescription);
	
	//*****Show bug saved successfully
	if($DaBug->Save())
	{
?>
<meta http-equiv="Refresh" content="1; URL=viewbug.php?bug=<?echo$bug?>">
<TITLE>Bug marked as fixed</TITLE>
</HEAD>
<BODY>
<TABLE STYLE="height:100%">
<TR STYLE="height:45%"><TD></TD></TR>
<TR><TD STYLE="text-align:center"><BR><A HREF="viewbug.php?bug=<?echo$bug?>">Bug successfully marked as fixed, taking you back to the bug info. <BR>Click here if you don't want to wait (Or your browser does not forward you)</A></TD></TR>
<TR STYLE="height:45%"><TD></TD></TR>
</TABLE>
</BODY>
</HTML>
<?	}
	//*****Show error saving bug
	else
	{
?>
<TITLE>Error saving bug</TITLE>
</HEAD>
<BODY>
<TABLE STYLE="height:100%">
<TR STYLE="height:45%"><TD></TD></TR>
<TR><TD STYLE="text-align:center"><BR><A HREF="fixbug.php?bug=<?echo$bug?>">There was an error saving the bug; either try re-sending the form or contact the webmaster<BR>Click here to go to the bug fix form</A></TD></TR>
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
	if(!$chDescription)				$Error.="No description entered about the fix; enter a description and try re-submitting\n<BR>";
	if(!$chName && !$PHP_AUTH_USER)	$Error.="No name entered; enter one and try re-sending\n<BR>";
}
?>
<TITLE>Marking Bug '<?echo$DaBug->Title?>' as Fixed/<?echo$ProgramName?> Bug Tracker</TITLE>
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
<?PrintHeader("Marking Bug '$DaBug->Title' as Fixed");
if($Error)echo"<SPAN STYLE=\"color:orange\">".$Error."</SPAN>";?>
<TABLE><FORM ACTION="fixbug.php?bug=<?echo$bug?>" METHOD="POST">
	<TR>
		<TD>
			<TABLE CLASS="noborders">
				<TR>
					<TD><B>Name:</B></TD>
<?if(!$DaUser->Load($PHP_AUTH_USER)){?>
					<TD><INPUT TYPE="TEXT" NAME="chName"></TD>
				</TR>
				<TR>
					<TD><B>Email:</B></TD>
					<TD><INPUT TYPE="TEXT" NAME="chEmail"></TD>
<?}else{?>
					<TD><?echo$DaUser->Username?></TD>
<?}?>
				</TR>
				<TR>
					<TD><B>Fixed in version:</B></TD>
					<TD>
						<SELECT NAME="chFixVersion">
<?$Versions = LoadVersions();
foreach($Versions as $Version)
{
	echo"\t\t\t\t\t\t<OPTION VALUE=\"".htmlspecialchars($Version)."\"";
	if($chVersion==$Version)	echo" SELECTED";
	echo">$Version</OPTION>\n";
}?>
						</SELECT> <A HREF="addversion.php">(Add version)</A>
					</TD>
				</TR>
				<TR>
					<TD><B>Description of fix:</B></TD>
					<TD><TEXTAREA NAME="chFixDescription" ROWS="10" COLS="50"><?echo StripSlashes($chFixDescription)?></TEXTAREA></TD>
				</TR>
			</TABLE>
		</TD>
		<TD>
			<SPAN STYLE="width:100%;background-color:#222222;text-align:center;"><B>Bug Description:</B></SPAN>
			<BR><?echo StripSlashes($DaBug->Description)?>
		</TD>
	</TR>
</TABLE>
<BR>
<DIV STYLE="text-align:center"><INPUT TYPE="Submit" VALUE="Mark Bug as Fixed" NAME="FixBug"></DIV>
</FORM>
</BODY>
</HTML>
<?}	//3
}	//2
//*****Bug couldn't be loaded
elseif($bug && !$DaBug->FixerUN && $DaBug->Status != "Fixed")
{	//2
?>
<TITLE>Error loading bug</TITLE>
</HEAD>
<BODY>
<TABLE STYLE="height:100%">
<TR STYLE="height:45%"><TD></TD></TR>
<TR><TD STYLE="text-align:center"><BR><A HREF="viewbug.php?bug=<?echo$bug?>">There was an error loading the bug<BR>Click here to go to back to the bug information screen</A></TD></TR>
<TR STYLE="height:45%"><TD></TD></TR>
</TABLE>
</BODY>
</HTML>
<?}	//2
//*****Bug is already fixed
elseif($bug)
{	//2
?>
<TITLE>Bug already fixed</TITLE>
</HEAD>
<BODY>
<TABLE STYLE="height:100%">
<TR STYLE="height:45%"><TD></TD></TR>
<TR><TD STYLE="text-align:center"><BR><A HREF="viewbug.php?bug=<?echo$bug?>">Bug <?echo$bug?> is already fixed!<BR>Click here to go to back to the bug information screen</A></TD></TR>
<TR STYLE="height:45%"><TD></TD></TR>
</TABLE>
</BODY>
</HTML>
<?}	//2
//*****Bug not specified
else
{	//2
?>
<TITLE>Bug not specified</TITLE>
</HEAD>
<BODY>
<TABLE STYLE="height:100%">
<TR STYLE="height:45%"><TD></TD></TR>
<TR><TD STYLE="text-align:center"><BR><A HREF="listbugs.php">No bug specified.<BR>Click here to go to back to the bugs listing</A></TD></TR>
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
<TR><TD STYLE="text-align:center"><BR><A HREF="listbugs.php">Only developers may mark bugs as fixed<BR>Click here to go back to the bugs listing</A></TD></TR>
<TR STYLE="height:45%"><TD></TD></TR>
</TABLE>
</BODY>
</HTML>
<?}	//1?>