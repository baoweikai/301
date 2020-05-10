<?php
namespace core;

class Redis extends \think\cache\Driver\Redis
{

    /**
     * 读取缓存
     * @access public
     * @param string $name    缓存变量名
     * @param mixed  $default 默认值
     * @return mixed
     */
    public function hget($key, $field, $default = null)
    {
        $this->readTimes++;

        $value = $this->handler->hget($this->getCacheKey($key), $field);

        if (false === $value || is_null($value)) {
            return $default;
        }

        return $this->unserialize($value);
    }

    /**
     * 写入缓存
     * @access public
     * @param string            $name   缓存变量名
     * @param mixed             $value  存储数据
     * @param integer|\DateTime $expire 有效时间（秒）
     * @return bool
     */
    public function hset($key, $field, $value, $expire = null): bool
    {
        $this->writeTimes++;

        if (is_null($expire)) {
            $expire = $this->options['expire'];
        }

        $key    = $this->getCacheKey($key);
        $expire = $this->getExpireTime($expire);
        $value  = $this->serialize($value);

        $this->handler->hset($key, $field, $value);
        
        return true;
    }
}