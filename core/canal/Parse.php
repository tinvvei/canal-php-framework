<?php

namespace core\canal;

use Com\Alibaba\Otter\Canal\Protocol\Column;
use Com\Alibaba\Otter\Canal\Protocol\Entry;
use Com\Alibaba\Otter\Canal\Protocol\EntryType;
use Com\Alibaba\Otter\Canal\Protocol\EventType;
use Com\Alibaba\Otter\Canal\Protocol\RowChange;
use Com\Alibaba\Otter\Canal\Protocol\RowData;


/**
 * 以数组的方式解析变更
 * Class Parse
 * @package app
 * @author 徐庭威
 */
class Parse
{

    /**
     * 解析变更
     * @param Entry $entry
     * @throws \Exception
     */
    public static function decode(Entry $entry)
    {
        switch ($entry->getEntryType()) {
            case EntryType::TRANSACTIONBEGIN:
            case EntryType::TRANSACTIONEND:
                return null;
        }

        $rowChange = new RowChange();
        $rowChange->mergeFromString($entry->getStoreValue());
        $evenType = $rowChange->getEventType();
        $header = $entry->getHeader();

        $schema = $header->getSchemaName();
        $table = $header->getTableName();

        $rows = [];

        /** @var RowData $rowData */
        foreach ($rowChange->getRowDatas() as $rowData) {

            $beforeRow = $afterRow = [];

            switch ($evenType) {
                case EventType::DELETE:
                    $beforeRow = self::decodeRow($rowData, 'before');
                    break;
                case EventType::INSERT:
                    $afterRow = self::decodeRow($rowData, 'after');
                    break;
                default:
                    $beforeRow = self::decodeRow($rowData, 'before');
                    $afterRow = self::decodeRow($rowData, 'after');
                    break;
            }

            $item = [];
            $allColumn = array_unique(array_merge(array_keys($beforeRow), array_keys($afterRow)));
            foreach ($allColumn as $column) {
                $before = $beforeRow[$column]['value'] ?? '';
                $after = $afterRow[$column]['value'] ?? '';
                $item[$column] = [
                    'before' => $before,
                    'after' =>$after,
                    'isUpdate' => $before != $after
                ];
            }
            $rows[] = $item;
        }

        return new Change($schema, $table, $evenType, $rows);
    }


    /**
     * 解析每一条记录
     * @param RowData $rowData
     * @param $type
     * @return array
     */
    private static function decodeRow(RowData $rowData, $type)
    {
        $method = ['before' => 'getBeforeColumns', 'after' => 'getAfterColumns'][$type];

        $row = [];
        /** @var Column $column */
        foreach ($rowData->$method() as $column) {
            $row[$column->getName()] = ['value' => $column->getValue(), 'isUpdated' => $column->getUpdated()];
        }

        return $row;
    }

}