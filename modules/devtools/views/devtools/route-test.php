
<h3 class="page-header">Route Tester</h3>

<form class="form-inline" method="get">
    <div class="form-group">
        <div class="input-group">
			<input type="text" name="url" class="form-control" style="width:350px;" value="<?= Arr::get($_GET, 'url')?>" placeholder="Test the url" required>
            <span class="input-group-btn">
                <button type="submit" class="btn btn-info">Test</button>
            </span>
        </div>
    </div>
</form>

<br>

<?php foreach ($tests as $test) :?>
<table class="table table-hover table-bordered">
	<tr><th colspan="3">Testing the url "<code><?php echo $test->url ?></code>"</th></tr>
	<?php if ($test->route_name === FALSE): ?>
		<tr><td colspan="3" class="text-danger">Did not match any routes</td></tr>
	<?php else:?>
		<?php if ($test->expected_params): ?>
			<tr><th width="200">param</th><th width="30%">result</th><th>expected</th>
			<?php
			foreach ($test->get_params() as $name => $param) {
				echo "<tr><td>{$name}</td>";
				echo "<td".($param['error'] ? ' class="text-danger"':' class="text-success"').">{$param['result']}</td>";
				echo "<td".($param['error'] ? ' class="text-danger"':' class="text-success"').">{$param['expected']}</td></tr>";
			}
			?>
		<?php else: ?>
    		<tr><th width="200">route</th><td colspan="2"><?php echo $test->route_name ?></td></tr>
    		<?php if (isset($test->params['directory'])):?>
    		<tr><th width="200">directory</th><td colspan="2"><?php echo $test->params['directory'] ?></td></tr>
    		<?php endif;?>
    		<?php if (isset($test->params['controller'])):?>
    		<tr><th width="200">controller</th><td colspan="2"><?php echo $test->params['controller'] ?></td></tr>
    		<?php endif;?>
    		<?php if (isset($test->params['action'])):?>
    		<tr><th width="200">action</th><td colspan="2"><?php echo $test->params['action'] ?></td></tr>
    		<?php endif;?>
			<?php foreach ($test->params as $key => $value): ?>
			    <?php if ($key=='directory' || $key=='controller' || $key=='action') continue;?>
				<tr><th width="200"><?php echo $key ?>:</th><td colspan="2"><?php echo $value ?></td></tr>
			<?php endforeach; ?>
		<?php endif; ?>
	<?php endif; ?>
</table>

<h4><?php echo $test->route_name ?></h4>
<?php
$route = $test->route;
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

<?php endforeach ?>