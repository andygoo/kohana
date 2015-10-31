
<h3 class="page-header">修改用户</h3>

<form action="" method="post" class="form-horizontal" autocomplete="off">
	<div class="form-group">
		<label class="col-sm-1 control-label">用户</label>
		<div class="col-sm-3">
			<p class="form-control-static"><?= $userInfo['username']?></p>
		</div>
	</div>

	<div class="form-group">
		<label class="col-sm-1 control-label">角色</label>
		<div class="col-sm-3">
			<select class="form-control" name="role_id">
				<option value="0"> - </option>
				<?php foreach ($role_list as $item):?>
                <?php if ($item['name']=='超级管理员') continue;?>
				<option value="<?= $item['id']?>" <?php if($userInfo['role_id']==$item['id']):?>selected<?php endif;?>><?= $item['name']?></option>
				<?php endforeach;?>
			</select>
		</div>
	</div>
	<div class="form-group">
		<div class="col-sm-offset-1 col-sm-3">
			<button type="submit" class="btn btn-primary">提交</button>
		</div>
	</div>
</form>
