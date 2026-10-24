<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Petshop Apriori</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #e9ecef; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; }
        .login-card { width: 100%; max-width: 400px; padding: 20px; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); background: white; }
    </style>
</head>
<body>

    <div class="login-card">
        <h3 class="text-center mb-4">🐾 Petshop Login</h3>
        
        <!-- Menampilkan pesan error jika login gagal (dikirim dari AuthController) -->
        <?php if(session()->getFlashdata('error')): ?>
            <div class="alert alert-danger text-center">
                <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>

        <!-- Form yang mengarah ke rute /login-proses -->
        <form action="<?= base_url('login-proses') ?>" method="post">
            <div class="mb-3">
                <label class="form-label">Username</label>
                <!-- username: admin -->
                <input type="text" name="username" class="form-control" required placeholder="Masukkan username">
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <!-- password: admin123 -->
                <input type="password" name="password" class="form-control" required placeholder="Masukkan password">
            </div>
            <button type="submit" class="btn btn-primary w-100 fw-bold">Masuk</button>
        </form>
    </div>

</body>
</html>