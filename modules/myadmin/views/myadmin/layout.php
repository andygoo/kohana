<!DOCTYPE html>
<html lang="zh-cn">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Myadmin</title>
<?= HTML::style('media/bootstrap/css/bootstrap.min.css')?>
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
<?= HTML::script('media/js/jquery.min.js')?>
<?= HTML::script('media/bootstrap/js/bootstrap.min.js')?>
</head>
<body>

<nav class="navbar navbar-inverse navbar-fixed-top">
	<div class="container-fluid">
		<div class="navbar-header">
			<a class="navbar-brand" href="<?= URL::site('myadmin')?>"><i class="glyphicon glyphicon-home"></i>&nbsp;phpMyAdmin</a>
		</div>
	</div>
</nav>

<div class="container-fluid">
	<div class="row">
		<div class="col-sm-3 col-md-2 sidebar">
			<ul class="nav nav-sidebar">
                <?php foreach ($tables as $table):?>
            	<li <?php if($table==$curr_table):?>class="active"<?php endif;?>>
            	    <?php $url = URL::site('myadmin/list/'.$curr_db.'/'.$table)?>
            	    <a href="<?= $url?>"><?= $table?></a>
            	</li>
				<?php endforeach;?>
            </ul>
		</div>
		<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main" id="content">
            <?= $content ?>
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
	$(document).on('click', 'a', function(){
		var t = $(this);
		//$('.nav-sidebar li').removeClass('active');
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
    window.addEventListener("popstate",function(event) {
        if(event && event.state) {
            document.title = event.state.title;
            $("#content").html(event.state.html);
        } else{
            document.title = currentState.title;
            $("#content").html(currentState.html);
        }
    });
});
</script>

<script>
$(function(){
    $(document).on('change', '#columns input[type=checkbox]', function(){
        var t = $(this);
        var curr_column = t.val();
        var column = t.attr('column');
        if (t.prop('checked')) {
            $('.'+column).show();
            $.get(curr_column+'&status='+1);
        } else {
            $('.'+column).hide();
            $.get(curr_column+'&status='+0);
        }
        return false;
    });
});
</script>
</body>
</html>