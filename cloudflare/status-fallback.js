// Cloudflare Worker for status.prusakovuuzeiga.lt (routes: "/" and "/online" only).
// Passes the request through the tunnel to the kasa; if the kasa does not answer,
// shows a branded "temporarily unavailable" page that retries every 30 seconds.

const OFFLINE_PAGE = `<!DOCTYPE html>
<html lang="lt">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<meta name="theme-color" content="#14664A">
<meta http-equiv="refresh" content="30">
<title>Užsakymai — Prusakovų užeiga</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@500;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
<style>
  :root { --green: #14664A; --cream: #FBF7F0; --sand: #F3ECDF; --line: #E8E0D2; --amber: #B5761B; --ink: #2A2620; --muted: #7A7064; }
  * { box-sizing: border-box; }
  body { margin: 0; min-height: 100vh; background: var(--cream); color: var(--ink); font-family: 'Manrope', system-ui, sans-serif; }
  main { max-width: 560px; margin: 0 auto; padding: max(20px, env(safe-area-inset-top)) 16px 28px; }
  header { text-align: center; margin-bottom: 20px; }
  header h1 { margin: 0; font-family: 'Playfair Display', Georgia, serif; font-size: 28px; color: var(--green); }
  header p { margin: 4px 0 0; color: var(--muted); font-size: 14px; }
  .card { padding: 32px 24px; border-radius: 20px; background: #fff; border: 1px solid var(--line); text-align: center; }
  .icon { display: inline-flex; align-items: center; justify-content: center; width: 64px; height: 64px; margin-bottom: 16px; border-radius: 50%; background: var(--sand); color: var(--amber); }
  .card h2 { margin: 0 0 10px; font-family: 'Playfair Display', Georgia, serif; font-size: 24px; color: var(--green); }
  .card p { margin: 0 0 10px; font-size: 16px; line-height: 1.5; }
  .retry { display: inline-flex; align-items: center; gap: 8px; margin-top: 8px; color: var(--muted); font-size: 14px; }
  .retry::before { content: ''; width: 8px; height: 8px; border-radius: 50%; background: var(--amber); animation: pulse 1.6s ease-in-out infinite; }
  @keyframes pulse { 50% { opacity: .25; } }
  .menu { display: inline-block; margin-top: 20px; color: var(--green); font-weight: 700; font-size: 15px; }
</style>
</head>
<body>
<main>
  <header>
    <h1>Prusakovų užeiga</h1>
    <p>Užsakymų būsena realiu laiku</p>
  </header>
  <div class="card" role="status">
    <span class="icon" aria-hidden="true">
      <svg width="30" height="30" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M17 21a1 1 0 0 0 1-1v-5.35c0-.457.316-.844.727-1.041a4 4 0 0 0-2.134-7.589 5 5 0 0 0-9.186 0 4 4 0 0 0-2.134 7.588c.411.198.727.585.727 1.041V20a1 1 0 0 0 1 1Z"/><path d="M6 17h12"/></svg>
    </span>
    <h2>Užsakymų būsena laikinai nepasiekiama</h2>
    <p>Jūsų užsakymas gaminamas kaip įprasta. Paruoštų užsakymų numerius matysite ekrane prie prekystalio.</p>
    <p class="retry">Puslapis atsinaujins automatiškai</p>
    <br>
    <a class="menu" href="https://prusakovuuzeiga.lt/">Žiūrėti meniu →</a>
  </div>
</main>
</body>
</html>`;

const offline = () => new Response(OFFLINE_PAGE, {
  status: 503,
  headers: {
    'Content-Type': 'text/html; charset=utf-8',
    'Cache-Control': 'no-store',
    'Retry-After': '30',
  },
});

export default {
  async fetch(request) {
    try {
      const response = await fetch(request);
      return response.status >= 500 ? offline() : response;
    } catch {
      return offline();
    }
  },
};
