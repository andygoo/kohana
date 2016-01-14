<h3 class="page-header">用户列表 
<a href="<?= URL::site('admin/add');?>" class="ajax-modal-sm">+</a>
</h3>

<div class="table-responsive">
<table class="table table-hover table-bordered">
<thead>
<tr>
    <th width="60">ID</th>
    <th width="15%">名称</th>
    <th width="15%">角色</th>
    <th width="20%">登录IP</th>
    <th width="20%">登录时间</th>
    <th width="80">状态</th>
    <th width="80">操作</th>
</tr>
</thead>
<tbody>
<?php foreach($list as $item): ?>
<?php if ($item['username']=='admin') continue;?>
<tr>
    <td><?= $item['id'] ?></td>
    <td><?= $item['username'] ?></td>
    <td class="<?= $item['rolestatus']=='normal' ? 'text-info' : 'text-danger';?>"><?= $item['rolename'] ?></td>
    <td><?= $item['client_ip'] ?></td>
    <td><?= date('Y-m-d H:i:s', $item['last_login']) ?></td>
    <td>
        <?php if ($item['status']=='normal'):?>
        <a href="<?= URL::site('admin/disable?id='.$item['id']);?>" class="btn btn-info btn-xs">禁用</a>
        <?php else:?>
        <a href="<?= URL::site('admin/enable?id='.$item['id']);?>" class="btn btn-danger btn-xs">启用</a>
        <?php endif;?>
    </td>
    <td>
        <a href="<?= URL::site('admin/edit?id='.$item['id']);?>" class="btn btn-info btn-xs ajax-modal-sm">修改</a>
    </td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>
<?= $pager?>

