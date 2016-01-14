<h3 class="page-header">角色列表
<a href="<?= URL::site('role/add');?>" class="ajax-modal">+</a>
</h3>

<div class="table-responsive">
<table class="table table-hover table-bordered">
<thead>
<tr>
	<th width="60">ID</th>
	<th width="130">名称</th>
	<th>权限</th>
	<th width="80">状态</th>
	<th width="80">操作</th>
</tr>
</thead>
<tbody>
<?php foreach($list as $item): ?>
<?php if ($item['name']=='超级管理员') continue;?>
<tr>
	<td><?= $item['id'] ?></td>
	<td><?= $item['name'] ?></td>
	<td><?= $item['permit'] ?></td>
	<td>
        <?php if ($item['status']=='normal'):?>
        <a href="<?= URL::site('role/disable?id='.$item['id']);?>" class="btn btn-info btn-xs">禁用</a>
        <?php else:?>
        <a href="<?= URL::site('role/enable?id='.$item['id']);?>" class="btn btn-danger btn-xs">启用</a>
        <?php endif;?>
	</td>
	<td>
	    <a href="<?= URL::site('role/edit?id='.$item['id']);?>" class="btn btn-info btn-xs ajax-modal">修改</a>
	</td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>

<?= $pager ?>
