<!DOCTYPE html>
<html lang="zh-cn">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>PHPRedmin</title>
<?= HTML::style('media/bootstrap/css/bootstrap.min.css')?>
<?= HTML::style('media/xeditable/css/bootstrap-editable.css')?>
<?= HTML::style('media/css/dashboard.css')?>
<style>
.sidebar{padding-top:0px}
.navbar-inverse{background: #3c8dbc;}
.navbar-inverse .navbar-brand{color:#eee}
.navbar-inverse .navbar-nav>li>a{color:#eee}

.sidebar .nav>li {
    margin-bottom: 0px;
    background-color: #f5f5f5;
    border: 0px;
    border-radius: 0px;
    -webkit-box-shadow: 0px;
    box-shadow: 0px;
    border-top: 1px solid #fff;
    border-bottom: 1px solid #dbdbdb;
}
.nav-sidebar a{color:#555}
.nav-sidebar>.active>a, .nav-sidebar>.active>a:hover, .nav-sidebar>.active>a:focus{
	color: #337ab7; background-color: #fff;border-color: #ddd;
}    
</style>
<?= HTML::script('media/js/jquery.min.js');?>
<?= HTML::script('media/bootstrap/js/bootstrap.min.js');?>
<?= HTML::script('media/xeditable/js/bootstrap-editable.min.js')?>
</head>
<body>
<nav class="navbar navbar-inverse navbar-fixed-top">
	<div class="container-fluid">
		<div class="navbar-header">
			<a class="navbar-brand" href="<?= URL::site('phpredmin')?>"><i class="glyphicon glyphicon-home"></i>&nbsp;PHPRedmin</a>
		</div>
	</div>
</nav>
<div class="container-fluid">
	<div class="row">
		<div class="col-sm-3 col-md-2 sidebar">
			<ul class="nav nav-sidebar">
			    <?php foreach ($dbs as $item):?>
                <li <?php if($item['db']==$curr_db):?>class="active"<?php endif;?>>
                    <a href="<?= URL::site('phpredmin/keys/'.$item['db'])?>"> DB <?= $item['db']?> <span class="badge pull-right" style="background:#5bc0de"><?= $item['keys']?></span></a>
                </li>
                <?php endforeach;?>
            </ul>
        </div>
		<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main" id="content">
		    <?= $content; ?>
	    </div>
    </div>
</div>

<script>
$(function(){
	var currentState = {
	        url: document.location.href,
	        title: document.title,
	        html: $("#content").html()
	    };
	$(document).on('click', '.sidebar a, #pager .pagination a', function() {
		var t = $(this);
		t.parent('li').addClass('active').siblings().removeClass('active');
		var url = t.attr('href');
		if (url.split('#')[0].length) {
    		$.get(url, function(res) {
    			$('#content').html(res);
    			var state = {
                        url: url,
                        title: document.title,
                        html: $("#content").html()
                    };
                    history.pushState(state,null,url);
    		});
		}
		return false;
	});
	
	$(document).on('click', '.view-detail', function() {
		var url = $(this).attr('href');
		var key = $(this).attr('data-key');
		$.get(url, function(res) {
			$('#myModal').modal('show');
			$('#myModal').find('.modal-body').html(res);
			$('#myModal').find('.modal-title').html(key);
	    });
		return false;
	});
	
	$(document).on('click', '#myModal .pagination a', function(){
		var t = $(this);
		var url = t.attr('href');
		if (url.split('#')[0].length) {
			$.get(url, function(res) {
				$('#myModal').find('.modal-body').html(res);
			});
		}
		return false;
	});
});
</script>
</body>
</html>
