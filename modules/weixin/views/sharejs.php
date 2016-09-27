
var wx_share_info = {};
wx_share_info.title = '<?= !empty($_GET['title']) ? htmlspecialchars($_GET['title']) : '';?>';
wx_share_info.desc = '<?= !empty($_GET['desc']) ? htmlspecialchars($_GET['desc']) : '';?>';
wx_share_info.link = '<?= !empty($_GET['link']) ? htmlspecialchars($_GET['link']) : '';?>';
wx_share_info.pic = '<?= !empty($_GET['pic']) ? htmlspecialchars($_GET['pic']) : '';?>';

var wx_jsapi_config = <?= $jsapi_config;?>;
wx_jsapi_config.jsApiList.push('onMenuShareTimeline','onMenuShareAppMessage');
wx.config(wx_jsapi_config);

wx.ready(function () {
    wx.onMenuShareAppMessage({
        title: wx_share_info.title, // 分享标题
	    desc: wx_share_info.desc ? wx_share_info.desc : location.title, // 分享描述
	    link: wx_share_info.link ? wx_share_info.link : location.href, // 分享链接
	    imgUrl: wx_share_info.pic ? wx_share_info.pic : document.images[0].src, // 分享图标
	    type: 'link', // 分享类型,music、video或link，不填默认为link
	    dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
	    success: function () {
	        //alert('success');
	    },
	    cancel: function () {
	        //alert('cancel');
	    }
	});
    wx.onMenuShareTimeline({
        title: wx_share_info.title ? wx_share_info.title : document.title, // 分享标题
	    link: wx_share_info.link ? wx_share_info.link : location.href, // 分享链接
	    imgUrl: wx_share_info.pic ? wx_share_info.pic : document.images[0].src, // 分享图标
	    success: function () {
	        //alert('success');
	    },
	    cancel: function () {
	        //alert('cancel');
	    }
	});
});
