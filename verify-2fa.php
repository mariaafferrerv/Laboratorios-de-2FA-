<?php
require __DIR__.'/config.php';
require __DIR__.'/vendor/autoload.php';

use Sonata\GoogleAuthenticator\GoogleAuthenticator;

if (empty($_SESSION['user_id']) || empty($_SESSION['must_2fa'])){ header('Location: login.php'); exit; }

$st = db()->prepare('SELECT secret_2fa FROM usuarios WHERE id=:id');
$st->execute([':id'=>$_SESSION['user_id']]);
$secret = $st->fetchColumn();

$err = '';
if ($_SERVER['REQUEST_METHOD']==='POST'){
  $g = new GoogleAuthenticator();
  $codigo_usuario = trim($_POST['codigo_2fa'] ?? '');
  if ($codigo_usuario !== '' && $secret && $g->checkCode($secret, $codigo_usuario)){
    unset($_SESSION['must_2fa']);
    session_regenerate_id(true);
    header('Location: home.php'); exit;
  }
  $err = 'Código inválido';
}
?>
<!doctype html>
<meta charset="utf-8">
<title>Verificación 2FA</title>
<style>
:root{--card:#111827;--muted:#9ca3af;--txt:#e5e7eb;--pri:#6366f1}
*{box-sizing:border-box}
body{margin:0;font-family:system-ui,-apple-system,Segoe UI,Roboto;background:#0b1220;color:var(--txt);display:grid;place-items:center;min-height:100vh}
.card{width:min(520px,92vw);background:var(--card);border:1px solid #1f2937;border-radius:16px;padding:28px}
h1{margin:0 0 12px}.muted{color:var(--muted);margin:0 0 14px}
input{width:100%;padding:.75rem .9rem;background:#0b1220;border:1px solid #243044;border-radius:10px;color:var(--txt)}
button{padding:.8rem 1rem;border-radius:10px;border:none;background:var(--pri);color:white;cursor:pointer;margin-top:.8rem}
.alert{padding:.7rem .9rem;border-radius:10px;margin-bottom:.7rem;background:#3a1e22;color:#fecaca;border:1px solid #7f1d1d}
</style>
<div class="card">
  <h1>Código 2FA</h1>
  <p class="muted">Introduce el código de 6 dígitos de tu app autenticadora.</p>
  <?php if($err): ?><div class="alert"><?=e($err)?></div><?php endif; ?>
  <form method="post">
    <input name="codigo_2fa" pattern="\d{6}" maxlength="6" required placeholder="123456">
    <button>Verificar</button>
  </form>
</div>
