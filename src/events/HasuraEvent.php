<?php

namespace jasonmccallister\hasura\events;

use yii\base\Event;

class HasuraEvent extends Event
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
