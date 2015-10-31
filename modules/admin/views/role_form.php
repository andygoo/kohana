
<form action="" method="post" class="form-horizontal">

	<div class="form-group">
		<label class="col-sm-1 control-label">名称</label>
		<div class="col-sm-3">
			<input type="text" class="form-control" name="name" value="<?= $info['name'] ?>">
		</div>
	</div>

	<div class="form-group">
		<label class="col-sm-1 control-label">状态</label>
		<div class="col-sm-3">
			<select name="status" class="form-control">
				<option value="normal" <?php if ($info['status'] == 'normal') echo 'selected';?>>open</option>
				<option value="disable" <?php if ($info['status'] == 'disable') echo 'selected';?>>close</option>
			</select>
		</div>
	</div>
	
	<div class="form-group">
		<label class="col-sm-1 control-label">权限</label>
		<div class="col-sm-10">
            <?php foreach ($permits as $cat => $items):?>
                <h4><strong><?= $cat?></strong></h4>
                <?php foreach ($items as $item):?>
                <label class="checkbox-inline">
                    <input type="checkbox" name="permit_ids[]" value="<?= $item['id']?>" data-url="<?= $item['url']?>" <?php if(in_array($item['id'], explode(',', $info['permit_ids']))):?>checked<?php endif;?>> <?= $item['name']?>
                </label>
                <?php endforeach;?><hr>
            <?php endforeach;?>
		</div>
	</div>

	<div class="form-group">
		<div class="col-sm-offset-1 col-sm-3">
			<button type="submit" class="btn btn-primary">提交</button>
		</div>
	</div>
</form>
