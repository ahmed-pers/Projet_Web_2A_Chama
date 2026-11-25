<?php
// View: display only. Static page; the translations live in assets/js/info.js.
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>FIXMYCITY – How to use</title>

  <style>
    /* ---------- Tokens (same as the main page) ---------- */
    :root {
      --cream: #f7f3ee;
      --white: #ffffff;
      --sand: #ece5dd;
      --peach: #eab48a;
      --ink: #1a1a1a;
      --muted: #8a847c;
      --line: #e4ded6;
      --radius-lg: 22px;
      --radius-md: 14px;
      --radius-pill: 999px;
      --font: "Inter", "Segoe UI", system-ui, -apple-system, Roboto, Arial, sans-serif;
    }

    *, *::before, *::after { box-sizing: border-box; }

    html { height: 100%; }

    body {
      margin: 0;
      min-height: 100%;
      font-family: var(--font);
      font-size: 15px;
      line-height: 1.5;
      color: var(--ink);
      background-color: var(--cream);
      background-image: url("assets/backgrounds/body_background.png");
      background-size: cover;
      background-attachment: fixed;

      display: grid;
      grid-template-columns: 240px minmax(0, 1fr);
      gap: 12px;
      padding: 12px;
    }

    h1, h2, p, ul, ol { margin: 0; }
    ul, ol { padding: 0; list-style: none; }
    a { color: inherit; text-decoration: none; }
    img { display: block; max-width: 100%; }
    :focus-visible { outline: 2px solid var(--ink); outline-offset: 3px; }

    /* ---------- Sidebar ---------- */
    #sidebar {
      display: flex;
      flex-direction: column;
      gap: 40px;
      padding: 24px 16px;
      border-radius: var(--radius-lg);
      background:
        linear-gradient(rgba(247, 243, 238, 0.88), rgba(247, 243, 238, 0.88)),
        url("assets/backgrounds/side_bar_background.jpg") center / cover no-repeat;
      background-color: var(--cream);
    }

    #sidebar header a {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 8px;
    }

    #sidebar header span {
      font-size: 18px;
      font-weight: 800;
      letter-spacing: 0.12em;
    }

    #sidebar nav ul { display: flex; flex-direction: column; gap: 4px; }

    #sidebar nav a {
      display: block;
      padding: 10px 14px;
      border-radius: 10px;
      font-weight: 500;
      color: var(--muted);
      transition: background-color 0.15s, color 0.15s;
    }

    #sidebar nav a:hover,
    #sidebar nav a[aria-current="page"] {
      background-color: var(--sand);
      color: var(--ink);
    }

    #sidebar nav a[aria-current="page"] { font-weight: 700; }

    /* ---------- Main card ---------- */
    #content {
      background-color: var(--white);
      border-radius: var(--radius-lg);
      padding: 32px 40px 40px;
    }

    .top {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      gap: 16px;
      flex-wrap: wrap;
      margin-bottom: 28px;
    }

    h1 { font-size: 28px; font-weight: 600; line-height: 1.2; }
    .top p { margin-top: 6px; color: var(--muted); }

    /* Language picker */
    .lang { display: flex; align-items: center; gap: 8px; }
    .lang label { font-size: 14px; font-weight: 500; color: var(--muted); }

    .lang select {
      padding: 8px 12px;
      font: inherit;
      color: var(--ink);
      background-color: var(--cream);
      border: 1px solid var(--line);
      border-radius: var(--radius-pill);
      cursor: pointer;
    }

    /* Steps */
    .steps {
      max-width: 640px;
      display: flex;
      flex-direction: column;
      counter-reset: step;
    }

    .steps li {
      position: relative;
      padding: 20px 0 20px 52px;
      border-top: 1px solid var(--line);
      counter-increment: step;
    }

    .steps li::before {
      content: counter(step);
      position: absolute;
      inset-inline-start: 0;
      top: 20px;
      width: 34px;
      height: 34px;
      display: grid;
      place-items: center;
      font-weight: 700;
      color: var(--white);
      background-color: var(--ink);
      border-radius: 50%;
    }

    /* flip the padding side for right-to-left languages */
    [dir="rtl"] .steps li { padding: 20px 52px 20px 0; }

    .steps h2 { font-size: 17px; font-weight: 600; }
    .steps p { margin-top: 2px; color: var(--muted); }

    .cta {
      display: inline-block;
      margin-top: 24px;
      padding: 14px 28px;
      font-weight: 600;
      color: var(--white);
      background-color: var(--ink);
      border-radius: var(--radius-pill);
    }

    /* ---------- Phone ---------- */
    @media (max-width: 720px) {
      body { grid-template-columns: minmax(0, 1fr); padding: 8px; }
      #sidebar {
        flex-direction: row;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 12px;
        overflow-x: auto;
      }
      #sidebar header a { flex-direction: row; }
      #sidebar header img { width: 32px; height: 32px; }
      #sidebar nav ul { flex-direction: row; }
      #sidebar nav a { white-space: nowrap; padding: 8px 12px; }
      #content { padding: 24px 20px 28px; }
    }
  </style>
</head>
<body>

  <!-- ========== SIDEBAR ========== -->
  <aside id="sidebar">
    <header>
      <a href="index.php?action=report_form">
        <img src="assets/imgs/logo.png" alt="FIXMYCITY logo" width="40" height="40">
        <span>FIXMYCITY</span>
      </a>
    </header>

    <nav>
      <ul>
        <li><a href="index.php?action=report_form" data-i18n="nav_report">Report a problem</a></li>
        <li><a href="index.php?action=info" aria-current="page" data-i18n="nav_info">How to use</a></li>
      </ul>
    </nav>
  </aside>

  <!-- ========== MAIN ========== -->
  <main id="content">
    <div class="top">
      <div>
        <h1 data-i18n="title">How to use FIXMYCITY</h1>
        <p data-i18n="subtitle">Five simple steps to report a problem in your city.</p>
      </div>

      <div class="lang">
        <label for="lang-select" data-i18n="lang_label">Language:</label>
        <select id="lang-select">
          <option value="en">English</option>
          <option value="fr">Français</option>
          <option value="ar">العربية</option>
        </select>
      </div>
    </div>

    <ol class="steps">
      <li>
        <h2 data-i18n="s1_title">Add a photo</h2>
        <p data-i18n="s1_text">Take a picture of the problem or choose one from your phone.</p>
      </li>
      <li>
        <h2 data-i18n="s2_title">Tell us where</h2>
        <p data-i18n="s2_text">Type the address or street, or enter the coordinates.</p>
      </li>
      <li>
        <h2 data-i18n="s3_title">Choose the category</h2>
        <p data-i18n="s3_text">Pick the type of problem: road hole, trash, water leak…</p>
      </li>
      <li>
        <h2 data-i18n="s4_title">Add details (optional)</h2>
        <p data-i18n="s4_text">Tick the option and write a few words if you want to.</p>
      </li>
      <li>
        <h2 data-i18n="s5_title">Send your report</h2>
        <p data-i18n="s5_text">Your report goes to the department in charge of that category.</p>
      </li>
    </ol>

    <a class="cta" href="index.php?action=report_form" data-i18n="cta">Start a report</a>
  </main>

  <script src="assets/js/info.js" defer></script>
</body>
</html>