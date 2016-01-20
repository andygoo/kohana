<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title>微信公众平台</title>
</head>
<body>
<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script>
wx.config(<?php echo $jsapi_config;?>);
var share_appmsg_config = {
    title: '拒绝炸成灰糖果礼物一大堆！', // 分享标题
    desc: '一起来抢糖果啦！我在炸弹堆里抢到了糖果，获得了丰厚的奖品~你也一起来玩儿吧!', // 分享描述
    link: '/activity/halloween/index', // 分享链接
    imgUrl: '/activity/halloween/img/share.jpg', // 分享图标
    type: 'link', // 分享类型,music、video或link，不填默认为link
    dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
    success: function () {
        alert('success');
    },
    cancel: function () {
        alert('cancel');
    }
}
var share_timeline_config = {
    title: '一起来抢糖果啦！我在好车无忧炸弹堆里抢到了糖果，获得了丰厚的奖品~你也一起来玩儿吧!', // 分享标题
    link: 'activity/halloween/index', // 分享链接
    imgUrl: 'activity/halloween/img/share.jpg', // 分享图标
    success: function () {
        alert('success');
    },
    cancel: function () {
        alert('cancel');
    }
}
wx.ready(function () {
    wx.onMenuShareAppMessage(share_appmsg_config);
    wx.onMenuShareTimeline(share_timeline_config);
});
</script>
</body>
</html>
