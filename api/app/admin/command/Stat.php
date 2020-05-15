<?php
namespace app\admin\command;

use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;
use think\cache\driver\Redis;
use app\common\model\Domain;
// use think\facade\Db;
use app\common\model\Stat as Model;

class Stat extends Command
{
    protected function configure()
    {
        $this->setName('stat')
        	->addArgument('name', Argument::OPTIONAL, "your name")
            ->addOption('city', null, Option::VALUE_REQUIRED, 'city name')
        	->setDescription('统计数据');
    }

    // 调用update 这个类时,会自动运行execute方法
    protected function execute(Input $input, Output $output)
    {
        $configs = config('cache.stores');
        $list = [];
        $date = date('Ymd', strtotime('-1 hour'));
        foreach($configs as $config){
            $redis = new Redis($config);
            $IpCount = $redis->handler()->hgetall('IpCount' . $date);
            $JumpCount = $redis->handler()->hgetall('JumpCount' . $date);
            $CitedCount = $redis->handler()->hgetall('CitedCount' . $date);
            $today = date('Y-m-d', strtotime('-1 hour'));

            foreach($IpCount as $k => $val){
                if (!isset($list[$k])) {
                    $list[$k] = ['date' => $today, 'domain_id' => $k, 'ip_count' => 0, 'jump_count' => 0, 'cited_count' => 0];
                }
                $list[$k]['ip_count'] += $val;
            }
            foreach($JumpCount as $k => $val){
                $list[$k]['jump_count'] += $val;
            }
            foreach($CitedCount as $k => $val){
                $list[$k]['cited_count'] += $val;
            }
        }

        $ids = Model::where([['date', '=', $today], ['domain_id', 'in', array_keys($list)]])->column('id', 'domain_id');

        foreach($list as $k => $v){
            if(isset($ids[$k])){
                $list[$k]['id'] = $ids[$k];
            }
        }
        $model = new Model;
        $model->saveAll($list);

        $output->writeln('执行完成');
    }
}