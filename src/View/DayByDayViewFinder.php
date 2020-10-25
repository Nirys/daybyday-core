<?php

namespace SpiritSystems\DayByDay\Core\View;

use Illuminate\View\FileViewFinder;

class DayByDayViewFinder extends FileViewFinder
{
    protected function findInPaths($name, $paths)
    {
        $newPaths = [
            realpath(__DIR__.'/../../daybyday-resources/views'),
        ];

        $base = base_path();
        $vendors = scandir($base.'/vendor');
        foreach ($vendors as $vendor) {
            if ($vendor == '.' || $vendor == '..' || is_file($base.'/vendor/'.$vendor)) {
                continue;
            }

            $modules = scandir($base.'/vendor/'.$vendor);
            foreach ($modules as $module) {
                if ($module == '.' || $module == '..') {
                    continue;
                }

                if (file_exists($base.'/vendor/'.$vendor.'/'.$module.'/daybyday-resources/views')) {
                    $newPaths[] = $base.'/vendor/'.$vendor.'/'.$module.'/daybyday-resources/views';
                }
            }
        }

        $paths = array_merge($newPaths, $paths);

        return parent::findInPaths($name, $paths);
    }
}
