<?php include("bugclasses.php")?>
<HTML>
<HEAD>
<?if($CustomCSS){?><LINK REL="Stylesheet" TYPE="Text/CSS" HREF="<?echo$CustomCSS?>"><?}?>
<LINK REL="Stylesheet" TYPE="Text/CSS" HREF="style.css">
<?php

$DaUser = new User($PHP_AUTH_USER);
if($bug)
{	//1
$DaBug = new Bug;

//*****Bug claiming notification page
if($DaBug->Load($bug) && $DaBug->DeveloperUN == $PHP_AUTH_USER && !$DaBug->FixerUN)
{	//2
	$DaBug->DeveloperUN = NULL;
	$DaUser->RemoveBugFromList($bug);
	$DaUser->Save();
	$DaBug->Save();
?>
<meta http-equiv="Refresh" content="1; URL=listbugs.php">
<TITLE>Bug successfully unclaimed</TITLE>
</HEAD>
<BODY>
<TABLE STYLE="height:100%">
<TR STYLE="height:45%"></TR>
<TR><TD STYLE="text-align:center"><BR><A HREF="listbugs.php">Bug successfully unclaimed, taking you back to the bugs listing. <BR>Click here if you don't want to wait (Or your browser does not forward you)</A></TD></TR>
<?
}	//2
elseif($DaBug->DeveloperUN != $PHP_AUTH_USER)
{
?>
<TITLE>Error unclaiming bug</TITLE>
</HEAD>
<BODY>
<TABLE STYLE="height:100%">
<TR STYLE="height:45%"></TR>
<TR><TD STYLE="text-align:center"><BR><A HREF="listbugs.php">This bug is not claimed by you. <BR>Click here to go back to the bugs listing</A></TD></TR>
<?
}	//2
//*****Error unclaiming bug page
else
{	//2
?>
<TITLE>Error unclaiming bug</TITLE>
</HEAD>
<BODY>
<TABLE STYLE="height:100%">
<TR STYLE="height:45%"></TR>
<TR><TD STYLE="text-align:center"><BR><A HREF="listbugs.php">An error occured while attempting to unclaim the bug: <?echo$Error?><BR>Click here to go back to the bugs listing</A></TD></TR>
<?
}	//2
}	//1
//*****No bug entered
else
{	//1
?>
<TITLE>Error unclaiming bug</TITLE>
</HEAD>
<BODY>
<TABLE STYLE="height:100%">
<TR STYLE="height:45%"></TR>
<TR><TD STYLE="text-align:center"><A HREF="listbugs.php">No bug ID given<BR>Click here to go back to the bugs listing</A></TD></TR>
<?
}	//2
?>
<TR STYLE="height:45%"></TR>
</TABLE>
</BODY>
</HTML>