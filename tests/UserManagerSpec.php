<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use UserManager;
use PDO;
use InvalidArgumentException;
use Exception;

require_once __DIR__ . '/../src/UserManager.php';

class UserManagerSpec extends TestCase
{
    private UserManager $userManager;
    private PDO $db;

    protected function setUp(): void
    {
        $this->db = new PDO("mysql:host=localhost;dbname=user_management;charset=utf8", "root", "", [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
        
        $this->db->exec("CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(191) NOT NULL,
            email VARCHAR(191) NOT NULL UNIQUE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
        
        $this->userManager = new UserManager();
    }

    protected function tearDown(): void
    {
        // $this->db->exec("DROP TABLE users");
    }

    public function testAddUser(): void
    {
        $this->userManager->addUser("John Doe", "john@example.com");
        $users = $this->userManager->getUsers();
        
        $this->assertCount(1, $users);
        $this->assertEquals("John Doe", $users[0]['name']);
        $this->assertEquals("john@example.com", $users[0]['email']);
    }

    public function testAddUserEmailException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->userManager->addUser("John Doe", "invalid-email");
    }

    public function testUpdateUser(): void
    {
        $this->userManager->addUser("John Doe", "john@example.com");
        $user = $this->userManager->getUsers()[0];
        
        $this->userManager->updateUser($user['id'], "John Smith", "john.smith@example.com");
        $updatedUser = $this->userManager->getUser($user['id']);
        
        $this->assertEquals("John Smith", $updatedUser['name']);
        $this->assertEquals("john.smith@example.com", $updatedUser['email']);
    }

    public function testRemoveUser(): void
    {
        $this->userManager->addUser("John Doe", "john@example.com");
        $user = $this->userManager->getUsers()[0];
        
        $this->userManager->removeUser($user['id']);
        $this->assertCount(0, $this->userManager->getUsers());
    }

    public function testGetUsers(): void
    {
        $this->userManager->addUser("Alice", "alice@example.com");
        $this->userManager->addUser("Bob", "bob@example.com");
        
        $users = $this->userManager->getUsers();
        $this->assertCount(2, $users);
    }

    public function testInvalidUpdateThrowsException(): void
    {
        // Ensure we're starting with a clean slate
        $this->db->exec("TRUNCATE TABLE users");
        
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Utilisateur introuvable.");
        
        $this->userManager->updateUser(999, "Nonexistent User", "nonexistent@example.com");
    }

    public function testInvalidDeleteThrowsException(): void
    {
        // Ensure we're starting with a clean slate
        $this->db->exec("TRUNCATE TABLE users");
        
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Utilisateur introuvable.");
        
        $this->userManager->removeUser(999);
    }
}