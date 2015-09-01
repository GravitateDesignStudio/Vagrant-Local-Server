<?php

// Index

?>

<style>
body { background-color: #444 !important; }
a { color: #eee  !important; background-color: inherit !important;}
</style>

<?php

if(!empty($_GET['phpinfo']))
{
	?>
	<a href="javascript:window.history.back();">< back</a><br><br>
	<?php
	phpinfo();
	exit;
}

?>
<a href="?phpinfo=1">phpInfo()</a><br><br>
<?php

foreach (glob('*local.*') as $site)
{
	?>
	<a href="http://<?php echo basename($site);?>"><?php echo basename($site);?></a>
	<?php
}

