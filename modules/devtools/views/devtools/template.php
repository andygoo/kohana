<!DOCTYPE html>
<html lang="zh-cn">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>DevTools</title>
<?= HTML::style('media/bootstrap/css/bootstrap.min.css')?>
<?= HTML::style('media/css/dashboard.css')?>
<?= HTML::style('media/offcanvas/css/bootstrap.offcanvas.css')?>
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

.navbar-toggle {border :none}
.navbar-inverse .navbar-toggle:focus, .navbar-inverse .navbar-toggle:hover {
	background-color:transparent;
}
@media (max-width: 767px) {
    .navbar-offcanvas {
        z-index: 9999;top: 50px;
    }
    .navbar-offcanvas.offcanvas-transform.in {
    	background-color: #f5f5f5;
    }
}
</style>
<?= HTML::script('media/js/jquery.min.js')?>
<?= HTML::script('media/offcanvas/js/bootstrap.offcanvas.js')?>
</head>
<body>
<nav class="navbar navbar-inverse navbar-fixed-top">
	<div class="container-fluid">
		<div class="navbar-header">
			<a class="navbar-brand" href="<?= URL::site('devtools')?>"><i class="glyphicon glyphicon-home"></i>&nbsp;DevTools</a>
		    <button type="button" class="navbar-toggle offcanvas-toggle pull-right" data-toggle="offcanvas" data-target="#js-bootstrap-offcanvas" style="float:left;">
                <span class="sr-only">Toggle navigation</span>
                <span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
                </span>
            </button>
		</div>
	</div>
</nav>
<?php $action = Request::instance()->action; ?>
<div class="container-fluid">
	<div class="row">
		<div class="col-sm-3 col-md-2 sidebar">
			<div class="navbar-offcanvas navbar-offcanvas-touch" id="js-bootstrap-offcanvas">
			<ul class="nav nav-sidebar">
                <li <?php if($action=='info'):?>class="active"<?php endif;?>><?php echo HTML::anchor(Route::get('devtools')->uri(array('action'=>'info')),'Kohana info') ?></li>
				<li <?php if($action=='phpinfo'):?>class="active"<?php endif;?>><?php echo HTML::anchor(Route::get('devtools')->uri(array('action'=>'phpinfo')),'PHP info') ?></li>
				<li <?php if($action=='routetest'):?>class="active"<?php endif;?>><?php echo HTML::anchor(Route::get('devtools')->uri(array('action'=>'routetest')),'Route tester') ?></li>
				<li <?php if($action=='routes'):?>class="active"<?php endif;?>><?php echo HTML::anchor(Route::get('devtools')->uri(array('action'=>'routes')),'Route dump') ?></li>
			</ul>
			</div>
		</div>
		<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main" id="content">
			<?php echo $content; ?>
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

</body>
</html>