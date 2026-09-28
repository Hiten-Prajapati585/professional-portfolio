<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
if (!empty($_SESSION['admin_id'])) redirect('index.php');
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  try {
    verify_csrf();
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $st = $db->prepare('SELECT id,email,password_hash FROM admins WHERE email=? LIMIT 1');
    $st->bind_param('s', $email);
    $st->execute();
    $a = $st->get_result()->fetch_assoc();
    if ($a && password_verify($password, $a['password_hash'])) {
      session_regenerate_id(true);
      $_SESSION['admin_id'] = $a['id'];
      $_SESSION['admin_email'] = $a['email'];
      $_SESSION['csrf'] = bin2hex(random_bytes(32));
      redirect('index.php');
    }
    $error = 'Invalid email or password.';
  } catch (Throwable $e) {
    $error = 'Login failed. Please try again.';
  }
}
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Admin Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body class="admin-page">
  <nav class="navbar navbar-dark bg-dark">
    <div class="container"><a class="navbar-brand fw-bold" href="../index.php"><i class="bi bi-arrow-left"></i> Portfolio</a><span class="text-white-50">Admin Login</span></div>
  </nav>
  <div class="admin-login">
    <div class="card shadow-lg border-0">
      <div class="card-body p-4 p-md-5">
        <div class="text-center mb-4">
          <div class="admin-icon"><i class="bi bi-shield-lock"></i></div>
          <h3 class="fw-bold">Admin Access</h3>
          <p class="text-secondary">Sign in to manage your portfolio.</p>
        </div><?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?><form method="post"><input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>"><label class="form-label">Email</label><input type="email" name="email" class="form-control mb-3" required autocomplete="username"><label class="form-label">Password</label>
          <div class="input-group mb-3"><input type="password" name="password" id="password" class="form-control" required autocomplete="current-password"><button class="btn btn-outline-secondary" type="button" onclick="const p=document.getElementById('password');p.type=p.type==='password'?'text':'password';"><i class="bi bi-eye"></i></button></div><button class="btn btn-primary w-100"><i class="bi bi-box-arrow-in-right"></i> Login</button>
        </form>
        <div class="small text-secondary mt-3">Default SQL account: <code>admin@example.com</code> / <code>portfolio123</code>. Change it immediately from Admin Settings.</div>
      </div>
    </div>
  </div>
</body>

</html>