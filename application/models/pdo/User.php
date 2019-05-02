<?php

namespace application\models\pdo;

class User extends \core\ModelPDO {
    
	public static function create($name, $email, $password) {
        $result = false;

        if (!empty($name) && !empty($email) && !empty($password)) {
            $new_id = static::uuid();
            $now = date("Y-m-d H:i:s");
            $password = password_hash($password, PASSWORD_DEFAULT);

            $db = static::getDB();
            $st = $db->prepare(
            "INSERT INTO users (id, name, created, updated, email, password)
            VALUES (:id, :name, :created, :updated, :email, :password)"
            );
            $st->bindParam(':id', $new_id);
            $st->bindParam(':name', $name);
            $st->bindParam(':created', $now);
            $st->bindParam(':updated', $now);
            $st->bindParam(':email', $email);
            $st->bindParam(':password', $password);
            $result = $st->execute();
        }

        if ($result) {
            $result = $new_id;
        }

    	return $result;
    }

    // -------------------------------------

    public static function getById($id) {
        $db = static::getDB();
        $st = $db->prepare("SELECT * FROM users WHERE id = :id");
        $st->bindParam(':id', $id, \PDO::PARAM_STR);
        $st->execute();
        $user = $st->fetchObject();

        if ($user !== false) {
            return $user;
        }
        else {
            return null;
        }
    }

    // -------------------------------------

    public static function findByEmail($email) {
        $result = null;

        if (!empty($email)) {
            $db = static::getDB();
            $st = $db->prepare("SELECT * FROM users WHERE email = :email");
            $st->bindParam(':email', $email);
            $st->execute();
            $user = $st->fetchObject();

            if ($user !== false && !empty($user->id)) {
                $result = $user;
            }
        }

        return $result;
    }

    // -------------------------------------

    public static function getNamesWithIds() {
        $result = null;

        $db = static::getDB();
        $st = $db->query("SELECT id, name, email FROM users");
        $users = $st->fetchAll(\PDO::FETCH_CLASS);

        if ($users && count($users) >= 1) {
            $result = $users;
        }

        return $result;
    }
}
