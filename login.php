<?php
session_start();

if(isset($_POST['login'])){
    $email = $_POST['user'];
    $password = $_POST['password'];

    if(isset($_SESSION['user']) && isset($_SESSION['password'])){
        if($email == $_SESSION['user'] && $password == $_SESSION['password']){
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Wrong user or password";
        }
    } else {
        $error = "Please create account first";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login — Maison Velour</title>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;1,300;1,400&family=Josefin+Sans:wght@100;200;300;400&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{margin:0;padding:0;box-sizing:border-box;}
:root{
  --cream:#F7F3EE;--gold:#B8963E;--gold-light:#D4AF6A;
  --gold-pale:#E8D9B5;--charcoal:#1C1A18;--stone:#8A8278;
  --mist:#C9C2B8;--dark-bg:#111009;--dark2:#1C1A18;
}
html,body{height:100%;font-family:'Josefin Sans',sans-serif;}
body{
  min-height:100vh;display:flex;align-items:center;
  justify-content:center;position:relative;overflow:hidden;
  background:var(--dark-bg);
}

/* BG */
.bg{
  position:fixed;inset:0;z-index:0;
  background:
    radial-gradient(ellipse at 20% 50%, rgba(184,150,62,0.12) 0%, transparent 60%),
    radial-gradient(ellipse at 80% 20%, rgba(184,150,62,0.08) 0%, transparent 50%),
    radial-gradient(ellipse at 60% 80%, rgba(184,150,62,0.06) 0%, transparent 40%);
  background-color:var(--dark-bg);
}
.bg-grid{
  position:fixed;inset:0;z-index:1;
  background-image:
    linear-gradient(rgba(184,150,62,0.04) 1px, transparent 1px),
    linear-gradient(90deg, rgba(184,150,62,0.04) 1px, transparent 1px);
  background-size:60px 60px;
}
.bg-text{
  position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);
  font-family:'Cormorant Garamond',serif;font-size:220px;font-weight:300;
  color:rgba(184,150,62,0.04);letter-spacing:-0.02em;white-space:nowrap;
  pointer-events:none;user-select:none;z-index:1;
}

/* CARD */
.card-wrap{
  position:relative;z-index:10;width:100%;
  max-width:420px;padding:20px;
  animation:slide-up 0.8s cubic-bezier(0.22,1,0.36,1) both;
}
@keyframes slide-up{
  from{opacity:0;transform:translateY(40px);}
  to{opacity:1;transform:translateY(0);}
}
.card{
  background:rgba(28,26,24,0.95);
  backdrop-filter:blur(20px);
  border:1px solid rgba(184,150,62,0.2);
  border-radius:4px;
  padding:52px 44px 44px;
  box-shadow:0 40px 80px rgba(0,0,0,0.6), 0 0 0 1px rgba(184,150,62,0.08);
}

/* HEADER */
.card-header{text-align:center;margin-bottom:40px;}
.logo{
  font-family:'Cormorant Garamond',serif;
  font-size:13px;letter-spacing:0.4em;
  text-transform:uppercase;color:var(--gold);
  margin-bottom:8px;
}
.logo span{color:var(--gold-light);}
.divider-line{
  width:40px;height:1px;background:var(--gold);
  margin:16px auto;opacity:0.5;
}
.card-title{
  font-family:'Cormorant Garamond',serif;
  font-size:32px;font-weight:300;
  color:var(--cream);letter-spacing:0.05em;
  margin-bottom:8px;
}
.card-subtitle{
  font-size:9px;letter-spacing:0.3em;
  text-transform:uppercase;color:var(--stone);
}

/* FORM */
.form-group{margin-bottom:20px;}
label{
  display:block;font-size:9px;
  letter-spacing:0.3em;text-transform:uppercase;
  color:var(--stone);margin-bottom:10px;
}
input[type="text"],input[type="password"]{
  width:100%;padding:14px 16px;
  background:rgba(247,243,238,0.04);
  border:1px solid rgba(184,150,62,0.2);
  border-radius:2px;
  font-family:'Josefin Sans',sans-serif;
  font-size:13px;color:var(--cream);outline:none;
  transition:border-color 0.3s,background 0.3s,box-shadow 0.3s;
  letter-spacing:0.05em;
}
input:focus{
  background:rgba(184,150,62,0.06);
  border-color:rgba(184,150,62,0.6);
  box-shadow:0 0 0 3px rgba(184,150,62,0.08);
}
input::placeholder{color:rgba(138,130,120,0.4);font-size:12px;}

/* ERROR */
.error-msg{
  background:rgba(192,57,43,0.1);
  border:1px solid rgba(192,57,43,0.3);
  border-left:3px solid #c0392b;
  padding:10px 14px;font-size:11px;
  color:#e74c3c;margin-bottom:20px;
  letter-spacing:0.05em;
}

/* BUTTON */
.btn-login{
  width:100%;padding:16px;
  background:var(--gold);color:var(--dark-bg);
  border:none;border-radius:2px;
  font-family:'Josefin Sans',sans-serif;
  font-size:10px;font-weight:400;
  letter-spacing:0.35em;text-transform:uppercase;
  cursor:pointer;margin-top:8px;
  transition:background 0.3s,transform 0.15s;
  position:relative;overflow:hidden;
}
.btn-login::after{
  content:'';position:absolute;inset:0;
  background:rgba(255,255,255,0.1);
  transform:translateX(-100%);
  transition:transform 0.4s;
}
.btn-login:hover::after{transform:translateX(0);}
.btn-login:hover{background:var(--gold-light);}
.btn-login:active{transform:scale(0.99);}

/* FOOTER */
.card-footer{
  text-align:center;margin-top:28px;
  font-size:9px;letter-spacing:0.2em;
  text-transform:uppercase;color:var(--stone);
}
.card-footer a{
  color:var(--gold);text-decoration:none;
  transition:color 0.2s;
}
.card-footer a:hover{color:var(--gold-light);}

/* WATERMARK */
.watermark{
  position:fixed;bottom:24px;left:50%;
  transform:translateX(-50%);z-index:10;
  font-family:'Cormorant Garamond',serif;
  font-size:10px;letter-spacing:0.4em;
  color:rgba(184,150,62,0.25);
  text-transform:uppercase;white-space:nowrap;
  font-style:italic;
}
</style>
</head>
<body>

<div class="bg"></div>
<div class="bg-grid"></div>
<div class="bg-text">VELOUR</div>

<div class="card-wrap">
  <div class="card">
    <div class="card-header">
      <div class="logo">MAISON <span>VELOUR</span></div>
      <div class="divider-line"></div>
      <h1 class="card-title">Welcome Back</h1>
      <p class="card-subtitle">Sign in to your account</p>
    </div>

    <form method="POST" action="">

      <?php if(isset($error)): ?>
        <div class="error-msg"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <div class="form-group">
        <label for="user">Username</label>
        <input type="text" id="user" name="user" placeholder="Enter your username" required>
      </div>

      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="Enter your password" required>
      </div>

      <button type="submit" name="login" class="btn-login">Sign In →</button>

    </form>

    <div class="card-footer">
      No account? &nbsp;<a href="register.php">Create Account</a>
    </div>
  </div>
</div>

<div class="watermark">MAISON VELOUR</div>
</body>
</html>