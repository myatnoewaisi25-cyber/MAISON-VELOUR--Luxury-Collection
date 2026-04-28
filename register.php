<?php
// ═══════════════════════════════════════
//  MAISON VELOUR — Register Handler
// ═══════════════════════════════════════
session_start();

$errors  = [];
$success = false;
$old     = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old = [
        'first_name' => trim($_POST['first_name'] ?? ''),
        'last_name'  => trim($_POST['last_name']  ?? ''),
        'email'      => trim($_POST['email']       ?? ''),
        'phone'      => trim($_POST['phone']       ?? ''),
    ];
    $password         = $_POST['password']         ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';
    $agree            = isset($_POST['agree']);

    // Validation
    if (empty($old['first_name']))
        $errors['first_name'] = 'First name is required.';
    if (empty($old['last_name']))
        $errors['last_name'] = 'Last name is required.';
    if (empty($old['email']) || !filter_var($old['email'], FILTER_VALIDATE_EMAIL))
        $errors['email'] = 'A valid email address is required.';

    // ── PASSWORD: min 5 characters only ──
    if (strlen($password) < 5)
        $errors['password'] = 'Password must be at least 5 characters.';
    if ($password !== $password_confirm)
        $errors['password_confirm'] = 'Passwords do not match.';
    if (!$agree)
        $errors['agree'] = 'You must accept the terms to continue.';

    if (empty($errors)) {
        $_SESSION['user']             = $old['email'];
        $_SESSION['password']         = $password;
        $_SESSION['registered_name']  = $old['first_name'];
        $success = true;
    }
}

function hasErr($key, $errors) {
    return isset($errors[$key]) ? 'has-error' : '';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>MAISON VELOUR — Request Access</title>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;1,300;1,400&family=Josefin+Sans:wght@100;200;300;400&display=swap" rel="stylesheet">
<style>
  *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }
  :root {
    --cream: #F7F3EE; --warm-white: #FAF8F5; --gold: #B8963E;
    --gold-light: #D4AF6A; --gold-pale: #E8D9B5; --charcoal: #1C1A18;
    --stone: #8A8278; --mist: #C9C2B8; --dark-bg: #111009;
    --error: #c0392b;
  }
  html { scroll-behavior: smooth; }
  body {
    font-family: 'Josefin Sans', sans-serif;
    background: var(--dark-bg);
    color: var(--charcoal);
    cursor: none;
    min-height: 100vh;
  }

  /* ── CURSOR ── */
  .cursor { position: fixed; width: 8px; height: 8px; background: var(--gold); border-radius: 50%; pointer-events: none; z-index: 9999; transform: translate(-50%,-50%); transition: width .3s, height .3s; }
  .cursor-ring { position: fixed; width: 32px; height: 32px; border: 1px solid var(--gold); border-radius: 50%; pointer-events: none; z-index: 9998; transform: translate(-50%,-50%); transition: left .12s ease, top .12s ease, width .3s, height .3s; opacity: .6; }

  /* ── LAYOUT ── */
  .page { display: grid; grid-template-columns: 1fr 1.4fr; min-height: 100vh; }

  /* ── LEFT PANEL ── */
  .panel-left {
    position: sticky; top: 0; height: 100vh; overflow: hidden;
    background: linear-gradient(155deg, #0E0C08 0%, #1A150C 30%, #2E2214 60%, #4A3418 100%);
  }
  .panel-left::before {
    content: ''; position: absolute; inset: 0;
    background: radial-gradient(ellipse at 40% 50%, rgba(184,150,62,.16) 0%, transparent 65%);
  }
  .bg-word {
    position: absolute; top: 50%; left: 50%;
    transform: translate(-50%,-50%) rotate(-90deg);
    font-family: 'Cormorant Garamond', serif; font-size: 190px; font-weight: 300;
    color: rgba(184,150,62,.05); letter-spacing: -.02em; white-space: nowrap;
    pointer-events: none; user-select: none;
  }
  .grid-lines {
    position: absolute; inset: 0;
    background-image: linear-gradient(rgba(184,150,62,.04) 1px, transparent 1px),
                      linear-gradient(90deg, rgba(184,150,62,.04) 1px, transparent 1px);
    background-size: 50px 50px;
  }
  .orb {
    position: absolute; width: 380px; height: 380px; border-radius: 50%;
    background: radial-gradient(circle at 35% 35%, rgba(212,175,106,.2) 0%, rgba(184,150,62,.05) 55%, transparent 75%);
    top: 50%; left: 50%; transform: translate(-50%,-50%);
    animation: pulse-orb 8s ease-in-out infinite;
  }
  @keyframes pulse-orb { 0%,100%{transform:translate(-50%,-50%) scale(1);opacity:.7} 50%{transform:translate(-50%,-53%) scale(1.07);opacity:1} }

  .panel-left-inner {
    position: relative; z-index: 2; height: 100%;
    display: flex; flex-direction: column; justify-content: space-between;
    padding: 48px 56px;
  }
  .brand-logo {
    font-family: 'Cormorant Garamond', serif; font-size: 22px; font-weight: 300;
    letter-spacing: .3em; color: var(--cream); text-decoration: none;
  }
  .brand-logo span { color: var(--gold); }

  .panel-copy { flex: 1; display: flex; flex-direction: column; justify-content: center; }
  .tag-line { font-size: 9px; letter-spacing: .45em; text-transform: uppercase; color: var(--gold); margin-bottom: 24px; display: flex; align-items: center; gap: 14px; }
  .tag-line::before { content: ''; width: 36px; height: 1px; background: var(--gold); }

  .panel-headline {
    font-family: 'Cormorant Garamond', serif;
    font-size: clamp(40px, 3.8vw, 58px); font-weight: 300; line-height: 1.1;
    color: var(--cream); margin-bottom: 28px;
  }
  .panel-headline em { font-style: italic; color: var(--gold-light); }
  .panel-body { font-size: 10px; letter-spacing: .14em; line-height: 2; color: var(--stone); max-width: 300px; }

  .benefits { margin-top: 40px; display: flex; flex-direction: column; gap: 16px; }
  .benefit { display: flex; align-items: flex-start; gap: 14px; }
  .benefit-icon { width: 28px; height: 28px; border: 1px solid rgba(184,150,62,.35); flex-shrink: 0; display: flex; align-items: center; justify-content: center; color: var(--gold); font-size: 10px; margin-top: 1px; }
  .benefit-text { font-size: 9px; letter-spacing: .12em; line-height: 1.8; color: var(--stone); }
  .benefit-text strong { display: block; color: var(--cream); letter-spacing: .2em; text-transform: uppercase; font-weight: 300; margin-bottom: 2px; font-size: 8px; }

  .panel-footer { display: flex; align-items: center; gap: 16px; }
  .badge-circle {
    width: 68px; height: 68px; border-radius: 50%;
    border: 1px solid rgba(184,150,62,.3);
    display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 2px;
    animation: spin-slow 22s linear infinite; flex-shrink: 0;
  }
  @keyframes spin-slow { from{transform:rotate(0)} to{transform:rotate(360deg)} }
  .badge-circle span { font-size: 7px; letter-spacing: .1em; color: var(--gold); line-height: 1.5; }
  .panel-footer-text { font-family: 'Cormorant Garamond', serif; font-size: 13px; font-style: italic; color: var(--stone); line-height: 1.6; }

  /* ── RIGHT PANEL ── */
  .panel-right {
    background: var(--warm-white);
    padding: 60px 80px;
    position: relative; overflow: hidden;
  }
  .panel-right::before {
    content: ''; position: absolute; top: -100px; right: -100px;
    width: 320px; height: 320px; border-radius: 50%;
    background: radial-gradient(circle, rgba(184,150,62,.07) 0%, transparent 70%);
    pointer-events: none;
  }

  /* Steps indicator */
  .steps-bar { display: flex; align-items: center; gap: 0; margin-bottom: 52px; }
  .step { display: flex; align-items: center; gap: 10px; }
  .step-num {
    width: 28px; height: 28px; border-radius: 50%;
    border: 1px solid var(--gold-pale);
    display: flex; align-items: center; justify-content: center;
    font-size: 9px; letter-spacing: .1em; color: var(--mist);
    transition: all .3s;
  }
  .step.active .step-num { background: var(--gold); border-color: var(--gold); color: var(--dark-bg); }
  .step.done .step-num { background: var(--charcoal); border-color: var(--charcoal); color: var(--gold); }
  .step-label { font-size: 8px; letter-spacing: .2em; text-transform: uppercase; color: var(--mist); }
  .step.active .step-label { color: var(--charcoal); }
  .step-line { flex: 1; height: 1px; background: var(--gold-pale); margin: 0 16px; }

  .form-header { margin-bottom: 40px; }
  .form-label-top { font-size: 8px; letter-spacing: .45em; text-transform: uppercase; color: var(--gold); margin-bottom: 12px; display: flex; align-items: center; gap: 12px; }
  .form-label-top::before { content: ''; width: 24px; height: 1px; background: var(--gold); }
  .form-title { font-family: 'Cormorant Garamond', serif; font-size: 38px; font-weight: 300; color: var(--charcoal); line-height: 1.1; }
  .form-title em { font-style: italic; color: var(--stone); }
  .form-sub { font-size: 9px; letter-spacing: .14em; line-height: 2; color: var(--stone); margin-top: 10px; }

  .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
  .form-row.single { grid-template-columns: 1fr; }

  .field { margin-bottom: 22px; }
  .field-label { display: block; font-size: 8px; letter-spacing: .3em; text-transform: uppercase; color: var(--stone); margin-bottom: 8px; }
  .field-wrap { position: relative; }
  .field-icon { position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: var(--mist); font-size: 12px; pointer-events: none; transition: color .3s; }
  .field-input {
    width: 100%; padding: 15px 16px 15px 42px;
    background: var(--cream); border: 1px solid var(--gold-pale);
    font-family: 'Josefin Sans', sans-serif; font-size: 11px; letter-spacing: .1em;
    color: var(--charcoal); outline: none;
    transition: border-color .3s, box-shadow .3s, background .3s;
  }
  .field-input::placeholder { color: var(--mist); }
  .field-input:focus { border-color: var(--gold); box-shadow: 0 0 0 3px rgba(184,150,62,.1); background: var(--warm-white); }
  .field-focus-bar { position: absolute; bottom: 0; left: 0; right: 0; height: 2px; background: var(--gold); transform: scaleX(0); transform-origin: left; transition: transform .35s ease; }
  .field-input:focus ~ .field-focus-bar { transform: scaleX(1); }
  .field-wrap:focus-within .field-icon { color: var(--gold); }

  .field.has-error .field-input { border-color: var(--error); }
  .field-error { font-size: 8px; letter-spacing: .12em; color: var(--error); margin-top: 6px; display: flex; align-items: center; gap: 6px; }
  .field-error::before { content: '⚠'; font-size: 9px; }

  /* Password */
  .pwd-toggle { position: absolute; right: 14px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: var(--mist); font-size: 10px; letter-spacing: .1em; text-transform: uppercase; transition: color .2s; padding: 4px; }
  .pwd-toggle:hover { color: var(--gold); }

  /* ── STRENGTH BAR (5-char based) ── */
  .strength-bar { display: flex; gap: 4px; margin-top: 8px; }
  .strength-seg { flex: 1; height: 2px; background: var(--gold-pale); border-radius: 2px; transition: background .3s; }
  .strength-seg.active-weak   { background: var(--error); }
  .strength-seg.active-fair   { background: #e67e22; }
  .strength-seg.active-strong { background: #27ae60; }
  .strength-label { font-size: 8px; letter-spacing: .15em; color: var(--mist); margin-top: 4px; }

  /* Terms */
  .terms-row { margin-bottom: 28px; }
  .checkbox-label { display: flex; align-items: flex-start; gap: 12px; cursor: pointer; font-size: 9px; letter-spacing: .12em; line-height: 1.8; color: var(--stone); }
  .checkbox-label input[type="checkbox"] { display: none; }
  .checkbox-box { width: 16px; height: 16px; border: 1px solid var(--mist); display: flex; align-items: center; justify-content: center; transition: border-color .2s, background .2s; flex-shrink: 0; margin-top: 2px; }
  .checkbox-label input:checked ~ .checkbox-box { background: var(--gold); border-color: var(--gold); }
  .checkbox-label input:checked ~ .checkbox-box::after { content: '✓'; color: var(--dark-bg); font-size: 9px; }
  .terms-link { color: var(--gold); text-decoration: none; border-bottom: 1px solid rgba(184,150,62,.3); }
  .terms-error { font-size: 8px; letter-spacing: .12em; color: var(--error); margin-top: 6px; display: flex; align-items: center; gap: 6px; }
  .terms-error::before { content: '⚠'; }

  /* Submit */
  .btn-register {
    width: 100%; padding: 18px;
    background: var(--charcoal); border: none; cursor: pointer;
    font-family: 'Josefin Sans', sans-serif; font-size: 10px;
    font-weight: 400; letter-spacing: .3em; text-transform: uppercase;
    color: var(--cream); position: relative; overflow: hidden;
    transition: background .35s; margin-bottom: 24px;
  }
  .btn-register::before {
    content: ''; position: absolute; inset: 0;
    background: var(--gold); transform: translateX(-100%);
    transition: transform .45s cubic-bezier(.77,0,.18,1);
  }
  .btn-register:hover::before { transform: translateX(0); }
  .btn-register span { position: relative; z-index: 1; }

  .login-row { text-align: center; }
  .login-text { font-size: 9px; letter-spacing: .15em; color: var(--stone); }
  .login-link { color: var(--gold); text-decoration: none; border-bottom: 1px solid rgba(184,150,62,.3); padding-bottom: 1px; transition: border-color .2s; }
  .login-link:hover { border-color: var(--gold); }

  /* Success */
  .success-screen { display: none; text-align: center; padding: 40px 0; animation: fade-in .6s ease; }
  @keyframes fade-in { from{opacity:0;transform:translateY(16px)} to{opacity:1;transform:translateY(0)} }
  .success-icon { width: 80px; height: 80px; border-radius: 50%; border: 1px solid var(--gold); margin: 0 auto 32px; display: flex; align-items: center; justify-content: center; font-size: 28px; color: var(--gold); animation: spin-slow 20s linear infinite; }
  .success-title { font-family: 'Cormorant Garamond', serif; font-size: 40px; font-weight: 300; color: var(--charcoal); margin-bottom: 16px; }
  .success-title em { font-style: italic; color: var(--stone); }
  .success-body { font-size: 10px; letter-spacing: .14em; line-height: 2; color: var(--stone); max-width: 320px; margin: 0 auto 40px; }
  .btn-goto-login { display: inline-block; padding: 16px 48px; background: var(--gold); color: var(--dark-bg); font-family: 'Josefin Sans', sans-serif; font-size: 10px; letter-spacing: .3em; text-transform: uppercase; text-decoration: none; transition: background .3s; }
  .btn-goto-login:hover { background: var(--gold-light); }

  .corner-deco { position: absolute; bottom: 40px; right: 40px; width: 80px; height: 80px; opacity: .15; border-right: 1px solid var(--gold); border-bottom: 1px solid var(--gold); pointer-events: none; }
  .corner-deco-tl { position: absolute; top: 40px; left: 40px; width: 50px; height: 50px; opacity: .12; border-left: 1px solid var(--gold); border-top: 1px solid var(--gold); pointer-events: none; }

  @media (max-width: 900px) {
    .page { grid-template-columns: 1fr; }
    .panel-left { position: relative; height: auto; }
    .panel-left-inner { padding: 36px 28px; }
    .benefits { display: none; }
    .panel-right { padding: 48px 24px; }
    .form-row { grid-template-columns: 1fr; }
  }
</style>
</head>
<body>

<div class="cursor" id="cursor"></div>
<div class="cursor-ring" id="cursorRing"></div>

<div class="page">

  <!-- ══ LEFT PANEL ══ -->
  <div class="panel-left">
    <div class="bg-word">JOIN</div>
    <div class="grid-lines"></div>
    <div class="orb"></div>
    <div class="panel-left-inner">
      <a href="index.html" class="brand-logo">MAISON <span>VELOUR</span></a>
      <div class="panel-copy">
        <div class="tag-line">Membership</div>
        <h1 class="panel-headline">Become<br><em>Part of</em><br>Something<br>Rare</h1>
        <p class="panel-body">Membership is more than an account — it is an invitation to a world of exclusive access, curated service, and lasting elegance.</p>
        <div class="benefits">
          <div class="benefit">
            <div class="benefit-icon">✦</div>
            <div class="benefit-text"><strong>Early Access</strong>First to discover new collections before public release.</div>
          </div>
          <div class="benefit">
            <div class="benefit-icon">◈</div>
            <div class="benefit-text"><strong>Private Styling</strong>Complimentary one-on-one appointments with our stylists.</div>
          </div>
          <div class="benefit">
            <div class="benefit-icon">◇</div>
            <div class="benefit-text"><strong>Lifetime Care</strong>Full repair and restoration coverage on every piece.</div>
          </div>
          <div class="benefit">
            <div class="benefit-icon">⬡</div>
            <div class="benefit-text"><strong>Members Rewards</strong>Earn and redeem points with every purchase.</div>
          </div>
        </div>
      </div>
      <div class="panel-footer">
        <div class="badge-circle">
          <span>MAISON</span>
          <span style="color:var(--gold-light);font-size:11px">◆</span>
          <span>1998</span>
        </div>
        <p class="panel-footer-text">"An invitation to<br>belong beautifully."</p>
      </div>
    </div>
  </div>

  <!-- ══ RIGHT PANEL ══ -->
  <div class="panel-right">
    <div class="corner-deco"></div>
    <div class="corner-deco-tl"></div>

    <div class="steps-bar">
      <div class="step active" id="step1"><div class="step-num">1</div><div class="step-label">Details</div></div>
      <div class="step-line"></div>
      <div class="step" id="step2"><div class="step-num">2</div><div class="step-label">Security</div></div>
      <div class="step-line"></div>
      <div class="step" id="step3"><div class="step-num">3</div><div class="step-label">Confirm</div></div>
    </div>

    <!-- SUCCESS -->
    <?php if ($success): ?>
    <div class="success-screen" style="display:block">
      <div class="success-icon">◆</div>
      <h2 class="success-title">Welcome,<br><em><?= htmlspecialchars($_SESSION['registered_name']) ?></em></h2>
      <p class="success-body">Your membership has been created. You are now part of the Maison Velour Inner Circle.</p>
      <a href="login.php" class="btn-goto-login">Sign In to Your Account</a>
    </div>

    <!-- FORM -->
    <?php else: ?>
    <div id="registerFormWrap">
      <div class="form-header">
        <div class="form-label-top">Request Access</div>
        <h2 class="form-title">Create <em>Account</em></h2>
        <p class="form-sub">Join the Inner Circle and unlock a world of exclusive privileges.</p>
      </div>

      <form method="POST" action="" id="registerForm" novalidate>

        <!-- Name -->
        <div class="form-row">
          <div class="field <?= hasErr('first_name', $errors) ?>">
            <label class="field-label" for="first_name">First Name</label>
            <div class="field-wrap">
              <span class="field-icon">✦</span>
              <input type="text" name="first_name" id="first_name" class="field-input"
                placeholder="Alexandra"
                value="<?= htmlspecialchars($old['first_name'] ?? '') ?>">
              <div class="field-focus-bar"></div>
            </div>
            <?php if (isset($errors['first_name'])): ?>
              <div class="field-error"><?= htmlspecialchars($errors['first_name']) ?></div>
            <?php endif; ?>
          </div>

          <div class="field <?= hasErr('last_name', $errors) ?>">
            <label class="field-label" for="last_name">Last Name</label>
            <div class="field-wrap">
              <span class="field-icon">✦</span>
              <input type="text" name="last_name" id="last_name" class="field-input"
                placeholder="Beaumont"
                value="<?= htmlspecialchars($old['last_name'] ?? '') ?>">
              <div class="field-focus-bar"></div>
            </div>
            <?php if (isset($errors['last_name'])): ?>
              <div class="field-error"><?= htmlspecialchars($errors['last_name']) ?></div>
            <?php endif; ?>
          </div>
        </div>

        <!-- Email -->
        <div class="form-row single">
          <div class="field <?= hasErr('email', $errors) ?>">
            <label class="field-label" for="email">Email Address</label>
            <div class="field-wrap">
              <span class="field-icon">◎</span>
              <input type="email" name="email" id="email" class="field-input"
                placeholder="your@email.com"
                value="<?= htmlspecialchars($old['email'] ?? '') ?>">
              <div class="field-focus-bar"></div>
            </div>
            <?php if (isset($errors['email'])): ?>
              <div class="field-error"><?= htmlspecialchars($errors['email']) ?></div>
            <?php endif; ?>
          </div>
        </div>

        <!-- Phone -->
        <div class="form-row single">
          <div class="field">
            <label class="field-label" for="phone">Phone <span style="opacity:.5;font-size:7px">(Optional)</span></label>
            <div class="field-wrap">
              <span class="field-icon">◇</span>
              <input type="tel" name="phone" id="phone" class="field-input"
                placeholder="+62 812 3456 7890"
                value="<?= htmlspecialchars($old['phone'] ?? '') ?>">
              <div class="field-focus-bar"></div>
            </div>
          </div>
        </div>

        <!-- Password -->
        <div class="form-row">
          <div class="field <?= hasErr('password', $errors) ?>">
            <label class="field-label" for="password">Password</label>
            <div class="field-wrap">
              <span class="field-icon">◈</span>
              <input type="password" name="password" id="password" class="field-input"
                placeholder="Min. 5 characters">
              <button type="button" class="pwd-toggle" onclick="togglePwd('password', this)">SHOW</button>
              <div class="field-focus-bar"></div>
            </div>
            <!-- Strength bar -->
            <div class="strength-bar">
              <div class="strength-seg" id="seg1"></div>
              <div class="strength-seg" id="seg2"></div>
              <div class="strength-seg" id="seg3"></div>
            </div>
            <div class="strength-label" id="strengthLabel">Enter password</div>
            <?php if (isset($errors['password'])): ?>
              <div class="field-error"><?= htmlspecialchars($errors['password']) ?></div>
            <?php endif; ?>
          </div>

          <div class="field <?= hasErr('password_confirm', $errors) ?>">
            <label class="field-label" for="password_confirm">Confirm Password</label>
            <div class="field-wrap">
              <span class="field-icon">◈</span>
              <input type="password" name="password_confirm" id="password_confirm" class="field-input"
                placeholder="Repeat password">
              <button type="button" class="pwd-toggle" onclick="togglePwd('password_confirm', this)">SHOW</button>
              <div class="field-focus-bar"></div>
            </div>
            <?php if (isset($errors['password_confirm'])): ?>
              <div class="field-error"><?= htmlspecialchars($errors['password_confirm']) ?></div>
            <?php endif; ?>
          </div>
        </div>

        <!-- Terms -->
        <div class="terms-row">
          <label class="checkbox-label">
            <input type="checkbox" name="agree" id="agree" <?= isset($_POST['agree']) ? 'checked' : '' ?>>
            <span class="checkbox-box"></span>
            <span>I agree to the <a href="#" class="terms-link">Terms of Service</a> and <a href="#" class="terms-link">Privacy Policy</a> of Maison Velour.</span>
          </label>
          <?php if (isset($errors['agree'])): ?>
            <div class="terms-error"><?= htmlspecialchars($errors['agree']) ?></div>
          <?php endif; ?>
        </div>

        <button type="submit" class="btn-register">
          <span>Create My Membership</span>
        </button>

      </form>

      <div class="login-row">
        <span class="login-text">Already a member? &nbsp;<a href="login.php" class="login-link">Sign In</a></span>
      </div>
    </div>
    <?php endif; ?>

  </div><!-- /panel-right -->
</div><!-- /page -->

<script>
  // Custom cursor
  const cursor = document.getElementById('cursor');
  const ring   = document.getElementById('cursorRing');
  document.addEventListener('mousemove', e => {
    cursor.style.left = e.clientX + 'px';
    cursor.style.top  = e.clientY + 'px';
    ring.style.left   = e.clientX + 'px';
    ring.style.top    = e.clientY + 'px';
  });

  // Toggle password visibility
  function togglePwd(id, btn) {
    const inp = document.getElementById(id);
    if (inp.type === 'password') {
      inp.type = 'text';
      btn.textContent = 'HIDE';
    } else {
      inp.type = 'password';
      btn.textContent = 'SHOW';
    }
  }

  // ── PASSWORD STRENGTH (min 5 chars) ──
  const pwdInput      = document.getElementById('password');
  const seg1          = document.getElementById('seg1');
  const seg2          = document.getElementById('seg2');
  const seg3          = document.getElementById('seg3');
  const strengthLabel = document.getElementById('strengthLabel');

  pwdInput.addEventListener('input', function() {
    const len = this.value.length;
    // Reset
    [seg1, seg2, seg3].forEach(s => s.className = 'strength-seg');

    if (len === 0) {
      strengthLabel.textContent = 'Enter password';
    } else if (len < 5) {
      // Weak — 1 red bar
      seg1.classList.add('active-weak');
      strengthLabel.textContent = 'Too short (min 5)';
      strengthLabel.style.color = '#c0392b';
    } else if (len < 8) {
      // Fair — 2 orange bars
      seg1.classList.add('active-fair');
      seg2.classList.add('active-fair');
      strengthLabel.textContent = 'Fair';
      strengthLabel.style.color = '#e67e22';
    } else {
      // Strong — 3 green bars
      seg1.classList.add('active-strong');
      seg2.classList.add('active-strong');
      seg3.classList.add('active-strong');
      strengthLabel.textContent = 'Strong';
      strengthLabel.style.color = '#27ae60';
    }
  });

  // Step indicator animation on submit
  document.getElementById('registerForm')?.addEventListener('submit', function() {
    document.getElementById('step1').classList.remove('active');
    document.getElementById('step1').classList.add('done');
    document.getElementById('step2').classList.add('active');
  });
</script>
</body>
</html>