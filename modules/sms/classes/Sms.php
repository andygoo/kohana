<?php

class Sms {

    public static function button($name, $value, array $attributes = NULL) {
        $id = 'send_sms_btn_' . mt_rand(1000, 9999);
        $url = URL::site('sms/send');
        $js = <<<EOF
        <script>
        $(function() {
            var timer = null;
            var countdown = 60;
            $('#$id').click(function() {
                var t = $(this);
                t.attr('disabled', true);
                var url = '$url';
                var params = {};
                params.type = 'reg';
                params.phone = $('input[name=$name]').val();
                $.get(url, params, function(res) {
                    alert(res.info);
                    if (res.status=='n') {
                        t.attr('disabled', false);
                    } else {
                        if(typeof(res.timeleft)!='undefined') {
                            countdown = res.timeleft;
                        }
                        t.val(countdown+"秒后重发");
                        clearTimeout(timer);
                        timer = setInterval(function() {
                            if (countdown == 1) {
                                clearTimeout(timer);
                                t.attr('disabled', false);
                                t.val("获取验证码");
                                countdown = 60;
                            } else {
                                countdown--;
                                t.val(countdown+"秒后重发");
                            }
                        },1000);
                    }
                });
            });
        });
        </script>
EOF;
        $attributes['type'] = 'button';
        return "<input id=\"$id\" value=\"$value\" ".HTML::attributes($attributes).">\n$js";
    }

    public static function send($phone, $content) {
        $sms_url = "http://utf8.sms.webchinese.cn/?Uid=a5436530&Key=56bd158dd5586s9d12dse&smsMob=$phone&smsText=$content";
        $sms_return = file_get_contents($sms_url);
        if ($sms_return == '1') {
            //$smsid = $sms[0];
            //$ret = $m_sms->update(array('send_time'=>time(), 'status'=>1), $smsid);
        }
    }
    
    public static function valid($smscode) {
        $session = Session::instance();
        $sms_verify = $session->get('sms_verify');
        return $smscode==$sms_verify;
    }

}

