<?php

namespace application;

class Config
{
    const PDO = [
    	'host' => 'localhost',
    	'db' => 'todoapp',
    	'user' => 'root',
    	'password' => 'rootgfhjkm',
        'charset' => 'utf8mb4'
    ];
    
    const DATABASE = 'PDO';

    const TASKS_PER_PAGE = 10;
}
