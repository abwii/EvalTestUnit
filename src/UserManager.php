<?php
class UserManager {
    private PDO $db;

    public function __construct() {
        $dsn = "mysql:host=localhost;dbname=user_management;charset=utf8";
        $username = "root";
        $password = "";
        $this->db = new PDO($dsn, $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
    }

    public function addUser(string $name, string $email, ?string $dateAdded = null): void {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("Email invalide.");
        }

        if ($dateAdded === null) {
            $stmt = $this->db->prepare("INSERT INTO users (name, email, date_added) VALUES (:name, :email, NOW())");
            $stmt->execute(['name' => $name, 'email' => $email]);
        } else {
            $stmt = $this->db->prepare("INSERT INTO users (name, email, date_added) VALUES (:name, :email, :date_added)");
            $stmt->execute(['name' => $name, 'email' => $email, 'date_added' => $dateAdded]);
        }
    }

    public function removeUser(int $id): void {
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = :id");
        $stmt->execute(['id' => $id]);
        if ($stmt->rowCount() === 0) {
            throw new Exception("Utilisateur introuvable.");
        }
    }

    public function getUsers(): array {
        $stmt = $this->db->query("SELECT * FROM users");
        return $stmt->fetchAll();
    }

    public function getUser(int $id): array {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $user = $stmt->fetch();
        if (!$user) throw new Exception("Utilisateur introuvable.");
        return $user;
    }

    public function updateUser(int $id, string $name, string $email, ?string $dateAdded = null): void {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("Email invalide.");
        }

        if ($dateAdded === null) {
            $stmt = $this->db->prepare("UPDATE users SET name = :name, email = :email, date_added = NOW() WHERE id = :id");
            $stmt->execute(['id' => $id, 'name' => $name, 'email' => $email]);
        } else {
            $stmt = $this->db->prepare("UPDATE users SET name = :name, email = :email, date_added = :date_added WHERE id = :id");
            $stmt->execute(['id' => $id, 'name' => $name, 'email' => $email, 'date_added' => $dateAdded]);
        }
        if ($stmt->rowCount() === 0) {
            throw new Exception("Utilisateur introuvable.");
        }
    }
}
?>