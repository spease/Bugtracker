<?php include("bugclasses.php")?>
<HTML>
<HEAD>
<?if($CustomCSS){?><LINK REL="Stylesheet" TYPE="Text/CSS" HREF="<?echo$CustomCSS?>"><?}?>
<LINK REL="Stylesheet" TYPE="Text/CSS" HREF="style.css">
<?php

$DaUser = new User($PHP_AUTH_USER);
$DaBug = new Bug;
if($bug && $DaBug->Load($bug))
{	//2
if($HTTP_POST_VARS['AddComment'] && ($PHP_AUTH_USER || (trim($chUsername) && trim($chEmail))))
{	//3
	if($PHP_AUTH_USER)
	{
		$DaBug->AddComment($PHP_AUTH_USER, StripSlashes($chComment));
	}
	else
	{
		$DaBug->AddComment(StripSlashes($chUsername), StripSlashes($chComment), StripSlashes($chEmail));
	}
	
	//*****Show bug saved successfully
	if($DaBug->Save())
	{
?>
<meta http-equiv="Refresh" content="1; URL=viewbug.php?bug=<?echo$bug?>">
<TITLE>Comment added</TITLE>
</HEAD>
<BODY>
<TABLE STYLE="height:100%">
<TR STYLE="height:45%"><TD></TD></TR>
<TR><TD STYLE="text-align:center"><BR><A HREF="viewbug.php?bug=<?echo$bug?>">Comment successfully added, taking you back to the bug info <BR>Click here if you don't want to wait (Or your browser does not forward you)</A></TD></TR>
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
<TR><TD STYLE="text-align:center"><BR><A HREF="addcomment.php?bug=<?echo$bug?>">There was an error saving the bug; either try re-sending the form or contact the webmaster<BR>Click here to go to the add comment form</A></TD></TR>
<TR STYLE="height:45%"><TD></TD></TR>
</TABLE>
</BODY>
</HTML>
<?	}
}	//3
//*****Show form entry screen
else
{	//3
if($HTTP_POST_VARS['AddComment'])
{
	if(!$chComment)	$Error .= "No comment entered; enter one and try re-sending\n<BR>";
	else
	{
		if(!$chUsername) $Error .= "No username entered; enter one and try re-sending\n<BR>";
		if(!$chEmail) $Error .= "No username entered; enter one and try re-sending\n<BR>";
	}
}
?>
<TITLE>Adding comment to '<?echo$DaBug->Title?>'/<?echo$ProgramName?> Bug Tracker</TITLE>
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
<?PrintHeader("Adding comment to '$DaBug->Title'");
if($Error)echo"<SPAN STYLE=\"color:orange\">".$Error."</SPAN>";?>
<TABLE><FORM ACTION="addcomment.php?bug=<?echo$bug?>" METHOD="POST">
	<TR>
		<TD>
			<TABLE CLASS="noborders">
				<TR>
					<TD><B>Name:</B></TD>
<?if(!$DaUser->Load($PHP_AUTH_USER)){?>
					<TD><INPUT TYPE="TEXT" NAME="chUsername"></TD>
				</TR>
				<TR>
					<TD><B>Email:</B></TD>
					<TD><INPUT TYPE="TEXT" NAME="chEmail"></TD>
<?}else{?>
					<TD><?echo$DaUser->Username?></TD>
<?}?>
				</TR>
				<TR>
					<TD><B>Comment:</B></TD>
					<TD><TEXTAREA NAME="chComment" ROWS="10" COLS="50"><?echo StripSlashes($chComment)?></TEXTAREA></TD>
				</TR>
			</TABLE>
		</TD>
		<TD>
			<DIV STYLE="text-align:center" CLASS="sectionheader"><B>Bug Description:</B></DIV>
			<BR><?echo StripSlashes($DaBug->Description)?>
		</TD>
	</TR>
</TABLE>
<BR>
<DIV STYLE="text-align:center"><INPUT TYPE="Submit" VALUE="Add Comment" NAME="AddComment"></DIV>
</FORM>
</BODY>
</HTML>
<?}	//3
}	//2
//*****Bug couldn't be loaded
elseif($bug)
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
?>
