<?php

namespace jasonmccallister\Hasura\events;

use yii\base\Event;

class HasuraEvent extends Event
{
    /**
     * The name of the trigger
     *
     * @var String
     */
    public $trigger;

    /**
     * The name of the table
     *
     * @var String
     */
    public $table;

    /**
     * The payload object as a whole.
     *
     * @var Object
     */
    public $payload;
}
