<?php

namespace App\Josel\Models\Factories;

/**
 * This interface describes a model factory.
 */
class Model
{
    /**
     * Gets the model.
     *
     * @return    The model. if fails to load retuns null otherwise
     */
    public static function getModel($class)
    {
        $model = null;
        if (empty($class)) {
            return $model;
        }

        try {
            $model_class = new \ReflectionClass($class);
        } catch (\Throwable $e) {
            $model_class = null;
        }

        if (empty($model_class)) {
            return $model;
        }

        $model = $model_class->newInstanceArgs([]);
        return $model;
    }
}
