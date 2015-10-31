
<h3 class="page-header">添加用户</h3>

<form action="" method="post" class="form-horizontal" autocomplete="off">
	<div class="form-group">
		<label class="col-sm-1 control-label">角色</label>
		<div class="col-sm-3">
			<select class="form-control" name="role_id">
				<option value="0"> - </option>
				<?php foreach ($role_list as $item):?>
                <?php if ($item['name']=='超级管理员') continue;?>
				<option value="<?= $item['id']?>"><?= $item['name']?></option>
				<?php endforeach;?>
			</select>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-1 control-label">用户</label>
		<div class="col-sm-3">
			<input class="form-control" type="text" name="username" required>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-1 control-label">密码</label>
		<div class="col-sm-3">
			<input class="form-control" type="password" name="password" required>
		</div>
	</div>
	<div class="form-group">
		<div class="col-sm-offset-1 col-sm-3">
			<button type="submit" class="btn btn-primary">提交</button>
		</div>
	</div>
</form>







