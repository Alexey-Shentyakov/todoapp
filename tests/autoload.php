<?php

spl_autoload_register(
	function ($class) {
		$root = dirname(__DIR__);
		$file = $root . '/' . str_replace('\\', '/', $class) . '.php';
		
		if (is_readable($file)) {
			require $file;
		}
	}
);
