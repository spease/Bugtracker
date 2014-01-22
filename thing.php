<? include("bugclasses.php");
$DaBug = new Bug;
if($DaBug->Load(5))
{
	$DaBug->AddComment("Steven","Hi there");
	echo"Comment added";
}
$DaBug->Save();
?>