
<div class="table-responsive">
<?php 
ob_start();
phpinfo();
$phpinfo = ob_get_clean();
$phpinfo = trim($phpinfo);
preg_match_all('#<body[^>]*>(.*)</body>#si', $phpinfo, $output);
$output = $output[1][0];
echo $output;
?>
</div>

<script>
$(function(){
	$('table').attr('class', 'table table-hover table-bordered');
	$('.e').attr('width', '240');
});
</script>
