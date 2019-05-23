<?php

namespace jasonmccallister\hasura\events;

use yii\base\Event as BaseEvent;

class HasuraEvent extends BaseEvent
{
    /**
     * The name of the trigger
     *
     * @var String
     */
    public $triggerName;

    /**
     * The name of the table
     *
     * @var String
     */
    public $tableName;

    /**
     * The payload object as a whole.
     *
     * @var Object
     */
    public $payload;
}
