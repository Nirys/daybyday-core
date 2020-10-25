<?php
namespace SpiritSystems\DayByDay\Core\Services;

use Illuminate\Support\Facades\Facade;

/**
 * @method static void addProvider($className)
 * @method static string render()
 * @method static array items()
 *
 * @see \AlphaIris\Core\MenuServiceManager
 */
class MenuService extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return MenuServiceManager::class;
    }
}