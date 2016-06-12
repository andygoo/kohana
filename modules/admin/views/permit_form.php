
<form action="" method="post" class="col-sm-6 col-md-4">
	<div class="form-group">
		<input type="text" class="form-control" list="cats" name="cat" value="<?= $info['cat'] ?>" placeholder="类别">
		<datalist id="cats">
		    <?php foreach ($cats as $item):?>
            <option value="<?= $item?>">
            <?php endforeach;?>
        </datalist>
	</div>
	<div class="form-group">
		<input type="text" class="form-control" name="name" value="<?= $info['name'] ?>" placeholder="名称">
	</div>
	<div class="form-group">
		<input type="text" class="form-control" name="url" value="<?= $info['url'] ?>" placeholder="URL">
	</div>
	<div class="form-group">
		<button type="submit" class="btn btn-info">提交</button>
	</div>
</form>
