<?php

namespace DavidArl\WaFiture;

use DavidArl\WaFiture\Traits\ControlDevice;
use Illuminate\Database\Eloquent\Collection;

class WaFiture
{
    use ControlDevice;

    public function instanceofCollection($class, $collectionOrClass): ?bool
    {
        if ($collectionOrClass instanceof $class) {
            return true;
        } else if ($collectionOrClass instanceof Collection) {
            if ($collectionOrClass->first() === null) {
                return null;
            } else if ($collectionOrClass->first() instanceof $class) {
                return true;
            } else {
                return false;
            }
        }
        return false;
    }
}
