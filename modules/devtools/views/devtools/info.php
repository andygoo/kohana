
<h3 class="page-header">Environment Tests</h3>

<p>
	The following tests have been run to determine if Kohana will work in your environment.
</p>

<table class="table table-hover table-bordered">
	<tr>
		<th>DOCROOT</th>
		<td><?php echo DOCROOT ?></td>
	</tr>
	<tr>
		<th>APPPATH</th>
		<td><?php echo APPPATH ?></td>
	</tr>
	<tr>
		<th>SYSPATH</th>
		<td><?php echo SYSPATH ?></td>
	</tr>
	<tr>
		<th>MODPATH</th>
		<td><?php echo MODPATH ?></td>
	</tr>
	<tr>
		<th>Kohana::init() settings</th>
		<td><code>
		"base_url" = <?php echo Debug::dump(Kohana::$base_url) ?><br />
		"index_file" = <?php echo Debug::dump(Kohana::$index_file) ?><br />
		"errors" = <?php echo Debug::dump(Kohana::$errors) ?><br />
		"profile" = <?php echo Debug::dump(Kohana::$profiling) ?><br />
		</code></td>
	</tr>
	<tr>
		<th>PCRE UTF-8</th>
		<?php if ( ! @preg_match('/^.$/u', 'ñ')): $failed = TRUE ?>
			<td class="text-danger"><a href="http://php.net/pcre">PCRE</a> has not been compiled with UTF-8 support.</td>
		<?php elseif ( ! @preg_match('/^\pL$/u', 'ñ')): $failed = TRUE ?>
			<td class="text-danger"><a href="http://php.net/pcre">PCRE</a> has not been compiled with Unicode property support.</td>
		<?php else: ?>
			<td class="text-success">Pass</td>
		<?php endif ?>
	</tr>
	<tr>
		<th>SPL Enabled</th>
		<?php if (function_exists('spl_autoload_register')): ?>
			<td class="text-success">Pass</td>
		<?php else: $failed = TRUE ?>
			<td class="text-danger">PHP <a href="http://www.php.net/spl">SPL</a> is either not loaded or not compiled in.</td>
		<?php endif ?>
	</tr>
	<tr>
		<th>Reflection Enabled</th>
		<?php if (class_exists('ReflectionClass')): ?>
			<td class="text-success">Pass</td>
		<?php else: $failed = TRUE ?>
			<td class="text-danger">PHP <a href="http://www.php.net/reflection">reflection</a> is either not loaded or not compiled in.</td>
		<?php endif ?>
	</tr>
	<tr>
		<th>Filters Enabled</th>
		<?php if (function_exists('filter_list')): ?>
			<td class="text-success">Pass</td>
		<?php else: $failed = TRUE ?>
			<td class="text-danger">The <a href="http://www.php.net/filter">filter</a> extension is either not loaded or not compiled in.</td>
		<?php endif ?>
	</tr>
	<tr>
		<th>Iconv Extension Loaded</th>
		<?php if (extension_loaded('iconv')): ?>
			<td class="text-success">Pass</td>
		<?php else: $failed = TRUE ?>
			<td class="text-danger">The <a href="http://php.net/iconv">iconv</a> extension is not loaded.</td>
		<?php endif ?>
	</tr>
	<?php if (extension_loaded('mbstring')): ?>
	<tr>
		<th>Mbstring Not Overloaded</th>
		<?php if (ini_get('mbstring.func_overload') & MB_OVERLOAD_STRING): $failed = TRUE ?>
			<td class="text-danger">The <a href="http://php.net/mbstring">mbstring</a> extension is overloading PHP's native string functions.</td>
		<?php else: ?>
			<td class="text-success">Pass</td>
		<?php endif ?>
	</tr>
	<?php endif ?>
	<tr>
		<th>Character Type (CTYPE) Extension</th>
		<?php if ( ! function_exists('ctype_digit')): $failed = TRUE ?>
			<td class="text-danger">The <a href="http://php.net/ctype">ctype</a> extension is not enabled.</td>
		<?php else: ?>
			<td class="text-success">Pass</td>
		<?php endif ?>
	</tr>
	<tr>
		<th>URI Determination</th>
		<?php if (isset($_SERVER['REQUEST_URI']) OR isset($_SERVER['PHP_SELF']) OR isset($_SERVER['PATH_INFO'])): ?>
			<td class="text-success">Pass</td>
		<?php else: $failed = TRUE ?>
			<td class="text-danger">Neither <code>$_SERVER['REQUEST_URI']</code>, <code>$_SERVER['PHP_SELF']</code>, or <code>$_SERVER['PATH_INFO']</code> is available.</td>
		<?php endif ?>
	</tr>
</table>

<h3 class="page-header">Optional Tests</h3>

<p>
	The following extensions are not required to run the Kohana core, but if enabled can provide access to additional classes.
</p>

<table class="table table-hover table-bordered">
	<tr>
		<th>cURL Enabled</th>
		<?php if (extension_loaded('curl')): ?>
			<td class="text-success">Pass</td>
		<?php else: ?>
			<td class="text-danger">Kohana can use the <a href="http://php.net/curl">cURL</a> extension for the Request_Client_External class.</td>
		<?php endif ?>
	</tr>
	<tr>
		<th>mcrypt Enabled</th>
		<?php if (extension_loaded('mcrypt')): ?>
			<td class="text-success">Pass</td>
		<?php else: ?>
			<td class="text-danger">Kohana requires <a href="http://php.net/mcrypt">mcrypt</a> for the Encrypt class.</td>
		<?php endif ?>
	</tr>
	<tr>
		<th>GD Enabled</th>
		<?php if (function_exists('gd_info')): ?>
			<td class="text-success">Pass</td>
		<?php else: ?>
			<td class="text-danger">Kohana requires <a href="http://php.net/gd">GD</a> v2 for the Image class.</td>
		<?php endif ?>
	</tr>
	<tr>
		<th>MySQL Enabled</th>
		<?php if (function_exists('mysql_connect')): ?>
			<td class="text-success">Pass</td>
		<?php else: ?>
			<td class="text-danger">Kohana can use the <a href="http://php.net/mysql">MySQL</a> extension to support MySQL databases.</td>
		<?php endif ?>
	</tr>
	<tr>
		<th>PDO Enabled</th>
		<?php if (class_exists('PDO')): ?>
			<td class="text-success">Pass</td>
		<?php else: ?>
			<td class="text-danger">Kohana can use <a href="http://php.net/pdo">PDO</a> to support additional databases.</td>
		<?php endif ?>
	</tr>
</table>

<h3 class="page-header">Loaded Modules</h3>

<?php if (count(Kohana::modules()) > 0): ?>
	<table class="table table-hover table-bordered">
		<?php foreach (Kohana::modules() as $module => $path): ?>
		<tr>
			<th><?php echo $module ?></th>
			<td><?php echo $path ?>
				<?php if (is_file($path.'init.php')) echo ' <small><em>(has init.php file)<em></small>'; ?>
			</td>
		</tr>
		<?php endforeach; ?>
	</table>
<?php else: ?>
<p>No modules loaded</p>
<?php endif; ?>


<script>
$(function(){
	$('table th').attr('width', '200');
});
</script>

