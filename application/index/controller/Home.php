<?php
namespace app\index\controller;
use think\captcha\Captcha;
class Home
{
    public function index()
    {
        return 'home hello';
    }
//    获取验证码
    public function getCaptcha()
    {
        $captcha = new Captcha();
        $captcha->useCurve = false;
        $captcha->useNoise = false;
        $captcha->length = 4;
        return $captcha->entry();
    }
//    校验api接口
    public function checkCaptcha()
    {
        $input_captcha = input('captcha');
        $captcha = new Captcha();
        if ($captcha->check($input_captcha)) {
            return json([
                'status' => 1,
                'msg' => '验证成功'
            ]);
        } else {
            return json([
                'status' => 0,
                'msg' => '验证失败'
            ]);
        }
    }
}
?>