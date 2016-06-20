<h3 class="page-header"><?= implode('-', $params) ?>

<form action="" class="form-inline pull-right" method='get'>
    <div class="form-group">
        <div class="input-group">
            <?php echo Form::select('level', Arr::merge(array('' => 'ALL'), array_combine($levels, $levels)), $level, array('class'=>'form-control')); ?>
            <span class="input-group-btn">
                <button type="submit" class="btn btn-info">Show</button>
            </span>
        </div>
    </div>
</form>

</h3>

<div class="table-responsive">
<table class="table table-hover">
<thead>
<tr>
	<th width="20">No.</th>
	<th width="60">Time</th>
	<th width="80">Level</th>
	<th>Message</th>
</tr>
</thead>
<tbody>
<?php $total = count($msgs);?>
<?php foreach ($msgs as $key=>$message): ?>
<?php 
if ($message['elevel'] == '-') {
    $label = str_replace(array('ERROR', 'WARNING', 'NOTICE', 'INFO', 'DEBUG'), array('danger', 'warning', 'info', 'success', 'primary'), $message['level']);
}
?>
<tr class="text-<?php echo $label ?>">
	<td class="bg-<?php echo $label ?>"><?php echo $total-$key ?></td>
	<td><?php echo $message['time'] ?></td>
	<td><?php echo $message['level'] ?></td>
	<td><?php echo $message['body'] ?></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>
