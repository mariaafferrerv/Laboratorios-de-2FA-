<?php require __DIR__.'/config.php';

$ok=$err='';
if($_SERVER['REQUEST_METHOD']==='POST'){
  try{
    if(!filter_var($_POST['email']??'', FILTER_VALIDATE_EMAIL)) throw new Exception('Email inválido');
    if(strlen($_POST['password']??'')<6) throw new Exception('La contraseña debe tener 6+ caracteres');

    $st=db()->prepare('SELECT 1 FROM usuarios WHERE email=:e');
    $st->execute([':e'=>trim($_POST['email'])]);
    if($st->fetch()) throw new Exception('Ese email ya está registrado');

    db()->prepare('INSERT INTO usuarios(nombre,apellido,email,HashMagic,sexo)
                   VALUES(:n,:a,:e,:h,:s)')
      ->execute([
        ':n'=>trim($_POST['nombre']??''),
        ':a'=>trim($_POST['apellido']??''),
        ':e'=>trim($_POST['email']??''),
        ':h'=>password_hash($_POST['password'], PASSWORD_BCRYPT),
        ':s'=>($_POST['sexo']??null) ?: null,
      ]);
    $ok='Registro exitoso. Ahora inicia sesión.';
  }catch(Exception $ex){ $err=$ex->getMessage(); }
}
?>
<!doctype html>
<meta charset="utf-8">
<title>Registro</title>
<style>
:root{--bg:#0f172a;--card:#111827;--muted:#9ca3af;--txt:#e5e7eb;--pri:#6366f1;--err:#ef4444;--ok:#22c55e}
*{box-sizing:border-box}
body{margin:0;font-family:system-ui,-apple-system,Segoe UI,Roboto;background:#0b1220;color:var(--txt);display:grid;place-items:center;min-height:100vh}
.card{width:min(560px,92vw);background:var(--card);border:1px solid #1f2937;border-radius:16px;padding:28px}
h1{margin:0 0 10px} .muted{color:var(--muted);margin:0 0 14px}
.form{display:grid;gap:.8rem}
input,select{width:100%;padding:.75rem .9rem;background:#0b1220;border:1px solid #243044;border-radius:10px;color:var(--txt)}
button{padding:.8rem 1rem;border-radius:10px;border:none;background:var(--pri);color:white;cursor:pointer}
.alert{padding:.7rem .9rem;border-radius:10px;margin-bottom:.7rem}
.alert.err{background:#3a1e22;color:#fecaca;border:1px solid #7f1d1d}
.alert.ok{background:#173222;color:#bbf7d0;border:1px solid #14532d}
a{color:#c7d2fe}
</style>
<div class="card">
  <h1>Crear cuenta</h1>
  <p class="muted">Completa tus datos para continuar.</p>

  <?php if($err): ?><div class="alert err"><?=e($err)?></div><?php endif; ?>
  <?php if($ok): ?><div class="alert ok"><?=e($ok)?> <a href="login.php">Ir al login</a></div><?php endif; ?>

  <form class="form" method="post" autocomplete="off">
    <div><input name="nombre" placeholder="Nombre" required></div>
    <div><input name="apellido" placeholder="Apellido" required></div>
    <div><input type="email" name="email" placeholder="Correo electrónico" required></div>
    <div><input type="password" name="password" placeholder="Contraseña (6+)" required></div>
    <div>
      <select name="sexo">
        <option value="">Sexo (opcional)</option>
        <option value="M">M</option><option value="F">F</option>
      </select>
    </div>
    <div><button>Registrarme</button></div>
    <div><a href="login.php">¿Ya tienes cuenta? Inicia sesión</a></div>
  </form>
</div>
