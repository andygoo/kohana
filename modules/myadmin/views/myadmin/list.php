<style>
.dropdown-menu>li>label {
  display: block;
  padding: 3px 20px;
  clear: both;
  font-weight: 400;
  line-height: 1.42857143;
  color: #333;
  white-space: nowrap;
  cursor: pointer;
}
.dropdown-menu>li>label:hover {
    color: #262626;
    background-color: #f5f5f5
}
</style>
<h3 class="page-header"><?= $curr_table?> <small>
<a href="<?= URL::site('myadmin/desc/'.$curr_db.'/'.$curr_table)?>">表结构</a>
</small></h3>

<div class="dropdown pull-right">
    <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown">显示字段 <span class="caret"></span></button>
    <ul class="dropdown-menu" id="columns">
        <?php foreach ($columns as $column=>$display):?>
        <li>
        <label style="width: 100%;">
        <input style="vertical-align: top;" type="checkbox" column="<?= $column?>" value="<?= URL::site('myadmin/display/'.$curr_db.'/'.$curr_table.'?field='.$column); ?>" <?php if ($display):?>checked<?php endif;?>> 
        <?= $column?>
        </label>
        </li>
	    <?php endforeach;?>
    </ul>
</div>

<form class="form-inline" method="get">
    <div class="form-group">
        <select class="form-control" name="field_name">
            <?php foreach ($columns as $column=>$display):?>
            <option value="<?= $column?>" <?php if($column == $field_name):?>selected<?php endif;?>><?= $column?></option>
            <?php endforeach;?>
        </select>
    </div>
    <div class="form-group">
        <select class="form-control" name="op">
            <option value="=" <?php if($op == '='):?>selected<?php endif;?>> = </option>
            <option value=">" <?php if($op == '>'):?>selected<?php endif;?>> > </option>
            <option value="&lt;" <?php if($op == '<'):?>selected<?php endif;?>> < </option>
            <option value="!=" <?php if($op == '!='):?>selected<?php endif;?>> != </option>
            <option value="LIKE" <?php if($op == 'LIKE'):?>selected<?php endif;?>> LIKE </option>
        </select>
    </div>
    <div class="form-group">
        <div class="input-group">
            <input type="text" name="field_value" class="form-control" value="<?php echo $field_value?>" placeholder="">
            <span class="input-group-btn">
                <button type="submit" class="btn btn-info">查找</button>
            </span>
        </div>
    </div>
</form>
<br>

<div class="table-responsive">
<table class="table table-hover table-bordered">
<thead>
<tr>
    <?php foreach ($columns as $column=>$display):?>
	<th width="<?= intval(100/count($columns))?>%" class="<?= $column?> <?php if($order=='asc'):?>dropup<?php endif;?>" <?php if (!$display):?>style="display: none;"<?php endif;?>>
	<?php $query_str = URL::query(array('sort'=>$column.'|'.(($order=='desc') ? 'asc' : 'desc')))?>
	<a href="<?= URL::site('myadmin/'.$action.'/'.$curr_db.'/'.$curr_table.$query_str)?>"><?= $column?></a>
	<?php if($field==$column):?><span class="caret"></span><?php endif;?>
	</th>
	<?php endforeach;?>
</tr>
</thead>
<tbody>
<?php foreach($list as $row): ?>
<tr>
    <?php $k=0;?>
    <?php foreach($row as $column=>$val): ?>
    <?php $pk = $row['id']?>
    <?php $display = $columns[$column]?>
	<td class="<?= $column?>" width="<?= intval(100/count($row))?>%" style="<?php if (!$display):?>display: none;<?php endif;?>max-width:150px; overflow: hidden;white-space: nowrap;text-overflow: ellipsis;">
	    <?php if ($column=='id'):?>
	    <?= $val ?>
	    <?php else:?>
	    <span class="editable" data-name="<?= $column?>" data-pk="<?= $pk?>"><?= strip_tags($val) ?></span>
	    <?php endif;?>
	</td>
    <?php $k++;?>
    <?php endforeach; ?>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>

<?= $pager ?>

