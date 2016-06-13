<h3 class="page-header">权限列表 
<a href="<?= URL::site('permit/add');?>" class="ajax-modal">+</a>
</h3>

<form class="form-inline ajax-submit" method="get">
    <div class="form-group">
        <div class="input-group">
            <select class="form-control" name="cat">
    			<option value="">选择分类</option>
    			<?php foreach ($cats as $item):?>
    			<option value="<?= $item?>" <?php if($item==Arr::get($_GET, 'cat')):?>selected<?php endif;?>><?= $item?></option>
    			<?php endforeach;?>
            </select>
            <span class="input-group-btn">
                <button class="btn btn-info" type="submit">查找</button>
            </span>
        </div>
    </div>
</form>
<br>

<div class="table-responsive">
<table class="table table-hover table-bordered">
<thead>
<tr>
	<th>ID</th>
	<th>Cat</th>
	<th>Name</th>
	<th>URL</th>
	<th width="150">操作</th>
</tr>
</thead>
<tbody>
<?php foreach($list as $item): ?>
<tr>
	<td><?= $item['id'] ?></td>
	<td><?= $item['cat'] ?></td>
	<td><?= $item['name'] ?></td>
	<td><?= $item['url'] ?></td>
	<td>
        <a href="<?= URL::site('permit/edit?id='.$item['id']);?>" class="btn btn-info btn-xs ajax-modal">修改</a>&nbsp;&nbsp;
	    <a href="<?= URL::site('permit/del?id='.$item['id']);?>" onclick="return confirm('确定删除这条记录吗？')" class="btn btn-info btn-xs ajax-del">删除</a></a>
	</td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>

<?= $pager ?>
