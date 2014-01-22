<?php
/*Bug tracker install
*
* Description:
*	Sets up directories and checks that it is possible to run the bug tracker
*/
include("bugclasses.php");
?>
<HTML>
<HEAD>
<?if($CustomCSS){?><LINK REL="Stylesheet" TYPE="Text/CSS" HREF="<?echo$CustomCSS?>"><?}?>
<LINK REL="Stylesheet" TYPE="Text/CSS" HREF="style.css">
<TITLE>Bugbuster Installer (<?echo$ProgramName?>)</TITLE>
</HEAD>
<BODY>
<H1>Bugbuster Install (<?echo$ProgramName?>)</H1>
<?php
if(!$HTTP_POST_VARS['AddAdmin'])
{
/*****STAGE 1*****/
$TaskErrors = 0;

function TaskStart($Task)
{
	global $CurrentTask;
	$CurrentTask = $Task;
}

function TaskCompleted($Success = true, $Error = NULL)
{
	global $CurrentTask;
	global $TaskErrors;
	
	$Output = "\n\t<tr><td colspan=2 style=\"border:0px\"><hr></td></tr>";
	$Output.= "\n\t<TR><TD>$CurrentTask</TD><TD>";
	if($Success)
	{
		$Output.= "<SPAN STYLE=\"color:green\">SUCCESSFUL";
	}
	else
	{
		$Output.= "<SPAN STYLE=\"color:red\">FAILED";
		$TaskErrors++;
	}
	$Output.= "</TD></TR>";
	
	if($Error)
	{
		$Output.= "\n\t<TR><TD colspan=2 STYLE=\"text-align:center\">$Error</TD></TR>";
	}
	
	echo $Output;
	$CurrentTask="";
}
?>
<H2>Part One</H2>
<HR>
<TABLE STYLE="width:80%;margin:auto">
	<TR><TD><B>Task</B></TD><TD STYLE="width:100px"><B>Status</B></TD></TR><?php
//Bugs directory
TaskStart("Creating Bugs Directory");
if(!trim($BugsDir))
{
	TaskCompleted(false, "\$BugsDir not set; check bugclasses.php for more info");
}
elseif(file_exists($BugsDir))
{
	$DirInfo = posix_getpwuid(fileowner($BugsDir));
	if($DirInfo['name'] == trim(shell_exec('whoami')) && is_dir($BugsDir))
	{
		TaskCompleted();
	}
	elseif(!is_dir($BugsDir))
	{
		TaskCompleted(false, "'$BugsDir' already exists and is not a directory");
	}
	else
	{
		TaskCompleted(false, "File or folder named '$BugsDir' already exists");
	}
}
else
{
	if(@mkdir($BugsDir))
	{
		TaskCompleted();
	}
	else
	{
		TaskCompleted(false, "Permissions not set correctly");
	}
}

//Bugs directory permissions
TaskStart("Setting Bugs Directory Permissions");
$DirInfo = posix_getpwuid(fileowner($BugsDir));
if(!trim($BugsDir))
{
	TaskCompleted(false, "\$BugsDir not set; check bugclasses.php for more info");
}
elseif(!file_exists($BugsDir))
{
	TaskCompleted(false, "'$BugsDir' does not exist");
}
elseif($DirInfo['name'] != trim(shell_exec('whoami')))
{
	TaskCompleted(false, "Owner of '$BugsDir' is incorrect.");
}
elseif(@chmod($BugsDir, 0707))
{
	TaskCompleted();
}
else
{
	TaskCompleted(false);
}

//Users directory
TaskStart("Creating Users Directory");
if(!trim($UsersDir))
{
	TaskCompleted(false, "\$UsersDir not set; check bugclasses.php for more info");
}
elseif(file_exists($UsersDir))
{
	$DirInfo = posix_getpwuid(fileowner($UsersDir));
	if($DirInfo['name'] == trim(shell_exec('whoami')) && is_dir($UsersDir))
	{
		TaskCompleted();
	}
	elseif(!is_dir($UsersDir))
	{
		TaskCompleted(false, "'$UsersDir' already exists and is not a directory");
	}
	else
	{
		TaskCompleted(false, "File or folder named '$UsersDir' already exists");
	}
}
else
{
	if(@mkdir($UsersDir))
	{
		TaskCompleted();
	}
	else
	{
		TaskCompleted(false, "Permissions not set correctly");
	}
}

//Users directory permissions
TaskStart("Setting User Directory Permissions");
$DirInfo = posix_getpwuid(fileowner($UsersDir));
if(!trim($UsersDir))
{
	TaskCompleted(false, "\$UsersDir not set; check bugclasses.php for more info");
}
elseif(!file_exists($UsersDir))
{
	TaskCompleted(false, "'$UsersDir' does not exist");
}
elseif($DirInfo['name'] != trim(shell_exec('whoami')))
{
	TaskCompleted(false, "Owner of '$UsersDir' is incorrect.");
}
elseif(@chmod($UsersDir, 0707))
{
	TaskCompleted();
}
else
{
	TaskCompleted(false);
}
?>
</TABLE>
<HR>
<?if($TaskErrors){?>
<DIV STYLE="text-align:right;color:red">FAILED</DIV>
<?}else{?>
<DIV STYLE="text-align:right;color:green">SUCCESSFUL</DIV>
<?/*****Stage 2*****/?>
<BR><H2>Part Two</H2>
<HR>
<TABLE STYLE="width:80%;margin:auto">
	<TR><TD><B>Task</B></TD><TD STYLE="width:100px"><B>Status</B></TD></TR><?php
TaskStart("Creating password file");
if(!trim($PasswordFile))
{
	TaskCompleted(false, "\$PasswordFile not set; check bugclasses.php for more info");
}
elseif(file_exists($PasswordFile))
{
	$FileInfo = posix_getpwuid(fileowner($PasswordFile));
	if($DirInfo['name'] != trim(shell_exec('whoami')))
	{
		TaskCompleted(false, "Owner of '$PasswordFile' is incorrect");
	}
	elseif(filesize($PasswordFile) > 0)
	{
		TaskCompleted(false, "'$PasswordFile' is not empty");
	}
	elseif(is_dir($PasswordFile))
	{
		TaskCompleted(false, "'$PasswordFile' already exists and is a directory");
	}
	else
	{
		TaskCompleted();
	}
}
elseif(!is_dir(dirname($PasswordFile)))
{
	TaskCompleted(false, "Password file cannot be created inside another file");
}
elseif(!($fp = fopen($PasswordFile, "w")))
{
	TaskCompleted(false, "Permissions not set correctly");
}
else
{
	TaskCompleted();
	fclose($fp);
}

TaskStart("Setting password file permissions");
$FileInfo = posix_getpwuid(fileowner($PasswordFile));
if(!trim($PasswordFile))
{
	TaskCompleted(false, "\$PasswordFile not set; check bugclasses.php for more info");
}
elseif(!file_exists($PasswordFile))
{
	TaskCompleted(false, "'$PasswordFile' does not exist");
}
elseif($FileInfo['name'] != trim(shell_exec('whoami')))
{
	TaskCompleted(false, "Owner of '$PasswordFile' is incorrect.");
}
elseif(@chmod($PasswordFile, 0777))
{
	TaskCompleted();
}
else
{
	TaskCompleted(false);
}
?>
</TABLE>
<HR>
<?if($TaskErrors){?>
<DIV STYLE="text-align:right;color:red">FAILED</DIV>
<?}else{?>
<DIV STYLE="text-align:right;color:green">SUCCESSFUL</DIV>
<?/*****Stage 3*****/?>
<BR><H2>Part Three</H2>
<HR>
<FORM ACTION="install.php" METHOD="post">
	<TABLE>
		<TR><TD><B>Admin username:</B></TD><TD><INPUT TYPE="TEXT" NAME="chAdminName"></TD></TR>
		<TR><TD><B>Admin password:</B></TD><TD><INPUT TYPE="PASSWORD" NAME="chAdminPass"></TD></TR>
		<TR><TD><B>Confirm admin password:</B></TD><TD><INPUT TYPE="PASSWORD" NAME="chConfAdminPass"></TD></TR>
	</TABLE>
	<HR>
	<DIV STYLE="text-align:right"><INPUT TYPE="Submit" VALUE="Add Admin account >>" NAME="AddAdmin"></DIV>
</FORM>
<?php
}
}
}
else
{/*****Admin setup*****/
?>
<?php if(!trim($HTTP_POST_VARS['chAdminName']) || !trim($HTTP_POST_VARS['chAdminPass']) || $HTTP_POST_VARS['chAdminPass'] != $HTTP_POST_VARS['chConfAdminPass'])
{
?>
<H2>Part Three</H2>
<HR>
Admin account name or password not entered; please fill in all fields correctly to continue.
<FORM ACTION="install.php" METHOD="post">
	<TABLE>
		<TR><TD><B>Admin username:</B></TD><TD><INPUT TYPE="TEXT" NAME="chAdminName" VALUE="<?echo$HTTP_POST_VARS['chAdminName']?>"></TD></TR>
		<TR><TD><B>Admin password:</B></TD><TD><INPUT TYPE="PASSWORD" NAME="chAdminPass" VALUE="<?echo$HTTP_POST_VARS['chAdminPass']?>"></TD></TR>
		<TR><TD><B>Confirm admin password:</B></TD><TD><INPUT TYPE="PASSWORD" NAME="chAdminPass" VALUE="<?echo$HTTP_POST_VARS['chConfAdminPass']?>"></TD></TR>
	</TABLE>
	<HR>
	<DIV STYLE="text-align:right"><INPUT TYPE="Submit" VALUE="Add Admin account >>" NAME="AddAdmin"></DIV>
</FORM>
<?php 
}
else
{
$DaUser = new User();
$DaUser->Username = $HTTP_POST_VARS['chAdminName'];
$DaUser->IsAdmin = true;
$DaUser->Add($HTTP_POST_VARS['chAdminPass']);
$DaUser->Save();
echo $Error;
?>
<H2>Complete</H2>
<HR>
Congratulations! The admin account was set up successfully. BugBuster is now ready for operation.
<HR>
<DIV STYLE="text-align:right;color:green">SUCCESSFUL</DIV>
<?php
}
}
?>
</BODY>
</HTML>