<?php
include("bugclasses.php");
?>
<HTML>
<HEAD>
<?if($CustomCSS){?><LINK REL="Stylesheet" TYPE="Text/CSS" HREF="<?echo$CustomCSS?>"><?}?>
<LINK REL="Stylesheet" TYPE="Text/CSS" HREF="style.css">
<TITLE><?
$DaBug = new Bug;
if($bug && $DaBug->Load($bug))
{
	$DaUser=new User($PHP_AUTH_USER);
	echo"Viewing Bug ".$bug.": ".$DaBug->Title."/$ProgramName Bug Tracker";?></TITLE>
</HEAD>
<BODY>
<?PrintHeader("Viewing Bug '$DaBug->Title'")?>
<TABLE>
<TR><TD STYLE="vertical-align:top">
	<TABLE CLASS="nofirstborders">
		<TR CLASS="sectionheader"><TD STYLE='border-width:0;text-align:center' COLSPAN="2"><B>Bug Info</B></TD></TR>
		<TR><TD STYLE=';width:150'><B>Title:</B></TD><TD><?echo$DaBug->Title?></TD></TR>
		<TR><TD><B>Status:</B></TD><TD><?echo$DaBug->Status?></TD></TR>
		<TR><TD><B>Severity:</B></TD><TD><?echo$DaBug->Level?></TD></TR>
		<TR><TD><B>Appeared in:</B></TD><TD><?echo$DaBug->Version?></TD></TR>
		<TR><TD><B>Reported on:</B></TD><TD><?if(!$DaBug->ReportDate){echo"";}else{echo Date("m-d-Y H:i",$DaBug->ReportDate);}?></TD></TR>
		<TR><TD><B>Description:</B></TD><TD><?echo $DaBug->Description?></TD></TR>
		<TR><TD COLSPAN="2"></TD></TR>
		<?if($DaBug->Status=="Fixed"){?>
		<TR CLASS="sectionheader"><TD STYLE='border-width:0;text-align:center' COLSPAN="2"><B>Fix Info</B></TD></TR>
		<TR><TD STYLE='border-width:0'><B>Fix Description:</B></TD><TD><?echo stripslashes($DaBug->FixDescription)?></TD></TR>
		<?}?>
	</TABLE>
</TD><TD STYLE="width:325px">
	<TABLE CLASS="nofirstborders">
		<TR CLASS="sectionheader"><TD STYLE='text-align:center' COLSPAN="2"><B>Reporter Info</B></TD></TR>
<?if($DaBug->ReporterUN){?>
		<TR><TD STYLE='width:150'><B>Name:</B></TD><TD><A HREF="viewuser.php?user=<?echo rawurlencode($DaBug->ReporterUN)?>"><?echo$DaBug->ReporterUN?></A></TD></TR>
<?}else{?>
		<TR><TD><B>Name:</B></TD><TD><A HREF="mailto:<?echo$DaBug->ReporterEmail?>"><?echo$DaBug->ReporterName?></A></TD></TR>
<?}?>
		<TR><TD><B>Reported on:</B></TD><TD><?if(!$DaBug->ReportDate){echo"";}else{echo Date("m-d-Y H:i",$DaBug->ReportDate);}?></TD></TR>
		<TR><TD><B>OS:</B></TD><TD><?echo$DaBug->OS?></TD></TR>
		<TR><TD><B>Processor:</B></TD><TD><?echo$DaBug->Processor?></TD></TR>
		<TR><TD><B>Memory:</B></TD><TD><?echo$DaBug->Memory?> MB</TD></TR>
		<TR><TD><B>Video Card:</B></TD><TD><?echo$DaBug->VideoCard?></TD></TR>
		<TR><TD><B>Sound Card:</B></TD><TD><?echo$DaBug->SoundCard?></TD></TR>
		<TR><TD COLSPAN="2"></TD></TR>
<?if($DaBug->Status=="Claimed" || $DaBug->Status=="Fixed"){?>
		<TR CLASS="sectionheader"><TD STYLE='text-align:center' COLSPAN="2"><B>Assigned Developer Info</B></TD></TR>
<?if($DaBug->DeveloperUN){?>
		<TR><TD><B>Name:</B></TD><TD><A HREF="viewuser.php?user=<?echo rawurlencode($DaBug->DeveloperUN)?>"><?echo$DaBug->DeveloperUN?></A></TD></TR>
<?}else{?>
		<TR><TD><B>Name:</B></TD><TD><A HREF="mailto:<?echo$DaBug->DeveloperEmail?>"><?echo$DaBug->DeveloperName?></A></TD></TR>
<?}?>
		<TR><TD><B>Claimed on:</B></TD><TD><?if(!$DaBug->ClaimDate){echo"";}else{echo Date("m-d-Y H:i",$DaBug->ClaimDate);}?></TD></TR>
<?}?>
		<?if($DaBug->Status=="Fixed"){?>
		<TR CLASS="sectionheader"><TD STYLE='text-align:center' COLSPAN="2"><B>Fix information</B></TD></TR>
		<?if($DaBug->FixerUN){?>
		<TR><TD><B>Fixed by:</B></TD><TD><A HREF="viewuser.php?user=<?echo rawurlencode($DaBug->FixerUN)?>"><?echo$DaBug->FixerUN?></A></TD></TR>
		<?}else{?>
		<TR><TD><B>Fixed by:</B></TD><TD><A HREF="mailto:<?echo$DaBug->FixerEmail?>"><?echo$DaBug->FixerName?></A></TD></TR>
		<?}?>
		<TR><TD><B>Fixed on:</B></TD><TD><?if(!$DaBug->FixDate){echo"";}else{echo Date("m-d-Y H:i",$DaBug->FixDate);}?></TD></TR>
		<TR><TD><B>Fixed in version:</B></TD><TD><?echo$DaBug->FixVersion?></TD></TR>
		<?}?>
	</TABLE>
</TD></TR>
<?
if($DaBug->Comments)
{
	echo"<TR CLASS=\"sectionheader\"><TD STYLE='border-width:0;text-align:center' COLSPAN=\"2\"><B>Comments</B></TD></TR>";
	foreach($DaBug->Comments as $Comment)
	{
		echo"<TR><TD>";
		if(!$Comment[1] && UserExists($Comment[0]))
		{
			echo"<A HREF=\"viewuser.php?user=$Comment[0]\">$Comment[0]</A>";
		}
		elseif(!$Comment[1])
		{
			echo $Comment[0];
		}
		else
		{
			echo"<A HREF=\"mailto:".htmlspecialchars($Comment[1])."\">".htmlspecialchars($Comment[0])."</A>";
		}
		echo"\n<BR>".Date("m-d-Y H:i",$Comment[2])."</TD>\n<TD colspan=2>".htmlspecialchars($Comment[3])."</TD></TR>\n";
	}
}
?>
</TABLE>
<HR>
<TABLE CLASS="noborders">
	<TR>
		<TD STYLE='width:20%'><A HREF="listbugs.php"><--Return to bugs listing</A></TD>
		<TD STYLE='width:20%'><?$prevbug = $bug-1;if(BugExists($prevbug)){?><A HREF="viewbug.php?bug=<?echo($prevbug)?>"><- Previous bug</A><?}?></TD>
		<TD STYLE='width:20%;text-align:center'><A HREF="addcomment.php?bug=<?echo$bug?>">Add Comment</A></TD>
		<TD STYLE='width:20%;text-align:right'><?$nextbug = $bug+1;if(BugExists($nextbug)){?><A HREF="viewbug.php?bug=<?echo($nextbug)?>">Next bug -></A><?}?></TD>
		<TD STYLE='width:20%'></TD>
	</TR>
	<TR>
		<TD COLSPAN=2></TD>
		<TD STYLE='width:20%;text-align:center'><?if($DaUser->IsDeveloper && !$DaBug->DeveloperUN && !$DaBug->FixerUN){?><A HREF="claimbug.php?bug=<?echo$bug?>">Claim bug</A><?}elseif($PHP_AUTH_USER==$DaBug->DeveloperUN && !$DaBug->FixerUN){?><A HREF="fixbug.php?bug=<?echo$bug?>">Mark bug as fixed</A><?}?></TD>
		<TD COLSPAN=2></TD>
	</TR>
</TABLE>
<?}elseif($bug){?>
Bug Not Found</TITLE>
<H2>MechWar3D Bug Tracker</H2>
<H3>Bug Not Found</H2>
The specified bug was not found. For a full listing of valid bugs, click <A HREF="listbugs.php">here</A>.
<?}else{?>
No user specified</TITLE>
</HEAD>
<H2>MechWar3D Bug Tracker</H2>
No user specified to view. For a full listing of valid bugs, click <A HREF="listbugs.php">here</A>.
<?}?>
</BODY>
</HTML>
