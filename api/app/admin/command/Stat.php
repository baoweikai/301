<?php
namespace app\command;

use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;
use think\cache\driver\Redis;
use app\common\model\Domain;
// use think\facade\Db;
use app\admin\model\Stat as Model;

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
        $dids = Domain::column('id');
        $configs = config('cache.stores');
        $stat = [];
        $date = date('Ymd', strtotime('-1 hour'));
        foreach($configs as $config){
            $redis = new Redis($config);
            foreach($dids as $did){
                if (!isset($stat[$did])) {
                    $stat[$did] = ['date' => date('Y-m-d'), 'domain_id' => $did, 'ip_count' => 0, 'jump_count' => 0, 'cited_count' => 0];
                }

                $stat[$did]['ip_count'] += intval($redis->get('IpCount_' . $did . '_' . $date));
                $stat[$did]['jump_count'] += intval($redis->get('JumpCount_' . $did . '_' . $date));
                $stat[$did]['cited_count'] += intval($redis->get('CitedCount_' . $did . '_' . $date));
            }
        }
        foreach($stat as $did => $row){
            $model = Model::where([['domain_id', '=', $did], ['date', '=', $date]])->find();
            if($model !== null){
                $model->ip_count = $row['ip_count'];
                $model->jump_count = $row['jump_count'];
                $model->cited_count = $row['cited_count'];
                $model->save();
            }
        }

        $output->writeln('执行完成');
    }
}