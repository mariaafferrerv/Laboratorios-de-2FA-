<?php
require __DIR__.'/config.php';
require __DIR__.'/vendor/autoload.php';

use Sonata\GoogleAuthenticator\GoogleAuthenticator;

if (empty($_SESSION['user_id'])){ header('Location: login.php'); exit; }

$st = db()->prepare('SELECT secret_2fa FROM usuarios WHERE id=:id');
$st->execute([':id'=>$_SESSION['user_id']]);
$secret = $st->fetchColumn();

$g = new GoogleAuthenticator();

$codigo_usuario = trim($_POST['codigo_2fa'] ?? '');
$ok = ($codigo_usuario !== '' && $secret && $g->checkCode($secret, $codigo_usuario));
?>
<!doctype html>
<meta charset="utf-8">
<title>Verificar 2FA</title>
<style>
body{margin:0;font-family:system-ui,-apple-system,Segoe UI,Roboto;background:#0b1220;color:#e5e7eb;display:grid;place-items:center;min-height:100vh}
.card{width:min(520px,92vw);background:#111827;border:1px solid #1f2937;border-radius:16px;padding:28px;text-align:center}
a.btn{display:inline-block;margin-top:12px;padding:.7rem 1rem;border-radius:10px;background:#6366f1;color:#fff;text-decoration:none}
.ok{color:#a7f3d0}.bad{color:#fecaca}
</style>
<div class="card">
  <?php if($ok): ?>
    <h2 class="ok">✅ Acceso concedido</h2>
    <a class="btn" href="home.php">Continuar</a>
  <?php else: ?>
    <h2 class="bad">❌ Código incorrecto</h2>
    <a class="btn" href="enable-2fa.php">Intentar de nuevo</a>
  <?php endif; ?>
</div>
