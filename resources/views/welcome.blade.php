<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'BATITRAVOO') }}</title>
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

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(26px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes floatSoft {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        @keyframes sectionReveal {
            from {
                opacity: 0;
                transform: translateY(32px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes gradientFlow {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }

        @keyframes brushPulse {
            0%, 100% { filter: brightness(1); }
            50% { filter: brightness(1.06); }
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Inter', system-ui, sans-serif;
            color: var(--navy);
            background: var(--white);
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
        }

        a { color: inherit; text-decoration: none; }

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
            transition: box-shadow var(--dur) var(--ease-out-expo), background 0.3s ease;
        }

        .nav-inner {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }

        .brand {
            display: flex;
            align-items: center;
            flex-shrink: 0;
        }

        .brand img {
            height: 72px;
            width: auto;
            object-fit: contain;
            transition: transform 0.4s var(--ease-out-expo);
        }

        .brand:hover img {
            transform: scale(1.04);
        }

        .nav-links {
            display: none;
            align-items: center;
            gap: 1.75rem;
            list-style: none;
            font-size: 0.9375rem;
            font-weight: 500;
            color: var(--text-muted);
        }

        @media (min-width: 900px) {
            .nav-links { display: flex; }
        }

        .nav-links a {
            position: relative;
            padding: 0.25rem 0;
            transition: color 0.3s ease;
        }

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

        .nav-links a:hover {
            color: var(--navy);
        }

        .nav-links a:hover::after {
            transform: scaleX(1);
            transform-origin: left;
        }

        .btn-nav {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.55rem 1.35rem;
            font-size: 0.9375rem;
            font-weight: 600;
            border-radius: 9999px;
            background: var(--navy);
            color: var(--white);
            border: 2px solid var(--navy);
            transition: transform 0.3s var(--ease-out-expo), box-shadow 0.3s ease, background 0.3s ease;
            white-space: nowrap;
            box-shadow: 0 4px 14px rgba(0, 33, 71, 0.2);
        }

        .btn-nav:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(0, 33, 71, 0.28);
        }

        .btn-nav:active {
            transform: translateY(0);
        }

        .hero {
            padding: 4.5rem 1.5rem 5.5rem;
            max-width: 1280px;
            margin: 0 auto;
            overflow: hidden;
        }

        .hero-title {
            animation: fadeInUp 0.85s var(--ease-out-expo) 0.05s both;
        }

        .hero-sub {
            animation: fadeInUp 0.85s var(--ease-out-expo) 0.15s both;
        }

        .hero-actions {
            animation: fadeInUp 0.85s var(--ease-out-expo) 0.28s both;
        }

        .hero-visual--left img {
            animation: floatSoft 6s ease-in-out infinite;
            animation-delay: 0.5s;
        }

        .hero-visual--right img {
            animation: floatSoft 6.5s ease-in-out infinite;
            animation-delay: 1s;
        }

        .hero-grid {
            display: grid;
            grid-template-columns: 1fr;
            align-items: center;
            gap: 2.75rem;
        }

        @media (min-width: 1024px) {
            .hero-grid {
                grid-template-columns: minmax(200px, 1fr) minmax(280px, 520px) minmax(200px, 1fr);
                gap: 1.5rem 2.5rem;
            }
        }

        .hero-visual {
            display: flex;
            justify-content: center;
            max-width: 340px;
            margin: 0 auto;
        }

        @media (min-width: 1024px) {
            .hero-visual { max-width: none; margin: 0; }
            .hero-visual--left { justify-content: flex-end; }
            .hero-visual--right { justify-content: flex-start; }
        }

        .hero-visual img {
            width: 100%;
            max-width: 320px;
            height: auto;
            object-fit: contain;
        }

        .hero-center {
            text-align: center;
            order: -1;
        }

        @media (min-width: 1024px) {
            .hero-center { order: 0; }
        }

        .hero-title {
            font-size: clamp(1.65rem, 4vw, 2.35rem);
            font-weight: 700;
            line-height: 1.25;
            color: var(--navy-soft);
            max-width: 32ch;
            margin: 0 auto 1.25rem;
        }

        .hero-title .brush {
            position: relative;
            display: inline;
            padding: 0 0.15em;
            background: linear-gradient(180deg, transparent 12%, var(--orange-brush) 12%, var(--orange-brush) 88%, transparent 88%);
            border-radius: 2px;
            animation: brushPulse 4s ease-in-out infinite;
        }

        .hero-sub {
            font-size: clamp(0.9rem, 2vw, 1rem);
            font-weight: 500;
            color: var(--text-muted);
            line-height: 1.6;
            max-width: 52ch;
            margin: 0 auto 1.75rem;
        }

        .hero-actions {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 0.875rem;
        }

        .btn-outline {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.65rem 1.5rem;
            font-size: 0.9375rem;
            font-weight: 600;
            border-radius: 9999px;
            background: var(--white);
            color: var(--navy);
            border: 2px solid var(--navy);
            transition: transform 0.3s var(--ease-out-expo), background 0.3s ease, box-shadow 0.3s ease;
            box-shadow: var(--shadow-sm);
        }

        .btn-outline:hover {
            background: var(--nav-bg);
            transform: translateY(-3px);
            box-shadow: var(--shadow-md);
        }

        .btn-outline:active {
            transform: translateY(-1px);
        }

        .btn-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.65rem 1.5rem;
            font-size: 0.9375rem;
            font-weight: 600;
            border-radius: 9999px;
            background: var(--navy);
            color: var(--white);
            border: 2px solid var(--navy);
            transition: transform 0.3s var(--ease-out-expo), box-shadow 0.3s ease, background 0.3s ease;
            box-shadow: 0 6px 20px rgba(0, 33, 71, 0.22);
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 32px rgba(0, 33, 71, 0.3);
        }

        .btn-primary:active {
            transform: translateY(-1px);
        }

        .section-reveal {
            opacity: 0;
            animation: sectionReveal 0.95s var(--ease-out-expo) forwards;
            animation-delay: var(--reveal-delay, 0s);
        }

        .section-reveal--d1 { --reveal-delay: 0.06s; }
        .section-reveal--d2 { --reveal-delay: 0.12s; }
        .section-reveal--d3 { --reveal-delay: 0.18s; }
        .section-reveal--d4 { --reveal-delay: 0.24s; }
        .section-reveal--d5 { --reveal-delay: 0.3s; }
        .section-reveal--d6 { --reveal-delay: 0.36s; }

        .section-about {
            background: var(--section-muted-bg);
            padding: 5.5rem 1.5rem;
        }

        .section-about__inner {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr;
            gap: 2.75rem;
            align-items: center;
        }

        @media (min-width: 900px) {
            .section-about__inner {
                grid-template-columns: 1fr 1fr;
                gap: 3.5rem;
            }
        }

        .section-about__content {
            color: var(--navy-text);
        }

        .section-about__title {
            font-size: clamp(1.5rem, 3.2vw, 2rem);
            font-weight: 700;
            line-height: 1.25;
            margin-bottom: 1.25rem;
        }

        .section-about__text {
            font-size: clamp(0.95rem, 1.8vw, 1.0625rem);
            font-weight: 400;
            line-height: 1.65;
            max-width: 48ch;
        }

        .section-about__figure {
            margin: 0;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: var(--shadow-md);
            transition: transform 0.5s var(--ease-out-expo), box-shadow 0.5s ease;
        }

        .section-about__figure:hover {
            transform: translateY(-4px) scale(1.01);
            box-shadow: var(--shadow-hover);
        }

        .section-about__figure img {
            display: block;
            width: 100%;
            height: auto;
            border-radius: 14px;
            object-fit: cover;
            transition: transform 0.65s var(--ease-out-expo);
        }

        .section-about__figure:hover img {
            transform: scale(1.03);
        }

        .section-actors {
            background: var(--white);
            padding: 5.5rem 1.5rem 6rem;
        }

        .section-actors__inner {
            max-width: 1200px;
            margin: 0 auto;
        }

        .section-actors__title {
            font-size: clamp(1.5rem, 3.2vw, 2rem);
            font-weight: 700;
            line-height: 1.3;
            color: var(--navy-text);
            text-align: center;
            max-width: 22ch;
            margin: 0 auto 3.25rem;
        }

        .section-actors__grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.25rem;
        }

        @media (min-width: 640px) {
            .section-actors__grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (min-width: 1024px) {
            .section-actors__grid {
                grid-template-columns: repeat(4, 1fr);
                gap: 1.5rem;
            }
        }

        .actor-card {
            background: var(--white);
            border-radius: 16px;
            padding: 1.75rem 1.25rem;
            text-align: center;
            box-shadow: var(--shadow-sm);
            border: 1px solid rgba(0, 33, 71, 0.06);
            display: flex;
            flex-direction: column;
            align-items: center;
            height: 100%;
            transition: transform 0.4s var(--ease-out-expo), box-shadow 0.4s ease, border-color 0.3s ease;
        }

        .actor-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-hover);
            border-color: rgba(241, 155, 52, 0.35);
        }

        .actor-card__icon {
            width: 88px;
            height: 88px;
            margin-bottom: 1.25rem;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.45s var(--ease-out-expo);
        }

        .actor-card:hover .actor-card__icon {
            transform: scale(1.08) translateY(-4px);
        }

        .actor-card__icon img {
            max-width: 100%;
            max-height: 100%;
            width: auto;
            height: auto;
            object-fit: contain;
        }

        .actor-card__heading {
            font-size: 1.0625rem;
            font-weight: 700;
            color: var(--navy-text);
            margin-bottom: 0.75rem;
            line-height: 1.3;
        }

        .actor-card__text {
            font-size: 0.9375rem;
            font-weight: 400;
            color: var(--navy-text);
            line-height: 1.55;
            flex-grow: 1;
        }

        .section-keys {
            background: #eef1f6;
            border-radius: 48px 48px 0 0;
            padding: 5rem 1.5rem 6.5rem;
            margin-top: 0;
        }

        .section-keys__inner {
            max-width: 1200px;
            margin: 0 auto;
        }

        .section-keys__title {
            font-size: clamp(1.5rem, 3.2vw, 2rem);
            font-weight: 700;
            line-height: 1.3;
            color: var(--navy-text);
            text-align: center;
            margin: 0 auto 3.25rem;
            max-width: 28ch;
        }

        .section-keys__grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.25rem;
        }

        @media (min-width: 640px) {
            .section-keys__grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (min-width: 1024px) {
            .section-keys__grid {
                grid-template-columns: repeat(4, 1fr);
                gap: 1.5rem;
            }
        }

        .action-card {
            background: #ffffff;
            border-radius: 14px;
            padding: 1.75rem 1.15rem;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            height: 100%;
            box-shadow: var(--shadow-sm);
            border: 1px solid rgba(0, 33, 71, 0.04);
            transition: transform 0.4s var(--ease-out-expo), box-shadow 0.4s ease;
        }

        .action-card:hover {
            transform: translateY(-6px);
            box-shadow: var(--shadow-md);
        }

        .action-card__icon-wrap {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: var(--orange);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            flex-shrink: 0;
            transition: transform 0.45s var(--ease-out-expo), box-shadow 0.4s ease;
            box-shadow: 0 6px 18px rgba(241, 155, 52, 0.35);
        }

        .action-card:hover .action-card__icon-wrap {
            transform: scale(1.1) rotate(-4deg);
            box-shadow: 0 10px 28px rgba(241, 155, 52, 0.45);
        }

        .action-card__icon-wrap svg {
            width: 28px;
            height: 28px;
            color: var(--navy-text);
            transition: transform 0.35s ease;
        }

        .action-card:hover .action-card__icon-wrap svg {
            transform: scale(1.05);
        }

        .action-card__heading {
            font-size: 1.0625rem;
            font-weight: 700;
            color: var(--navy-text);
            margin-bottom: 0.65rem;
            line-height: 1.25;
        }

        .action-card__text {
            font-size: 0.875rem;
            font-weight: 400;
            color: var(--navy-text);
            line-height: 1.55;
            flex-grow: 1;
        }

        .section-testimonials {
            background: var(--white);
            padding: 5.5rem 1.5rem 6.5rem;
        }

        .section-testimonials__inner {
            max-width: 1200px;
            margin: 0 auto;
        }

        .section-testimonials__title {
            font-size: clamp(1.5rem, 3.2vw, 2rem);
            font-weight: 700;
            line-height: 1.3;
            color: var(--navy-text);
            text-align: center;
            margin: 0 auto 3.25rem;
        }

        .section-testimonials__grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }

        @media (min-width: 900px) {
            .section-testimonials__grid {
                grid-template-columns: repeat(3, 1fr);
                gap: 1.5rem;
            }
        }

        .testimonial-card {
            background: #e9eff2;
            border-radius: 16px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            height: 100%;
            border: 1px solid rgba(0, 33, 71, 0.05);
            transition: transform 0.45s var(--ease-out-expo), box-shadow 0.45s ease;
        }

        .testimonial-card:hover {
            transform: translateY(-6px);
            box-shadow: var(--shadow-md);
        }

        .testimonial-card__photo {
            margin: 0;
            line-height: 0;
            overflow: hidden;
        }

        .testimonial-card__photo img {
            display: block;
            width: 100%;
            height: auto;
            object-fit: cover;
            aspect-ratio: 16 / 10;
            border-radius: 14px 14px 0 0;
            transition: transform 0.6s var(--ease-out-expo);
        }

        .testimonial-card:hover .testimonial-card__photo img {
            transform: scale(1.05);
        }

        .testimonial-card__body {
            padding: 1.25rem 1.25rem 1rem;
            flex-grow: 1;
        }

        .testimonial-card__quote {
            font-size: 0.9375rem;
            font-weight: 400;
            color: var(--navy-text);
            line-height: 1.55;
            text-align: center;
            margin: 0;
        }

        .testimonial-card__footer {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0 1.25rem 1.35rem;
            margin-top: auto;
        }

        .testimonial-card__avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            object-fit: cover;
            flex-shrink: 0;
        }

        .testimonial-card__author {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
            min-width: 0;
        }

        .testimonial-card__name {
            font-size: 0.9375rem;
            font-weight: 700;
            color: var(--navy-text);
            font-style: normal;
        }

        .testimonial-card__stars {
            display: flex;
            gap: 2px;
            color: var(--orange);
            font-size: 0.875rem;
            line-height: 1;
            letter-spacing: 0.02em;
        }

        .section-cta {
            background: #f8f9fa;
            padding: 5rem 1.5rem 5.5rem;
        }

        .section-cta__wrap {
            max-width: 720px;
            margin: 0 auto;
            padding: 2px;
            border-radius: 20px;
            background: linear-gradient(120deg, #8ecae6 0%, #bde0fe 35%, #ffe5d0 65%, #ffb981 100%);
            background-size: 200% 200%;
            animation: gradientFlow 10s ease-in-out infinite;
        }

        .section-cta__card {
            background: var(--white);
            border-radius: 18px;
            padding: 2.75rem 1.75rem;
            text-align: center;
            transition: transform 0.4s var(--ease-out-expo);
        }

        .section-cta__wrap:hover .section-cta__card {
            transform: scale(1.01);
        }

        .section-cta__title {
            font-size: clamp(1.35rem, 3vw, 1.75rem);
            font-weight: 700;
            color: var(--navy-text);
            line-height: 1.3;
            margin: 0 0 1rem;
        }

        .section-cta__sub {
            font-size: 1rem;
            font-weight: 400;
            color: var(--navy-text);
            line-height: 1.6;
            max-width: 48ch;
            margin: 0 auto 1.75rem;
        }

        .section-cta__actions {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 0.875rem;
        }

        .site-footer {
            background: #002147;
            color: rgba(255, 255, 255, 0.92);
        }

        .site-footer__main {
            max-width: 1200px;
            margin: 0 auto;
            padding: 3.5rem 1.5rem 2.5rem;
            display: grid;
            grid-template-columns: 1fr;
            gap: 2.25rem;
        }

        @media (min-width: 640px) {
            .site-footer__main {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (min-width: 1024px) {
            .site-footer__main {
                grid-template-columns: 1.1fr 1fr 1fr 1fr;
                gap: 2rem;
            }
        }

        .site-footer__brand {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--white);
            margin-bottom: 1rem;
        }

        .site-footer__social {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .site-footer__social a {
            color: rgba(255, 255, 255, 0.88);
            font-size: 0.9375rem;
            transition: color 0.25s ease, transform 0.3s var(--ease-out-expo), padding-left 0.3s ease;
            display: inline-block;
        }

        .site-footer__social a:hover {
            color: var(--orange);
            transform: translateX(4px);
        }

        .site-footer__col-title {
            font-size: 1rem;
            font-weight: 700;
            color: var(--white);
            margin-bottom: 1rem;
        }

        .site-footer__links {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .site-footer__links a {
            color: rgba(255, 255, 255, 0.88);
            font-size: 0.9375rem;
            transition: color 0.25s ease, transform 0.3s var(--ease-out-expo);
            display: inline-block;
        }

        .site-footer__links a:hover {
            color: var(--orange);
            transform: translateX(4px);
        }

        .site-footer__bottom {
            background: #00152e;
            padding: 1rem 1.5rem;
            text-align: center;
        }

        .site-footer__copyright {
            font-size: 0.8125rem;
            color: rgba(255, 255, 255, 0.75);
            margin: 0;
        }

        @media (prefers-reduced-motion: reduce) {
            html {
                scroll-behavior: auto;
            }

            .section-reveal {
                opacity: 1 !important;
                transform: none !important;
                animation: none !important;
            }

            .hero-title,
            .hero-sub,
            .hero-actions,
            .hero-title .brush,
            .hero-visual--left img,
            .hero-visual--right img,
            .section-cta__wrap {
                animation: none !important;
            }
        }
    </style>
</head>
<body>
    <header class="site-header">
        <nav class="nav-inner" aria-label="Principale">
            <a href="{{ url('/') }}" class="brand">
                <img src="{{ asset('images/logo.png') }}" alt="BATITRAVOO" width="180" height="72">
            </a>
            <ul class="nav-links">
                <li><a href="#accueil">Accueil</a></li>
                <li><a href="#solution">Solution</a></li>
                <li><a href="#pour-qui">Pour qui</a></li>
                <li><a href="#fonctionnalites">Fonctionnalités</a></li>
                <li><a href="#temoignages">Témoignages</a></li>
            </ul>
            @if (Route::has('register'))
                <a href="{{ route('register') }}" class="btn-nav">S'inscrire</a>
            @else
                <a href="#inscription" class="btn-nav">S'inscrire</a>
            @endif
        </nav>
    </header>

    <section class="hero" id="accueil">
        <div class="hero-grid">
            <div class="hero-visual hero-visual--left">
                <img src="{{ asset('images/Photo 2 1.png') }}" alt="Artisan au travail sur un chantier">
            </div>
            <div class="hero-center">
                <h1 class="hero-title">
                    Tous vos <span class="brush">projets bâtiment,</span><br>
                    sur une seule plateforme.
                </h1>
                <p class="hero-sub">
                    Trouvez des prestataires, recrutez pour vos chantiers, réalisez vos travaux ou achetez vos matériaux facilement avec BATITRAVOO.
                </p>
                <div class="hero-actions">
                    @if (Route::has('login'))
                        <a href="{{ route('login') }}" class="btn-outline">Se connecter</a>
                    @else
                        <a href="#connexion" class="btn-outline">Se connecter</a>
                    @endif
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="btn-primary">Créer un compte</a>
                    @else
                        <a href="#inscription" class="btn-primary">Créer un compte</a>
                    @endif
                </div>
            </div>
            <div class="hero-visual hero-visual--right">
                <img src="{{ asset('images/Group 105.png') }}" alt="Illustration maison moderne">
            </div>
        </div>
    </section>

    <section class="section-about section-reveal section-reveal--d1" id="solution" aria-labelledby="about-heading">
        <div class="section-about__inner">
            <div class="section-about__content">
                <h2 class="section-about__title" id="about-heading">Un secteur complexe… enfin simplifié</h2>
                <p class="section-about__text">
                    Trouver un artisan fiable, recruter rapidement ou accéder aux bons matériaux reste un défi. BATITRAVOO centralise tous les acteurs du bâtiment pour vous faire gagner du temps et sécuriser vos projets.
                </p>
            </div>
            <figure class="section-about__figure">
                <img src="{{ asset('images/Rectangle 8.png') }}" alt="Équipe sur un chantier de construction" width="600" height="400" loading="lazy">
            </figure>
        </div>
    </section>

    <section class="section-actors section-reveal section-reveal--d2" id="pour-qui" aria-labelledby="actors-heading">
        <div class="section-actors__inner">
            <h2 class="section-actors__title" id="actors-heading">Une solution pour tous les acteurs du bâtiment</h2>
            <div class="section-actors__grid">
                <article class="actor-card">
                    <div class="actor-card__icon">
                        <img src="{{ asset('images/image 2.png') }}" alt="" width="88" height="88" loading="lazy">
                    </div>
                    <h3 class="actor-card__heading">Entreprises BTP</h3>
                    <p class="actor-card__text">Recrutez rapidement et gérez vos projets efficacement</p>
                </article>
                <article class="actor-card">
                    <div class="actor-card__icon">
                        <img src="{{ asset('images/image 3.png') }}" alt="" width="88" height="88" loading="lazy">
                    </div>
                    <h3 class="actor-card__heading">Artisans</h3>
                    <p class="actor-card__text">Accédez à des missions et développez votre activité</p>
                </article>
                <article class="actor-card">
                    <div class="actor-card__icon">
                        <img src="{{ asset('images/image 4.png') }}" alt="" width="88" height="88" loading="lazy">
                    </div>
                    <h3 class="actor-card__heading">Particuliers</h3>
                    <p class="actor-card__text">Trouvez des prestataires fiables et réalisez vos travaux</p>
                </article>
                <article class="actor-card">
                    <div class="actor-card__icon">
                        <img src="{{ asset('images/image 5.png') }}" alt="" width="88" height="88" loading="lazy">
                    </div>
                    <h3 class="actor-card__heading">Fournisseurs</h3>
                    <p class="actor-card__text">Vendez vos matériaux et accédez à une clientèle qualifiée</p>
                </article>
            </div>
        </div>
    </section>

    <section class="section-keys section-reveal section-reveal--d3" id="fonctionnalites" aria-labelledby="keys-heading">
        <div class="section-keys__inner">
            <h2 class="section-keys__title" id="keys-heading">Une plateforme, 4 actions clés</h2>
            <div class="section-keys__grid">
                <article class="action-card">
                    <div class="action-card__icon-wrap" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <circle cx="10.5" cy="10.5" r="5.5" stroke="currentColor" stroke-width="2"/>
                            <path d="M16 16l5 5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                    </div>
                    <h3 class="action-card__heading">Trouver</h3>
                    <p class="action-card__text">Recherchez des artisans et entreprises qualifiés en quelques clics</p>
                </article>
                <article class="action-card">
                    <div class="action-card__icon-wrap" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path d="M22 2L11 13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M22 2L15 22l-4-9-9-4 18-7z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <h3 class="action-card__heading">Publier</h3>
                    <p class="action-card__text">Publiez vos besoins et recevez des candidatures adaptées</p>
                </article>
                <article class="action-card">
                    <div class="action-card__icon-wrap" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path d="M21 11.5a8.38 8.38 0 01-.9 3.8 8.5 8.5 0 01-7.6 4.7 8.38 8.38 0 01-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 01-.9-3.8 8.5 8.5 0 014.7-7.6 8.38 8.38 0 013.8-.9h.5a8.48 8.48 0 018 8.5z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <text x="12" y="15.5" text-anchor="middle" font-size="11" font-weight="700" fill="currentColor" font-family="Inter, system-ui, sans-serif">?</text>
                        </svg>
                    </div>
                    <h3 class="action-card__heading">Demander</h3>
                    <p class="action-card__text">Contactez directement des prestataires pour vos travaux</p>
                </article>
                <article class="action-card">
                    <div class="action-card__icon-wrap" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <circle cx="9" cy="20" r="1.5" fill="currentColor"/>
                            <circle cx="18" cy="20" r="1.5" fill="currentColor"/>
                            <path d="M1 1h4l2.68 12.39a2 2 0 002 1.61h9.72a2 2 0 002-1.61L23 6H6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <h3 class="action-card__heading">Acheter</h3>
                    <p class="action-card__text">Accédez à un catalogue de matériaux et commandez facilement</p>
                </article>
            </div>
        </div>
    </section>

    <section class="section-testimonials section-reveal section-reveal--d4" id="temoignages" aria-labelledby="testimonials-heading">
        <div class="section-testimonials__inner">
            <h2 class="section-testimonials__title" id="testimonials-heading">Ils font confiance à BATITRAVOO</h2>
            <div class="section-testimonials__grid">
                <article class="testimonial-card">
                    <figure class="testimonial-card__photo">
                        <img src="{{ asset('images/Rectangle 29.png') }}" alt="" width="400" height="250" loading="lazy">
                    </figure>
                    <div class="testimonial-card__body">
                        <blockquote class="testimonial-card__quote">
                            « J'avais du mal à trouver un artisan sérieux. En quelques heures sur BATITRAVOO, j'ai reçu plusieurs propositions et j'ai pu lancer mes travaux rapidement. »
                        </blockquote>
                    </div>
                    <footer class="testimonial-card__footer">
                        <img class="testimonial-card__avatar" src="{{ asset('images/Ellipse 7.png') }}" alt="Jean Marc Kouassi" width="48" height="48" loading="lazy">
                        <div class="testimonial-card__author">
                            <cite class="testimonial-card__name">Jean Marc Kouassi</cite>
                            <div class="testimonial-card__stars" aria-label="5 sur 5">★★★★★</div>
                        </div>
                    </footer>
                </article>
                <article class="testimonial-card">
                    <figure class="testimonial-card__photo">
                        <img src="{{ asset('images/Rectangle 29 (1).png') }}" alt="" width="400" height="250" loading="lazy">
                    </figure>
                    <div class="testimonial-card__body">
                        <blockquote class="testimonial-card__quote">
                            « J'avais du mal à trouver un artisan sérieux. En quelques heures sur BATITRAVOO, j'ai reçu plusieurs propositions et j'ai pu lancer mes travaux rapidement. »
                        </blockquote>
                    </div>
                    <footer class="testimonial-card__footer">
                        <img class="testimonial-card__avatar" src="{{ asset('images/Ellipse 7 (1).png') }}" alt="Mariam Tapé" width="48" height="48" loading="lazy">
                        <div class="testimonial-card__author">
                            <cite class="testimonial-card__name">Mariam Tapé</cite>
                            <div class="testimonial-card__stars" aria-label="5 sur 5">★★★★★</div>
                        </div>
                    </footer>
                </article>
                <article class="testimonial-card">
                    <figure class="testimonial-card__photo">
                        <img src="{{ asset('images/Rectangle 29 (2).png') }}" alt="" width="400" height="250" loading="lazy">
                    </figure>
                    <div class="testimonial-card__body">
                        <blockquote class="testimonial-card__quote">
                            « J'avais du mal à trouver un artisan sérieux. En quelques heures sur BATITRAVOO, j'ai reçu plusieurs propositions et j'ai pu lancer mes travaux rapidement. »
                        </blockquote>
                    </div>
                    <footer class="testimonial-card__footer">
                        <img class="testimonial-card__avatar" src="{{ asset('images/Ellipse 7 (2).png') }}" alt="Carol Zabré" width="48" height="48" loading="lazy">
                        <div class="testimonial-card__author">
                            <cite class="testimonial-card__name">Carol Zabré</cite>
                            <div class="testimonial-card__stars" aria-label="5 sur 5">★★★★★</div>
                        </div>
                    </footer>
                </article>
            </div>
        </div>
    </section>

    <section class="section-cta section-reveal section-reveal--d5" id="inscription" aria-labelledby="cta-heading">
        <div class="section-cta__wrap">
            <div class="section-cta__card">
                <h2 class="section-cta__title" id="cta-heading">Rejoignez BATITRAVOO dès aujourd'hui</h2>
                <p class="section-cta__sub">
                    Simplifiez vos projets, développez votre activité et accédez à de nouvelles opportunités dans le bâtiment.
                </p>
                <div class="section-cta__actions">
                    @if (Route::has('login'))
                        <a href="{{ route('login') }}" class="btn-outline">Se connecter</a>
                    @else
                        <a href="#connexion" class="btn-outline">Se connecter</a>
                    @endif
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="btn-primary">Créer un compte</a>
                    @else
                        <a href="#inscription" class="btn-primary">Créer un compte</a>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <footer class="site-footer section-reveal section-reveal--d6" role="contentinfo">
        <div class="site-footer__main">
            <div>
                <p class="site-footer__brand">Batitravoo</p>
                <ul class="site-footer__social">
                    <li><a href="#" rel="noopener noreferrer">Facebook</a></li>
                    <li><a href="#" rel="noopener noreferrer">LinkedIn</a></li>
                    <li><a href="#" rel="noopener noreferrer">Instagram</a></li>
                    <li><a href="#" rel="noopener noreferrer">WhatsApp</a></li>
                </ul>
            </div>
            <nav aria-label="Navigation pied de page">
                <p class="site-footer__col-title">Navigation</p>
                <ul class="site-footer__links">
                    <li><a href="#accueil">Accueil</a></li>
                    <li><a href="#solution">Solution</a></li>
                    <li><a href="#pour-qui">Pour qui</a></li>
                    <li><a href="#fonctionnalites">Fonctionnalités</a></li>
                    <li><a href="#temoignages">Témoignages</a></li>
                </ul>
            </nav>
            <nav aria-label="Utilisateurs">
                <p class="site-footer__col-title">Utilisateurs</p>
                <ul class="site-footer__links">
                    <li><a href="#">Entrepreneur Bâtiment</a></li>
                    <li><a href="#">Entreprise fournisseur</a></li>
                    <li><a href="#">Artisan</a></li>
                    <li><a href="#">Particulier</a></li>
                </ul>
            </nav>
            <nav aria-label="Support">
                <p class="site-footer__col-title">Support</p>
                <ul class="site-footer__links">
                    <li><a href="#">Contact</a></li>
                    <li><a href="#">Centre d'aide</a></li>
                    <li><a href="#">FAQ</a></li>
                    <li><a href="#">Conditions d'utilisation</a></li>
                    <li><a href="#">Politique de confidentialité</a></li>
                </ul>
            </nav>
        </div>
        <div class="site-footer__bottom">
            <p class="site-footer__copyright">© 2026 BATITRAVOO – Tous droits réservés</p>
        </div>
    </footer>
</body>
</html>
