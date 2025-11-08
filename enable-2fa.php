<?php
// enable-2fa.php
require __DIR__.'/config.php';
if (empty($_SESSION['user_id'])){ header('Location: login.php'); exit; }

require __DIR__.'/vendor/autoload.php';

use Sonata\GoogleAuthenticator\GoogleAuthenticator;
use Sonata\GoogleAuthenticator\GoogleQrUrl;

// 1) Traer datos del usuario
$st = db()->prepare('SELECT email, secret_2fa FROM usuarios WHERE id=:id');
$st->execute([':id'=>$_SESSION['user_id']]);
$u = $st->fetch();
if(!$u){ session_destroy(); header('Location: login.php'); exit; }

// 2) Si NO tiene secreto, se genera y se GUARDA de inmediato (tal cual guía)
if (empty($u['secret_2fa'])){
  $g = new GoogleAuthenticator();
  $secret = $g->generateSecret();

  db()->prepare('UPDATE usuarios SET secret_2fa=:s WHERE id=:id')
     ->execute([':s'=>$secret, ':id'=>$_SESSION['user_id']]);

  $u['secret_2fa'] = $secret;
}

// 3) Construir la URL otpauth y el PNG del QR con api.qrserver.com
$correo = $u['email'] ?: 'usuario@ejemplo.com';
$app    = 'MiSistema'; // usa el mismo nombre que tu guía/consigna

// otpauth://… (lo genera Sonata)
$url    = GoogleQrUrl::generate($correo, $u['secret_2fa'], $app);

// *** IMPORTANTE: SIN urlencode() para que la app escanee correctamente ***
$qr_url = 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' . $url;

function h($s){ return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }
?>
<!doctype html>
<meta charset="utf-8">
<title>Activar 2FA</title>
<style>
:root{--card:#111827;--muted:#9ca3af;--txt:#e5e7eb;--pri:#6366f1}
*{box-sizing:border-box}
body{margin:0;font-family:system-ui,-apple-system,Segoe UI,Roboto;background:#0b1220;color:var(--txt);
display:grid;place-items:center;min-height:100vh}
.card{width:min(640px,94vw);background:var(--card);border:1px solid #1f2937;border-radius:16px;padding:28px}
h1{margin:0 0 10px}.muted{color:var(--muted);margin:0 0 14px}
.grid{display:grid;gap:1rem}
.qr{background:#0b1220;border:1px dashed #2a3550;border-radius:12px;padding:12px;display:grid;place-items:center}
.qr img{max-width:256px;height:auto;display:block}
input{width:100%;padding:.75rem .9rem;background:#0b1220;border:1px solid #243044;border-radius:10px;color:var(--txt)}
button{padding:.8rem 1rem;border-radius:10px;border:none;background:var(--pri);color:white;cursor:pointer}
small{color:var(--muted)}
code{background:#0b1220;border:1px solid #243044;border-radius:8px;padding:.1rem .4rem}
</style>
<div class="card">
  <h1>Activar 2FA</h1>
  <p class="muted">Escanea el QR con Google Authenticator y escribe el código de 6 dígitos.</p>

  <div class="grid">
    <div class="qr">
      <!-- QR PNG generado por api.qrserver.com con la URI otpauth SIN urlencode -->
      <img src="<?=$qr_url?>" alt="Escanea este QR con Google Authenticator">
    </div>

    <!-- (Opcional útil) Mostrar el secreto por si necesitan agregarlo manualmente -->
    <small>Secreto (para ingreso manual): <code><?=h($u['secret_2fa'])?></code></small>

    <form method="post" action="enable-2fa-verify.php">
      <!-- nombre del campo tal cual la guía -->
      <input name="codigo_2fa" pattern="\d{6}" maxlength="6" placeholder="Código 6 dígitos" required>
      <button>Verificar y activar</button>
    </form>

    <small>Si no valida, sincroniza la hora de Windows (Hora e idioma → “Sincronizar ahora”).</small>
  </div>
</div>

