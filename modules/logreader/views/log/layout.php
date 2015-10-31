<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>LogReader</title>
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
			<a class="navbar-brand" href="#"><i class="glyphicon glyphicon-home"></i>&nbsp;Log Reader</a>
		</div>
	</div>
</nav>

<div class="container-fluid">
	<div class="row">
		<div class="col-sm-3 col-md-2 sidebar">
			<ul class="nav nav-sidebar">
                <?php $now = strtotime('now');?>
			    <?php for ($i=0; $i<14; $i++):?>
			    <?php $logfile = realpath(APPPATH . '/logs/'.date('Y/m/d', $now).'.php');?>
			    <?php if(file_exists($logfile)):?>
        		<li class="<?php if(implode('-', $params) == date('Y-m-d', $now)) echo 'active' ?>">
        			<?php echo HTML::anchor(URL::site('log/show/' . date('Y/m/d', $now)), date('Y-m-d', $now));?>
        		</li>
        		<?php endif;?>
        		<?php $now = strtotime('-1 day', $now);?>
			    <?php endfor;?>
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
	$(document).on('click', 'a', function(){
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

</body>
</html>