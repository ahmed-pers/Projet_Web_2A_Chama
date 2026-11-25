<?php
$messages = [
    'registered' => 'Account created. You can now sign in.',
    'invalid' => 'Please check your details and try again.',
    'credentials' => 'Email or password was not recognized.',
    'exists' => 'That username or email is already registered.',
    'sent' => 'If that email is registered, a reset link has been sent.',
    'reset' => 'Your password was updated. Sign in with your new password.',
    'expired' => 'That reset link is invalid or has expired.',
];
?>
<!doctype html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>FIXMYCITY | Account</title><link rel="stylesheet" href="assets/css/style.css"></head>
<body><main id="content" class="simple-form">
  <h1>FIXMYCITY</h1>
  <?php if (isset($messages[$message])): ?><p class="notice" role="status"><?= e($messages[$message]) ?></p><?php endif; ?>
  <?php if ($mode === 'register'): ?>
    <h2>Create account</h2><form action="index.php?action=register" method="post"><input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
      <label>Username <input name="username" minlength="3" maxlength="50" required></label><label>Email <input type="email" name="email" maxlength="150" required></label><label>Password <input type="password" name="password" minlength="8" required></label><label>Confirm password <input type="password" name="password_confirm" minlength="8" required></label><button type="submit">Create account</button>
    </form><p><a href="index.php?action=auth">Sign in</a></p>
  <?php elseif ($mode === 'forgot'): ?>
    <h2>Reset password</h2><form action="index.php?action=forgot_password" method="post"><input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>"><label>Account email <input type="email" name="email" required></label><button type="submit">Send reset link</button></form><p><a href="index.php?action=auth">Sign in</a></p>
  <?php elseif ($mode === 'reset'): ?>
    <h2>Choose a new password</h2><form action="index.php?action=reset_password" method="post"><input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>"><input type="hidden" name="token" value="<?= e($resetToken) ?>"><label>New password <input type="password" name="password" minlength="8" required></label><button type="submit">Update password</button></form>
  <?php else: ?>
    <h2>Sign in</h2><form action="index.php?action=login" method="post"><input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>"><label>Email or username <input name="identity" autocomplete="username" required></label><label>Password <input type="password" name="password" autocomplete="current-password" required></label><button type="submit">Sign in</button></form><p><a href="index.php?action=auth&amp;mode=register">Create account</a> · <a href="index.php?action=auth&amp;mode=forgot">Forgot password?</a></p>
  <?php endif; ?>
</main></body></html>
