<?php

namespace application\models\pdo;

class Task extends \core\ModelPDO {
    
    public static function create($name, $body, $parent_id, $target_time, $user_id) {
        $result = false;

        if (!empty($name) && !empty($body) && !empty($user_id)) {
            $new_id = static::uuid();
            $status = 'active';

            $db = static::getDB();

            $st = $db->prepare(
            "INSERT INTO tasks (id, name, target_time, body, parent_id, status, user_id)
            VALUES (:id, :name, :target_time, :body, :parent_id, :status, :user_id)"
            );

            $st->bindParam(':id', $new_id, \PDO::PARAM_STR);
            $st->bindParam(':name', $name, \PDO::PARAM_STR);
            $st->bindParam(':target_time', $target_time, \PDO::PARAM_STR);
            $st->bindParam(':body', $body, \PDO::PARAM_STR);
            $st->bindParam(':parent_id', $parent_id, \PDO::PARAM_STR);
            $st->bindParam(':status', $status, \PDO::PARAM_STR);
            $st->bindParam(':user_id', $user_id, \PDO::PARAM_STR);
            $result = $st->execute();
        }

        if ($result) {
            $result = $new_id;
        }

    	return $result;
    }

    // ----------------------------

    public static function save($task) {
        $id = $task->id;
        $name = $task->name;
        $target_time = $task->target_time;
        $body = $task->body;
        $parent_id = $task->parent_id;
        $status = $task->status;
        $user_id = $task->user_id;

        $db = static::getDB();
        
        $st = $db->prepare(
        "UPDATE tasks
        SET name = :name, target_time = :target_time, body = :body, parent_id = :parent_id, status = :status, user_id = :user_id
        WHERE id = :id"
        );

        $st->bindParam(':id', $id, \PDO::PARAM_STR);
        $st->bindParam(':name', $name, \PDO::PARAM_STR);
        $st->bindParam(':target_time', $target_time, \PDO::PARAM_STR);
        $st->bindParam(':body', $body, \PDO::PARAM_STR);
        $st->bindParam(':parent_id', $parent_id, \PDO::PARAM_STR);
        $st->bindParam(':status', $status, \PDO::PARAM_STR);
        $st->bindParam(':user_id', $user_id, \PDO::PARAM_STR);
        $result = $st->execute();

        return $result;
    }

    // ----------------------------

    public static function getTopLevelTasks() {
        $db = static::getDB();
        $st = $db->prepare("SELECT * FROM tasks WHERE parent_id IS NULL ORDER BY name ASC");
        $st->execute();
        $result = $st->fetchAll(\PDO::FETCH_CLASS);

        return $result;
    }

    // ----------------------------

    public static function getById($id) {
        $db = static::getDB();
        $st = $db->prepare("SELECT * FROM tasks WHERE id = :id");
        $st->bindParam(':id', $id, \PDO::PARAM_STR);
        $st->execute();
        $task = $st->fetchObject();

        if ($task !== false) {
            return $task;
        }
        else {
            return null;
        }
    }

    // ----------------------------

    public static function getAllTasks($page) {

        $tasks_per_page = \application\Config::TASKS_PER_PAGE;

        if ($page >= 1) {
            $offset = ($page - 1) * $tasks_per_page;
        }
        else {
            $offset = 0;
        }

        $db = static::getDB();
        $st = $db->prepare("SELECT * FROM tasks ORDER BY target_time ASC LIMIT $tasks_per_page OFFSET :offset");
        $st->bindParam(':offset', $offset, \PDO::PARAM_INT);
        $result = $st->execute();

        $result = $st->fetchAll(\PDO::FETCH_CLASS);

        return $result;
    }

    // ----------------------------

    public static function getChildTasks($id) {
        $db = static::getDB();
        $st = $db->prepare("SELECT * FROM tasks WHERE parent_id = :id");
        $st->bindParam(':id', $id, \PDO::PARAM_STR);
        $st->execute();
        $result = $st->fetchAll(\PDO::FETCH_CLASS);

        return $result;
    }

    // ----------------------------

    public static function getNumOfPages() {
        $tasks_per_page = \application\Config::TASKS_PER_PAGE;
        
        $db = static::getDB();
        $st = $db->query("SELECT COUNT(id) FROM tasks");
        $result = ceil($st->fetch()[0] / $tasks_per_page);

        return $result;
    }

    // ---------------------------

    public static function close($id) {
        $task = static::getById($id);

        // close this task
        $task->status = 'closed';
        $result = static::save($task);

        if ($task->parent_id === null) {
            // top level task
            $children = static::getChildTasks($id);
            
            foreach ($children as $ch) {
                // close all child tasks
                if ($ch->status === 'active') {
                    $ch->status = 'closed';
                    static::save($ch);
                }
            }
        }
        else {
            // child task
            
            $all_children_closed = true;
            
            $children = static::getChildTasks($task->parent_id);

            foreach ($children as $ch) {
                if ($ch->status === 'active') {
                    $all_children_closed = false;
                    break;
                }
            }

            if ($all_children_closed) {
                // close parent task because all child tasks are closed
                $parent_task = static::getById($task->parent_id);
                $parent_task->status = 'closed';
                static::save($parent_task);
            }
        }

        return $result;
    }
}
