<?php

namespace App\Corporations\CreepyPastaMachine;

use Illuminate\Support\Collection;

class Bootstrap
{

    public static function models() : Collection
    {
        $modelsPath = __Dir__ . '/Database/Models';
        $files = scandir($modelsPath);

        $models = [];
        foreach ($files as $file) {
            // Skip non-PHP files and directories
            if (is_dir($file) || pathinfo($file, PATHINFO_EXTENSION) !== 'php') {
                continue;
            }

            // Get the class name from the file name
            $className = pathinfo($file, PATHINFO_FILENAME);

            // Resolve the full class name with namespace
            $fullClassName = "App\\Corporations\\CreepyPastaMachine\\Database\\Models\\$className";

            // Check if class exists and is a subclass of Eloquent Model
            if (class_exists($fullClassName) && is_subclass_of($fullClassName, \Illuminate\Database\Eloquent\Model::class)) {
                // Instantiate the model and add it to the array
                $models[$className] = new $fullClassName;
            }
        }

        return collect($models);
    }

}