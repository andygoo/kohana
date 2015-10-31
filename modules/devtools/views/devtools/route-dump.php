
<h3 class="page-header">Route Dump</h3>

<?php if (count(Route::all()) > 0): ?>

<?php foreach (Route::all() as $route): ?>
<h4><?php echo Route::name($route) ?></h4>
<?php
$array = (array) $route;
foreach ($array as $key => $value) {
	$new_key = substr($key, strrpos($key, "\x00") + 1);
	$array[$new_key] = $value;
	unset($array[$key]);
}
?>
<table class="table table-hover table-bordered">
	<tr>
		<th width="200">Route uri</th>
		<td><code><?php echo HTML::chars($array['_uri']) ?></code></td>
	</tr>
	<tr>
		<th width="200">Params with regex</th>
		<td><?php if (count($array['_regex']) == 0) echo "none"; foreach( $array['_regex'] as $param => $regex) echo "<code>\"$param\" = \"$regex\"</code><br/>" ?></td>
	</tr>
	<tr>
		<th width="200">Defaults</th>
		<td><?php if (count($array['_defaults']) == 0) echo "none"; foreach( $array['_defaults'] as $param => $default) echo "<code>\"$param\" = \"$default\"</code><br/>" ?></td>

	</tr>
	<tr>
		<th width="200">Compiled Regex</th>
		<td><code><?php echo HTML::chars($array['_route_regex']) ?></code></td>
	</tr>
</table>
<?php endforeach; ?>

<?php else: ?>
<p>No routes</p>
<?php endif; ?>