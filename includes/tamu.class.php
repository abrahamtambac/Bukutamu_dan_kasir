<?php
// includes/tamu.class.php

class Tamu {
    private $db;

    public function __construct($conn) {
        $this->db = $conn;
    }

    public function tambah($nama, $email, $pesan) {
        $sql = "INSERT INTO buku_tamu (nama, email, pesan) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$nama, $email, $pesan]);
    }

    public function semua() {
        $sql = "SELECT * FROM buku_tamu ORDER BY id DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function cari($keyword = '') {
        $sql = "SELECT * FROM buku_tamu WHERE nama LIKE ? OR email LIKE ? OR pesan LIKE ? ORDER BY id DESC";
        $stmt = $this->db->prepare($sql);
        $like = "%$keyword%";
        $stmt->execute([$like, $like, $like]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update($id, $nama, $email, $pesan) {
        $sql = "UPDATE buku_tamu SET nama = ?, email = ?, pesan = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$nama, $email, $pesan, $id]);
    }

    public function hapus($id) {
        $sql = "DELETE FROM buku_tamu WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }
}