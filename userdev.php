<?php
$PFileName=".htpasswd"; //Location of the Password file being updated, including the name (ie ".htpasswd")
//This function is from my (WMCoolmon's) Bug Tracker. Very nifty.
function ValidateUser($Username, $Password)
{
	global $Error;
	global $PFileName;
	
	if($PFileName && file_exists($PFileName))
	{
		$PF=fopen($PFileName,"r");
	    $PFRead=fread($PF,filesize($PFileName));
		
		$PFRead=str_replace("\r\n","\n",$PFRead);
		$PFRead=str_replace("\r","\n",$PFRead);
		$UserPairs=explode("\n",$PFRead);
		
		foreach($UserPairs as $UserPair)
	    {
	    	$UserInfo=explode(":",$UserPair);
	    	if($Username==$UserInfo[0] && crypt($Password,$UserInfo[1])==$UserInfo[1])
	    	{
	    		return true;
	    	}
	    }
	}
	elseif($Username==FALSE)
	{
		$Error.="No username exists-cannot validate user.\n<BR>";
		return false;
	}
	elseif($PFileName==FALSE OR $PFileName=="")
	{
		$Error.="$PasswordFile not defined, cannot validate user.\n<BR>";
		return false;
	}
	elseif($PFileName && !file_exists($PFileName))
	{
		$Error.="Password file could not be found, unable to validate user.\n<BR>";
		return false;
	}
	else
	{
		$Error.="Username or password not found; an incorrect one was entered.\n<BR>";
		return false;
	}
}
if(ValidateUser($HTTP_SERVER_VARS['PHP_AUTH_USER'],$HTTP_SERVER_VARS['PHP_AUTH_PW'])
	&& $HTTP_SERVER_VARS['PHP_AUTH_USER'] == "WMCoolmon")
{
?>
<HTML>
<HEAD>
<?php 
$PF = fopen($PFileName,"a+");

if(!$PF)
{
	if(file_exists($PFileName))
	{
		$Error .= "ERROR: Password file privileges are not set properly\n<BR>";
	}
	else
	{
		$Error .= "ERROR: Password file not found\n<BR>";
	}
}

function AddAUser($username,$password)
{
	global $Error;
	global $PF,$PFileName;
	
	$username = stripslashes($username);
	$password = stripslashes($password);
	
	$PFRead=fread($PF,filesize($PFileName));
	if(strstr($PFRead,"\r\n"))
	{
    	$Div="\r\n";
    }
    elseif(strstr($PFRead,"\r"))
    {
    	$Div="\r";
   	}
	elseif(filesize($PFileName) != 0 && substr($PFRead, -1) != "\n" && substr($PFRead, -1) != "\r")
	{
		$Div="\n";
    }
	$cryptpw=crypt($password);
	if(fwrite($PF,"$Div$username:$cryptpw"))
	{
		$Error .= "Update completed; ".$username." added successfully\n<BR>";
	}
	else
	{
		$Error .= "ERROR: Could not write to password file.";
	}
}

if($adduser)
{
	if($chpassword==$chcpassword && $chusername && $chpassword && sizeof($chusername)!=0 && sizeof($chpassword)!=0)
	{
		AddAUser($chusername,$chpassword);
	}
	else
	{
		$Error .= "Either you did not enter a username or password to add to the database, or the password and confirm password fields did not match\n<BR>";
	}
}
if($checkuser)
{
	if(ValidateUser($vusername,$vpassword))
	{
		$Error .= "Username/Password combination works\n<BR>";
	}
	else
	{
		$Error .= "Username/Password combination did not work\n<BR>";
	}
}?>
<TITLE>User Manager</TITLE>
</HEAD>
<BODY>
<TABLE style="width:100%">
	<TR>
		<TD STYLE="border: 1px solid black">
			<H2>Current Valid Users:</H2>
			<?php
			rewind($PF);
			$fcontent=fread($PF,filesize($PFileName));
			$pairs=explode("\n",$fcontent);
			$a=0;
			foreach($pairs as $pair)
			{
				$users[$a]=explode(":",$pair);
				$a++;
			}
			$b=0;
			?>
			<TABLE>
				<TR style="vertical-align:top">
					<TD>
						<?php
						while($users[$b][0])
						{
							echo$users[$b][0]."<BR>\r\n";
							$b++;
							if($b==20){echo'</TD><TD>';}
						}
						?>
					</TD>
				</TR>
			</TABLE>
		</TD>
		<TD style="vertical-align:top;text-align:right;border: 1px solid black">
			<H2>Add a User:</H2>
			<FORM METHOD="Post" ACTION="<?echo$PHP_SELF?>">
			Username:<INPUT TYPE="Text" NAME="chusername">
			<BR>Password:<INPUT TYPE="Password" NAME="chpassword">
			<BR>Confirm Password:<INPUT TYPE="Password" NAME="chcpassword">
			<BR><INPUT TYPE="Submit" VALUE="Add User" NAME="adduser">
			<H2>Validate a User/Password combination:</H2>
			Username:<INPUT TYPE="Text" NAME="vusername">
			<BR>Password:<INPUT TYPE="Password" NAME="vpassword">
			<BR><INPUT TYPE="Submit" VALUE="Check User" NAME="checkuser">
			</FORM>
		</TD>
	</TR>
</TABLE>
<? if($Error) {?>
<DIV STYLE="border: 1px solid black"><H2>Errors</H2>
<?echo$Error?>
</DIV>
<?}?>
</BODY>
</HTML>
<?php fclose($PF);} else {echo'<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">
<HTML><HEAD>
<TITLE>404 Not Found</TITLE>
</HEAD><BODY>
<H1>Not Found</H1>
The requested URL '.$PHP_SELF.' was not found on this server.<P>
<HR>
'.$SERVER_SIGNATURE.'
</BODY></HTML>';}