<?php

namespace jasonmccallister\hasura\events;

use yii\base\Event as BaseEvent;

class HasuraEvent extends BaseEvent
{
    public $id;

    public $event;

    public $createdAt;

    public $trigger;

    public $table;
}
