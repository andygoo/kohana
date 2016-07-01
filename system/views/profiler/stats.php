<?php defined('SYSPATH') OR die('No direct script access.') ?>

<?php
$group_stats      = Profiler::group_stats();
$group_cols       = array('min', 'max', 'average', 'total');
$application_cols = array('min', 'max', 'average', 'current');
?>

<style>
<?php include __DIR__ . '/bootstrap.min.css'; ?>
<?php include __DIR__ . '/style.css'; ?>
</style>

<a id="profiler_btn" style="position:fixed;bottom:30px;right:30px" type="button" class="btn btn-info btn-fab">P</a>

<div class="modal" id="profiler_modal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            <h4 class="modal-title">Profiler</h4>
        </div>
        <div class="modal-body">
            <?php foreach (Profiler::groups() as $group => $benchmarks): ?>
            <table class="table table-bordered">
            	<tr>
            		<th rowspan="2"><?= ucfirst($group) ?></th>
            		<td colspan="4"><?= number_format($group_stats[$group]['total']['time'], 6) ?> s</td>
            	</tr>
            	<tr>
            		<td colspan="4"><?= number_format($group_stats[$group]['total']['memory'] / 1024, 4) ?> kB</td>
            	</tr>
            	<tr>
            		<th>Benchmark</th>
            		<?php foreach ($group_cols as $key): ?>
            		<th><?= ucfirst($key) ?></th>
            		<?php endforeach ?>
            	</tr>
            	<?php foreach ($benchmarks as $name => $tokens): ?>
            	<tr>
            		<?php $stats = Profiler::stats($tokens) ?>
            		<td rowspan="2" width="40%"><?= HTML::chars($name) . ' (' . count($tokens) . ')' ?></td>
            		<?php foreach ($group_cols as $key): ?>
            		<td><?= number_format($stats[$key]['time'], 6) ?> s</td>
            		<?php endforeach ?>
            	</tr>
            	<tr>
            		<?php foreach ($group_cols as $key): ?>
            		<td><?= number_format($stats[$key]['memory'] / 1024, 4) ?> kB</td>
            		<?php endforeach ?>
            	</tr>
            	<?php endforeach ?>
            </table>
            <?php endforeach ?>
            
            <table class="table table-bordered">
            	<?php $stats = Profiler::application() ?>
            	<tr>
            		<th rowspan="2" width="40%">Application Execution <?= '('.$stats['count'].')' ?></th>
            		<?php foreach ($application_cols as $key): ?>
            		<td><?= number_format($stats[$key]['time'], 6) ?> s</td>
            		<?php endforeach ?>
            	</tr>
            	<tr>
            		<?php foreach ($application_cols as $key): ?>
            		<td><?= number_format($stats[$key]['memory'] / 1024, 4) ?> kB</td>
            		<?php endforeach ?>
            	</tr>
            </table>
        </div>
    </div>
  </div>
</div>

<script>
if (typeof jQuery !== 'undefined') {
$(function() {
	$('#profiler_btn').click(function() {
		var modal_backdrop = $('<div class="modal-backdrop in"></div>');
		$('#profiler_modal').show();
	    $('body').append(modal_backdrop);
	    $('#profiler_modal').click(function(e) {
		    if ($(e.target).closest('.modal-content').length==0 || $(e.target).closest('.close').length!=0) {
		        $('#profiler_modal').hide();
		        modal_backdrop.remove();
		    }
		});
	});
});
}
</script>
