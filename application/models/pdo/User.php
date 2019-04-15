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
            $st = $db->prepare("insert into users (id, name, created, updated, email, password) values (:id, :name, :created, :updated, :email, :password)");
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

    public static function findByEmail($email) {
        $result = null;

        if (!empty($email)) {
            $db = static::getDB();
            $st = $db->prepare("select * from users where email = :email");
            $st->bindParam(':email', $email);
            $st->execute();
            $user = $st->fetchObject();

            if ($user !== false && !empty($user->id)) {
                $result = $user;
            }
        }

        return $result;
    }
}
