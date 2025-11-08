<?php require __DIR__.'/config.php';

$err='';
if($_SERVER['REQUEST_METHOD']==='POST'){
  $st=db()->prepare('SELECT * FROM usuarios WHERE email=:e');
  $st->execute([':e'=>trim($_POST['email']??'')]); $u=$st->fetch();
  if($u && password_verify($_POST['password']??'', $u['HashMagic'])){
    $_SESSION['user_id']=$u['id'];
    if(!empty($u['secret_2fa'])){ $_SESSION['must_2fa']=true; header('Location: verify-2fa.php'); exit; }
    header('Location: enable-2fa.php'); exit;
  }
  $err='Credenciales inválidas';
}
?>
<!doctype html>
<meta charset="utf-8">
<title>Login</title>
<style>
:root{--bg:#0f172a;--card:#111827;--muted:#9ca3af;--txt:#e5e7eb;--pri:#6366f1}
*{box-sizing:border-box}
body{margin:0;font-family:system-ui,-apple-system,Segoe UI,Roboto;background:#0b1220;color:var(--txt);display:grid;place-items:center;min-height:100vh}
.card{width:min(520px,92vw);background:var(--card);border:1px solid #1f2937;border-radius:16px;padding:28px}
h1{margin:0 0 10px}.muted{color:var(--muted);margin:0 0 14px}
.form{display:grid;gap:.8rem}
input{width:100%;padding:.75rem .9rem;background:#0b1220;border:1px solid #243044;border-radius:10px;color:var(--txt)}
button{padding:.8rem 1rem;border-radius:10px;border:none;background:var(--pri);color:white;cursor:pointer}
.alert{padding:.7rem .9rem;border-radius:10px;margin-bottom:.7rem;background:#3a1e22;color:#fecaca;border:1px solid #7f1d1d}
a{color:#c7d2fe}
</style>
<div class="card">
  <h1>Iniciar sesión</h1>
  <p class="muted">Si ya activaste 2FA, te pediremos el código.</p>
  <?php if($err): ?><div class="alert"><?=e($err)?></div><?php endif; ?>
  <form class="form" method="post">
    <input type="email" name="email" placeholder="Correo" required>
    <input type="password" name="password" placeholder="Contraseña" required>
    <button>Entrar</button>
  </form>
  <p><a href="register.php">Crear cuenta</a></p>
</div>
