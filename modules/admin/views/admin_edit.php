
<h3 class="page-header">修改用户</h3>

<form action="" method="post" autocomplete="off" class="col-sm-6 col-md-4">
	<div class="form-group">
		<p class="form-control-static"><?= $userInfo['username']?></p>
	</div>
	<div class="form-group">
		<select class="form-control" name="role_id">
			<option value="0"> -选择角色- </option>
			<?php foreach ($role_list as $item):?>
            <?php if ($item['name']=='超级管理员') continue;?>
			<option value="<?= $item['id']?>" <?php if($userInfo['role_id']==$item['id']):?>selected<?php endif;?>><?= $item['name']?></option>
			<?php endforeach;?>
		</select>
	</div>
	<div class="form-group">
		<button type="submit" class="btn btn-info">提交</button>
	</div>
</form>
