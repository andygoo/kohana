
<?php if (is_array($values)):?>
<div class="table-responsive">
<table class="table table-hover table-bordered">
<thead>
<tr>
    <th width="60">#</th>
    <th>Value</th>
</tr>
</thead>
<tbody>
<?php foreach ($values as $row=>$value):?>
<tr>
    <td><?= $row?></td>
    <td><?= $value?></td>
</tr>
<?php endforeach;?>
</tbody>
</table>
</div>
<?= $pager ?>

<?php else:?>
<textarea class="form-control" style="height:260px">
<?= $values?>
</textarea>
<?php endif;?>

