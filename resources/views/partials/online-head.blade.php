<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<meta name="theme-color" content="#14664A">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-title" content="Užsakymai">
<title>{{ $title }}</title>
<link rel="manifest" href="{{ $manifest }}">
<link rel="apple-touch-icon" href="/online-icon-180.png">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@500;600;700;800&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
<style>
    :root {
        --green: #14664A;
        --green-soft: #E3EFE8;
        --cream: #FBF7F0;
        --sand: #F3ECDF;
        --line: #E8E0D2;
        --amber: #B5761B;
        --ink: #2A2620;
        --muted: #7A7064;
    }
    * { box-sizing: border-box; }
    body {
        margin: 0;
        min-height: 100vh;
        background: var(--cream);
        color: var(--ink);
        font-family: 'Manrope', system-ui, sans-serif;
        -webkit-tap-highlight-color: transparent;
    }
    main {
        max-width: 560px;
        margin: 0 auto;
        padding: max(20px, env(safe-area-inset-top)) 16px max(28px, env(safe-area-inset-bottom));
    }
    header { text-align: center; margin-bottom: 20px; }
    header h1 {
        margin: 0;
        font-family: 'Playfair Display', Georgia, serif;
        font-size: 28px;
        color: var(--green);
    }
    header p { margin: 4px 0 0; color: var(--muted); font-size: 14px; }
    .unavailable { display: none; padding: 32px 24px; border-radius: 20px; background: #fff; border: 1px solid var(--line); text-align: center; }
    .unavailable .icon { display: inline-flex; align-items: center; justify-content: center; width: 64px; height: 64px; margin-bottom: 16px; border-radius: 50%; background: var(--sand); color: var(--amber); }
    .unavailable h2 { margin: 0 0 10px; font-family: 'Playfair Display', Georgia, serif; font-size: 24px; color: var(--green); }
    .unavailable p { margin: 0 0 10px; color: var(--ink); font-size: 16px; line-height: 1.5; }
    .unavailable .retry { display: inline-flex; align-items: center; gap: 8px; margin: 8px 0 0; color: var(--muted); font-size: 14px; }
    .unavailable .retry::before { content: ''; width: 8px; height: 8px; border-radius: 50%; background: var(--amber); animation: pulse 1.6s ease-in-out infinite; }
    @keyframes pulse { 50% { opacity: .25; } }
</style>
