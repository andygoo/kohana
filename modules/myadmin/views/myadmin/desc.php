<h3 class="page-header"><?= $curr_table?> <small>
<a href="<?= URL::site('myadmin/list/'.$curr_db.'/'.$curr_table)?>">表数据</a>
</small></h3>

<ul class="nav nav-tabs">
<li class="active"><a href="#field" data-toggle="tab">字段</a></li>
<li><a href="#desc" data-toggle="tab">描述</a></li>
</ul>
<br>

<div class="tab-content">

<div class="tab-pane" id="desc">
    <pre><?php echo $table_desc?></pre>
</div>

<div class="tab-pane active" id="field">
<div class="table-responsive">
<table class="table table-hover table-bordered">
<thead>
<tr>
	<th>Field</th>
	<th>Type</th>
	<th>Collation</th>
	<th>Null</th>
	<th>Key</th>
	<th>Default</th>
	<th>Extra</th>
	<!--<th>Privileges</th>-->
	<th>Comment</th>
</tr>
</thead>
<tbody>
<?php foreach($columns as $column): ?>
<tr>
	<td><?php echo $column['Field'];?></td>
	<td><?php echo $column['Type'];?></td>
	<td><?php echo $column['Collation'];?></td>
	<td><?php echo $column['Null'];?></td>
	<td><?php echo $column['Key'];?></td>
	<td><?php echo $column['Default'];?></td>
	<td><?php echo $column['Extra'];?></td>
	<!--<td><?php echo $column['Privileges'];?></td>-->
	<td><?php echo $column['Comment'];?></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>
</div>

</div>