<?php
/*Bug classes file(bugclasses.php)
* 
* Description:
* 	Contains the core of the bug tracker, should be included in every file
* 
* 
* User-settable variables:
* 
* 	ProgramName - What the name of the program is that's being tracked
* 	BugsDir - Where bug files are to be saved
* 	UserDir - Where user files are to be saved
*	UseApache - Whether to use Apache authentication or not
* 	PasswordFile - Where a .htpasswd-style password file can be found
* 	UserListFields - Order that user variables should be displayed when viewing a list of users
* 	BugListFields - Same as UserListFields, only with bugs.
*	CustomCSS - Custom CSS file to use in addition to the bug tracker one
*
*
* Version functions:
*	LoadVersions() - Loads the versions into an array
*	AddVersion(Version Name) - Adds a version to the versions list
*
*
* Classes:
* 
* 	User
* 		Load(User ID or Username) - Loads a user's info from a file
*		AddBugToList($ID) - Adds a bug to user's claimed bugs, if they are a developer
*		RemoveBugFromList($ID) - Removes a bug from user's claimed bugs
* 		ListView() - Outputs a single row to be used in a table containing basic user info
* 		Save() - Saves a user's info to a file
* 		Validate(Password) - Checks a supplied password against an Apache-style password file
* 
* 	Bug
* 		Load(Bug ID) - Loads a bug from a file
* 		ListView() - Outputs a single row for a table containing basic bug info
* 		Save() - Saves the bug's information to a file
*
*
* Functions:
*
*	EchoBugHeaders()
*	ListClaimedBugs()
* 	ListBugs(Maximum number to list or blank for all)-Generates a list view of all the bugs in the bugs directory
* 	ListUsers - Lists all valid users
* 	PrintHeader(Page name) - Prints the bug tracker's header, along with the page name if specified
*	SwitchUser(Username, Password) - Switches the current user to Username if Password is correct
*/

//What the name of the program is that's being tracked
$ProgramName="/etc.com";

//Name and/or path to the versions file
$VersionsFile="versions.ver.php";

//Where the bug files should be saved/loaded; use "." for the current directory, NO TRAILING SLASH
$BugsDir="bugs";

//Where to look for User files; use "." for the current director, NO TRAILING SLASH
$UsersDir="users";

//Use .htaccess auth method if available?
$UseApache=true;

//Path to an Apache-style password file from this script for using the Validate function
$PasswordFile=".htpasswd";

//In what order should the user variables be displayed in list view?
$UserListFields=array("Username","RealName","OS","VideoCard","SoundCard");

//Order of variables in Bug listview
$BugListFields=array("ID","Title","ReporterUN","ReportDate","Status");

//Path to a custom CSS file
$CustomCSS = "http://www.slashetc.net/mainlayout.css";

//**********NOTHING BELOW THIS LINE SHOULD BE MODIFIED**********\\
//**************************************************************\\

/*****Startup stuffs*****/
if(!$PHP_AUTH_USER) $UseApache = false;
if($UseApache)
{
	$CurrentUser = new User($PHP_AUTH_USER);
}
else
{
	session_name("BugtrackerLogin");
	session_start();
	
	$CurrentUser = new User($_SESSION['Username']);
	if($CurrentUser && $CurrentUser->Password != $_SESSION['Password'])
	{
		unset($CurrentUser);
	}
}

if(trim($CurrentUser->Stylesheet))
{
	$CustomCSS = $CurrentUser->Stylesheet;
}

/*****Handy functions*****/

function RemSlashes($String)
{
	return str_replace("\'", "'",$String);
}

/*****Version handling*****/
function LoadVersions()
{
	global $VersionsFile;
	$VersionFile = fopen($VersionsFile,"r");
	if($VersionFile)
	{
		$VersionContents = fread($VersionFile,filesize($VersionsFile));
		$VersionContents = strtr($VersionContents,"\r\n","\n");
		$VersionContents = strtr($VersionContents,"\r","\n");
		$Versions = explode("\n",$VersionContents);
		
		fclose($VersionFile);
		
		return $Versions;
	}
	else
	{
		$Error .= "Error opening versions file.\n";
		return false;
	}
}

function AddVersion($VersionName)
{
	global $VersionsFile;
	$VersionFile = fopen($VersionsFile,"a+");
	if($VersionFile)
	{
		fwrite($VersionFile, $VersionName."\n");
		
		fclose($VersionFile);
		
		return true;
	}
	else
	{
		$Error .= "Error opening versions file.\n";
		return false;
	}
}

/*****User class*****/

class User
{
	function User($UN=NULL)
	{
		//General User info
		$this->Username;
		$this->Password;
		$this->CryptPW;
		$this->RealName;
		$this->Email;
		$this->VitalInfo;
		
		//Software
		$this->OS;
		
		//Hardware
		$this->Processor;
		$this->Memory;
		$this->VideoCard;
		$this->SoundCard;
		
		//Development
		$this->IsDeveloper;		//Bool value to indicate whether user has access to developer pages
		$this->ClaimedBugs;	//An array for claimed bug IDs
		
		//Administration
		$this->IsAdmin;		//Bool value to indicate adminness
		
		if($UN)
		{
			return $this->Load($UN);
		}
		return true;
	}
	function Load($UN)
	{
		global $UsersDir;
		global $Error;
		
		if(file_exists("$UsersDir/$UN.usr.php"))
		{
			include("$UsersDir/$UN.usr.php");
			
			$this->Username=	RemSlashes($Username);
			$this->CryptPW=		RemSlashes($CryptPW);
			$this->RealName=	RemSlashes($RealName);
			$this->Email=		RemSlashes($Email);
			$this->VitalInfo=	RemSlashes($VitalInfo);
			$this->Stylesheet=	RemSlashes($Stylesheet);
			
			$this->OS=			RemSlashes($OS);
			
			$this->Processor=	RemSlashes($Processor);
			$this->Memory=		RemSlashes($Memory);
			$this->VideoCard=	RemSlashes($VideoCard);
			$this->SoundCard=	RemSlashes($SoundCard);
			
			$this->IsDeveloper=	$IsDeveloper;
			$this->ClaimedBugs=	$ClaimedBugs;
			$this->IsAdmin=		$IsAdmin;
			
			return true;
		}
		else
		{	//If the file doesn't exist, it clearly can't be opened
			//$Error="Specified user file does not exist or proper permissions weren't set-user file not loaded";
			return false;
		}
	}
	function AddBugToList($ID)
	{
		global $BugsDir;
		if(file_exists("$BugsDir/$ID.bug.php") && $this->IsDeveloper)
		{
			$this->ClaimedBugs[] = $ID;
			return true;
		}
		else return false;
	}
	function RemoveBugFromList($ID)
	{
		$Count = count($ClaimedBugs);
		for($i=0;$i<$Count;$i++)
		{
			if($ClaimedBugs[i]==$ID)
			{
				unset($ClaimedBugs[$i]);
				return true;
			}
		}
		return false;
	}
	function ListView()
	{
		global $UserListFields;
		
		echo"<TR>\n";
		foreach($UserListFields as $UserField)
		{
			if($UserField != "Username")
			{
				echo "\t<TD>".$this->$UserField."</TD>\n";
			}
			elseif($UserField=="Username")
			{
				echo"\t<TD><A HREF=\"viewuser.php?user=".rawurlencode($this->Username)."\">$this->Username</A></TD>\n";
			}
		}
		echo"</TR>";
	}
	function Add($Password)
	{
		global $PasswordFile;
		global $Error;
		
		if(!trim($this->Username))
		{
			$Error .= "Cannot add user, no username specified\n";
			return false;
		}
		elseif(!trim($Password))
		{
			$Error .= "Cannot add user, no password specified\n";
			return false;
		}
		elseif(!file_exists($PasswordFile))
		{
			$Error .= "Cannot add user, '$PasswordFile' does not exist.\n";
			return false;
		}
		elseif(!is_readable($PasswordFile))
		{
			$Error .= "Cannot add user, '$PasswordFile' is not readable\n";
			return false;
		}
		elseif(!is_writeable($PasswordFile))
		{
			$Error .= "Cannot add user, '$PasswordFile is not writeable\n";
			return false;
		}
		elseif($fp = fopen($PasswordFile, "r"))
		{
			$PFileContents = fread($fp, filesize($PasswordFile));
			$PFileContents = preg_replace("/(\r\n|\r)/", "\n",$PFileContents);
			fclose($fp);
			
			for($i = 0; $UserPair = sscanf($PFileContents, "%[^[]]:%[^[]]\n"); $i++)
			{
				if($UserPair[0] == $this->Username)
				{
					$Error .= "Cannot add user, user already exists\n";
					return false;
				}
			}
			trim($PFileContents);
			if($PFileContents)
			{
				$PFileContents .= "\n";
			}
			$PFileContents .= $this->Username.":".crypt($Password);
			
			$fp = fopen($PasswordFile, "w");
			if(!fwrite($fp, $PFileContents))
			{
				$Error .= "Error writing to '$PasswordFile'\n";
			}
			fclose($fp);
			
			return true;
		}
		else
		{
			$Error .= "Cannot add user, unable to open '$PasswordFile'\n";
			return false;
		}
	}
	function Save()
	{
		global $BugsDir,$UsersDir;
		global $Error;
		
		//Start making the file contents
		if(!$this->Username)	
		{
			$Error.="Cannot save, no username specified\n";
			return false;
		}
		$SaveFile="<?php\n";
		
		$SaveFile .= '$Username="'.AddSlashes($this->Username)."\";\n";
		$SaveFile .= '$CryptPW=\''.AddSlashes(crypt($this->Password))."';\n";
		$SaveFile .= '$RealName="'.AddSlashes($this->RealName)."\";\n";
		$SaveFile .= '$Email="'.AddSlashes($this->Email)."\";\n";
		$SaveFile .= '$VitalInfo="'.AddSlashes($this->VitalInfo)."\";\n";
		$SaveFile .= '$Stylesheet="'.AddSlashes($this->Stylesheet)."\";\n";
		
		$SaveFile .= '$OS="'.AddSlashes($this->OS)."\";\n";
		
		$SaveFile .= '$Processor="'.AddSlashes($this->Processor)."\";\n";
		$SaveFile .= '$Memory="'.AddSlashes($this->Memory)."\";\n";
		$SaveFile .= '$VideoCard="'.AddSlashes($this->VideoCard)."\";\n";
		$SaveFile .= '$SoundCard="'.AddSlashes($this->SoundCard)."\";\n";
		
		if($this->IsDeveloper)	$SaveFile .= '$IsDeveloper=true;'."\n";
		if($this->ClaimedBugs)
		{
			$SaveFile .= '$ClaimedBugs = array(';
			foreach($this->ClaimedBugs as $ClaimedBug)	$SaveFile.="$ClaimedBug,";
			$SaveFile = substr($SaveFile,0,-1);
			$SaveFile .=");\n";
		}
		if($this->IsAdmin)	$SaveFile .= '$IsAdmin=true;'."\n";
		
		$SaveFile .= '?>';

		
		$fp=fopen("$UsersDir/$this->Username.usr.php","w+");
		
		$fw=fwrite($fp,$SaveFile);
		if($fw)
		{
			fclose($fp);
			return true;
		}
		else
		{
			fclose($fp);
			$Error.="User file failed to be written; check that you have proper permissions for the directory set.\n";
			return false;
		}
	}
}

class Bug
{
	function Bug($ID=NULL)
	{
		//Bug-specific
		$this->ID;
		$this->Title;
		$this->Description;
		$thos->Level;

		//Software
		$this->Version;
		$this->OS;
		
		//Hardware
		$this->Processor;
		$this->Memory;
		$this->VideoCard;
		$this->SoundCard;
		
		$this->ReportDate;		//Not to be changed manually
		$this->ReporterUN;		//Either UN or Name/Email
		$this->ReporterName;
		$this->ReporterEmail;
		
		$this->DeveloperUN;		//Either UN or Name/Email
		$this->DeveloperName;
		$this->DeveloperEmail;
		$this->ClaimDate;		//Not to be changed manually
		
		$this->FixerUN;
		$this->FixerName;
		$this->FixerEmail;
		$this->FixVersion;
		$this->FixDate;			//Not to be changed manually
		$this->FixDescription;
		
		$this->Comments;
		
		$this->Status;			//Not to be changed manually
		
		if($ID)
		{
			if($this->Load($ID))
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		return true;
	}
	function Load($ID)
	{
		global $Error;
		global $BugsDir;
		if(file_exists("$BugsDir/$ID.bug.php"))
		{
			include ("$BugsDir/$ID.bug.php");
			
			$this->ID=$ID;
			$this->Title=RemSlashes($Title);
			$this->Description=RemSlashes($Description);
			$this->Level=RemSlashes($Level);
			
			$this->Version=RemSlashes($Version);
			$this->OS=RemSlashes($OS);
			
			$this->Processor=RemSlashes($Processor);
			$this->Memory=RemSlashes($Memory);
			$this->VideoCard=RemSlashes($VideoCard);
			$this->SoundCard=RemSlashes($SoundCard);
			
			$this->ReporterUN=RemSlashes($ReporterUN);
			$this->ReporterName=RemSlashes($ReporterName);
			$this->ReporterEmail=RemSlashes($ReporterEmail);
			$this->ReportDate=$ReportDate;
			
			$this->DeveloperUN=RemSlashes($DeveloperUN);
			$this->DeveloperName=RemSlashes($DeveloperName);
			$this->DeveloperEmail=RemSlashes($DeveloperEmail);
			$this->ClaimDate=$ClaimDate;
			
			$this->FixerUN=RemSlashes($FixerUN);
			$this->FixerName=RemSlashes($FixerName);
			$this->FixerEmail=RemSlashes($FixerEmail);
			$this->FixVersion=RemSlashes($FixVersion);
			$this->FixDate=$FixDate;
			$this->FixDescription=RemSlashes($FixDescription);
			
			if($Comments)
			{
				foreach($Comments as $Key => $Comment)
				{
					$this->Comments[$Key][0] = RemSlashes($Comment[0]);
					$this->Comments[$Key][1] = RemSlashes($Comment[1]);
					$this->Comments[$Key][2] = $Comment[2];
					$this->Comments[$Key][3] = RemSlashes($Comment[3]);
				}
			}
			
			$this->Status=$Status;

			return true;
		}
		else
		{	//Oops! The file doesn't exist
			$Error.="Bug file could not be opened-either it doesn't exist or proper permissions weren't set.\n";
			return false;
		}
	}
	function ListView($DaUser=null)
	{
		global $UsersDir;
		global $BugListFields;
		if(!$DaUser)
		{
			global $CurrentUser;
			$DaUser = $CurrentUser;
		}
		
		echo"<TR>\n";
		foreach($BugListFields as $BugField)
		{
			if($BugField != "ReporterUN" && $BugField != "DeveloperUN" && $BugField != "ReportDate" &&  $BugField != "ClaimDate" && $BugField != "FixDate" && $BugField != "Title" && $BugField != "Status")
			{
				echo"\t<TD>".$this->$BugField."</TD>\n";
			}
			elseif($BugField == "Title")
			{
				echo"\t<TD><A HREF=\"viewbug.php?bug=".$this->ID."\">".$this->Title."</A></TD>\n";
			}
			elseif($BugField == "ReportDate")
			{
				echo"\t<TD>".Date("m-d-Y H:i",$this->ReportDate)."</TD>\n";
			}
			elseif($BugField == "ClaimDate")
			{
				if($this->ClaimDate != 0)		echo"\t<TD>".Date("m-d-Y H:i",$this->ClaimDate)."</TD>\n";
				elseif($DaUser->IsDeveloper)	echo"\t<TD><A HREF=\"claimbug.php?bug=".$this->ID."\">Unclaimed (Claim)</A></TD>\n";
				else							echo"\t<TD STYLE=\"color:Orange\">Unclaimed</TD>\n";
			}
			elseif($BugField == "FixDate")
			{
				if($this->FixDate != 0)
				{
					echo"\t<TD>".Date("m-d-Y H:i",$this->FixDate)."</TD>\n";
				}
				else
				{
					echo"\t<TD STYLE=\"color:Orange\">Not finished</TD>\n";
				}
			}
			elseif($BugField == "Status")
			{
				if($this->Status)
				{
					if($this->Status=="Unclaimed" && $DaUser->IsDeveloper)	echo"\t<TD><A HREF=\"claimbug.php?bug=".$this->ID."\">Unclaimed (Claim)</A></TD>\n";
					elseif($this->Status=="Unclaimed")	echo"\t<TD STYLE=\"color:Orange\">Unclaimed</TD>\n";
					else echo"\t<TD>$this->Status</TD>\n";
				}
				elseif($this->FixerUN || $this->FixerName)			echo"\t<TD>Fixed</TD>\n";
				elseif($this->DeveloperUN || $this->DeveloperName)	echo"\t<TD>Claimed</TD>\n";
				elseif($DaUser->IsDeveloper)						echo"\t<TD><A HREF=\"claimbug.php?bug=".$this->ID."\">Unclaimed (Claim)</A></TD>\n";
				else												echo"\t<TD STYLE=\"color:Orange\">Unclaimed</TD>\n";
			}
			elseif($BugField == "ReporterUN")
			{
				if($this->ReporterUN && file_exists("$UsersDir/$this->ReporterUN.usr.php"))
				{
					echo"\t<TD><A HREF=\"viewuser.php?user=".rawurlencode($this->ReporterUN)."\">$this->ReporterUN</TD>\n";
				}
				elseif($this->ReporterUN)
				{
					echo"\t<TD>$this->ReporterUN</TD>\n";
				}
				else
				{
					echo'\t<TD>';
					if($this->ReporterEmail)
					{
						echo'<A HREF="'.$this->ReporterEmail.'">'.$this->ReporterName."</A>";
					}
					else
					{
						echo$this->ReporterName;
					}
					echo"</TD>\n";
				}
			}
			elseif($BugField=="DeveloperUN")
			{
				if($this->DeveloperUN && file_exists("$UsersDir/$this->DeveloperUN.usr.php"))
				{
					echo"\t<TD><A HREF=\"viewuser.php?user=".rawurlencode($this->DeveloperUN)."\">$this->DeveloperUN</TD>\n";
				}
				elseif($this->DeveloperUN)
				{
					echo"\t<TD>$this->DeveloperUN</TD>\n";
				}
				else
				{
					echo'\t<TD>';
					if($this->DeveloperEmail)
					{
						echo'<A HREF="'.$this->DeveloperEmail.'">'.$this->DeveloperName."</A>";
					}
					else
					{
						echo$this->DeveloperName;
					}
					echo"</TD>\n";
				}
			}
			elseif($BugField=="FixerUN")
			{
				if($this->FixerUN && file_exists("$UsersDir/$this->FixerUN.usr.php"))
				{
					echo"\t<TD><A HREF=\"viewuser.php?user=".rawurlencode($this->FixerUN)."\">$this->FixerUN</TD>\n";
				}
				elseif($this->FixerUN)
				{
					echo"\t<TD>$this->FixerUN</TD>\n";
				}
				else
				{
					echo'\t<TD>';
					if($this->FixerEmail)
					{
						echo'<A HREF="'.$this->FixerEmail.'">'.$this->FixerName."</A>";
					}
					else
					{
						echo$this->FixerName;
					}
					echo"</TD>\n";
				}
			}
		}
		echo"</TR>";
	}
	function AddComment($Username, $Comment, $Email = NULL)
	{
		$this->Comments[count($this->Comments)] = Array($Username, $Email, time(), $Comment);
	}
	function Save()
	{
		global $Error;
		global $BugsDir;
		
		$this->Status="Unclaimed";
		$SaveFile="<?php\n";
		
		//General bug info
		$SaveFile.='$Title="'.AddSlashes($this->Title)."\";\n";
		$SaveFile.='$Description="'.AddSlashes($this->Description)."\";\n";
		$SaveFile.='$Level="'.AddSlashes($this->Level)."\";\n";
		
		$SaveFile.='$Version="'.AddSlashes($this->Version)."\";\n";
		$SaveFile.='$OS="'.AddSlashes($this->OS)."\";\n";
		
		$SaveFile.='$Processor="'.AddSlashes($this->Processor)."\";\n";
		$SaveFile.='$Memory="'.AddSlashes($this->Memory)."\";\n";
		$SaveFile.='$VideoCard="'.AddSlashes($this->VideoCard)."\";\n";
		$SaveFile.='$SoundCard="'.AddSlashes($this->SoundCard)."\";\n";
		
		//Reporting info
		if($this->ReporterUN)
		{
			$SaveFile.='$ReporterUN="'.AddSlashes($this->ReporterUN)."\";\n";
		}
		else
		{
			$SaveFile.='$ReporterName="'.AddSlashes($this->ReporterName)."\";\n";
			$SaveFile.='$ReporterEmail="'.AddSlashes($this->ReporterEmail)."\";\n";
		}
		if($this->ReportDate)	$SaveFile.='$ReportDate="'.$this->ReportDate."\";\n";
		else					$SaveFile.='$ReportDate="'.time()."\";\n";
		
		//Developer info
		if($this->DeveloperUN || $this->DeveloperName)
		{
			if($this->DeveloperUN)	$SaveFile.='$DeveloperUN="'.AddSlashes($this->DeveloperUN)."\";\n";
			else
			{
				$SaveFile.='$DeveloperName="'.AddSlashes($this->DeveloperName)."\";\n";
				$SaveFile.='$DeveloperEmail="'.AddSlashes($this->DeveloperEmail)."\";\n";
			}
			
			if($this->ClaimDate)	$SaveFile.='$ClaimDate="'.$this->ClaimDate."\";\n";
			else					$SaveFile.='$ClaimDate="'.time()."\";\n";
			
			$this->Status="Claimed";
		}
		
		//Fix info
		if($this->FixerUN || $this->FixerName)
		{
			if($this->FixerUN)	$SaveFile.='$FixerUN="'.AddSlashes($this->FixerUN)."\";\n";
			else
			{
				$SaveFile.='$FixerName="'.AddSlashes($this->FixerName)."\";\n";
				$SaveFile.='$FixerEmail="'.AddSlashes($this->FixerEmail)."\";\n";
			}
			
			$SaveFile.='$FixVersion="'.$this->FixVersion."\";\n";
			
			if($this->FixDate)	$SaveFile.='$FixDate="'.$this->FixDate."\";\n";
			else				$SaveFile.='$FixDate="'.time()."\";\n";
			
			$SaveFile.='$FixDescription="'.AddSlashes($this->FixDescription)."\";\n";

			$this->Status="Fixed";
		}
		
		//Comments...
		for($i = 0; $i < count($this->Comments); $i++)
		{
			$SaveFile.="\$Comments[".$i."]=Array(\"".AddSlashes($this->Comments[$i][0])."\",";
			if(!$this->Comments[$i][1])	$SaveFile.="NULL,";
			else						$SaveFile.="\"".AddSlashes($this->Comments[$i][1])."\",";
			$SaveFile.="\"".$this->Comments[$i][2]."\",";
			$SaveFile.="\"".AddSlashes($this->Comments[$i][3])."\");\n";
		}
		
		
		//Overall status
		$SaveFile.='$Status="'.$this->Status."\";\n";
		$SaveFile.='?>';
		
		if(!$this->ID)
		{
			$dir=opendir($BugsDir);
			$i=0;
			while(($file = readdir($dir)) !== FALSE)
			{
				if($file != "." && $file !=".." && is_dir($file) == FALSE && substr("$file",-8) == ".bug.php")
				{
					$file=ereg_replace(".bug.php","",$file);
					$files[$i]=$file;
					$i++;
				}
			}
			if($files)
			{
				sort($files);
				foreach($files as $fileID)
				{
					$LastFileID=$fileID;
				}
				$LastFileID++;
			}
			else
			{
				$LastFileID=1;
			}
		}
		else $LastFileID = $this->ID;
		
		$fp=fopen("$BugsDir/$LastFileID.bug.php","w+");
		$fw=fwrite($fp,$SaveFile);
		if($fw)
		{
			fclose($fp);
			return true;
		}
		else
		{
			fclose($fp);
			$error.="Bug file could not be written; check permissions are set properly.\n";
			return false;
		}
	}
}
function EchoBugHeaders()
{
	global $ProgramName;
	global $BugListFields;
	
	//So that the table can easily be modified, this little code snippet outputs the headers for each column
	$Headers="<TR>\n";
	foreach($BugListFields as $BugField)
	{
		$Headers.="<TD style=\"border:0;\"><B>";
			
		if($BugField=="ID")					$Headers .= "ID";
		elseif($BugField=="ReporterUN")		$Headers .= "Reporter";
		elseif($BugField=="Title")			$Headers .= "Short Description";
		elseif($BugField=="Version")		$Headers .= "$ProgramName Version";
		elseif($BugField=="Level")			$Headers .= "Severity";
		elseif($BugField=="ReportDate")		$Headers .= "Reported on";
		elseif($BugField=="FixerUN")		$Headers .= "Fixed by";
		elseif($BugField=="DeveloperUN")	$Headers .= "Claimed by";
		elseif($BugField=="ClaimDate")		$Headers .= "Claimed on";
		elseif($BugField=="FixDate")		$Headers .= "Fixed on";
		elseif($BugField=="FixVersion")		$Headers .= "Version fixed";
		elseif($BugField=="FixDescription")	$Headers .= "Fix Description";
		elseif($BugField=="VideoCard")		$Headers .= "Video card";
		elseif($BugField=="SoundCard")		$Headers .= "Sound card";
		else								$Headers .= "$BugField";
		
		$Headers.="</B></TD>\n";
	}
	$Headers.="</TR>\n";
	echo $Headers;
}
function ListClaimedBugs($DaUser=null)
{
	global $BugListFields;
	
	echo"<H3>Bugs claimed by $DaUser->Username</H3>\n";
	echo"<HR>\n";
	
	if(count($DaUser->ClaimedBugs) > 0)
	{
		$DaBug = new Bug;

		echo"<TABLE style=\"width:100%\">\n";
		EchoBugHeaders();

		foreach($DaUser->ClaimedBugs as $ClaimedBug)
		{
			if($DaBug->Load($ClaimedBug))
			{
				$DaBug->ListView();
			}
			else
			{
				printf("<TR><TD>$ClaimedBug</TD><TD STYLE=\"text-align:center\" colspan=\"%d\">ERROR: Bug does not exist</TD></TR>",count($BugListFields)-1);
			}
		}

		echo"</TABLE><HR>";

		return true;
	}
	else
	{
		echo"<DIV style=\"text-align:center\">No bugs claimed</DIV>";
		echo"<HR>";
		return false;
	}
}
function ListBugs($numa=NULL,$numb=NULL)
{
	global $Error;
	global $BugsDir;
	global $CurrentUser;
	
	if($numa == 1 && ($numb == NULL || $numb == 1))
	{
		echo"<H3>Last bug</H3>\n";
	}
	elseif($numa > 1 && $numb == NULL)
	{
		echo"<H3>Last $numa bugs</H3>\n";
	}
	elseif($numa > 1 && $numb > 1)
	{
		echo"<H3>Bugs $numa to $numb</H3>\n";
		$i=$numa;
	}
	else
	{
		echo"<H3>All bugs</H3>\n";
	}
	
	echo"<HR>\n";
	
	if(file_exists($BugsDir)&&is_dir($BugsDir))
	{
		$dir=opendir($BugsDir);
	}
	else
	{
		echo"<DIV style=\"text-align:center;color:orange\">ERROR: Bugs directory does not exist</DIV>";
		echo"<HR>";
		return false;
	}
	
	$i=0;
	
	while(($file = readdir($dir)) !== FALSE)
	{
		if($file != "." && $file !=".." && is_dir($file) == FALSE && substr("$file",-8) == ".bug.php")
		{
			$file=ereg_replace(".bug.php","",$file);
			$files[$i]=$file;
			$i++;
		}
	}
		
	if($files)
	{
		echo'<TABLE style="width:100%">';
		
		EchoBugHeaders();
		
		rsort($files);
		if($numa && ($numb == NULL || $numb == 1))
		{
			for($i=0;$i<$numa && $files[$i];$i++)
			{
				$bug=new Bug($files[$i]);
				$bug->ListView($CurrentUser);
			}
		}
		elseif($numa && $numb >= 2)
		{
			for($i=$numa;$i<$numb && $files[$i];$i++)
			{
				$bug=new Bug($files[$i]);
				$bug->ListView($Current);
			}
		}
		else
		{
			foreach($files as $ID)
			{
				$bug=new Bug($ID);
				$bug->ListView($Current);
			}
		}
		echo"</TABLE><HR>";
		return true;
	}
	else
	{
		echo"<DIV style=\"text-align:center\">No bugs have been reported</DIV>";
		echo"<HR>";
		return false;
	}
}
function ListUsers()
{
	global $Error;
	global $UsersDir;
	global $UserListFields;
	
	echo"<H3>All valid users</H3>\n<HR>\n";	
	
	if(file_exists($UsersDir)&&is_dir($UsersDir))
	{
		$dir=opendir($UsersDir);
	}
	else
	{
		echo"<DIV style=\"text-align:center;color:orange\">ERROR: Users directory does not exist</DIV>";
		echo"<HR>";
		return false;
	}
	
	$i=0;
	
	while(($file = readdir($dir)) !== FALSE)
	{
		if($file != "." && $file !=".." && is_dir($file) == FALSE && substr("$file",-8) == ".usr.php")
		{
			$file=str_replace(".usr.php","",$file);
			$files[$i]=$file;
			$i++;
		}
	}
	
	if($files)
	{
		echo'<TABLE style="width:100%">';

		//So that the table can easily be modified, this little code snippet outputs the headers for each column
		$Headers="<TR>\n";
		foreach($UserListFields as $UserField)
		{
			$Headers .= "<TD style=\"border:0;\"><B>";
			
			if($UserField=="Username")		$Headers .= "Username";
			elseif($UserField=="RealName")	$Headers .= "Real name";
			elseif($UserField=="Email")		$Headers .= "Email address";
			elseif($UserField=="OS")		$Headers .= "OS";
			elseif($UserField=="Processor")	$Headers .= "Processor";
			elseif($UserField=="Memory")	$Headers .= "Memory";
			elseif($UserField=="VideoCard")	$Headers .= "Video card";
			elseif($UserField=="SoundCard")	$Headers .= "Sound card";
			elseif($UserField=="VitalInfo")	$Headers .= "Vital Info";
			
			$Headers .= "</B></TD>\n";
		}
		echo $Headers;
		
		sort($files);
		
		foreach($files as $name)
		{
			$user=new User($name);
			$user->ListView();
		}
		
		echo"</TABLE><HR>";
		return true;
	}
	else
	{
		echo"<DIV style=\"text-align:center\">No valid users exist</DIV>";
		echo"<HR>";
		return false;
	}
}
function PrintHeader($PageHeader=NULL)
{
	global $ProgramName;
	global $CurrentUser;
?>
<TABLE STYLE="width:100%;height:55px;border-width:1px;border-style:solid" CLASS="header">
	<TR>
		<TD><H2 STYLE="margin:0"><?echo$ProgramName?> Bug Tracker</H2>
		<?if($PageHeader){echo"<H3 STYLE=\"margin:0\">$PageHeader</H3>";}?></TD>
		<TD STYLE="width:350px">
			<TABLE STYLE="font-size:10pt">
				<TR>
					<TD><B>Bugs:</B></TD>
					<TD><A HREF="listbugs.php">List bugs</A></TD>
					<TD><A HREF="addbug.php">Add a bug</A></TD>
				</TR>
				<TR>
					<TD><B>Users:</B></TD>
					<TD><A HREF="listusers.php">List users</A></TD>
					<TD><A HREF="updateprofile.php">Update profile</A></TD>
				</TR>
<?if($CurrentUser->IsDeveloper){?>
				<TR>
					<TD><B>Developers:</B></TD>
					<TD><A HREF="listclaims.php">List claimed bugs</A></TD>
					<TD><A HREF="addversion.php">Add version</A></TD>
<?}?>
				</TR>
			</TABLE>
		</TD>
	</TR>
</TABLE>
<?}?>
<?
Function UserExists($user)
{
	global $UsersDir;
	
	if(file_exists("$UsersDir/$user.usr.php"))
	{
		return true;
	}
	else
	{
		return false;
	}
}

function BugExists($bug)
{
	global $BugsDir;
	
	if(file_exists("$BugsDir/$bug.bug.php"))
	{
		return true;
	}
	else
	{
		return false;
	}
}
function SwitchUser($UN, $Password)
{
	global $CurrentUser;
	
	if($UseApache)
	{
		return false;
	}
	
	$DaUser = new User($UN);
	if(crypt($Password, $DaUser->CryptPW) == $DaUser->CryptPW)
	{
		$CurrentUser = $DaUser;
		$_SESSION['Username'] = $UN;
		$_SESSION['Password'] = $Password;
		
		return true;
	}
	else
	{
		echo '"'.crypt($Password, $DaUser->CryptPW).'" to "'.$DaUser->CryptPW.'"';
	}
}
function ResponsePage($Message, $Hyperlink, $Redirect = NULL, $ClickMessage = NULL, $Title = NULL)
{
	global $CustomCSS;
?>
<HTML>
<HEAD>
<?if($CustomCSS){?><LINK REL="Stylesheet" TYPE="Text/CSS" HREF="<?echo$CustomCSS?>"><?}?>
<LINK REL="Stylesheet" TYPE="Text/CSS" HREF="style.css">
<?if($Redirect){?><meta http-equiv="Refresh" content="1; URL=<?echo$Redirect?>"><?}?>
<TITLE><?if($Title){echo$Title;}else{echo$Message;}?></TITLE>
</HEAD>
<BODY>
<TABLE STYLE="height:100%">
<TR STYLE="height:45%"><TD></TD></TR>
<TR><TD STYLE="text-align:center"><BR><A HREF="<?echo$Hyperlink?>">
<?echo$Message?>
<BR><?if($Redirect){?>Click here if you don't want to wait (Or your browser does not forward you)<?}elseif($ClickMessage){echo$ClickMessage;}else{?>Click here to continue<?}?></A></TD></TR>
<TR STYLE="height:45%"><TD></TD></TR>
</TABLE>
</BODY>
</HTML>
<?}?>
