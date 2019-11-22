<?php

declare(strict_types=1);

namespace App\Router;

use Nette;
use Nette\Application\Routers\RouteList;
use Nette\Application\IRouter;
use Nette\Application\Routers\Route;
use Nette\StaticClass;


final class RouterFactory
{
	use Nette\StaticClass;

	public static function createRouter(): RouteList
	{
		$router = new RouteList;
		$router[] = new Route('<presenter>/<action>[/<id>]', 'Homepage:default');
		$router[] = new Route('<action>[/<url>]', [
            'presenter' => 'Core:Movie',
            'action' => [
                Route::FILTER_STRICT => true,
                Route::FILTER_TABLE => [
                    // řetězec v URL => akce presenteru
                    'RemovePerf' => 'RemovePerf',
                    'AddPerf' => 'AddPerf'
                ]
            ]
        ]);
		return $router;
	}
}
