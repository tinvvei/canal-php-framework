<?php

namespace core\canal;

use core\Db;
use core\Log;
use xingwenge\canal_php\CanalClient;
use xingwenge\canal_php\CanalConnectorFactory;

/**
 * canal客户端
 * Class Canal
 * @package core\canal
 * @author 徐庭威
 */
class Canal
{
    protected $host;

    protected $port;

    protected $filters;

    public function __construct($ip, $port = '11111',  $filters = '.*\\..*')
    {
        $this->host = $ip;
        $this->port = $port;
        $this->filters = $filters;
    }

    protected function isBreak($e)
    {
        $info = [
            'Socket operation failed',
            'Socket error'
        ];

        $error = $e->getMessage();

        foreach ($info as $msg) {
            if (false !== stripos($error, $msg)) {
                return true;
            }
        }
        return false;
    }


    protected function reconnect($client)
    {
        $interval = [
            5,
            7,
            10,
            20,
            30,
            50,
            70,
            100,
            300,
            500,
            700,
            3600,
            7200
        ];

        foreach ($interval as $key => $second) {
            $times = $key + 1;
            Log::info("正在尝试第{$times}次重连.......\n");

            try {
                $client->connect($this->host, $this->port);
                $client->subscribe("1001", "example", $this->filters);

                Log::info("重连成功\n");
                break;
            } catch (\Exception $e) {

                if ($times == count($interval)) {
                    Log::info("重连失败\n");
                }

                sleep($second);
                continue;
            }
        }
    }



    public function run()
    {
        $client = CanalConnectorFactory::createClient(CanalClient::TYPE_SOCKET_CLUE);
        $client->connect($this->host, $this->port);
        $client->subscribe("1001", "example", $this->filters);

        while (true) {
            try {
                $message = $client->get();
                $entries = $message->getEntries();
                if (!$entries) {
                    continue;
                }

                foreach ($entries as $entry) {
                    /** @var Change $change */
                    $change = Parse::decode($entry);
                    if (!$change || !$change->getRows()) {
                        continue;
                    }

                    $id = $this->_receive($change);
                    $logicClass = config('app.logic_base') . $change->getSchema() . "\\" . toCamel($change->getTable());
                    $instance = new $logicClass($change);
                    $result = $instance->handle();
                    $this->_finish($id, $result);
                }

                sleep(1);

            } catch (\Exception | \Throwable $e) {
                if ($this->isBreak($e)) {
                    $this->reconnect($client);
                    continue;
                }
                Log::error('RuntimeErr:' . $e->getMessage());
            }
        }

    }

    private function _receive(Change $change)
    {
        Db::instance()->link('change_log')->insert('canal_change_log', [
            'schema' => $change->getSchema(),
            'table' => $change->getTable(),
            'event_type' => $change->getEventType(),
            'rows' => json_encode($change->getRows()),
            'status' => 'begin',
            'create_time' => date("Y-m-d H:i:s")
        ]);

        return Db::instance()->link('change_log')->id();
    }


    private function _finish($logId, $result)
    {
        Db::instance()->link('change_log')->update(
            'canal_change_log',
            [
                'status' => $result ? 'finish' : 'error',
                'finish_time' => date("Y-m-d H:i:s")
            ],
            ['id' => $logId]
        );
    }





}