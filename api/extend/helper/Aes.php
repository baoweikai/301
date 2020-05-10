<?php

namespace helper;

/**
 * AES加密/解密工具类
 */
class Aes
{
    private $key = 'PdtjWh8szPBf7wn5';
    private $method = 'aes-128-cbc';
    private $iv = 'I9WetNeSpJDCqOse';

    public function __construct($key, $iv = '')
    {
        // 是否启用了openssl扩展
        extension_loaded('openssl') or die('未启用 OPENSSL 扩展');
        $this->key = !empty($key) ? $key : $this->key;
        $this->iv = !empty($iv) ? $iv : $this->iv;
    }

    public function encrypt($plaintext)
    {
        if(!in_array($this->method, openssl_get_cipher_methods()))
        {
            die('不支持该加密算法!');
        }
        // options为1, 不需要手动填充
        //$plaintext = $this->padding($plaintext);
        // 获取加密算法要求的初始化向量的长度
        // 生成对应长度的初始化向量. aes-128模式下iv长度是16个字节, 也可以自由指定.
        if(empty($this->iv)){
            $ivlen = openssl_cipher_iv_length($this->method);
            $this->iv = openssl_random_pseudo_bytes($ivlen);
        }
        // 加密数据
        $ciphertext = openssl_encrypt($plaintext, $this->method, $this->key, 1, $this->iv);
        // $hmac = hash_hmac('sha256', $ciphertext, $this->key, false);

        return base64_encode($this->iv . $ciphertext);
    }

    public function decrypt($ciphertext)
    {
        $ciphertext = base64_decode($ciphertext);
        // $ivlen = openssl_cipher_iv_length($this->method);

        $plaintext = openssl_decrypt($ciphertext, $this->method, $this->key, 1, $this->iv);
        // 加密时未手动填充, 不需要去填充
        //if($plaintext)
        //{
        //    $plaintext = $this->unpadding($plaintext);
        //    echo $plaintext;
        //}
        return $plaintext;
    }

    private function padding(string $data) : string
    {
        $padding = 16 - (strlen($data) % 16);
        $chr = chr($padding);
        return $data . str_repeat($chr, $padding);
    }

    private function unpadding($ciphertext)
    {
        $chr = substr($ciphertext, -1);
        $padding = ord($chr);

        if($padding > strlen($ciphertext))
        {
            return false;
        }

        if(strspn($ciphertext, $chr, -1 * $padding, $padding) !== $padding)
        {
            return false;
        }

        return substr($ciphertext, 0, -1 * $padding);
    }
}
