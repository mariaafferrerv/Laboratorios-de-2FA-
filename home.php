<?php
require __DIR__.'/config.php';
if(empty($_SESSION['user_id']) || !empty($_SESSION['must_2fa'])){ header('Location: login.php'); exit; }
$st=db()->prepare('SELECT nombre,email FROM usuarios WHERE id=:id');
$st->execute([':id'=>$_SESSION['user_id']]); $u=$st->fetch();
?>
<!doctype html>
<meta charset="utf-8">
<title>Inicio</title>
<style>
body{margin:0;font-family:system-ui,-apple-system,Segoe UI,Roboto;background:#0b1220;color:#e5e7eb;display:grid;place-items:center;min-height:100vh}
.card{width:min(600px,94vw);background:#111827;border:1px solid #1f2937;border-radius:16px;padding:28px;text-align:center}
a.btn{display:inline-block;margin-top:12px;padding:.7rem 1rem;border-radius:10px;background:#6366f1;color:#fff;text-decoration:none}
</style>
<div class="card">
  <h2>Bienvenido <?=e($u['nombre'] ?? '👋')?> </h2>
  <p>Autenticación 2FA completada correctamente.</p>
  <a class="btn" href="logout.php">Cerrar sesión</a>
</div>
