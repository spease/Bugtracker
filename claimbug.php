<?php include("bugclasses.php")?>
<HTML>
<HEAD>
<?if($CustomCSS){?><LINK REL="Stylesheet" TYPE="Text/CSS" HREF="<?echo$CustomCSS?>"><?}?>
<LINK REL="Stylesheet" TYPE="Text/CSS" HREF="style.css">
<?php

$DaUser = new User($PHP_AUTH_USER);
if($DaUser->IsDeveloper)
{	//1
if($bug)
{	//2
$DaBug = new Bug;

//*****Bug claiming notification page
if($DaBug->Load($bug) && !$DaBug->DeveloperUN && !$DaBug->FixerUN)
{	//3
	$DaBug->DeveloperUN = $PHP_AUTH_USER;
	$DaUser->AddBugToList($bug);
	$DaUser->Save();
	$DaBug->Save();
?>
<meta http-equiv="Refresh" content="1; URL=listbugs.php">
<TITLE>Bug successfully claimed</TITLE>
</HEAD>
<BODY>
<TABLE STYLE="height:100%">
<TR STYLE="height:45%"></TR>
<TR><TD STYLE="text-align:center"><BR><A HREF="listbugs.php">Bug successfully claimed, taking you back to the bugs listing. <BR>Click here if you don't want to wait (Or your browser does not forward you)</A></TD></TR>
<?
}	//3
//*****Error claiming bug page
else
{	//3
?>
<TITLE>Error claiming bug</TITLE>
</HEAD>
<BODY>
<TABLE STYLE="height:100%">
<TR STYLE="height:45%"></TR>
<TR><TD STYLE="text-align:center"><BR><A HREF="listbugs.php">An error occured while attempting to claim the bug: <?echo$Error?><BR>Click here to go back to the bugs listing</A></TD></TR>
<?
}	//3
}	//2
//*****No bug entered
else
{	//2
?>
<TITLE>Error claiming bug</TITLE>
</HEAD>
<BODY>
<TABLE STYLE="height:100%">
<TR STYLE="height:45%"></TR>
<TR><TD STYLE="text-align:center"><A HREF="listbugs.php">No bug ID given<BR>Click here to go back to the bugs listing</A></TD></TR>
<?
}	//2
}	//1
//*****Unauthorized access
else
{	//1
?>
<TITLE>Permission error</TITLE>
</HEAD>
<BODY>
<TABLE STYLE="height:100%">
<TR STYLE="height:45%"></TR>
<TR><TD STYLE="text-align:center"><BR><A HREF="listbugs.php">Only developers may claim bugs <?echo$Error?><BR>Click here to go back to the bugs listing</A></TD></TR>
<?}	//1?>
<TR STYLE="height:45%"></TR>
</TABLE>
</BODY>
</HTML>