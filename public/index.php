<?php

//print_r($_SERVER);exit;

spl_autoload_register(
	function ($class) {
		$root = dirname(__DIR__);
		$file = $root . '/' . str_replace('\\', '/', $class) . '.php';
		
		if (is_readable($file)) {
			require $file;
		}
	}
);

$router = new core\Router();

$router->add('/', ['controller' => 'Main', 'action' => 'index']);
$router->add('/{controller}', ['action' => 'index']);
$router->add('/{controller}/{action}');
$router->add('/main/listTasksAjax/{page:\d+}', ['controller' => 'Main', 'action' => 'listTasksAjax']);
$router->add('/main/listTasks/{page:\d+}', ['controller' => 'Main', 'action' => 'listTasks']);
$router->add('/main/editTask/{id:[a-z0-9\-]+}', ['controller' => 'Main', 'action' => 'editTask']);
$router->add('/main/closeTask/{id:[a-z0-9\-]+}', ['controller' => 'Main', 'action' => 'closeTask']);

#echo '<pre>';
#echo htmlspecialchars(print_r($router->getRoutes(), true));
#echo '</pre>';

$url = $_SERVER['REQUEST_URI'];
$router->dispatch($url);
