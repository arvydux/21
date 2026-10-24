<!DOCTYPE html>
<html lang="lt">
<head>
    @include('partials.online-head', [
        'title' => $number === null ? 'Užsakymas — Prusakovų užeiga' : "Užsakymas Nr. {$number} — Prusakovų užeiga",
        'manifest' => route('online-orders.order-manifest', $token, absolute: false),
    ])
    <style>
        .order {
            padding: 28px 20px 24px;
            border-radius: 24px;
            background: #fff;
            border: 1px solid var(--line);
            text-align: center;
            transition: background .3s, color .3s;
        }
        .order .label { margin: 0; color: var(--muted); font-size: 13px; font-weight: 800; letter-spacing: .12em; text-transform: uppercase; }
        .order .number { margin: 2px 0 14px; font-size: 104px; font-weight: 800; line-height: 1; font-variant-numeric: tabular-nums; }
        .order .state {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            border-radius: 999px;
            background: var(--sand);
            color: var(--amber);
            font-size: 15px;
            font-weight: 800;
            letter-spacing: .06em;
            text-transform: uppercase;
        }
        .order .state::before { content: ''; width: 10px; height: 10px; border-radius: 50%; background: currentColor; }
        .order[data-status="preparing"] .state::before { animation: pulse 1.6s ease-in-out infinite; }
        .order .text { margin: 14px 0 0; font-size: 16px; line-height: 1.5; }
        .order[data-status="ready"] { background: var(--green); border-color: var(--green); color: #fff; }
        .order[data-status="ready"] .label { color: rgba(255, 255, 255, .75); }
        .order[data-status="ready"] .state { background: #fff; color: var(--green); }
        .order:is([data-status="taken"], [data-status="missing"]) .number { color: var(--muted); }
        .order:is([data-status="taken"], [data-status="missing"]) .state { color: var(--muted); }
        .order[data-status="missing"] :is(.label, .number) { display: none; }

        .notify { margin-top: 18px; }
        .notify[hidden], .notify [hidden] { display: none; }
        .notify button {
            width: 100%;
            padding: 14px;
            border: 0;
            border-radius: 14px;
            background: var(--green);
            color: #fff;
            font: inherit;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
        }
        .notify button:disabled { background: var(--green-soft); color: var(--green); }
        .notify p { margin: 0; color: var(--muted); font-size: 14px; line-height: 1.45; }

        .links { display: flex; justify-content: center; gap: 20px; margin-top: 20px; font-size: 15px; font-weight: 700; }
        .links a { color: var(--green); }
        footer { margin-top: 16px; text-align: center; color: var(--muted); font-size: 13px; }
        footer .offline { display: none; color: #B3412F; font-weight: 700; }
        footer.is-offline .offline { display: block; }
        main.is-unavailable .unavailable { display: block; }
        main.is-unavailable :is(.order, .links, footer) { display: none !important; }
    </style>
</head>
<body>
<main data-main>
    <header>
        <h1>Prusakovų užeiga</h1>
        <p>Jūsų užsakymo būsena</p>
    </header>

    <div class="order" data-order data-status="{{ $status }}" role="status" aria-live="polite">
        <p class="label">Užsakymas Nr.</p>
        <p class="number" data-order-number>{{ $number }}</p>
        <span class="state" data-state>{{ match ($status) {
            'preparing' => 'Gaminamas',
            'ready' => 'Paruoštas',
            'taken' => 'Atsiimtas',
            default => 'Nerastas',
        } }}</span>
        <p class="text" data-text>{{ match ($status) {
            'preparing' => 'Pranešime, kai bus paruoštas.',
            'ready' => 'Atsiimkite prie prekystalio.',
            'taken' => 'Ačiū ir skanaus!',
            default => 'Šis kvitas nebegalioja. Visų užsakymų būseną matysite bendrame ekrane.',
        } }}</p>

        <div class="notify" data-notify-area hidden>
            <button type="button" data-notify hidden></button>
            <p data-notify-hint hidden></p>
        </div>
    </div>

    <div class="unavailable" data-unavailable role="status" aria-live="polite">
        <span class="icon" aria-hidden="true">
            <svg width="30" height="30" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M17 21a1 1 0 0 0 1-1v-5.35c0-.457.316-.844.727-1.041a4 4 0 0 0-2.134-7.589 5 5 0 0 0-9.186 0 4 4 0 0 0-2.134 7.588c.411.198.727.585.727 1.041V20a1 1 0 0 0 1 1Z"/><path d="M6 17h12"/></svg>
        </span>
        <h2>Užsakymų būsena laikinai nepasiekiama</h2>
        <p>Jūsų užsakymas gaminamas kaip įprasta. Paruoštų užsakymų numerius matysite ekrane prie prekystalio.</p>
        <p class="retry">Puslapis atsinaujins automatiškai</p>
    </div>

    <nav class="links">
        <a href="{{ route('online-orders', absolute: false) }}">Visi užsakymai</a>
        <a href="https://prusakovuuzeiga.lt/">Meniu</a>
    </nav>

    <footer data-footer>
        <p class="offline">Nepavyksta atnaujinti, bandome dar kartą…</p>
    </footer>
</main>

<script>
    (() => {
        const token = @js($token);
        const dataUrl = @js(route('online-orders.order-data', $token, absolute: false));
        const subscribeUrl = @js(route('online-orders.subscribe', absolute: false));
        const vapidKey = @js(config('online-orders.vapid.public_key'));
        const notifyKey = 'notified-order-token';
        const pushSupported = Boolean(vapidKey) && 'serviceWorker' in navigator && 'PushManager' in window && 'Notification' in window;
        const isIos = /iPhone|iPad|iPod/.test(navigator.userAgent) || (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1);
        const isStandalone = window.matchMedia('(display-mode: standalone)').matches || navigator.standalone === true;
        const states = {
            preparing: ['Gaminamas', 'Pranešime, kai bus paruoštas.'],
            ready: ['Paruoštas', 'Atsiimkite prie prekystalio.'],
            taken: ['Atsiimtas', 'Ačiū ir skanaus!'],
            missing: ['Nerastas', 'Šis kvitas nebegalioja. Visų užsakymų būseną matysite bendrame ekrane.'],
        };
        const main = document.querySelector('[data-main]');
        const card = document.querySelector('[data-order]');
        const footer = document.querySelector('[data-footer]');
        const failuresBeforeUnavailable = 2;
        let failures = 0;
        let status = card.dataset.status;
        let number = Number(card.querySelector('[data-order-number]').textContent) || null;
        let subscribing = false;
        let isNotified = false;
        try { isNotified = localStorage.getItem(notifyKey) === token; } catch {}

        const urlBase64ToUint8Array = (base64) => {
            const padded = (base64 + '='.repeat((4 - base64.length % 4) % 4)).replace(/-/g, '+').replace(/_/g, '/');
            return Uint8Array.from(atob(padded), (char) => char.charCodeAt(0));
        };

        const startNotifying = async () => {
            subscribing = true;
            render();
            try {
                const permission = await Notification.requestPermission();
                if (permission !== 'granted') return;
                const registration = await navigator.serviceWorker.register('/online-sw.js');
                await navigator.serviceWorker.ready;
                const subscription = await registration.pushManager.getSubscription()
                    || await registration.pushManager.subscribe({ userVisibleOnly: true, applicationServerKey: urlBase64ToUint8Array(vapidKey) });
                const response = await fetch(subscribeUrl, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
                    body: JSON.stringify({ token, ...subscription.toJSON() }),
                });
                if (response.ok) {
                    isNotified = true;
                    try { localStorage.setItem(notifyKey, token); } catch {}
                }
            } catch {
            } finally {
                subscribing = false;
                render();
            }
        };

        const renderNotify = () => {
            const area = card.querySelector('[data-notify-area]');
            const button = area.querySelector('[data-notify]');
            const hint = area.querySelector('[data-notify-hint]');
            const isBlocked = pushSupported && Notification.permission === 'denied';
            const needsHomeScreen = ! pushSupported && isIos && ! isStandalone;

            area.hidden = status !== 'preparing' || (! pushSupported && ! needsHomeScreen);
            button.hidden = ! pushSupported || isBlocked;
            button.disabled = isNotified || subscribing;
            button.textContent = isNotified ? '🔔 Pranešimas įjungtas' : (subscribing ? 'Įjungiama…' : '🔔 Pranešti man, kai bus paruoštas');
            hint.hidden = ! needsHomeScreen && ! isBlocked;
            hint.textContent = isBlocked
                ? 'Pranešimai šioje naršyklėje užblokuoti. Juos galite įjungti naršyklės nustatymuose.'
                : 'Norėdami gauti pranešimą iPhone: spauskite „Bendrinti“ ⎙ → „Į pradžios ekraną“, tada atidarykite puslapį iš ten.';
        };

        const render = () => {
            card.dataset.status = status;
            card.querySelector('[data-order-number]').textContent = number ?? '';
            card.querySelector('[data-state]').textContent = states[status][0];
            card.querySelector('[data-text]').textContent = states[status][1];
            renderNotify();
            document.title = status === 'ready' ? `✅ Nr. ${number} paruoštas` : `Užsakymas Nr. ${number ?? ''} — Prusakovų užeiga`;
        };

        const isFinished = () => status === 'taken' || status === 'missing';

        const refresh = async () => {
            try {
                const response = await fetch(dataUrl, { cache: 'no-store', headers: { Accept: 'application/json' } });
                if (! response.ok && response.status !== 404) throw new Error(response.status);
                const next = await response.json();
                const becameReady = status !== 'ready' && next.status === 'ready';
                status = next.status;
                number = next.number ?? number;
                if (status !== 'preparing' && isNotified) {
                    isNotified = false;
                    try { localStorage.removeItem(notifyKey); } catch {}
                }
                render();
                if (becameReady && navigator.vibrate) navigator.vibrate([300, 150, 300]);
                failures = 0;
                footer.classList.remove('is-offline');
                main.classList.remove('is-unavailable');
            } catch {
                failures++;
                footer.classList.add('is-offline');
                main.classList.toggle('is-unavailable', failures >= failuresBeforeUnavailable);
            }
        };

        document.addEventListener('click', (e) => {
            if (e.target.closest('[data-notify]')) startNotifying();
        });

        document.addEventListener('visibilitychange', () => {
            if (document.visibilityState === 'visible' && ! isFinished()) refresh();
        });

        setInterval(() => {
            if (document.visibilityState === 'visible' && ! isFinished()) refresh();
        }, 5000);

        if (pushSupported) {
            navigator.serviceWorker.getRegistration('/').then((registration) => registration?.update()).catch(() => {});
        }

        render();
    })();
</script>
</body>
</html>
