<?php

class Controller_Alipay extends Controller {

    public function action_pay() {
        $total = "0.01";
        $order_id = date("Ymd") . rand(1000, 2000);
        
        $order_info = array(
            'order_id' => $order_id,
            'order_amount' => $total,
        );
        
        $payment = Payment::instance('alipay');
        $pay_url = $payment->get_pay_url($order_info);
        $this->redirect($pay_url);
    }

    public function action_notify() {
        $payment = Payment::instance('alipay');
        if ($payment->verify_sign($_POST)) {
            //请在这里加上商户的业务逻辑程序代
            

            //——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
            //获取支付宝的通知返回参数，可参考技术文档中服务器异步通知参数列表
            $out_trade_no = $_POST['out_trade_no']; //获取订单号
            $trade_no = $_POST['trade_no']; //获取支付宝交易号
            $total_fee = $_POST['total_fee']; //获取总价格
            

            if ($_POST['trade_status'] == 'TRADE_FINISHED') {
                //判断该笔订单是否在商户网站中已经做过处理
                //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
                //如果有做过处理，不执行商户的业务程序
                

                //注意：
                //该种交易状态只在两种情况下出现
                //1、开通了普通即时到账，买家付款成功后。
                //2、开通了高级即时到账，从该笔交易成功时间算起，过了签约时的可退款时限（如：三个月以内可退款、一年以内可退款等）后。
                

                //调试用，写文本函数记录程序运行情况是否正常
                //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
            } else if ($_POST['trade_status'] == 'TRADE_SUCCESS') {
                //判断该笔订单是否在商户网站中已经做过处理
                //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
                //如果有做过处理，不执行商户的业务程序
                

                //注意：
                //该种交易状态只在一种情况下出现——开通了高级即时到账，买家付款成功后。
                

                //调试用，写文本函数记录程序运行情况是否正常
                //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
            }
            
            //——请根据您的业务逻辑来编写程序（以上代码仅作参考）——
            

            echo "success"; //请不要修改或删除
        }
    }

    public function action_return() {
        $payment = Payment::instance('alipay');
        /*
        https://tradeexprod.alipay.com/fastpay/cashierReturnMiddlePage.htm?
        tradeNo=2012123125748882&
        isNeedAsync=false&
        sign=K1ib4ElSNeZ%252FUg3Tgm9X4YFD%252F4TaoQsv0qJQupgftmPFCQ%253D%253D
        
        http://jiesc.net/alipay/return_url.php?
        buyer_email=xiaowenjie0%40gmail.com&
        buyer_id=2088102240322824&
        exterface=create_direct_pay_by_user&
        is_success=T&
        notify_id=RqPnCoPT3K9%252Fvwbh3I70WVibnzyu8zCUGzIMPl1%252BS7e81CFHrUXc9TSFqyp4WBKOwifp&
        notify_time=2012-12-31+12%3A50%3A11&
        notify_type=trade_status_sync&
        out_trade_no=201212311887&
        payment_type=8&
        seller_email=brtc2011%40yahoo.cn&
        seller_id=2088701119218789&
        subject=%E7%9F%A5%E6%88%91%E7%BD%91+-+%E4%BA%A4%E6%98%93%E7%BC%96%E5%8F%B7+-+1201212311887&
        total_fee=0.01&
        trade_no=2012123125748882&
        trade_status=TRADE_SUCCESS&
        sign=3d6fd65662c412e2d57e921059379469&
        sign_type=MD5
        */
        
        if ($payment->verify_sign($_GET)) {
            //请在这里加上商户的业务逻辑程序代码
            

            //——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
            //获取支付宝的通知返回参数，可参考技术文档中页面跳转同步通知参数列表
            $out_trade_no = $_GET['out_trade_no']; //获取订单号
            $trade_no = $_GET['trade_no']; //获取支付宝交易号
            $total_fee = $_GET['total_fee']; //获取总价格
            

            if ($_GET['trade_status'] == 'TRADE_FINISHED' || $_GET['trade_status'] == 'TRADE_SUCCESS') {
                //判断该笔订单是否在商户网站中已经做过处理
                //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
                //如果有做过处理，不执行商户的业务程序
            } else {
                echo "trade_status=" . $_GET['trade_status'];
            }
            
            echo "验证成功<br />";
            echo "trade_no=" . $trade_no;
            //——请根据您的业务逻辑来编写程序（以上代码仅作参考）——
        }
    }
}
