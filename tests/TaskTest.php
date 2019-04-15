<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class TaskTest extends TestCase {
    public function testCanBeCreatedAndFetched(): void {
        // create new task
        $name = "mytask 1";
        $body = "body 1";
        $parent_id = null;
        $target_time = "2019-04-12 13:59:24";
        $task_id = \application\models\pdo\Task::create($name, $body, $parent_id, $target_time);

        // check created task
        $task = \application\models\pdo\Task::getById($task_id);
        $this->assertInstanceOf(\stdClass::class, $task);
        $this->assertEquals($name, $task->name);
    }

    public function testCanBeUpdated(): void {
        // create new task
        $name = "mytask 1";
        $body = "body 1";
        $parent_id = null;
        $target_time = "2019-04-12 13:59:24";
        $task_id = \application\models\pdo\Task::create($name, $body, $parent_id, $target_time);

        // check original name
        $task = \application\models\pdo\Task::getById($task_id);
        $this->assertEquals($name, $task->name);

        // update task
        $task->name = "HELLO";
        $save_result = \application\models\pdo\Task::save($task);
        $this->assertTrue($save_result);

        // check updated name
        $updated_task = \application\models\pdo\Task::getById($task_id);
        $this->assertEquals($updated_task->name, 'HELLO');
    }
}
