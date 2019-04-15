<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class UserTest extends TestCase {
    public function testCanBeCreatedWithNameEmailPassword(): void {

        // create new user and check it's id format
        $name = "John Smith " . time();
        $email = time() . "a@b.com";
        $password = "ppp";
        $user_id = \application\models\pdo\User::create($name, $email, $password);

        $this->assertStringMatchesFormat('%x-%x-%x-%x-%x', $user_id);
    }

    public function testCannotBeCreatedWithoutEmail(): void {
        
        // try to create new user without email
        $name = "John Smith";
        $email = null;
        $password = "ppp";
        $user_id = \application\models\pdo\User::create($name, $email, $password);

        $this->assertFalse($user_id);
    }
}
