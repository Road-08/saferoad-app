<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SafeRoad · Akun</title>
    <style>
        :root{--ink:#172b27;--muted:#73817a;--paper:#f5f7f2;--green:#14745f;--green-dark:#0c2823;--lime:#d9f27a;--line:#e1e9e1}
        *{box-sizing:border-box}body{margin:0;min-height:100vh;background:var(--paper);color:var(--ink);font-family:Inter,ui-sans-serif,system-ui,-apple-system,"Segoe UI",sans-serif;-webkit-font-smoothing:antialiased}
        .topbar{height:76px;padding:0 clamp(22px,6vw,88px);display:flex;align-items:center;justify-content:space-between;background:#fff;border-bottom:1px solid var(--line)}
        .brand{display:flex;align-items:center;gap:11px;color:var(--ink);text-decoration:none;font-size:19px;font-weight:760;letter-spacing:-.045em}.mark{width:38px;height:38px;display:grid;place-items:center;border-radius:13px;background:var(--green);color:var(--lime)}.mark svg{width:23px;height:23px}
        .top-actions{display:flex;align-items:center;gap:18px}.user-email{color:var(--muted);font-size:12px}.logout{padding:10px 15px;border:1px solid #dce5dd;border-radius:10px;background:#fff;color:var(--ink);font-size:12px;font-weight:650;cursor:pointer}.logout:hover{border-color:#8fb8a3;background:#f8fbf7}
        main{width:min(100% - 40px,1050px);margin:clamp(40px,8vh,86px) auto}.eyebrow{display:flex;align-items:center;gap:9px;color:var(--green);font-size:11px;font-weight:750;letter-spacing:.15em;text-transform:uppercase}.eyebrow:before{content:"";width:20px;height:1px;background:var(--green)}
        h1{max-width:720px;margin:18px 0 12px;font-size:clamp(38px,6vw,64px);line-height:1.02;letter-spacing:-.065em;font-weight:620}main>p{margin:0;color:var(--muted);font-size:15px;line-height:1.7}
        .account-card{margin-top:42px;padding:26px 28px;display:flex;align-items:center;justify-content:space-between;gap:24px;border:1px solid var(--line);border-radius:18px;background:#fff;box-shadow:0 16px 45px rgba(26,48,39,.06)}.account-card h2{margin:0 0 7px;font-size:15px}.account-card p{margin:0;color:var(--muted);font-size:13px}.status{display:inline-flex;align-items:center;gap:9px;white-space:nowrap;color:#355d49;font-size:12px;font-weight:650}.status:before{content:"";width:9px;height:9px;border-radius:50%;background:#35a474;box-shadow:0 0 0 4px #e6f5eb}
        @media(max-width:600px){.topbar{height:68px;padding-inline:18px}.user-email{display:none}.top-actions{gap:8px}main{width:calc(100% - 36px);margin:48px auto}.account-card{align-items:flex-start;flex-direction:column;padding:22px}}
    </style>
</head>
<body>
    <header class="topbar">
        <a class="brand" href="{{ route('dashboard') }}" aria-label="SafeRoad">
            <span class="mark" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><path d="M12 2.8 19 5.5v5.8c0 4.5-2.9 8.3-7 9.9-4.1-1.6-7-5.4-7-9.9V5.5l7-2.7Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="M12 7v3.2m0 2.2v4.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg></span>
            <span>SafeRoad</span>
        </a>
        <div class="top-actions">
            <span class="user-email">{{ auth()->user()->email }}</span>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="logout" type="submit">Keluar</button>
            </form>
        </div>
    </header>
    <main>
        <span class="eyebrow">Akun SafeRoad</span>
        <h1>Selamat datang, {{ auth()->user()->name }}.</h1>
        <p>Kamu sudah masuk. Sesi akun SafeRoad ini aktif.</p>
        <section class="account-card" aria-label="Status akun">
            <div>
                <h2>Autentikasi berhasil</h2>
                <p>{{ auth()->user()->email }}</p>
            </div>
            <span class="status">Sesi aktif</span>
        </section>
    </main>
</body>
</html>
