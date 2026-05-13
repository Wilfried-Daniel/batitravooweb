<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name', 'BATITRAVOO'))</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --navy: #001f3f;
            --navy-soft: #0a2540;
            --orange: #F19B34;
            --orange-brush: rgba(241, 155, 52, 0.35);
            --nav-bg: #e8eaed;
            --text-muted: #4b5563;
            --white: #ffffff;
            --section-muted-bg: #f8f9fa;
            --navy-text: #002147;
            --shadow-sm: 0 2px 12px rgba(0, 33, 71, 0.06);
            --shadow-md: 0 8px 32px rgba(0, 33, 71, 0.09);
            --shadow-lg: 0 20px 50px rgba(0, 33, 71, 0.11);
            --shadow-hover: 0 14px 40px rgba(0, 33, 71, 0.14);
            --ease-out-expo: cubic-bezier(0.22, 1, 0.36, 1);
            --dur: 0.45s;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', system-ui, sans-serif;
            color: var(--navy);
            background: radial-gradient(1200px 380px at 50% 0%, rgba(241, 155, 52, 0.14), transparent 60%),
                        linear-gradient(180deg, #ffffff 0%, #fbfcfe 50%, #ffffff 100%);
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
        }

        a { color: inherit; text-decoration: none; }

        .section-reveal { opacity: 1; transform: none; }

        .site-header {
            position: sticky;
            top: 0;
            z-index: 100;
            background: rgba(232, 234, 237, 0.88);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            padding: 0.875rem 1.5rem;
            border-bottom: 1px solid rgba(0, 33, 71, 0.06);
            box-shadow: 0 4px 24px rgba(0, 33, 71, 0.04);
        }

        .nav-inner {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }

        .brand { display: flex; align-items: center; flex-shrink: 0; }
        .brand img { height: 64px; width: auto; object-fit: contain; }

        .nav-links {
            display: none;
            align-items: center;
            gap: 1.25rem;
            list-style: none;
            font-size: 0.9375rem;
            font-weight: 600;
            color: var(--text-muted);
        }

        @media (min-width: 900px) { .nav-links { display: flex; } }

        .nav-links a { position: relative; padding: 0.25rem 0; transition: color 0.3s ease; }
        .nav-links a::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 100%;
            height: 2px;
            background: var(--orange);
            transform: scaleX(0);
            transform-origin: right;
            transition: transform 0.35s var(--ease-out-expo);
        }
        .nav-links a:hover { color: var(--navy); }
        .nav-links a:hover::after { transform: scaleX(1); transform-origin: left; }

        .btn-nav {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.55rem 1.35rem;
            font-size: 0.9375rem;
            font-weight: 700;
            border-radius: 9999px;
            background: var(--navy);
            color: var(--white);
            border: 2px solid var(--navy);
            white-space: nowrap;
            box-shadow: 0 4px 14px rgba(0, 33, 71, 0.2);
            transition: transform 0.3s var(--ease-out-expo), box-shadow 0.3s ease;
        }
        .btn-nav:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(0, 33, 71, 0.28); }

        .page {
            max-width: 1100px;
            margin: 0 auto;
            padding: 2.25rem 1.5rem 4.5rem;
        }

        .page-card {
            background: #ffffff;
            border: 1px solid rgba(0, 33, 71, 0.08);
            border-radius: 18px;
            box-shadow: var(--shadow-sm);
            padding: 2rem;
            overflow: hidden;
            position: relative;
        }

        .page-card::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(850px 280px at 12% -10%, rgba(241, 155, 52, 0.16), transparent 60%),
                        radial-gradient(850px 280px at 85% 0%, rgba(0, 33, 71, 0.08), transparent 60%);
            pointer-events: none;
        }

        .page-card > * { position: relative; }

        .breadcrumb {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: rgba(0, 33, 71, 0.62);
            font-weight: 600;
            font-size: 0.9rem;
            margin-bottom: 1.25rem;
        }
        .breadcrumb a { text-decoration: none; color: rgba(0, 33, 71, 0.72); }
        .breadcrumb a:hover { color: var(--navy-text); }
        .crumb-dot { width: 6px; height: 6px; border-radius: 50%; background: rgba(241, 155, 52, 0.85); display: inline-block; }

        .page-hero {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.25rem;
            padding: 0.5rem 0 1.25rem;
            border-bottom: 1px solid rgba(0, 33, 71, 0.08);
            margin-bottom: 1.75rem;
        }
        @media (min-width: 900px) { .page-hero { grid-template-columns: 1.2fr 0.8fr; align-items: start; } }

        .page-title {
            font-size: clamp(1.45rem, 3vw, 2rem);
            font-weight: 800;
            letter-spacing: -0.02em;
            color: var(--navy-soft);
            margin-bottom: 0.5rem;
        }

        .page-subtitle {
            font-size: 1rem;
            color: var(--text-muted);
            line-height: 1.7;
            margin-bottom: 1.25rem;
            max-width: 70ch;
        }

        .hero-aside {
            display: grid;
            gap: 0.75rem;
            align-content: start;
        }

        .pill {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.45rem 0.85rem;
            border-radius: 9999px;
            border: 1px solid rgba(0, 33, 71, 0.12);
            background: rgba(255, 255, 255, 0.85);
            box-shadow: 0 1px 0 rgba(0, 33, 71, 0.02);
            font-weight: 700;
            color: rgba(0, 33, 71, 0.82);
            font-size: 0.9rem;
            width: fit-content;
        }
        .pill .dot { width: 10px; height: 10px; border-radius: 50%; background: var(--orange); box-shadow: 0 6px 18px rgba(241, 155, 52, 0.35); }

        .grid-2 {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.25rem;
        }
        @media (min-width: 900px) { .grid-2 { grid-template-columns: 1fr 1fr; } }

        .card {
            background: rgba(255, 255, 255, 0.92);
            border: 1px solid rgba(0, 33, 71, 0.1);
            border-radius: 16px;
            box-shadow: var(--shadow-sm);
            padding: 1.25rem;
        }
        .card-title {
            display: flex;
            align-items: center;
            gap: 0.7rem;
            margin-bottom: 0.75rem;
            font-weight: 900;
            color: var(--navy-text);
            letter-spacing: -0.01em;
        }
        .icon-badge {
            width: 38px;
            height: 38px;
            border-radius: 12px;
            background: rgba(241, 155, 52, 0.14);
            border: 1px solid rgba(241, 155, 52, 0.28);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .icon-badge svg { width: 20px; height: 20px; color: var(--navy-text); }

        .content h2 {
            font-size: 1.1rem;
            font-weight: 800;
            margin: 1.35rem 0 0.65rem;
            color: var(--navy-text);
        }

        .content p, .content li {
            color: rgba(0, 33, 71, 0.88);
            line-height: 1.75;
            font-size: 0.98rem;
        }

        .content ul {
            padding-left: 1.15rem;
            margin: 0.75rem 0;
            display: grid;
            gap: 0.4rem;
        }

        .content a {
            color: var(--navy-text);
            font-weight: 700;
            text-decoration: underline;
            text-decoration-thickness: 2px;
            text-underline-offset: 3px;
        }

        .muted {
            color: rgba(0, 33, 71, 0.68);
        }

        .form-row {
            display: grid;
            gap: 0.8rem;
            margin-top: 0.5rem;
        }
        .field label {
            display: block;
            font-weight: 800;
            color: rgba(0, 33, 71, 0.78);
            font-size: 0.9rem;
            margin-bottom: 0.35rem;
        }
        .field input, .field textarea {
            width: 100%;
            border: 1px solid rgba(0, 33, 71, 0.14);
            border-radius: 12px;
            padding: 0.8rem 0.9rem;
            font: inherit;
            background: rgba(255,255,255,0.9);
            outline: none;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }
        .field textarea { min-height: 120px; resize: vertical; }
        .field input:focus, .field textarea:focus {
            border-color: rgba(241, 155, 52, 0.75);
            box-shadow: 0 0 0 4px rgba(241, 155, 52, 0.18);
        }
        .btn-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.75rem 1.15rem;
            font-size: 0.95rem;
            font-weight: 900;
            border-radius: 12px;
            background: var(--navy);
            color: var(--white);
            border: 2px solid var(--navy);
            box-shadow: 0 10px 24px rgba(0, 33, 71, 0.18);
            transition: transform 0.25s var(--ease-out-expo), box-shadow 0.25s ease;
            cursor: pointer;
        }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 14px 34px rgba(0, 33, 71, 0.24); }

        .accordion {
            display: grid;
            gap: 0.75rem;
            margin-top: 0.25rem;
        }
        details.acc {
            background: rgba(255,255,255,0.9);
            border: 1px solid rgba(0, 33, 71, 0.12);
            border-radius: 14px;
            padding: 0.9rem 1rem;
            box-shadow: var(--shadow-sm);
        }
        details.acc summary {
            list-style: none;
            cursor: pointer;
            font-weight: 900;
            color: var(--navy-text);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }
        details.acc summary::-webkit-details-marker { display: none; }
        .chev { width: 18px; height: 18px; transition: transform 0.2s ease; flex-shrink: 0; opacity: 0.8; }
        details.acc[open] .chev { transform: rotate(180deg); }
        details.acc .acc-body { padding-top: 0.75rem; }

        .site-footer {
            background: #002147;
            color: rgba(255, 255, 255, 0.92);
            margin-top: 0;
        }
        .site-footer__main {
            max-width: 1200px;
            margin: 0 auto;
            padding: 3.5rem 1.5rem 2.5rem;
            display: grid;
            grid-template-columns: 1fr;
            gap: 2.25rem;
        }
        @media (min-width: 640px) { .site-footer__main { grid-template-columns: repeat(2, 1fr); } }
        @media (min-width: 1024px) { .site-footer__main { grid-template-columns: 1.1fr 1fr 1fr 1fr; gap: 2rem; } }
        .site-footer__brand { font-size: 1.5rem; font-weight: 800; color: var(--white); margin-bottom: 1rem; }
        .site-footer__col-title { font-size: 1rem; font-weight: 800; color: var(--white); margin-bottom: 1rem; }
        .site-footer__links, .site-footer__social { list-style: none; display: flex; flex-direction: column; gap: 0.5rem; }
        .site-footer__links a, .site-footer__social a {
            color: rgba(255, 255, 255, 0.88);
            font-size: 0.9375rem;
            transition: color 0.25s ease, transform 0.3s var(--ease-out-expo);
            display: inline-block;
        }
        .site-footer__links a:hover, .site-footer__social a:hover { color: var(--orange); transform: translateX(4px); }
        .site-footer__bottom { background: #00152e; padding: 1rem 1.5rem; text-align: center; }
        .site-footer__copyright { font-size: 0.8125rem; color: rgba(255, 255, 255, 0.75); }
    </style>
</head>
<body>
    <header class="site-header">
        <nav class="nav-inner" aria-label="Principale">
            <a href="{{ url('/') }}" class="brand">
                <img src="{{ asset('images/logo.png') }}" alt="BATITRAVOO" width="180" height="64">
            </a>
            <ul class="nav-links">
                <li><a href="{{ url('/') }}">Accueil</a></li>
                <li><a href="{{ route('vitrine.contact') }}">Contact</a></li>
                <li><a href="{{ route('vitrine.help_center') }}">Centre d’aide</a></li>
                <li><a href="{{ route('vitrine.faq') }}">FAQ</a></li>
            </ul>
            @if (Route::has('register'))
                <a href="{{ route('register') }}" class="btn-nav">S'inscrire</a>
            @else
                <a href="{{ url('/') }}#inscription" class="btn-nav">S'inscrire</a>
            @endif
        </nav>
    </header>

    <main class="page" role="main">
        <div class="page-card">
            <h1 class="page-title">@yield('h1')</h1>
            @hasSection('subtitle')
                <p class="page-subtitle">@yield('subtitle')</p>
            @endif
            <div class="content">
                @yield('content')
            </div>
        </div>
    </main>

    @include('vitrine.partials.footer')
</body>
</html>
