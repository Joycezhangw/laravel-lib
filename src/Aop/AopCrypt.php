<?php


namespace JoyceZ\LaravelLib\Aop;

/**
 * 加密工具
 * Class AopCrypt
 * @author alipay  https://github.com/alipay/alipay-sdk-php-all
 * @package JoyceZ\LaravelLib\Aop
 */
class AopCrypt
{
    /**
     * 密钥
     * @var string
     */
    protected $screctKey = '';


    /**
     * 设置密码加密盐
     * @param string $screctKey 加密盐
     * @return $this
     */
    public function withScrectKey(string $screctKey = '')
    {
        $this->screctKey = $screctKey ?? config('laraveladmin.crypt.screct_key');
        return $this;
    }

    /**
     * 加密方法
     * @param string $str
     * @return string
     */
    public function encrypt($str)
    {
        //AES, 128 模式加密数据 CBC
        $screct_key = base64_decode($this->screctKey);
        $str = trim($str);
        $str = $this->addPKCS7Padding($str);

        //设置全0的IV

        $iv = str_repeat("\0", 16);
        $encrypt_str = openssl_encrypt($str, 'aes-128-cbc', $screct_key, OPENSSL_NO_PADDING, $iv);
        return base64_encode($encrypt_str);
    }

    /**
     * 解密方法
     * @param string $str
     * @return string
     */
    public function decrypt($str)
    {
        //AES, 128 模式加密数据 CBC
        $str = base64_decode($str);
        $screct_key = base64_decode($this->screctKey);

        //设置全0的IV
        $iv = str_repeat("\0", 16);
        $decrypt_str = openssl_decrypt($str, 'aes-128-cbc', $screct_key, OPENSSL_NO_PADDING, $iv);
        $decrypt_str = $this->stripPKSC7Padding($decrypt_str);
        return $decrypt_str;
    }

    /**
     * 填充算法
     * @param string $source
     * @return string
     */
    private function addPKCS7Padding($source)
    {
        $source = trim($source);
        $block = 16;

        $pad = $block - (strlen($source) % $block);
        if ($pad <= $block) {
            $char = chr($pad);
            $source .= str_repeat($char, $pad);
        }
        return $source;
    }


    /**
     * 移去填充算法
     * @param string $source
     * @return string
     */
    private function stripPKSC7Padding($source)
    {
        $char = substr($source, -1);
        $num = ord($char);
        if ($num == 62) return $source;
        $source = substr($source, 0, -$num);
        return $source;
    }
}