
<h3 class="page-header">添加用户</h3>

<form method="post" autocomplete="off" class="col-sm-6 col-md-4 ajax-submit">
	<div class="form-group">
		<select class="form-control" name="role_id">
			<option value="0"> - 选择角色 - </option>
			<?php foreach ($role_list as $item):?>
            <?php if ($item['name']=='超级管理员') continue;?>
			<option value="<?= $item['id']?>"><?= $item['name']?></option>
			<?php endforeach;?>
		</select>
	</div>
	<div class="form-group">
		<input class="form-control" type="text" name="username" placeholder="用户名" required>
	</div>
	<div class="form-group">
		<input class="form-control" type="password" name="password" placeholder="密码" required>
	</div>
	<div class="form-group">
		<button type="submit" class="btn btn-info">提交</button>
	</div>
</form>







