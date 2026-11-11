<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <title>{{ $mode === 'register' ? 'Daftar' : 'Masuk' }} · SafeRoad</title>
    <style>
        :root{--ink:#172b27;--muted:#73817a;--paper:#f7f8f3;--line:#e5eae4;--green:#14745f;--green-dark:#0c2823;--lime:#d9f27a;--coral:#f28a6b;--danger:#a83f34}
        *{box-sizing:border-box}body{margin:0;min-width:320px;min-height:100vh;background:var(--paper);color:var(--ink);font-family:Inter,ui-sans-serif,system-ui,-apple-system,"Segoe UI",sans-serif;-webkit-font-smoothing:antialiased}button,input{font:inherit}a{color:inherit}
        .shell{min-height:100vh;max-width:1600px;margin:auto;display:grid;grid-template-columns:minmax(340px,.92fr) minmax(450px,1.08fr);background:#fff;box-shadow:0 24px 80px rgba(26,48,39,.12)}
        .story{position:relative;isolation:isolate;overflow:hidden;min-height:760px;padding:40px clamp(30px,5vw,76px) 28px;display:flex;flex-direction:column;justify-content:space-between;color:#f4f6ec;background:radial-gradient(ellipse at 78% 23%,rgba(57,120,91,.34),transparent 37%),linear-gradient(150deg,#10372e 0%,var(--green-dark) 58%,#0a211d 100%)}
        .story:before,.story:after{content:"";position:absolute;z-index:-1;border:1px solid rgba(224,242,194,.09);border-radius:50%;pointer-events:none}.story:before{width:650px;height:650px;top:20%;left:18%}.story:after{width:470px;height:470px;top:30%;left:34%}
        .brand{display:inline-flex;align-items:center;gap:11px;width:fit-content;color:#fff;text-decoration:none;font-size:19px;font-weight:760;letter-spacing:-.045em}.brand-mark{width:38px;height:38px;display:grid;place-items:center;border-radius:13px;background:var(--lime);color:var(--green-dark)}.brand-mark svg{width:23px;height:23px}
        .story-copy{position:relative;width:min(100%,560px);margin:auto 0}.eyebrow{display:inline-flex;align-items:center;gap:9px;color:#c7d8c9;font-size:11px;font-weight:750;letter-spacing:.16em;text-transform:uppercase}.eyebrow:before{content:"";width:20px;height:1px;background:var(--lime)}
        .story h1{max-width:510px;margin:20px 0 17px;font-size:clamp(42px,5.2vw,70px);line-height:.99;letter-spacing:-.065em;font-weight:590}.story h1 em{color:var(--lime);font-style:normal}.story-copy p{max-width:390px;margin:0;color:#c3d0c8;font-size:15px;line-height:1.7}
        .route{width:min(100%,440px);height:205px;margin:30px auto 0}.route svg{width:100%;height:100%}.route-dash{stroke-dasharray:8 11;animation:flow 24s linear infinite}@keyframes flow{to{stroke-dashoffset:-380}}
        .story-foot{display:flex;justify-content:space-between;gap:12px;color:#9cb4a7;font-size:11px}.story-foot span:first-child:before{content:"";display:inline-block;width:7px;height:7px;margin-right:8px;border-radius:50%;background:var(--lime);box-shadow:0 0 0 4px rgba(217,242,122,.12)}
        .auth-side{min-height:760px;padding:32px clamp(28px,5vw,78px) 24px;display:flex;flex-direction:column;justify-content:space-between}.top-note{min-height:28px;display:flex;justify-content:flex-end;gap:4px;color:#7d8982;font-size:12px}.top-note a,.text-link{color:var(--green);font-weight:650;text-decoration:none}.top-note a:hover,.text-link:hover{text-decoration:underline;text-underline-offset:3px}
        .form-wrap{width:min(100%,420px);margin:auto;padding:24px 0}.mobile-brand{display:none}.kicker{margin:0 0 10px;color:var(--green);font-size:11px;font-weight:760;letter-spacing:.14em;text-transform:uppercase}.form-heading h2{margin:0;color:var(--ink);font-size:clamp(30px,3vw,38px);line-height:1.12;letter-spacing:-.052em;font-weight:690}.form-heading>p:last-child{margin:10px 0 26px;color:var(--muted);font-size:14px;line-height:1.55}
        .tabs{display:grid;grid-template-columns:1fr 1fr;gap:4px;padding:4px;margin-bottom:22px;border:1px solid #e9eee8;border-radius:14px;background:#f5f7f3}.tab{min-height:43px;padding:10px 12px;border:0;border-radius:10px;color:#73817a;text-align:center;text-decoration:none;font-size:13px;font-weight:650}.tab[aria-current="page"]{background:#fff;color:var(--ink);box-shadow:0 2px 8px rgba(25,50,40,.08),0 0 0 1px rgba(34,62,47,.04)}
        .form{display:grid;gap:15px}.field{display:grid;gap:7px}.field label{color:#253832;font-size:12px;font-weight:650}.input-wrap{position:relative}.field input[type="text"],.field input[type="email"],.field input[type="password"]{width:100%;height:49px;padding:0 14px;border:1px solid #dce5dd;border-radius:11px;outline:0;background:#fff;color:var(--ink);font-size:14px;transition:border-color .18s,box-shadow .18s}.field input::placeholder{color:#a3aea7}.field input:focus{border-color:#4c9b7f;box-shadow:0 0 0 3px rgba(20,116,95,.11)}.field input[aria-invalid="true"]{border-color:#d27564}.input-wrap input{padding-right:50px!important}
        .show-password{position:absolute;top:50%;right:8px;width:34px;height:34px;display:grid;place-items:center;transform:translateY(-50%);border:0;border-radius:8px;background:transparent;color:#78877f;cursor:pointer}.show-password:hover{background:#f1f5f1;color:var(--green)}.show-password svg{width:18px;height:18px}.field-error{margin:0;color:var(--danger);font-size:11px;line-height:1.4}.hint{margin:0;color:#87948c;font-size:11px}
        .row-between{display:flex;align-items:center;justify-content:space-between;gap:12px}.checkline{display:inline-flex;align-items:flex-start;gap:9px;color:#66756d;font-size:12px;line-height:1.5;cursor:pointer}.checkline input{width:15px;height:15px;flex:0 0 15px;margin:1px 0 0;accent-color:var(--green)}
        .alert{margin:0 0 15px;padding:11px 13px;border:1px solid #f0d4ce;border-radius:10px;background:#fff7f5;color:var(--danger);font-size:12px;line-height:1.5}.submit{display:flex;align-items:center;justify-content:center;gap:10px;width:100%;min-height:51px;margin-top:4px;border:0;border-radius:12px;background:var(--green);color:#fff;font-size:14px;font-weight:700;cursor:pointer;box-shadow:0 5px 12px rgba(20,116,95,.17);transition:background .18s,transform .18s}.submit:hover{background:#0e624f;transform:translateY(-1px)}.submit svg{width:17px;height:17px}
        .fine-print{margin:16px 0 0;color:#8a968f;font-size:10px;line-height:1.55;text-align:center}.auth-foot{display:flex;justify-content:space-between;gap:14px;color:#97a19b;font-size:10px}.auth-foot a{color:#718078;text-decoration:none}.auth-foot a:hover{text-decoration:underline}
        @media(max-width:900px){.shell{display:block;min-height:100vh}.story{display:none}.auth-side{min-height:100vh;padding:18px 24px 18px}.top-note{min-height:28px}.form-wrap{max-width:440px;padding:22px 0 34px}.mobile-brand{display:inline-flex;margin-bottom:28px;color:var(--ink)}.mobile-brand .brand-mark{background:var(--green);color:var(--lime)}.auth-foot{padding-top:4px}}
        @media(max-width:420px){.auth-side{padding-inline:18px}.form-wrap{padding-top:15px}.mobile-brand{margin-bottom:24px}}
        @media(prefers-reduced-motion:reduce){*,*:before,*:after{animation-duration:.01ms!important;animation-iteration-count:1!important;transition-duration:.01ms!important}}
    </style>
</head>
<body>
@php($isRegister = $mode === 'register')
<main class="shell">
    <aside class="story" aria-label="Tentang SafeRoad">
        <a class="brand" href="{{ route('home') }}" aria-label="SafeRoad, beranda">
            <span class="brand-mark" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><path d="M12 2.8 19 5.5v5.8c0 4.5-2.9 8.3-7 9.9-4.1-1.6-7-5.4-7-9.9V5.5l7-2.7Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="M12 7v3.2m0 2.2v4.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg></span>
            <span>SafeRoad</span>
        </a>
        <div class="story-copy">
            <span class="eyebrow">Akun SafeRoad</span>
            <h1>Jalan lebih aman, <em>mulai dari sini.</em></h1>
            <p>Buat akun untuk mengakses fitur SafeRoad dan ikut menjaga perjalanan tetap aman.</p>
            <div class="route" aria-hidden="true"><svg viewBox="0 0 440 205" fill="none"><path d="M28 170C70 157 83 139 101 115c18-24 24-50 52-53 28-3 36 33 64 32 34-1 41-54 69-61 28-7 47 31 73 19 24-11 32-41 59-48" stroke="#78968A" stroke-width="28" stroke-linecap="round" opacity=".25"/><path d="M28 170C70 157 83 139 101 115c18-24 24-50 52-53 28-3 36 33 64 32 34-1 41-54 69-61 28-7 47 31 73 19 24-11 32-41 59-48" stroke="#DCE6D6" stroke-width="2" stroke-linecap="round" stroke-dasharray="3 10" opacity=".65"/><path class="route-dash" d="M28 170C70 157 83 139 101 115c18-24 24-50 52-53 28-3 36 33 64 32 34-1 41-54 69-61 28-7 47 31 73 19 24-11 32-41 59-48" stroke="#D9F27A" stroke-width="3" stroke-linecap="round"/><circle cx="28" cy="170" r="10" fill="#F28A6B"/><circle cx="28" cy="170" r="4" fill="#FFF4E9"/><circle cx="346" cy="90" r="10" fill="#D9F27A"/><circle cx="346" cy="90" r="4" fill="#143B30"/></svg></div>
        </div>
        <div class="story-foot"><span>Teman perjalananmu</span><span>SafeRoad · Indonesia</span></div>
    </aside>

    <section class="auth-side" aria-label="Formulir akun SafeRoad">
        <div class="top-note">
            @if ($isRegister)
                <span>Sudah punya akun?</span><a href="{{ route('login') }}">Masuk</a>
            @else
                <span>Belum punya akun?</span><a href="{{ route('register') }}">Daftar</a>
            @endif
        </div>
        <div class="form-wrap">
            <a class="brand mobile-brand" href="{{ route('home') }}" aria-label="SafeRoad, beranda">
                <span class="brand-mark" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><path d="M12 2.8 19 5.5v5.8c0 4.5-2.9 8.3-7 9.9-4.1-1.6-7-5.4-7-9.9V5.5l7-2.7Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="M12 7v3.2m0 2.2v4.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg></span>
                <span>SafeRoad</span>
            </a>
            <header class="form-heading">
                <p class="kicker">Akses akunmu</p>
                <h2>{{ $isRegister ? 'Mulai perjalananmu' : 'Selamat datang kembali' }}</h2>
                <p>{{ $isRegister ? 'Buat akun untuk mengakses semua fitur SafeRoad.' : 'Masuk untuk melanjutkan perjalanan dengan SafeRoad.' }}</p>
            </header>

            <nav class="tabs" aria-label="Pilih akses akun">
                <a class="tab" href="{{ route('login') }}" aria-current="{{ $isRegister ? 'false' : 'page' }}">Masuk</a>
                <a class="tab" href="{{ route('register') }}" aria-current="{{ $isRegister ? 'page' : 'false' }}">Daftar</a>
            </nav>

            @if ($errors->any())
                <div class="alert" role="alert" aria-live="polite">{{ $errors->first() }}</div>
            @endif

            <form class="form" method="POST" action="{{ $isRegister ? route('register.store') : route('login.store') }}">
                @csrf
                @if ($isRegister)
                    <div class="field">
                        <label for="name">Nama lengkap</label>
                        <input id="name" name="name" type="text" value="{{ old('name') }}" autocomplete="name" placeholder="Nama lengkap" maxlength="255" required @error('name') aria-invalid="true" aria-describedby="name-error" @enderror>
                        @error('name')<p class="field-error" id="name-error">{{ $message }}</p>@enderror
                    </div>
                @endif
                <div class="field">
                    <label for="email">Alamat email</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" placeholder="nama@email.com" maxlength="255" required @error('email') aria-invalid="true" aria-describedby="email-error" @enderror>
                    @error('email')<p class="field-error" id="email-error">{{ $message }}</p>@enderror
                </div>
                <div class="field">
                    <label for="password">Kata sandi</label>
                    <div class="input-wrap">
                        <input id="password" name="password" type="password" autocomplete="{{ $isRegister ? 'new-password' : 'current-password' }}" placeholder="{{ $isRegister ? 'Minimal 8 karakter' : 'Masukkan kata sandi' }}" {{ $isRegister ? 'minlength=8' : '' }} required @error('password') aria-invalid="true" aria-describedby="password-error" @enderror>
                        <button class="show-password" type="button" aria-label="Tampilkan kata sandi" aria-pressed="false" data-toggle="password"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M2.5 12s3.4-6 9.5-6 9.5 6 9.5 6-3.4 6-9.5 6-9.5-6-9.5-6Z" stroke="currentColor" stroke-width="1.6"/><circle cx="12" cy="12" r="2.6" stroke="currentColor" stroke-width="1.6"/></svg></button>
                    </div>
                    @if ($isRegister)<p class="hint">Gunakan minimal 8 karakter.</p>@endif
                    @error('password')<p class="field-error" id="password-error">{{ $message }}</p>@enderror
                </div>
                @if ($isRegister)
                    <div class="field">
                        <label for="password_confirmation">Ulangi kata sandi</label>
                        <div class="input-wrap">
                            <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" placeholder="Masukkan sekali lagi" minlength="8" required>
                            <button class="show-password" type="button" aria-label="Tampilkan kata sandi" aria-pressed="false" data-toggle="password_confirmation"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M2.5 12s3.4-6 9.5-6 9.5 6 9.5 6-3.4 6-9.5 6-9.5-6-9.5-6Z" stroke="currentColor" stroke-width="1.6"/><circle cx="12" cy="12" r="2.6" stroke="currentColor" stroke-width="1.6"/></svg></button>
                        </div>
                    </div>
                @else
                    <div class="row-between">
                        <label class="checkline"><input type="checkbox" name="remember" value="1" {{ old('remember') ? 'checked' : '' }}><span>Ingat saya</span></label>
                    </div>
                @endif
                <button class="submit" type="submit">
                    <span>{{ $isRegister ? 'Buat akun SafeRoad' : 'Masuk ke SafeRoad' }}</span>
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 12h13m-5-5 5 5-5 5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
            </form>
            <p class="fine-print">Gunakan akun SafeRoad untuk mengakses fitur yang memerlukan autentikasi.</p>
        </div>
        <footer class="auth-foot"><span>© {{ date('Y') }} SafeRoad</span><span>Area akun</span></footer>
    </section>
</main>
<script>
    document.querySelectorAll('[data-toggle]').forEach((button) => {
        button.addEventListener('click', () => {
            const input = document.getElementById(button.dataset.toggle);
            const show = input.type === 'password';
            input.type = show ? 'text' : 'password';
            button.setAttribute('aria-pressed', String(show));
            button.setAttribute('aria-label', show ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi');
        });
    });
</script>
</body>
</html>
