<?php

namespace Dan\Events\Traits;

trait EventTrigger
{
    /**
     * @param $name
     * @param array $args
     *
     * @return array
     */
    public function triggerEvent($name, $args = [])
    {
        try {
            return events()->fire($name, $args);
        } catch (\ReflectionException $e) {
            return false;
        }
    }
}
