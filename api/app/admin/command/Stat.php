<?php
namespace app\command;

use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;

class Stat extends Command
{
    protected function configure()
    {
        $this->setName('fresh')
        	->addArgument('name', Argument::OPTIONAL, "your name")
            ->addOption('city', null, Option::VALUE_REQUIRED, 'city name')
        	->setDescription('统计数据');
    }

    // 调用update 这个类时,会自动运行execute方法
    protected function execute(Input $input, Output $output)
    {
        $output->writeln('执行完成');
    }
}