<!DOCTYPE html>
<html lang="lt">
<head>
    @include('partials.online-head', ['title' => 'Užsakymai — Prusakovų užeiga', 'manifest' => '/online.webmanifest'])
    <style>
        .followed {
            display: none;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 16px;
            padding: 14px 16px;
            border-radius: 16px;
            background: var(--sand);
            border: 1px solid var(--line);
        }
        .followed.is-visible { display: flex; }
        .followed.is-ready { background: var(--green); border-color: var(--green); color: #fff; }
        .followed strong { display: block; font-size: 17px; }
        .followed span { font-size: 14px; opacity: .8; }
        .followed button {
            border: 0;
            background: transparent;
            color: inherit;
            font: inherit;
            font-size: 14px;
            text-decoration: underline;
            cursor: pointer;
        }

        .notify { flex-basis: 100%; }
        .notify[hidden] { display: none; }
        .notify button[data-notify] {
            width: 100%;
            padding: 12px;
            border: 0;
            border-radius: 12px;
            background: var(--green);
            color: #fff;
            font-size: 16px;
            font-weight: 700;
            text-decoration: none;
        }
        .notify button[data-notify]:disabled { background: var(--green-soft); color: var(--green); }
        .notify p { margin: 0; font-size: 14px; line-height: 1.45; }

        section {
            margin-bottom: 16px;
            padding: 16px;
            border-radius: 20px;
            background: #fff;
            border: 1px solid var(--line);
        }
        section h2 {
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 0 0 12px;
            font-size: 15px;
            font-weight: 800;
            letter-spacing: .06em;
            text-transform: uppercase;
        }
        section h2::before {
            content: '';
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: currentColor;
        }
        [data-online-ready] h2 { color: var(--green); }
        [data-online-preparing] h2 { color: var(--amber); }

        .numbers {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(76px, 1fr));
            gap: 10px;
        }
        .numbers button {
            padding: 14px 0;
            border: 2px solid transparent;
            border-radius: 14px;
            font: inherit;
            font-size: 28px;
            font-weight: 800;
            cursor: pointer;
        }
        [data-online-ready] .numbers button { background: var(--green); color: #fff; font-size: 34px; }
        [data-online-preparing] .numbers button { background: var(--sand); color: var(--ink); }
        .numbers button.is-followed { border-color: var(--amber); box-shadow: 0 0 0 3px rgba(181, 118, 27, .25); }
        .empty { margin: 4px 0; color: var(--muted); font-size: 15px; }

        footer { text-align: center; color: var(--muted); font-size: 13px; }
        footer .offline { display: none; color: #B3412F; font-weight: 700; }
        footer.is-offline .offline { display: block; }

        main.is-unavailable .unavailable { display: block; }
        main.is-unavailable :is(.followed, section, footer) { display: none !important; }
    </style>
</head>
<body>
<main data-main>
    <header>
        <h1>Prusakovų užeiga</h1>
        <p>Užsakymų būsena realiu laiku</p>
    </header>

    <div class="followed" data-followed role="status" aria-live="polite">
        <div>
            <strong data-followed-title></strong>
            <span data-followed-text></span>
        </div>
        <button type="button" data-unfollow>Nesekti</button>
        <div class="notify" data-notify-area hidden>
            <button type="button" data-notify hidden></button>
            <p data-notify-hint hidden></p>
        </div>
    </div>

    <section data-online-ready>
        <h2>Paruošti — atsiimkite</h2>
        <div class="numbers" data-list>
            @foreach ($ready as $number)
                <button type="button" data-order-number="{{ $number }}">{{ $number }}</button>
            @endforeach
        </div>
        <p class="empty" data-empty @if ($ready !== []) hidden @endif>Paruoštų užsakymų dar nėra</p>
    </section>

    <section data-online-preparing>
        <h2>Gaminami</h2>
        <div class="numbers" data-list>
            @foreach ($preparing as $number)
                <button type="button" data-order-number="{{ $number }}">{{ $number }}</button>
            @endforeach
        </div>
        <p class="empty" data-empty @if ($preparing !== []) hidden @endif>Šiuo metu nieko negaminame</p>
    </section>

    <div class="unavailable" data-unavailable role="status" aria-live="polite">
        <span class="icon" aria-hidden="true">
            <svg width="30" height="30" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M17 21a1 1 0 0 0 1-1v-5.35c0-.457.316-.844.727-1.041a4 4 0 0 0-2.134-7.589 5 5 0 0 0-9.186 0 4 4 0 0 0-2.134 7.588c.411.198.727.585.727 1.041V20a1 1 0 0 0 1 1Z"/><path d="M6 17h12"/></svg>
        </span>
        <h2>Užsakymų būsena laikinai nepasiekiama</h2>
        <p>Jūsų užsakymas gaminamas kaip įprasta. Paruoštų užsakymų numerius matysite ekrane prie prekystalio.</p>
        <p class="retry">Puslapis atsinaujins automatiškai</p>
    </div>

    <footer data-footer>
        <p>Paspauskite savo numerį — parodysime, kai bus paruoštas.</p>
        <p class="offline">Nepavyksta atnaujinti, bandome dar kartą…</p>
    </footer>
</main>

<script>
    (() => {
        const dataUrl = @js(route('online-orders.data', absolute: false));
        const subscribeUrl = @js(route('online-orders.subscribe', absolute: false));
        const unsubscribeUrl = @js(route('online-orders.unsubscribe', absolute: false));
        const vapidKey = @js(config('online-orders.vapid.public_key'));
        const storageKey = 'followed-order';
        const notifyKey = 'notified-order';
        const pushSupported = Boolean(vapidKey) && 'serviceWorker' in navigator && 'PushManager' in window && 'Notification' in window;
        const isIos = /iPhone|iPad|iPod/.test(navigator.userAgent) || (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1);
        const isStandalone = window.matchMedia('(display-mode: standalone)').matches || navigator.standalone === true;
        const sections = {
            ready: document.querySelector('[data-online-ready]'),
            preparing: document.querySelector('[data-online-preparing]'),
        };
        const banner = document.querySelector('[data-followed]');
        const footer = document.querySelector('[data-footer]');
        const main = document.querySelector('[data-main]');
        const failuresBeforeUnavailable = 2;
        let failures = 0;
        let isClosed = false;
        const numbersIn = (section) => [...section.querySelectorAll('[data-order-number]')].map((el) => Number(el.dataset.orderNumber));

        let state = { ready: numbersIn(sections.ready), preparing: numbersIn(sections.preparing) };
        let followed = null;
        let notified = null;
        let subscribing = false;
        try {
            followed = Number(localStorage.getItem(storageKey)) || null;
            notified = Number(localStorage.getItem(notifyKey)) || null;
        } catch {}

        const store = (key, number) => {
            try { number ? localStorage.setItem(key, number) : localStorage.removeItem(key); } catch {}
        };

        const postJson = (url, body) => fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
            body: JSON.stringify(body),
        });

        const stopNotifying = async () => {
            if (notified === null) return;
            notified = null;
            store(notifyKey, null);
            try {
                const registration = await navigator.serviceWorker.getRegistration('/');
                const subscription = registration && await registration.pushManager.getSubscription();
                if (subscription) await postJson(unsubscribeUrl, { endpoint: subscription.endpoint });
            } catch {}
        };

        const remember = (number) => {
            if (number !== followed) stopNotifying();
            followed = number;
            store(storageKey, number);
        };

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
                const response = await postJson(subscribeUrl, { number: followed, ...subscription.toJSON() });
                if (response.ok) {
                    notified = followed;
                    store(notifyKey, followed);
                }
            } catch {
            } finally {
                subscribing = false;
                render();
            }
        };

        const renderList = (section, numbers) => {
            const list = section.querySelector('[data-list]');
            list.replaceChildren(...numbers.map((number) => {
                const button = document.createElement('button');
                button.type = 'button';
                button.dataset.orderNumber = number;
                button.textContent = number;
                button.classList.toggle('is-followed', number === followed);
                return button;
            }));
            section.querySelector('[data-empty]').hidden = numbers.length > 0;
        };

        const renderBanner = () => {
            const isReady = followed !== null && state.ready.includes(followed);
            const isPreparing = followed !== null && state.preparing.includes(followed);
            banner.classList.toggle('is-visible', isReady || isPreparing);
            banner.classList.toggle('is-ready', isReady);
            banner.querySelector('[data-followed-title]').textContent = isReady
                ? `Nr. ${followed} paruoštas!`
                : `Nr. ${followed} gaminamas`;
            banner.querySelector('[data-followed-text]').textContent = isReady
                ? 'Atsiimkite prie prekystalio.'
                : 'Pranešime, kai bus paruoštas.';
            renderNotify(isPreparing);
            document.title = isReady ? `✅ Nr. ${followed} paruoštas` : 'Užsakymai — Prusakovų užeiga';
        };

        const renderNotify = (isPreparing) => {
            const area = banner.querySelector('[data-notify-area]');
            const button = area.querySelector('[data-notify]');
            const hint = area.querySelector('[data-notify-hint]');
            const isOn = notified !== null && notified === followed;
            const isBlocked = pushSupported && Notification.permission === 'denied';
            const needsHomeScreen = ! pushSupported && isIos && ! isStandalone;

            area.hidden = ! isPreparing || (! pushSupported && ! needsHomeScreen);
            button.hidden = ! pushSupported || isBlocked;
            button.disabled = isOn || subscribing;
            button.textContent = isOn ? '🔔 Pranešimas įjungtas' : (subscribing ? 'Įjungiama…' : '🔔 Pranešti man, kai bus paruoštas');
            hint.hidden = ! needsHomeScreen && ! isBlocked;
            hint.textContent = isBlocked
                ? 'Pranešimai šioje naršyklėje užblokuoti. Juos galite įjungti naršyklės nustatymuose.'
                : 'Norėdami gauti pranešimą iPhone: spauskite „Bendrinti“ ⎙ → „Į pradžios ekraną“, tada atidarykite puslapį iš ten.';
        };

        const render = () => {
            renderList(sections.ready, state.ready);
            renderList(sections.preparing, state.preparing);
            renderBanner();
        };

        const refresh = async () => {
            if (isClosed) return;
            try {
                const response = await fetch(dataUrl, { cache: 'no-store', headers: { Accept: 'application/json' } });
                if (response.status === 503) {
                    isClosed = true;
                    main.classList.add('is-unavailable');
                    return;
                }
                if (! response.ok) throw new Error(response.status);
                const next = await response.json();
                const becameReady = followed !== null && ! state.ready.includes(followed) && next.ready.includes(followed);
                if (followed !== null && ! next.ready.includes(followed) && ! next.preparing.includes(followed)) {
                    remember(null);
                }
                if (notified !== null && next.ready.includes(notified)) {
                    notified = null;
                    store(notifyKey, null);
                }
                state = next;
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
            const button = e.target.closest('[data-order-number]');
            if (button) {
                const number = Number(button.dataset.orderNumber);
                remember(number === followed ? null : number);
                render();
            }
            if (e.target.closest('[data-notify]')) {
                startNotifying();
            }
            if (e.target.closest('[data-unfollow]')) {
                remember(null);
                render();
            }
        });

        document.addEventListener('visibilitychange', () => {
            if (document.visibilityState === 'visible') refresh();
        });

        setInterval(() => {
            if (document.visibilityState === 'visible') refresh();
        }, 15000);

        if (pushSupported) {
            navigator.serviceWorker.getRegistration('/').then((registration) => registration?.update()).catch(() => {});
        }

        render();
    })();
</script>
</body>
</html>
