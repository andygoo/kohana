
<form action="" method="post">
	<div class="form-group">
		<input type="text" style="width: 200px;" class="form-control" name="name" value="<?= $info['name'] ?>" placeholder="名称" required>
	</div>
	<div class="form-group">
		<label class="radio-inline">
            <input type="radio" name="status" value="normal" checked> 正常
        </label>
        <label class="radio-inline">
            <input type="radio" name="status" value="disable" <?php if ($info['status'] == 'disable') echo 'checked';?>> 关闭
        </label>
	</div>
	<div class="form-group">
        <?php foreach ($permits as $cat => $items):?>
        <div class="panel panel-default">
        <div class="panel-heading"><?php echo $cat?></div>
        <ul class="list-group" style="display: noned">
            <li class="list-group-item">
            <?php foreach ($items as $item):?>
            <label class="checkbox-inline" style="margin-left: 0;margin-right: 10px;">
                <input type="checkbox" name="permit_ids[]" value="<?= $item['id']?>" data-url="<?= $item['url']?>" <?php if(in_array($item['id'], explode(',', $info['permit_ids']))):?>checked<?php endif;?>> <?= $item['name']?>
            </label>
            <?php endforeach;?>
            </li>
        </ul>
        </div>
        <?php endforeach;?>
	</div>
	<div class="form-group">
		<button type="submit" class="btn btn-info">提交</button>
	</div>
</form>
