<?php

namespace core\canal;

/**
 * 变更记录对象
 * Class Change
 * @package app
 * @author 徐庭威
 */
class Change
{
    protected $schema;

    protected $table;

    protected $eventType;


    protected $rows;

    public function __construct($schema, $table, $eventType, $rows)
    {
        $this->rows = $rows;
        $this->schema = $schema;
        $this->table = $table;
        $this->eventType = $eventType;
    }


    public function getSchema()
    {
        // 测试环境是jiayi_pre 生产环境是jiayicui
        if ($this->schema == 'jiayi_pre') {
            return 'jiayicui';
        }
        return $this->schema;
    }

    public function getTable()
    {
        return $this->table;
    }

    public function getEventType()
    {
        return $this->eventType;
    }

    public function getRows()
    {
        return $this->rows;
    }

}