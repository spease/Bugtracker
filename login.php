<?php include("bugclasses.php");
if($HTTP_POST_VARS['Login'])
{
	if(SwitchUser($HTTP_POST_VARS['chUser'], $HTTP_POST_VARS['chPassword']))
	{
		ResponsePage("Switched to user '".$HTTP_POST_VARS['chUser']."' successfully."
				,"index.php"
				,"index.php");
	}
	else
	{
		ResponsePage("Could not switch to user '".$HTTP_POST_VARS['chUser']."'."
				,"login.php"
				,NULL
				,"Click here to go back to the login page");
	}
}
else
{
?>
<HTML>
<HEAD>
<?if($CustomCSS){?><LINK REL="Stylesheet" TYPE="Text/CSS" HREF="<?echo$CustomCSS?>"><?}?>
<LINK REL="Stylesheet" TYPE="Text/CSS" HREF="style.css">
<TITLE>Login/<?echo$ProgramName?> Bug Tracker</TITLE>
</HEAD>
<BODY>
<?PrintHeader("Logging In")?>
<FORM ACTION="login.php" METHOD="POST">
<B>Username: <INPUT TYPE="Text" NAME="chUser">
<B>Password: <INPUT TYPE="Password" NAME="chPassword">
<INPUT TYPE="Submit" VALUE="Login" NAME="Login">
</FORM>
</BODY>
</HTML>
<?php }?>
