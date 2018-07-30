<?php

namespace App;

trait RecordsActivity
{
    protected static function boot()
    {
        parent::boot();

        foreach (static::getModelEvents() as $event) {
            static::$event(function (self $model) use ($event) {
                $model->addActivity($event);
            });
        }
    }

    /**
     * @return array
     */
    protected static function getModelEvents()
    {
        if (isset(static::$recordEvents)) {
            return static::$recordEvents;
        }

        return ['created', 'updated', 'deleted'];
    }

    /**
     * @param $event
     * @throws \ReflectionException
     */
    protected function addActivity($event)
    {
        Activity::create([
            'subject_id' => $this->id,
            'subject_type' => get_class($this),
            'name' => $this->getActivityName($this, $event),
            'user_id' => $this->user_id,
        ]);
    }

    /**
     * @param $model
     * @param $action
     * @return string
     * @throws \ReflectionException
     */
    protected function getActivityName($model, $action)
    {
        $name = strtolower((new \ReflectionClass($model))->getShortName());

        return "{$action}_{$name}";
    }
}