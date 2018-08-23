<?
include("include/main.php");
$db = new Main(0);

if ($db->system_under_construction == 'no') {
	//header("Location:home.php");
	//exit();
}

$db->show_header();

?><br />
<br />
<div align="center">System under construction. Try again in a few minutes.</div>
<br />
<noscript>
 For full functionality of this site it is necessary to enable JavaScript.
 Here are the <a href="http://www.enable-javascript.com/" target="_blank">
 instructions how to enable JavaScript in your web browser</a>.
</noscript>
<?
$db->show_footer();
?>
