<?php
// includes/db.php

class Database {
    private $host = 'localhost';
    private $dbname = 'db_buku_tamu';
    private $user = 'root';
    private $pass = 'root';           // ganti kalau ada password
    public $conn;

    public function __construct() {
        try {
            $this->conn = new PDO(
                "mysql:host=$this->host;dbname=$this->dbname;charset=utf8mb4",
                username: $this->user,
                password: $this->pass
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Koneksi gagal: " . $e->getMessage());
        }
    }
}