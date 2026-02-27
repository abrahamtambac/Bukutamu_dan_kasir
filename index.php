<?php
// index.php

require_once 'includes/db.php';
require_once 'includes/tamu.class.php';

$db = new Database();
$tamu = new Tamu($db->conn);

$message = '';

// Proses tambah data (dari modal tambah)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'tambah') {
    $nama  = trim($_POST['nama'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $pesan = trim($_POST['pesan'] ?? '');

    if ($nama && $email && $pesan) {
        if ($tamu->tambah($nama, $email, $pesan)) {
            $message = '<div class="alert alert-success alert-dismissible fade show">Data berhasil ditambahkan!<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
        } else {
            $message = '<div class="alert alert-danger alert-dismissible fade show">Gagal menambahkan data.<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
        }
    } else {
        $message = '<div class="alert alert-warning alert-dismissible fade show">Lengkapi semua field!<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
    }
}

// Proses edit (dari modal edit)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit') {
    $id    = (int)($_POST['id'] ?? 0);
    $nama  = trim($_POST['nama'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $pesan = trim($_POST['pesan'] ?? '');

    if ($id > 0 && $nama && $email && $pesan) {
        if ($tamu->update($id, $nama, $email, $pesan)) {
            $message = '<div class="alert alert-success alert-dismissible fade show">Data berhasil diperbarui!<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
        } else {
            $message = '<div class="alert alert-danger alert-dismissible fade show">Gagal memperbarui data.<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
        }
    }
}

// Proses hapus
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    if ($id > 0 && $tamu->hapus($id)) {
        $message = '<div class="alert alert-success alert-dismissible fade show">Data berhasil dihapus!<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
    } else {
        $message = '<div class="alert alert-danger alert-dismissible fade show">Gagal menghapus data.<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
    }
}

// Pencarian
$keyword = trim($_GET['search'] ?? '');
$daftar_tamu = $tamu->cari($keyword);  // method baru di class
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Buku Tamu Polmed</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">
    <a class="navbar-brand" href="index.php">Buku Tamu Polmed</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
          <a class="nav-link active" href="index.php">Home</a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<div class="container py-4">
  <h2>Buku Tamu Politeknik Negeri Medan</h2>
  <small>Silahkan masukkan identitas tamu yang berkunjung</small>

  <hr/>
  <?= $message ?>

  <!-- Form Pencarian + Tombol Tambah -->
  <div class="d-flex justify-content-between mb-3 flex-wrap gap-2">
    <form class="d-flex" method="GET">
      <input class="form-control me-2" type="search" name="search" placeholder="Cari nama, email, atau pesan..." value="<?= htmlspecialchars($keyword) ?>" style="min-width: 250px;">
      <button class="btn btn-outline-primary" type="submit">Cari</button>
      <?php if ($keyword): ?>
        <a href="index.php" class="btn btn-outline-secondary ms-2">Reset</a>
      <?php endif; ?>
    </form>

    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalTambah">
      + Tambah Tamu Baru
    </button>
  </div>

  <!-- Tabel -->
  <?php if (count($daftar_tamu) > 0): ?>
  <div class="table-responsive">
    <table class="table table-striped table-hover align-middle">
      <thead class="table-default">
        <tr>
          <th>No</th>
          <th>Nama</th>
          <th>Email</th>
          <th>Pesan</th>
          <th>Tanggal</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php $no = 1; foreach ($daftar_tamu as $row): ?>
        <tr>
          <td><?= $no++ ?></td>
          <td><?= htmlspecialchars($row['nama']) ?></td>
          <td><?= htmlspecialchars($row['email']) ?></td>
          <td><?=$row['pesan'];?></td>
          <td><?= date('d/m/Y H:i', strtotime($row['created_at'] ?? 'now')) ?></td>
          <td>
            <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#modalEdit" 
                    data-id="<?= $row['id'] ?>" 
                    data-nama="<?= htmlspecialchars($row['nama']) ?>" 
                    data-email="<?= htmlspecialchars($row['email']) ?>" 
                    data-pesan="<?= htmlspecialchars($row['pesan']) ?>">
              Edit
            </button>
            <a href="?hapus=<?= $row['id'] ?>" class="btn btn-sm btn-danger" 
               onclick="return confirm('Yakin ingin menghapus tamu ini?')">Hapus</a>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php else: ?>
  <div class="alert alert-info text-center">Belum ada data tamu <?= $keyword ? 'untuk pencarian "'.$keyword.'"' : '' ?>.</div>
  <?php endif; ?>

</div>

<!-- Modal Tambah -->
<div class="modal fade" id="modalTambah" tabindex="-1" aria-labelledby="modalTambahLabel">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="post">
        <input type="hidden" name="action" value="tambah">
        <div class="modal-header">
          <h5 class="modal-title" id="modalTambahLabel">Tambah Tamu Baru</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Nama</label>
            <input type="text" name="nama" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Pesan / Kesan</label>
            <textarea name="pesan" class="form-control" rows="4" required></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Edit -->
<div class="modal fade" id="modalEdit" tabindex="-1" aria-labelledby="modalEditLabel">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="post">
        <input type="hidden" name="action" value="edit">
        <input type="hidden" name="id" id="edit_id">
        <div class="modal-header">
          <h5 class="modal-title" id="modalEditLabel">Edit Data Tamu</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Nama</label>
            <input type="text" name="nama" id="edit_nama" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" id="edit_email" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Pesan / Kesan</label>
            <textarea name="pesan" id="edit_pesan" class="form-control" rows="4" required></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Update</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Auto-fill modal edit
document.getElementById('modalEdit').addEventListener('show.bs.modal', function (event) {
  const button = event.relatedTarget;
  document.getElementById('edit_id').value    = button.getAttribute('data-id');
  document.getElementById('edit_nama').value  = button.getAttribute('data-nama');
  document.getElementById('edit_email').value = button.getAttribute('data-email');
  document.getElementById('edit_pesan').value = button.getAttribute('data-pesan');
});
</script>
</body>
</html>