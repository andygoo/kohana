
<form action="" method="post" class="form-horizontal">

	<div class="form-group">
		<label class="col-sm-1 control-label">类别</label>
		<div class="col-sm-3">
			<input type="text" class="form-control" list="cats" name="cat" value="<?= $info['cat'] ?>">
			<datalist id="cats">
			    <?php foreach ($cats as $item):?>
                <option value="<?= $item?>">
                <?php endforeach;?>
            </datalist>
		</div>
	</div>

	<div class="form-group">
		<label class="col-sm-1 control-label">名称</label>
		<div class="col-sm-3">
			<input type="text" class="form-control" name="name" value="<?= $info['name'] ?>">
		</div>
	</div>

	<div class="form-group">
		<label class="col-sm-1 control-label">URL</label>
		<div class="col-sm-3">
			<input type="text" class="form-control" name="url" value="<?= $info['url'] ?>">
		</div>
	</div>
	
	<div class="form-group">
		<div class="col-sm-offset-1 col-sm-3">
			<button type="submit" class="btn btn-primary">提交</button>
		</div>
	</div>
</form>
