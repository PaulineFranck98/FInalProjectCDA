<?php
 
namespace App\Tests;
 
use App\Entity\User;
use PHPUnit\Framework\TestCase;
 
class UserTest extends TestCase
{
    public function testUserEmail()
    {
        $user = new User();
        $user->setEmail("test@example.com");
 
        $this->assertSame("test@example.com", $user->getEmail());
    }
 
    public function testPasswordHash()
    {
        $user = new User();
        $hashed = password_hash("secret", PASSWORD_BCRYPT);
 
        $user->setPassword($hashed);
 
        $this->assertTrue(password_verify("secret", $user->getPassword()));
    }
}