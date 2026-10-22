<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'SABU JAYA MART') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            overflow-x: hidden;
            background: #08090d;
            color: rgba(255, 255, 255, 0.92);
        }

        /* Animated Gradient Background */
        .bg-animated {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background:
                radial-gradient(circle at 12% 18%, rgba(239, 68, 68, 0.22) 0%, transparent 34%),
                radial-gradient(circle at 88% 76%, rgba(99, 102, 241, 0.2) 0%, transparent 38%),
                radial-gradient(circle at 52% 42%, rgba(249, 115, 22, 0.08) 0%, transparent 30%);
            z-index: -2;
        }

        .grid-pattern {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image:
                linear-gradient(rgba(255, 255, 255, 0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, 0.03) 1px, transparent 1px);
            background-size: 44px 44px;
            mask-image: linear-gradient(to bottom, black, transparent 92%);
            z-index: -1;
        }

        /* Floating Orbs */
        .orb {
            position: fixed;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.4;
            animation: float 20s infinite ease-in-out;
            z-index: -1;
        }

        .orb-1 {
            width: 400px;
            height: 400px;
            background: linear-gradient(135deg, #ef4444, #dc2626);
            top: -100px;
            left: -100px;
            animation-delay: 0s;
        }

        .orb-2 {
            width: 500px;
            height: 500px;
            background: linear-gradient(135deg, #8b5cf6, #7c3aed);
            bottom: -150px;
            right: -150px;
            animation-delay: -5s;
        }

        .orb-3 {
            width: 300px;
            height: 300px;
            background: linear-gradient(135deg, #f97316, #ea580c);
            top: 50%;
            left: 50%;
            animation-delay: -10s;
        }

        .flight-journey {
            position: fixed;
            inset: 0;
            z-index: 0;
            overflow: hidden;
            pointer-events: none;
            perspective: 1000px;
        }

        .flight-route {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            opacity: 0.38;
        }

        .flight-route path {
            fill: none;
            stroke: url(#flight-route-gradient);
            stroke-width: 1.5;
            stroke-dasharray: 5 11;
            vector-effect: non-scaling-stroke;
        }

        .flight-aircraft {
            position: absolute;
            top: var(--flight-y, 30%);
            left: var(--flight-x, 74%);
            width: clamp(125px, 19vw, 235px);
            transform: translate(-50%, -50%) rotate(var(--flight-angle, -12deg));
            transform-style: preserve-3d;
            will-change: top, left, transform;
            filter: drop-shadow(0 20px 22px rgba(75, 104, 255, 0.22));
        }

        .flight-aircraft svg {
            display: block;
            width: 100%;
            overflow: visible;
            animation: aircraft-hover 5s ease-in-out infinite;
            transform: rotateX(17deg) rotateY(-22deg);
            transform-style: preserve-3d;
        }

        .flight-cloud {
            position: absolute;
            width: clamp(110px, 17vw, 205px);
            height: clamp(42px, 6vw, 72px);
            border: 1px solid rgba(211, 220, 255, 0.14);
            border-radius: 50%;
            background: linear-gradient(145deg, rgba(186, 199, 255, 0.12), rgba(255, 198, 170, 0.035));
            box-shadow: inset -12px -10px 24px rgba(116, 116, 227, 0.09), 0 0 48px rgba(116, 122, 255, 0.08);
            opacity: 0.65;
            transform-style: preserve-3d;
            animation: cloud-drift 13s ease-in-out infinite;
        }

        .flight-cloud::before,
        .flight-cloud::after {
            position: absolute;
            bottom: 12px;
            border: inherit;
            border-bottom: 0;
            border-radius: 50% 50% 0 0;
            background: inherit;
            content: "";
        }

        .flight-cloud::before { left: 19%; width: 42%; height: 85%; }
        .flight-cloud::after { right: 13%; width: 34%; height: 62%; }
        .flight-cloud-one { top: 22%; left: 13%; }
        .flight-cloud-two { top: 67%; right: 12%; scale: 0.8; animation-delay: -5s; }
        .flight-cloud-three { right: 25%; bottom: 13%; scale: 0.55; opacity: 0.4; animation-delay: -9s; }

        .scroll-hint {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin: 34px auto 0;
            color: rgba(255, 255, 255, 0.45);
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.14em;
            text-transform: uppercase;
        }

        .scroll-hint i {
            color: #f97316;
            animation: scroll-bob 1.8s ease-in-out infinite;
        }

        body.motion-ready .feature-card,
        body.motion-ready .stat-item,
        body.motion-ready .cta-box {
            opacity: 0;
            transform: translateY(30px) rotateX(-5deg);
            transition: opacity 0.75s ease, transform 0.75s cubic-bezier(.2,.7,.2,1);
        }

        body.motion-ready .feature-card.is-visible,
        body.motion-ready .stat-item.is-visible,
        body.motion-ready .cta-box.is-visible {
            opacity: 1;
            transform: translateY(0) rotateX(0);
        }

        .market-stack-section {
            position: relative;
            height: 310vh;
            scroll-margin-top: 76px;
            background:
                radial-gradient(ellipse at 50% 48%, rgba(99, 102, 241, 0.1), transparent 48%),
                linear-gradient(180deg, #08090d, #101018 52%, #08090d);
        }

        .market-stack-stage {
            position: sticky;
            top: 0;
            height: 100vh;
            min-height: 560px;
            overflow: hidden;
            perspective: 1200px;
            perspective-origin: 50% 48%;
            isolation: isolate;
        }

        .market-stack-stage::before,
        .market-stack-stage::after {
            position: absolute;
            z-index: -1;
            border: 1px solid rgba(255, 255, 255, 0.055);
            border-radius: 50%;
            content: "";
            pointer-events: none;
        }

        .market-stack-stage::before {
            top: 50%;
            left: 50%;
            width: min(76vw, 960px);
            aspect-ratio: 1;
            transform: translate(-50%, -50%) rotateX(70deg);
        }

        .market-stack-stage::after {
            top: 50%;
            left: 50%;
            width: min(58vw, 740px);
            aspect-ratio: 1;
            transform: translate(-50%, -50%) rotateX(70deg);
        }

        .market-stack-heading {
            position: absolute;
            z-index: 5;
            top: 50%;
            left: 50%;
            width: min(90%, 680px);
            color: #fff;
            opacity: 0;
            text-align: center;
            transform: translate(-50%, calc(-50% + 12px)) scale(0.94);
            transition: opacity 0.12s linear;
            pointer-events: none;
        }

        .market-stack-heading .eyebrow {
            margin-bottom: 14px;
            color: #ff9b70;
            font-size: 0.72rem;
            font-weight: 800;
            letter-spacing: 0.2em;
            text-transform: uppercase;
        }

        .market-stack-heading h2 {
            margin: 0;
            font-size: clamp(2.15rem, 5.4vw, 5rem);
            font-weight: 850;
            letter-spacing: -0.065em;
            line-height: 0.98;
            text-wrap: balance;
        }

        .market-stack-heading h2 span {
            background: linear-gradient(110deg, #ff6b48, #ffb45f 55%, #f7d56c);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .market-stack-heading p {
            max-width: 42ch;
            margin: 18px auto 0;
            color: rgba(255, 255, 255, 0.62);
            font-size: clamp(0.88rem, 1.2vw, 1.05rem);
            line-height: 1.75;
        }

        .market-stack-cards {
            position: absolute;
            inset: 0;
            transform-style: preserve-3d;
        }

        .market-stack-card {
            position: absolute;
            top: 54%;
            left: 50%;
            display: flex;
            width: 21vw;
            height: 30vh;
            min-width: 170px;
            min-height: 205px;
            max-width: 290px;
            max-height: 300px;
            flex-direction: column;
            justify-content: space-between;
            overflow: hidden;
            padding: clamp(16px, 2vw, 25px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 22px;
            background:
                radial-gradient(circle at 80% 15%, var(--card-glow), transparent 42%),
                linear-gradient(150deg, var(--card-top), var(--card-bottom));
            box-shadow: 0 35px 90px rgba(0, 0, 0, 0.48), inset 0 1px 0 rgba(255,255,255,.2), inset 0 -1px 0 rgba(255,255,255,.06);
            transform: translate3d(calc(-50% + var(--stack-x, 0) * 1vw), calc(-50% + var(--stack-y, 0) * 1vh), 0) rotate(var(--stack-rotate, 0deg)) scale(.84);
            transform-style: preserve-3d;
            transform-origin: center;
            backface-visibility: hidden;
            transition: border-color .3s ease, box-shadow .3s ease;
            will-change: transform, width, height;
        }

        .market-stack-card::before {
            position: absolute;
            inset: 0;
            background:
                radial-gradient(circle at var(--stack-light-x, 35%) var(--stack-light-y, 20%), rgba(255,255,255,.24), transparent 42%),
                linear-gradient(145deg, rgba(255,255,255,.12), transparent 40%, rgba(0,0,0,.16));
            content: "";
            pointer-events: none;
        }

        .market-card-top,
        .market-card-bottom {
            position: relative;
            z-index: 1;
            transform: translateZ(26px);
        }

        .market-card-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            color: rgba(255,255,255,.82);
            font-size: 0.69rem;
            font-weight: 750;
            letter-spacing: .12em;
            text-transform: uppercase;
        }

        .market-card-top i { font-size: 1rem; }

        .market-card-art {
            position: absolute;
            top: 48%;
            left: 50%;
            display: grid;
            width: 72%;
            aspect-ratio: 1;
            place-items: center;
            border: 1px solid rgba(255,255,255,.18);
            border-radius: 50%;
            background: radial-gradient(circle at 36% 28%, rgba(255,255,255,.26), rgba(255,255,255,.035) 66%);
            box-shadow: inset 0 12px 30px rgba(255,255,255,.1), 0 22px 44px rgba(0,0,0,.2);
            color: rgba(255,255,255,.92);
            font-size: clamp(3rem, 7vw, 6rem);
            transform: translate(-50%, -50%) translateZ(52px);
        }

        .market-card-art i { filter: drop-shadow(0 14px 15px rgba(0,0,0,.3)); transform: translateZ(12px); }

        .market-card-bottom h3 {
            margin: 0 0 5px;
            color: #fff;
            font-size: clamp(1rem, 1.55vw, 1.25rem);
            font-weight: 800;
            letter-spacing: -.035em;
        }

        .market-card-bottom p {
            margin: 0;
            color: rgba(255,255,255,.7);
            font-size: clamp(.72rem, .9vw, .83rem);
        }

        .market-stack-card:nth-child(1) { --stack-x: -9; --stack-y: -8; --stack-rotate: -17deg; --card-top: #344826; --card-bottom: #182418; --card-glow: rgba(162, 214, 91, .38); }
        .market-stack-card:nth-child(2) { --stack-x: 8; --stack-y: -10; --stack-rotate: 15deg; --card-top: #68402e; --card-bottom: #291d1a; --card-glow: rgba(255, 179, 93, .35); }
        .market-stack-card:nth-child(3) { --stack-x: -14; --stack-y: 1; --stack-rotate: -9deg; --card-top: #28444b; --card-bottom: #17262b; --card-glow: rgba(89, 211, 220, .32); }
        .market-stack-card:nth-child(4) { --stack-x: 4; --stack-y: 4; --stack-rotate: 8deg; --card-top: #5c2d32; --card-bottom: #29191f; --card-glow: rgba(255, 113, 115, .35); }
        .market-stack-card:nth-child(5) { --stack-x: 15; --stack-y: 2; --stack-rotate: 18deg; --card-top: #514026; --card-bottom: #282116; --card-glow: rgba(247, 199, 86, .34); }
        .market-stack-card:nth-child(6) { --stack-x: -11; --stack-y: 10; --stack-rotate: 13deg; --card-top: #41335d; --card-bottom: #211b35; --card-glow: rgba(180, 139, 255, .32); }
        .market-stack-card:nth-child(7) { --stack-x: 2; --stack-y: -1; --stack-rotate: -3deg; --card-top: #3e4e35; --card-bottom: #1d2b20; --card-glow: rgba(181, 220, 116, .3); }
        .market-stack-card:nth-child(8) { --stack-x: 11; --stack-y: 11; --stack-rotate: -12deg; --card-top: #304365; --card-bottom: #1b243a; --card-glow: rgba(112, 169, 255, .34); }

        .market-card-target {
            position: absolute;
            inset: auto 0 0;
            height: 2px;
            background: linear-gradient(90deg, transparent, var(--card-glow), transparent);
            opacity: .75;
        }

        .market-stack-hint {
            position: absolute;
            z-index: 20;
            right: 0;
            bottom: 5vh;
            left: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            color: rgba(255,255,255,.48);
            font-size: .7rem;
            font-weight: 700;
            letter-spacing: .18em;
            text-transform: uppercase;
            transition: opacity .2s linear;
            pointer-events: none;
        }

        .market-stack-hint i { color: #ff9b70; animation: scroll-bob 1.7s ease-in-out infinite; }
        .market-stack-progress { position: absolute; z-index: 20; right: 0; bottom: 0; left: 0; height: 2px; background: rgba(255,255,255,.08); }
        .market-stack-progress span { display: block; width: 100%; height: 100%; background: linear-gradient(90deg, #f45c43, #ffc15d); transform: scaleX(0); transform-origin: left; }

        @media (max-width: 767px) {
            .market-stack-section { height: 290vh; }
            .market-stack-stage { min-height: 520px; }
            .market-stack-heading { top: 49%; width: 84%; }
            .market-stack-heading h2 { font-size: clamp(2rem, 9vw, 3.5rem); }
            .market-stack-card {
                width: 44vw;
                height: 21vh;
                min-width: 0;
                min-height: 0;
                max-width: 210px;
                max-height: 190px;
                padding: 13px;
                border-radius: 17px;
            }
            .market-card-art { width: 54%; font-size: clamp(2.4rem, 12vw, 4rem); }
            .market-card-top { font-size: .55rem; }
            .market-card-bottom h3 { font-size: clamp(.78rem, 3.7vw, 1rem); }
            .market-card-bottom p { font-size: .65rem; }
            .market-stack-hint { bottom: 3vh; }
        }

        @media (prefers-reduced-motion: reduce) {
            .market-stack-section { height: auto; }
            .market-stack-stage { position: relative; height: auto; min-height: 0; padding: 72px 16px; }
            .market-stack-heading { position: relative; top: auto; left: auto; width: min(100%, 600px); margin: 0 auto 32px; opacity: 1; transform: none; }
            .market-stack-cards { position: relative; display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px; transform: none; }
            .market-stack-card { position: relative; top: auto; left: auto; width: 100%; height: 190px; min-width: 0; min-height: 0; transform: none!important; }
            .market-stack-hint, .market-stack-progress { display: none; }
        }

        @keyframes float {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(50px, -50px) scale(1.1); }
            66% { transform: translate(-30px, 30px) scale(0.9); }
        }

        @keyframes aircraft-hover {
            0%, 100% { translate: 0 0; }
            50% { translate: 0 -9px; }
        }

        @keyframes cloud-drift {
            0%, 100% { translate: 0 0; }
            50% { translate: 18px -12px; }
        }

        @keyframes scroll-bob {
            0%, 100% { translate: 0 0; }
            50% { translate: 0 5px; }
        }

        /* Navbar */
        .navbar-modern {
            padding: 16px 0;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            background: transparent;
            border-bottom: 1px solid transparent;
            transition: background 0.35s ease, border-color 0.35s ease, padding 0.35s ease;
        }

        .navbar-modern.is-scrolled {
            padding-top: 11px;
            padding-bottom: 11px;
            background: rgba(8, 9, 13, 0.76);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            border-bottom-color: rgba(255, 255, 255, 0.09);
        }

        .hero-section,
        .stats-section,
        .cta-section {
            position: relative;
            z-index: 1;
        }

        .navbar-modern .container {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .navbar-brand-modern {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 1.5rem;
            font-weight: 800;
            color: white;
            text-decoration: none;
        }

        .logo-icon {
            width: 45px;
            height: 45px;
            background: linear-gradient(135deg, #ef4444, #f97316);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            box-shadow: 0 8px 20px rgba(239, 68, 68, 0.4);
            position: relative;
            overflow: hidden;
        }

        .logo-icon::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.28), transparent 45%);
            pointer-events: none;
        }

        .nav-links {
            display: flex;
            gap: 30px;
            align-items: center;
        }

        .nav-toggle {
            display: none;
            width: 44px;
            height: 44px;
            align-items: center;
            justify-content: center;
            color: white;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 10px;
            font-size: 1.25rem;
        }

        .nav-links a {
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            font-weight: 500;
            font-size: 0.95rem;
            transition: all 0.3s;
            position: relative;
        }

        .nav-links a:hover {
            color: white;
        }

        .nav-links a::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 0;
            height: 2px;
            background: linear-gradient(90deg, #ef4444, #f97316);
            transition: width 0.3s;
        }

        .nav-links a:hover::after {
            width: 100%;
        }

        .btn-nav {
            padding: 10px 24px;
            border-radius: 10px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
            font-size: 0.95rem;
        }

        .btn-login {
            background: transparent;
            color: white;
            border: 2px solid rgba(255, 255, 255, 0.2);
        }

        .btn-login:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.4);
            transform: translateY(-2px);
        }

        .btn-register {
            background: linear-gradient(135deg, #ef4444, #f97316);
            color: white;
            box-shadow: 0 4px 15px rgba(239, 68, 68, 0.4);
        }

        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(239, 68, 68, 0.5);
            color: white;
        }

        /* Hero Section */
        .hero-section {
            min-height: 100vh;
            min-height: 100svh;
            display: flex;
            align-items: center;
            padding: 100px 0 42px;
            position: relative;
            isolation: isolate;
            scroll-margin-top: 76px;
        }

        .hero-video-background {
            position: absolute;
            z-index: 0;
            inset: 0;
            overflow: hidden;
            background: #08090d;
            pointer-events: none;
        }

        .hero-video-background video {
            position: absolute;
            width: 100%;
            height: 100%;
            inset: 0;
            object-fit: cover;
            object-position: center;
            filter: brightness(.86) saturate(.95) contrast(1.04);
        }

        .hero-video-background::after {
            position: absolute;
            inset: 0;
            background:
                linear-gradient(90deg, rgba(18, 13, 9, .48) 0%, rgba(18, 13, 9, .34) 48%, rgba(18, 13, 9, .16) 100%),
                linear-gradient(0deg, rgba(18, 13, 9, .28), transparent 42%, rgba(18, 13, 9, .12));
            content: "";
        }

        .hero-section > .container {
            position: relative;
            z-index: 1;
        }

        .hero-section .scroll-hint {
            position: absolute;
            right: 16px;
            bottom: 25px;
            left: 16px;
            z-index: 3;
            margin: 0;
        }

        .hero-content {
            max-width: 1280px;
            margin: 0 auto;
            width: 100%;
            position: relative;
        }

        .hero-layout {
            display: grid;
            grid-template-columns: minmax(0, 1fr);
            align-items: center;
            gap: 0;
        }

        .hero-copy { position: relative; z-index: 2; }

        .hero-content::before {
            content: '';
            position: absolute;
            top: -70px;
            left: -50px;
            width: 1px;
            height: 430px;
            background: linear-gradient(to bottom, rgba(249, 115, 22, 0.8), transparent);
            opacity: 0.55;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.3);
            padding: 8px 20px;
            border-radius: 50px;
            color: #ef4444;
            font-weight: 600;
            font-size: 0.9rem;
            margin-bottom: 30px;
            animation: fadeInUp 0.8s ease-out;
        }

        .hero-badge i {
            animation: pulse 2s infinite;
        }

        .hero-title {
            max-width: 11ch;
            font-size: clamp(3.5rem, 7vw, 6.8rem);
            font-weight: 900;
            line-height: .98;
            letter-spacing: -0.075em;
            color: #f8fafc;
            margin-bottom: 25px;
            animation: fadeInUp 0.8s ease-out 0.1s both;
        }

        .hero-title .gradient-text {
            background: linear-gradient(135deg, #ef4444, #f97316, #fbbf24);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            background-size: 200% 200%;
            animation: gradientShift 5s ease infinite;
        }

        @keyframes gradientShift {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }

        .hero-description {
            font-size: clamp(1rem, 1.35vw, 1.18rem);
            color: rgba(255, 255, 255, 0.68);
            max-width: 48ch;
            margin-bottom: 28px;
            line-height: 1.75;
            animation: fadeInUp 0.8s ease-out 0.2s both;
        }

        .hero-buttons {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            margin-bottom: 0;
            animation: fadeInUp 0.8s ease-out 0.3s both;
        }

        .hero-visual {
            --hero-tilt-x: 0deg;
            --hero-tilt-y: 0deg;
            position: absolute;
            z-index: 0;
            inset: clamp(-64px, -6vh, -24px) clamp(-40px, -3vw, -16px);
            left: clamp(18%, 22vw, 300px);
            display: grid;
            place-items: center;
            isolation: isolate;
            perspective: 1100px;
            transform: rotateX(var(--hero-tilt-y)) rotateY(var(--hero-tilt-x));
            transform-style: preserve-3d;
            transition: transform .2s ease-out;
            animation: fadeInUp .9s ease-out .22s both;
            opacity: .42;
            mix-blend-mode: screen;
            pointer-events: none;
            display: none;
        }

        .hero-3d-canvas {
            position: absolute;
            z-index: 1;
            inset: 0;
            width: 100%;
            height: 100%;
            overflow: visible;
            touch-action: pan-y;
            pointer-events: none;
        }

        .hero-visual.webgl-ready .hero-product { opacity: 0; pointer-events: none; }
        .hero-visual.webgl-ready .hero-product::after { animation: none; }
        .hero-eye-fallback {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: clamp(8px, 1.4vw, 20px);
            width: min(100%, 760px);
            filter: drop-shadow(0 30px 58px rgba(85, 62, 255, .27));
            animation: eye-fallback-float 5s ease-in-out infinite;
        }

        .hero-eye-fallback .hero-eye-graphic {
            display: block;
            width: min(100%, 760px);
            overflow: visible;
            transform: translateY(2px);
            filter: drop-shadow(0 12px 18px rgba(4, 8, 24, .28));
        }

        .hero-visual.webgl-ready .hero-eye-fallback { opacity: 0; }
        .hero-eye-fallback .eye-iris-spin {
            transform-box: fill-box;
            transform-origin: center;
            animation: eye-iris-rotate 36s linear infinite;
        }

        .hero-eye-fallback .eye-pulse {
            transform-box: fill-box;
            transform-origin: center;
            animation: eye-pupil-pulse 4s ease-in-out infinite;
        }

        @keyframes eye-fallback-float {
            0%, 100% { translate: 0 0; }
            50% { translate: 0 -9px; }
        }

        @keyframes eye-iris-rotate {
            to { rotate: 360deg; }
        }

        @keyframes eye-pupil-pulse {
            0%, 100% { scale: 1; }
            50% { scale: 1.045; }
        }

        .hero-visual.webgl-ready::before,
        .hero-visual.webgl-ready::after { opacity: .22; }

        .hero-visual::before,
        .hero-visual::after {
            position: absolute;
            left: 50%;
            top: 50%;
            width: min(36vw, 470px);
            aspect-ratio: 1;
            border: 1px solid rgba(255,255,255,.1);
            border-radius: 50%;
            content: "";
            transform: translate(-50%, -50%) rotateX(66deg);
            pointer-events: none;
            animation: hero-ring-breathe 5s ease-in-out infinite;
        }

        .hero-visual::after {
            width: min(27vw, 350px);
            border-color: rgba(255,155,112,.13);
            animation-delay: -2.5s;
        }

        .hero-visual-glow {
            position: absolute;
            z-index: -1;
            width: min(32vw, 400px);
            aspect-ratio: 1;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(249,115,22,.21), rgba(239,68,68,.08) 42%, transparent 70%);
            filter: blur(8px);
            transform: translateZ(-80px);
            animation: hero-glow-pulse 6s ease-in-out infinite;
        }

        .hero-product {
            position: absolute;
            --card-float: 0px;
            display: flex;
            width: clamp(145px, 16vw, 205px);
            height: clamp(205px, 24vw, 292px);
            flex-direction: column;
            justify-content: space-between;
            overflow: hidden;
            padding: 19px;
            border: 1px solid rgba(255,255,255,.2);
            border-radius: 25px;
            color: #fff;
            box-shadow: 0 38px 78px rgba(0,0,0,.38), inset 0 1px rgba(255,255,255,.25), inset 0 -1px rgba(255,255,255,.08);
            transform-style: preserve-3d;
            transition: transform .45s cubic-bezier(.2,.7,.2,1);
            animation: hero-card-float 6s ease-in-out infinite;
            will-change: transform;
        }

        .hero-product::before {
            position: absolute;
            inset: 0;
            background:
                radial-gradient(circle at var(--hero-light-x, 35%) var(--hero-light-y, 20%), rgba(255,255,255,.27), transparent 38%),
                linear-gradient(150deg, rgba(255,255,255,.13), transparent 48%, rgba(0,0,0,.26));
            content: "";
            pointer-events: none;
        }

        .hero-product::after {
            position: absolute;
            inset: 0;
            border-radius: inherit;
            background: linear-gradient(115deg, transparent 25%, rgba(255,255,255,.15) 48%, transparent 68%);
            content: "";
            opacity: .55;
            pointer-events: none;
            transform: translateX(-130%);
            animation: hero-card-sheen 8s ease-in-out infinite;
        }

        .hero-product span,
        .hero-product i,
        .hero-product strong { position: relative; z-index: 1; }
        .hero-product span { font-size: .64rem; font-weight: 800; letter-spacing: .15em; text-transform: uppercase; transform: translateZ(28px); }
        .hero-product i { align-self: center; font-size: clamp(4.8rem, 8vw, 7.5rem); filter: drop-shadow(0 18px 15px rgba(0,0,0,.2)); transform: translateZ(56px); }
        .hero-product strong { font-size: clamp(.95rem, 1.5vw, 1.2rem); letter-spacing: -.04em; transform: translateZ(38px); }
        .hero-product-one { z-index: 3; background: radial-gradient(circle at 70% 22%, rgba(255,205,108,.46), transparent 43%), linear-gradient(150deg,#75452b,#31201d); transform: translate3d(-43%, -50%, 100px) rotate(-10deg); }
        .hero-product-two { z-index: 2; background: radial-gradient(circle at 70% 22%, rgba(176,220,111,.4), transparent 43%), linear-gradient(150deg,#405530,#1e2b22); transform: translate3d(12%, -40%, 20px) rotate(9deg); animation-delay: -2s; }
        .hero-product-three { z-index: 1; background: radial-gradient(circle at 70% 22%, rgba(134,172,255,.4), transparent 43%), linear-gradient(150deg,#41496e,#22253e); transform: translate3d(-9%, -27%, -60px) rotate(2deg); animation-delay: -4s; }
        .hero-visual:hover .hero-product-one { transform: translate3d(-49%, -53%, 120px) rotate(-13deg) rotateY(-5deg); }
        .hero-visual:hover .hero-product-two { transform: translate3d(18%, -43%, 35px) rotate(13deg) rotateY(5deg); }
        .hero-visual:hover .hero-product-three { transform: translate3d(-5%, -22%, -45px) rotate(-2deg) rotateY(-3deg); }

        @keyframes hero-card-float {
            0%, 100% { translate: 0 0; }
            50% { translate: 0 -10px; }
        }

        @keyframes hero-card-sheen {
            0%, 65%, 100% { transform: translateX(-130%); }
            82% { transform: translateX(130%); }
        }

        @keyframes hero-ring-breathe {
            0%, 100% { opacity: .48; scale: .98; }
            50% { opacity: .9; scale: 1.04; }
        }

        @keyframes hero-glow-pulse {
            0%, 100% { opacity: .65; scale: .92; }
            50% { opacity: 1; scale: 1.08; }
        }

        .hero-footnote {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 26px;
            color: rgba(255,255,255,.48);
            font-size: .7rem;
            font-weight: 700;
            letter-spacing: .14em;
            text-transform: uppercase;
        }

        .hero-footnote::before { width: 28px; height: 1px; background: #ff9b70; content: ""; }

        .story-section {
            position: relative;
            z-index: 1;
            padding: clamp(86px, 12vw, 160px) 0;
            background: linear-gradient(180deg, rgba(8,9,13,.96), rgba(14,14,20,.98));
            scroll-margin-top: 76px;
        }

        .story-intro {
            display: grid;
            grid-template-columns: minmax(0, 1.1fr) minmax(260px, .7fr);
            align-items: end;
            gap: 42px;
            margin-bottom: clamp(48px, 8vw, 92px);
        }

        .story-kicker,
        .feature-index {
            color: #ff9b70;
            font-size: .7rem;
            font-weight: 800;
            letter-spacing: .18em;
            text-transform: uppercase;
        }

        .story-title {
            max-width: 13ch;
            margin: 15px 0 0;
            color: #fff;
            font-size: clamp(2.5rem, 6.2vw, 5.6rem);
            font-weight: 800;
            letter-spacing: -.075em;
            line-height: .98;
            text-wrap: balance;
        }

        .story-aside p {
            max-width: 42ch;
            margin: 0 0 22px;
            color: rgba(255,255,255,.64);
            font-size: 1rem;
            line-height: 1.85;
        }

        .story-link {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            color: #fff;
            font-size: .86rem;
            font-weight: 750;
        }

        .story-link i { color: #ff9b70; transition: transform .25s ease; }
        .story-link:hover { color: #fff; }
        .story-link:hover i { transform: translateX(5px); }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0 52px;
            animation: none;
        }

        .feature-card {
            display: grid;
            grid-template-columns: 52px 44px minmax(0, 1fr) 18px;
            align-items: center;
            gap: 16px;
            min-height: 118px;
            padding: 20px 4px;
            border: 0;
            border-top: 1px solid rgba(255,255,255,.13);
            border-radius: 0;
            background: transparent;
            backdrop-filter: none;
            box-shadow: none;
        }

        .feature-card:last-child { border-bottom: 1px solid rgba(255,255,255,.13); }
        .feature-card::before { display: none; }
        .feature-card:hover { transform: translateX(5px); background: transparent; border-color: rgba(255,255,255,.25); box-shadow: none; }
        .feature-index { font-size: .65rem; }
        .feature-icon { width: 42px; height: 42px; margin: 0; border: 1px solid rgba(255,255,255,.13); border-radius: 50%; background: rgba(255,255,255,.05); box-shadow: none; font-size: 1.1rem; }
        .feature-title { margin: 0 0 5px; font-size: 1rem; }
        .feature-desc { margin: 0; font-size: .82rem; }
        .feature-arrow { color: rgba(255,255,255,.35); transition: color .25s, transform .25s; }
        .feature-card:hover .feature-arrow { color: #ff9b70; transform: translate(3px,-3px); }

        .story-reveal {
            opacity: 0;
            transform: translateY(24px);
            transition: opacity .7s ease, transform .7s cubic-bezier(.2,.7,.2,1);
        }
        .story-reveal.is-visible { opacity: 1; transform: translateY(0); }

        .site-footer {
            position: relative;
            z-index: 1;
            padding: 34px 0;
            border-top: 1px solid rgba(255,255,255,.1);
            background: #08090d;
        }

        .footer-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 24px;
        }

        .footer-brand { display: inline-flex; align-items: center; gap: 10px; color: #fff; font-size: .9rem; font-weight: 800; }
        .footer-brand:hover { color: #fff; }
        .footer-brand i { color: #ff9b70; }
        .footer-nav { display: flex; flex-wrap: wrap; gap: 22px; }
        .footer-nav a, .footer-copy { color: rgba(255,255,255,.48); font-size: .75rem; }
        .footer-nav a:hover { color: #fff; }

        .btn-hero-primary {
            background: linear-gradient(135deg, #ef4444, #f97316);
            color: white;
            padding: 16px 32px;
            border-radius: 14px;
            font-weight: 700;
            font-size: 1.05rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 12px;
            box-shadow: 0 10px 30px rgba(239, 68, 68, 0.4);
            transition: all 0.3s;
            border: none;
            position: relative;
            overflow: hidden;
        }

        .btn-hero-primary::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(110deg, transparent 25%, rgba(255, 255, 255, 0.22), transparent 75%);
            transform: translateX(-120%);
            transition: transform 0.6s ease;
        }

        .btn-hero-primary:hover::before {
            transform: translateX(120%);
        }

        .btn-hero-primary i,
        .btn-hero-primary {
            isolation: isolate;
        }

        .btn-hero-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(239, 68, 68, 0.5);
            color: white;
        }

        .btn-hero-secondary {
            background: rgba(255, 255, 255, 0.05);
            color: white;
            padding: 16px 32px;
            border-radius: 14px;
            font-weight: 700;
            font-size: 1.05rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 12px;
            border: 2px solid rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            transition: all 0.3s;
        }

        .btn-hero-secondary:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.3);
            transform: translateY(-3px);
            color: white;
        }

        /* Feature Cards */
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
            animation: fadeInUp 0.8s ease-out 0.4s both;
        }

        .feature-card {
            background: linear-gradient(145deg, rgba(255, 255, 255, 0.075), rgba(255, 255, 255, 0.018));
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 20px;
            padding: 30px;
            backdrop-filter: blur(10px);
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
        }

        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 30px;
            right: 30px;
            height: 2px;
            background: linear-gradient(90deg, #ef4444, #f97316, transparent);
            opacity: 0.7;
        }

        .feature-card:hover {
            transform: translateY(-5px);
            background: rgba(255, 255, 255, 0.05);
            border-color: rgba(239, 68, 68, 0.3);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
            border-color: rgba(249, 115, 22, 0.3);
        }

        .feature-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.2), rgba(249, 115, 22, 0.2));
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            margin-bottom: 20px;
            border: 1px solid rgba(239, 68, 68, 0.2);
            box-shadow: inset 0 1px rgba(255, 255, 255, 0.12), 0 10px 24px rgba(239, 68, 68, 0.1);
        }

        .feature-title {
            color: white;
            font-size: 1.2rem;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .feature-desc {
            color: rgba(255, 255, 255, 0.5);
            font-size: 0.95rem;
            line-height: 1.6;
        }

        /* Stats Section */
        .stats-section {
            padding: 100px 0;
            background: linear-gradient(90deg, rgba(239, 68, 68, 0.04), rgba(255, 255, 255, 0.025), rgba(99, 102, 241, 0.04));
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 30px;
            max-width: 1000px;
            margin: 0 auto;
        }

        .stat-item {
            text-align: center;
            padding: 30px;
        }

        .stat-number {
            font-size: 3.5rem;
            font-weight: 900;
            background: linear-gradient(135deg, #ef4444, #f97316);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 10px;
        }

        .stat-label {
            color: rgba(255, 255, 255, 0.6);
            font-size: 1rem;
            font-weight: 500;
        }

        /* CTA Section */
        .cta-section {
            padding: 120px 0;
            text-align: center;
        }

        .cta-box {
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.16), rgba(249, 115, 22, 0.08) 48%, rgba(99, 102, 241, 0.1));
            border: 1px solid rgba(239, 68, 68, 0.2);
            border-radius: 30px;
            padding: 80px 40px;
            position: relative;
            overflow: hidden;
        }

        .cta-box::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(239, 68, 68, 0.1) 0%, transparent 70%);
            animation: rotate 20s linear infinite;
        }

        @keyframes rotate {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .cta-title {
            font-size: clamp(2rem, 5vw, 3.5rem);
            font-weight: 900;
            color: white;
            margin-bottom: 20px;
            position: relative;
            z-index: 1;
        }

        .cta-desc {
            color: rgba(255, 255, 255, 0.6);
            font-size: 1.2rem;
            max-width: 600px;
            margin: 0 auto 40px;
            position: relative;
            z-index: 1;
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .navbar-modern {
                padding: 14px 0;
            }

            .navbar-brand-modern {
                gap: 8px;
                font-size: 1.25rem;
            }

            .logo-icon {
                width: 38px;
                height: 38px;
                border-radius: 10px;
            }

            .nav-toggle {
                display: inline-flex;
            }

            .nav-links {
                display: none;
                position: absolute;
                top: calc(100% + 1px);
                left: 0;
                right: 0;
                padding: 18px 20px 22px;
                flex-direction: column;
                align-items: stretch;
                gap: 8px;
                background: rgba(10, 10, 10, 0.97);
                border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            }

            .nav-links.is-open {
                display: flex;
            }

            .nav-links a {
                padding: 10px 4px;
            }

            .nav-links .btn-nav {
                text-align: center;
                margin-top: 4px;
            }

            .hero-section {
                min-height: auto;
                padding: 120px 0 56px;
            }

            .hero-title {
                font-size: clamp(2.35rem, 12vw, 3.5rem);
                line-height: 1.05;
                margin-bottom: 20px;
            }

            .hero-content::before {
                display: none;
            }

            .hero-description {
                font-size: 1rem;
                line-height: 1.7;
                margin-bottom: 28px;
            }

            .hero-badge {
                padding: 7px 13px;
                font-size: 0.78rem;
                margin-bottom: 22px;
            }

            .hero-buttons {
                gap: 12px;
                margin-bottom: 42px;
            }

            .hero-buttons {
                flex-direction: column;
            }

            .btn-hero-primary,
            .btn-hero-secondary {
                width: 100%;
                justify-content: center;
                padding: 14px 20px;
                font-size: 0.95rem;
            }

            .features-grid {
                grid-template-columns: 1fr;
                gap: 14px;
            }

            .feature-card {
                padding: 22px;
                border-radius: 16px;
            }

            .feature-card::before {
                left: 22px;
                right: 22px;
            }

            .feature-icon {
                width: 52px;
                height: 52px;
                margin-bottom: 15px;
            }

            .stats-section {
                padding: 56px 0;
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 10px;
            }

            .stat-item {
                padding: 18px 8px;
            }

            .stat-number {
                font-size: 2.4rem;
            }

            .stat-label {
                font-size: 0.82rem;
            }

            .cta-section {
                padding: 64px 0;
            }

            .cta-box {
                padding: 52px 20px;
                border-radius: 20px;
            }

            .cta-desc {
                font-size: 1rem;
                line-height: 1.7;
                margin-bottom: 28px;
            }

            .orb-1 {
                width: 240px;
                height: 240px;
            }

            .orb-2 {
                width: 280px;
                height: 280px;
            }

            .orb-3 {
                width: 180px;
                height: 180px;
            }
        }

        @media (max-width: 380px) {
            .container {
                padding-left: 18px;
                padding-right: 18px;
            }

            .hero-title {
                font-size: 2.2rem;
            }

            .hero-badge {
                font-size: 0.7rem;
            }
        }

        @media (max-width: 768px) {
            .flight-aircraft { width: 118px; opacity: 0.55; }
            .flight-cloud-one { top: 15%; left: -4%; }
            .flight-cloud-two { right: -8%; }
            .hero-section .scroll-hint {
                position: relative;
                right: auto;
                bottom: auto;
                left: auto;
                margin: 8px auto 0;
                font-size: 0.65rem;
            }
        }

        @media (max-width: 1024px) {
            .navbar-modern .container { position: relative; z-index: 1003; }
            .navbar-brand-modern span { white-space: nowrap; }
            .nav-toggle { position: relative; z-index: 1004; display: inline-flex; }
            .nav-links {
                position: fixed;
                inset: 0;
                z-index: 1002;
                display: flex;
                min-height: 100vh;
                min-height: 100svh;
                justify-content: center;
                padding: 94px 26px 34px;
                background: rgba(8,9,13,.97);
                backdrop-filter: blur(20px);
                -webkit-backdrop-filter: blur(20px);
                visibility: hidden;
                opacity: 0;
                transform: translateY(-12px);
                transition: opacity .28s ease, transform .28s ease, visibility .28s;
            }
            .nav-links.is-open { visibility: visible; opacity: 1; transform: translateY(0); }
            .nav-links > a:not(.btn-nav) { padding: 14px 4px; border-bottom: 1px solid rgba(255,255,255,.1); color: rgba(255,255,255,.9); font-size: clamp(1.35rem, 7vw, 2rem); font-weight: 700; }
            .nav-links > a:not(.btn-nav)::after { display: none; }
            .nav-links form { margin-top: 0; }
            .nav-links form button { width: 100%; }
            body.mobile-nav-active { overflow: hidden; }
            .hero-section { padding: 84px 0 28px; }
            .hero-layout { grid-template-columns: minmax(0, 1fr); gap: 0; }
            .hero-badge { margin-bottom: 22px; }
            .hero-title { margin-bottom: 18px; font-size: clamp(3rem, 5.6vw, 4.4rem); }
            .hero-description { margin-bottom: 20px; }
            .hero-buttons { flex-wrap: nowrap; gap: 10px; }
            .hero-buttons .btn-hero-primary,
            .hero-buttons .btn-hero-secondary { padding: 14px 15px; font-size: .9rem; white-space: nowrap; }
            .hero-footnote { display: none; }
            .hero-product { width: clamp(142px, 19vw, 185px); height: clamp(205px, 27vw, 260px); }
        }

        @media (max-width: 768px) {
            body.mobile-nav-active { overflow: hidden; }
            .nav-links {
                position: fixed;
                inset: 0;
                z-index: 1002;
                display: flex;
                min-height: 100vh;
                min-height: 100svh;
                justify-content: center;
                padding: 94px 26px 34px;
                background: rgba(8,9,13,.97);
                backdrop-filter: blur(20px);
                -webkit-backdrop-filter: blur(20px);
                visibility: hidden;
                opacity: 0;
                transform: translateY(-12px);
                transition: opacity .28s ease, transform .28s ease, visibility .28s;
            }
            .nav-links.is-open { visibility: visible; opacity: 1; transform: translateY(0); }
            .nav-links > a:not(.btn-nav) { padding: 14px 4px; border-bottom: 1px solid rgba(255,255,255,.1); color: rgba(255,255,255,.9); font-size: clamp(1.35rem, 7vw, 2rem); font-weight: 700; }
            .nav-links > a:not(.btn-nav)::after { display: none; }
            .nav-links form { margin-top: 0; }
            .nav-links form button { width: 100%; }
            .hero-section { min-height: 100svh; padding: 102px 0 30px; }
            .hero-layout { grid-template-columns: minmax(0, 1fr); gap: 0; }
            .hero-title { max-width: 12ch; font-size: clamp(2.7rem, 11.5vw, 4.2rem); }
            .hero-description { max-width: 47ch; }
            .hero-visual { inset: 3% -12%; opacity: .26; }
            .hero-video-background::after {
                background: linear-gradient(rgba(18, 13, 9, .34), rgba(18, 13, 9, .48));
            }
            .hero-eye-fallback { width: min(100%, 680px); gap: 0; }
            .hero-eye-fallback .hero-eye-graphic { width: 100%; }
            .hero-visual::before { width: min(70vw, 330px); }
            .hero-visual::after { width: min(52vw, 245px); }
            .hero-visual-glow { width: min(62vw, 285px); }
            .hero-product { width: 116px; height: 164px; padding: 12px; border-radius: 17px; }
            .hero-product span { font-size: .48rem; }
            .hero-product i { font-size: 4rem; }
            .hero-product strong { font-size: .72rem; }
            .hero-product-one { transform: translate3d(-43%, -50%, 65px) rotate(-10deg) scale(.94); }
            .hero-product-two { transform: translate3d(12%, -40%, 10px) rotate(9deg) scale(.94); }
            .hero-product-three { transform: translate3d(-9%, -27%, -35px) rotate(2deg) scale(.94); }
            .hero-visual:hover .hero-product-one { transform: translate3d(-43%, -50%, 65px) rotate(-10deg) scale(.94); }
            .hero-visual:hover .hero-product-two { transform: translate3d(12%, -40%, 10px) rotate(9deg) scale(.94); }
            .hero-visual:hover .hero-product-three { transform: translate3d(-9%, -27%, -35px) rotate(2deg) scale(.94); }
            .hero-footnote { margin-top: 16px; font-size: .6rem; }
            .story-section { padding: 82px 0; }
            .story-intro { grid-template-columns: 1fr; gap: 20px; margin-bottom: 42px; }
            .story-title { font-size: clamp(2.6rem, 10vw, 4rem); }
            .features-grid { grid-template-columns: 1fr; gap: 0; }
            .feature-card { grid-template-columns: 38px 38px minmax(0,1fr) 16px; gap: 12px; min-height: 100px; padding: 16px 0; }
            .feature-icon { width: 36px; height: 36px; font-size: 1rem; }
            .feature-title { font-size: .94rem; }
            .feature-desc { font-size: .76rem; }
            .footer-inner { flex-direction: column; align-items: flex-start; }
            .footer-nav { gap: 16px; }
        }

        @media (max-width: 430px) {
            .hero-section { padding-top: 88px; }
            .hero-title { font-size: clamp(2.35rem, 10vw, 2.8rem); }
            .hero-badge { max-width: 100%; }
            .hero-buttons { flex-direction: row; gap: 10px; }
            .hero-buttons .btn-hero-primary,
            .hero-buttons .btn-hero-secondary { width: auto; flex: 1; padding: 13px 10px; font-size: .82rem; }
            .hero-visual { inset: 5% -17%; opacity: .22; }
            .hero-eye-fallback { width: 100%; gap: 0; }
            .hero-eye-fallback .hero-eye-graphic { width: 100%; transform: translateY(2px); }
            .hero-product { width: 92px; height: 130px; }
            .feature-card { grid-template-columns: 30px 34px minmax(0,1fr) 14px; gap: 9px; }
            .feature-index { font-size: .58rem; }
            .feature-icon { width: 32px; height: 32px; }
            .feature-desc { font-size: .72rem; }
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                scroll-behavior: auto !important;
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }

            .hero-visual { transform: none !important; }
            .hero-product { translate: none !important; }

            body.motion-ready .feature-card,
            body.motion-ready .stat-item,
            body.motion-ready .cta-box {
                opacity: 1;
                transform: none;
            }
        }

        body {
            background: #100d09;
        }

        .bg-animated {
            background:
                radial-gradient(circle at 12% 18%, rgba(217, 119, 6, .2) 0%, transparent 34%),
                radial-gradient(circle at 88% 76%, rgba(180, 83, 9, .16) 0%, transparent 38%),
                radial-gradient(circle at 52% 42%, rgba(245, 158, 11, .08) 0%, transparent 30%);
        }

        .orb-1 { background: linear-gradient(135deg, #b45309, #f59e0b); }
        .orb-2 { background: linear-gradient(135deg, #78350f, #c2410c); }
        .orb-3 { background: linear-gradient(135deg, #d97706, #9a3412); }
        .flight-aircraft { display: none; }
        .flight-route { opacity: .2; }

        .flight-cloud {
            border-color: rgba(255, 214, 153, .14);
            background: linear-gradient(145deg, rgba(255, 196, 115, .12), rgba(255, 241, 214, .035));
            box-shadow: inset -12px -10px 24px rgba(180, 83, 9, .09), 0 0 48px rgba(245, 158, 11, .08);
        }

        .logo-icon,
        .btn-register,
        .btn-hero-primary {
            background: linear-gradient(135deg, #b45309, #f97316 58%, #fbbf24);
        }

        .logo-icon { box-shadow: 0 8px 24px rgba(180, 83, 9, .4); }
        .nav-links a::after { background: linear-gradient(90deg, #fbbf24, #f97316); }
        .btn-register { box-shadow: 0 4px 18px rgba(217, 119, 6, .38); }
        .btn-register:hover { box-shadow: 0 8px 26px rgba(249, 115, 22, .3); }
        .hero-content::before { background: linear-gradient(to bottom, rgba(251, 191, 36, .76), transparent); }
        .hero-badge {
            background: rgba(217, 119, 6, .17);
            border-color: rgba(251, 191, 36, .34);
            color: #fde68a;
        }

        .hero-title .gradient-text {
            background-image: linear-gradient(135deg, #fef3c7, #fbbf24 48%, #fb923c);
        }

        .hero-visual::before { border-color: rgba(251, 191, 36, .15); }
        .hero-visual::after { border-color: rgba(251, 146, 60, .2); }
        .hero-visual-glow {
            background: radial-gradient(circle, rgba(217, 119, 6, .2), rgba(251, 146, 60, .11) 42%, transparent 70%);
        }

        .hero-footnote::before { background: #fbbf24; }
        .scroll-hint i,
        .market-stack-hint i { color: #fbbf24; }
        .market-stack-heading .eyebrow,
        .story-kicker,
        .feature-index,
        .story-link i,
        .feature-card:hover .feature-arrow,
        .footer-brand i { color: #fbbf24; }

        .market-stack-heading h2 span {
            background-image: linear-gradient(110deg, #fef3c7, #fbbf24 55%, #fb923c);
        }

        .feature-icon { background: linear-gradient(135deg, rgba(217, 119, 6, .2), rgba(251, 146, 60, .16)); }
        .cta-box {
            background: linear-gradient(135deg, rgba(180, 83, 9, .2), rgba(251, 191, 36, .08) 48%, rgba(194, 65, 12, .14));
            border-color: rgba(251, 191, 36, .24);
        }

        .cta-box::before {
            background: radial-gradient(circle, rgba(251, 191, 36, .12) 0%, transparent 70%);
        }
    </style>
</head>
<body>
    <!-- Background Effects -->
    <div class="bg-animated"></div>
    <div class="grid-pattern"></div>
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>
    <div class="flight-journey" aria-hidden="true">
        <svg class="flight-route" viewBox="0 0 1000 1000" preserveAspectRatio="none">
            <defs>
                <linearGradient id="flight-route-gradient" x1="0" y1="0" x2="1" y2="1">
                    <stop offset="0%" stop-color="#8b9dff" stop-opacity=".05" />
                    <stop offset="48%" stop-color="#ff9b70" stop-opacity=".8" />
                    <stop offset="100%" stop-color="#8b9dff" stop-opacity=".12" />
                </linearGradient>
            </defs>
            <path d="M760 210 C 570 300, 380 290, 330 470 S 620 620, 700 770 S 470 890, 360 990" />
        </svg>
        <div class="flight-cloud flight-cloud-one"></div>
        <div class="flight-cloud flight-cloud-two"></div>
        <div class="flight-cloud flight-cloud-three"></div>
        <div class="flight-aircraft">
            <svg viewBox="0 0 220 150" role="img" aria-label="Pesawat 3D">
                <defs>
                    <linearGradient id="aircraft-body" x1="0" y1="0" x2="1" y2="1">
                        <stop offset="0%" stop-color="#ffffff" />
                        <stop offset="48%" stop-color="#c8d7ff" />
                        <stop offset="100%" stop-color="#758bdb" />
                    </linearGradient>
                    <linearGradient id="aircraft-wing" x1="0" y1="0" x2="0" y2="1">
                        <stop offset="0%" stop-color="#dce5ff" />
                        <stop offset="100%" stop-color="#7886d4" />
                    </linearGradient>
                </defs>
                <path d="M108 8c7 0 11 11 14 27l8 27 65 24c7 3 10 8 8 14l-2 5-73-11-8 24 20 12v7l-30-7-30 7v-7l20-12-8-24-73 11-2-5c-2-6 1-11 8-14l65-24 8-27c3-16 7-27 14-27Z" fill="url(#aircraft-wing)" stroke="#f5f7ff" stroke-opacity=".78" stroke-width="2" />
                <path d="M108 8c7 0 11 11 14 27l8 27 65 24c7 3 10 8 8 14l-2 5-73-11-8 24 20 12v7l-30-7-30 7v-7l20-12-8-24-73 11-2-5c-2-6 1-11 8-14l65-24 8-27c3-16 7-27 14-27Z" fill="url(#aircraft-body)" fill-opacity=".5" />
                <path d="M108 16v105" stroke="#fff" stroke-opacity=".72" stroke-width="2" />
                <path d="M99 41h18M94 56h28" stroke="#fff" stroke-opacity=".58" stroke-width="2" stroke-linecap="round" />
                <circle cx="108" cy="29" r="3" fill="#ffad82" />
            </svg>
        </div>
    </div>

    <!-- Navbar -->
    <nav class="navbar-modern">
        <div class="container">
            <a href="#" class="navbar-brand-modern">
                <div class="logo-icon">
                    <i class="bi bi-lightning-charge-fill" style="color: white; font-size: 1.35rem;"></i>
                </div>
                <span>{{ config('app.name', 'SABU JAYA MART') }}</span>
            </a>
            <nav class="nav-links" id="primary-navigation" aria-label="Navigasi utama">
                <a href="#features">Layanan</a>
                <a href="#features">Keunggulan</a>
                <a href="#community">Tentang Kami</a>
                @auth
                    @php
                        $user = auth()->user();
                        $isAdmin = method_exists($user, 'isAdmin') ? $user->isAdmin() : false;
                        $isCashier = method_exists($user, 'isCashier') ? $user->isCashier() : false;
                    @endphp
                    @if($isAdmin)
                        <a href="{{ route('admin.dashboard') }}" class="btn-nav btn-login">Dashboard Admin</a>
                    @elseif($isCashier)
                        <a href="{{ route('cashier.index') }}" class="btn-nav btn-login">Buka Kasir</a>
                    @else
                        <a href="{{ route('dashboard') }}" class="btn-nav btn-login">Dashboard</a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn-nav btn-register border-0">Keluar</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn-nav btn-login">Masuk</a>
                    <a href="{{ route('register') }}" class="btn-nav btn-register">Daftar</a>
                @endauth
            </nav>
            <button class="nav-toggle" type="button" aria-label="Open navigation" aria-expanded="false" aria-controls="primary-navigation">
                <i class="bi bi-list"></i>
            </button>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="hero-video-background" aria-hidden="true">
            <video autoplay muted loop playsinline preload="metadata" tabindex="-1">
                <source src="{{ asset('videos/welcome-background.mp4') }}" type="video/mp4">
            </video>
        </div>
        <div class="container">
            <div class="hero-content">
                <div class="hero-layout">
                    <div class="hero-copy">
                        <div class="hero-badge">
                            <i class="bi bi-lightning-charge-fill"></i>
                            <span>Belanja nyaman, transaksi terpercaya</span>
                        </div>

                        <h1 class="hero-title">
                            Semua kebutuhan<br>
                            ada di <span class="gradient-text">{{ config('app.name', 'SABU JAYA MART') }}</span>
                        </h1>

                        <p class="hero-description">
                            Kebutuhan harian lebih mudah dipenuhi dengan pembayaran cepat dan pelayanan yang nyaman.
                        </p>

                        <div class="hero-buttons">
                            @guest
                                <a href="{{ route('register') }}" class="btn-hero-primary">
                                    <i class="bi bi-person-plus"></i>
                                    Buat Akun
                                </a>
                            @else
                                @php
                                    $user = auth()->user();
                                    $isAdmin = method_exists($user, 'isAdmin') ? $user->isAdmin() : false;
                                    $isCashier = method_exists($user, 'isCashier') ? $user->isCashier() : false;
                                    $dashboardRoute = $isAdmin ? 'admin.dashboard' : ($isCashier ? 'cashier.index' : 'dashboard');
                                @endphp
                                <a href="{{ route($dashboardRoute) }}" class="btn-hero-primary">
                                    <i class="bi bi-arrow-right-circle"></i>
                                    Masuk ke Aplikasi
                                </a>
                            @endguest
                            <a href="#features" class="btn-hero-secondary">
                                <i class="bi bi-bag-check"></i>
                                Lihat Layanan
                            </a>
                        </div>
                        <div class="hero-footnote">Pilihan harian untuk seluruh keluarga</div>
                    </div>

                    <div class="hero-visual" aria-hidden="true">
                        <div class="hero-visual-glow"></div>
                        <canvas class="hero-3d-canvas" aria-hidden="true"></canvas>
                        <div class="hero-eye-fallback">
                            <svg class="hero-eye-graphic" viewBox="0 0 700 250" role="presentation" focusable="false">
                                <defs>
                                    <linearGradient id="welcome-anime-sclera" x1="0" y1="0" x2="0" y2="1">
                                        <stop offset="0%" stop-color="#ffffff" />
                                        <stop offset="100%" stop-color="#c9c9d2" />
                                    </linearGradient>
                                    <radialGradient id="welcome-anime-iris" cx="42%" cy="35%" r="72%">
                                        <stop offset="0%" stop-color="#ff4657" />
                                        <stop offset="58%" stop-color="#d50020" />
                                        <stop offset="100%" stop-color="#51000e" />
                                    </radialGradient>
                                    <clipPath id="welcome-anime-eye-clip">
                                        <path d="M18 126 C81 70 208 66 311 119 C248 157 91 172 18 126Z" />
                                    </clipPath>
                                    <clipPath id="welcome-anime-iris-clip">
                                        <circle cx="166" cy="125" r="53" />
                                    </clipPath>
                                    <g id="welcome-anime-eye">
                                        <path d="M19 111 C92 82 213 76 296 51 L278 69 C207 93 119 102 19 111Z" fill="#08080b" />
                                        <path d="M18 126 C81 70 208 66 311 119 C248 157 91 172 18 126Z" fill="url(#welcome-anime-sclera)" />
                                        <g clip-path="url(#welcome-anime-eye-clip)">
                                            <circle class="eye-pulse" cx="166" cy="126" r="54" fill="url(#welcome-anime-iris)" />
                                            <g class="eye-iris-spin" clip-path="url(#welcome-anime-iris-clip)" fill="none">
                                                <circle cx="166" cy="125" r="49" stroke="#27030a" stroke-width="4" />
                                                <circle cx="166" cy="125" r="40" stroke="#ff6674" stroke-width="2" />
                                                <circle cx="166" cy="125" r="31" stroke="#52000d" stroke-width="2.5" />
                                                <path d="M166 69V99 M166 151v29 M110 125h30 M193 125h29 M126 85l21 23 M185 143l24 25 M126 165l21-24 M185 105l24-24 M142 74l13 31 M177 145l13 34 M108 104l30 13 M194 135l32 14 M111 150l29-17 M192 112l32-18 M147 173l8-29 M177 105l10-31 M224 105l-30 12 M138 136l-32 14" stroke="#210208" stroke-width="3" />
                                                <path d="M166 77v22 M166 151v21 M120 125h23 M190 125h22 M135 94l17 18 M181 140l18 19 M135 157l17-19 M181 108l18-18" stroke="#ff293f" stroke-width="2" />
                                            </g>
                                            <circle cx="166" cy="126" r="20" fill="#050507" stroke="#1b0308" stroke-width="3" />
                                            <ellipse cx="149" cy="99" rx="8" ry="13" fill="#fff" fill-opacity=".88" transform="rotate(-28 149 99)" />
                                            <circle cx="179" cy="139" r="4" fill="#fff" fill-opacity=".78" />
                                        </g>
                                        <path d="M18 126 C81 70 208 66 311 119" fill="none" stroke="#050507" stroke-width="8" stroke-linecap="round" />
                                        <path d="M18 126 C91 168 238 158 311 119" fill="none" stroke="#111116" stroke-width="4" stroke-linecap="round" />
                                        <path d="M26 122l-12-11 M39 111l-11-12 M55 101l-8-12 M70 94l-5-11 M290 112l11-9" fill="none" stroke="#08080b" stroke-width="4" stroke-linecap="round" />
                                        <path d="M37 181 C104 195 224 189 282 164" fill="none" stroke="#15151c" stroke-opacity=".7" stroke-width="2" />
                                    </g>
                                </defs>
                                <use href="#welcome-anime-eye" x="0" y="12" />
                                <use href="#welcome-anime-eye" x="350" y="12" />
                            </svg>
                        </div>
                    </div>
                </div>
                <a href="#features" class="scroll-hint">
                    <span>Gulir untuk memulai perjalanan</span>
                    <i class="bi bi-arrow-down"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- Introduction and store features -->
    <section class="story-section" id="features" aria-labelledby="story-title">
        <div class="container">
            <div class="story-intro story-reveal">
                <div>
                    <div class="story-kicker">Dirancang untuk keseharian</div>
                    <h2 class="story-title" id="story-title">Hal kecil yang membuat belanja terasa lebih mudah.</h2>
                </div>
                <div class="story-aside">
                    <p>
                        Dari memilih kebutuhan sampai menyelesaikan pembayaran, kami menjaga setiap langkah tetap
                        praktis, jelas, dan nyaman untuk Anda.
                    </p>
                    <a class="story-link" href="#showcase">Jelajahi pilihan kami <i class="bi bi-arrow-up-right"></i></a>
                </div>
            </div>

            <div class="features-grid">
                <article class="feature-card">
                    <span class="feature-index">01</span>
                    <div class="feature-icon"><i class="bi bi-lightning-charge-fill" style="color: #ff9b70;"></i></div>
                    <div>
                        <h3 class="feature-title">Belanja Cepat</h3>
                        <p class="feature-desc">Proses transaksi ringkas agar kebutuhan Anda selesai tanpa antre lama.</p>
                    </div>
                    <i class="bi bi-arrow-up-right feature-arrow" aria-hidden="true"></i>
                </article>
                <article class="feature-card">
                    <span class="feature-index">02</span>
                    <div class="feature-icon"><i class="bi bi-stars" style="color: #fbbf24;"></i></div>
                    <div>
                        <h3 class="feature-title">Produk Pilihan</h3>
                        <p class="feature-desc">Pilihan kebutuhan harian dengan informasi harga yang jelas dan transparan.</p>
                    </div>
                    <i class="bi bi-arrow-up-right feature-arrow" aria-hidden="true"></i>
                </article>
                <article class="feature-card">
                    <span class="feature-index">03</span>
                    <div class="feature-icon"><i class="bi bi-credit-card-2-front" style="color: #82d5dd;"></i></div>
                    <div>
                        <h3 class="feature-title">Pembayaran Fleksibel</h3>
                        <p class="feature-desc">Mendukung tunai, QRIS, debit, kredit, e-wallet, dan transfer.</p>
                    </div>
                    <i class="bi bi-arrow-up-right feature-arrow" aria-hidden="true"></i>
                </article>
                <article class="feature-card">
                    <span class="feature-index">04</span>
                    <div class="feature-icon"><i class="bi bi-heart" style="color: #f28a92;"></i></div>
                    <div>
                        <h3 class="feature-title">Layanan Terpercaya</h3>
                        <p class="feature-desc">Kami siap membantu memberikan pengalaman belanja yang aman dan nyaman.</p>
                    </div>
                    <i class="bi bi-arrow-up-right feature-arrow" aria-hidden="true"></i>
                </article>
            </div>
        </div>
    </section>

    <!-- Scroll-driven product spread -->
    <section class="market-stack-section" id="showcase" aria-labelledby="market-stack-title">
        <div class="market-stack-stage">
            <div class="market-stack-heading">
                <div class="eyebrow">Pilihan untuk setiap hari</div>
                <h2 id="market-stack-title">Belanja mudah,<br><span>hidup lebih praktis.</span></h2>
                <p>Kebutuhan harian pilihan, harga jelas, dan pengalaman belanja yang nyaman untuk seluruh keluarga.</p>
            </div>

            <div class="market-stack-cards">
                <article class="market-stack-card" data-x="-35" data-y="-20" data-rotate="-8" data-w="16" data-h="22" data-sm-x="-21" data-sm-y="-27" data-sm-w="38" data-sm-h="16">
                    <div class="market-card-top"><span>01 / Segar</span><i class="bi bi-basket2-fill"></i></div>
                    <div class="market-card-art"><i class="bi bi-basket2-fill"></i></div>
                    <div class="market-card-bottom"><h3>Buah &amp; Sayur</h3><p>Segar setiap hari</p></div><span class="market-card-target"></span>
                </article>
                <article class="market-stack-card" data-x="-20" data-y="-20" data-rotate="5" data-w="15" data-h="23" data-sm-x="21" data-sm-y="-27" data-sm-w="38" data-sm-h="16">
                    <div class="market-card-top"><span>02 / Favorit</span><i class="bi bi-cup-hot-fill"></i></div>
                    <div class="market-card-art"><i class="bi bi-cup-hot-fill"></i></div>
                    <div class="market-card-bottom"><h3>Kopi &amp; Teh</h3><p>Teman waktu santai</p></div><span class="market-card-target"></span>
                </article>
                <article class="market-stack-card" data-x="20" data-y="-20" data-rotate="-4" data-w="17" data-h="22" data-sm-x="-21" data-sm-y="-9" data-sm-w="38" data-sm-h="16">
                    <div class="market-card-top"><span>03 / Pilihan</span><i class="bi bi-bag-heart-fill"></i></div>
                    <div class="market-card-art"><i class="bi bi-bag-heart-fill"></i></div>
                    <div class="market-card-bottom"><h3>Camilan</h3><p>Lezat untuk dinikmati</p></div><span class="market-card-target"></span>
                </article>
                <article class="market-stack-card" data-x="35" data-y="-20" data-rotate="8" data-w="15" data-h="23" data-sm-x="21" data-sm-y="-9" data-sm-w="38" data-sm-h="16">
                    <div class="market-card-top"><span>04 / Rumah</span><i class="bi bi-house-heart-fill"></i></div>
                    <div class="market-card-art"><i class="bi bi-house-heart-fill"></i></div>
                    <div class="market-card-bottom"><h3>Kebutuhan Rumah</h3><p>Lengkap dan praktis</p></div><span class="market-card-target"></span>
                </article>
                <article class="market-stack-card" data-x="35" data-y="20" data-rotate="-7" data-w="16" data-h="22" data-sm-x="-21" data-sm-y="11" data-sm-w="38" data-sm-h="16">
                    <div class="market-card-top"><span>05 / Perawatan</span><i class="bi bi-droplet-fill"></i></div>
                    <div class="market-card-art"><i class="bi bi-droplet-fill"></i></div>
                    <div class="market-card-bottom"><h3>Perawatan Diri</h3><p>Rawat diri setiap hari</p></div><span class="market-card-target"></span>
                </article>
                <article class="market-stack-card" data-x="20" data-y="20" data-rotate="5" data-w="18" data-h="23" data-sm-x="21" data-sm-y="11" data-sm-w="38" data-sm-h="16">
                    <div class="market-card-top"><span>06 / Dapur</span><i class="bi bi-egg-fried"></i></div>
                    <div class="market-card-art"><i class="bi bi-egg-fried"></i></div>
                    <div class="market-card-bottom"><h3>Bahan Dapur</h3><p>Siap untuk kreasi lezat</p></div><span class="market-card-target"></span>
                </article>
                <article class="market-stack-card" data-x="-20" data-y="20" data-rotate="-6" data-w="16" data-h="22" data-sm-x="-21" data-sm-y="31" data-sm-w="38" data-sm-h="16">
                    <div class="market-card-top"><span>07 / Andalan</span><i class="bi bi-box-seam-fill"></i></div>
                    <div class="market-card-art"><i class="bi bi-box-seam-fill"></i></div>
                    <div class="market-card-bottom"><h3>Produk Harian</h3><p>Semua dalam satu tempat</p></div><span class="market-card-target"></span>
                </article>
                <article class="market-stack-card" data-x="-35" data-y="20" data-rotate="7" data-w="16" data-h="22" data-sm-x="21" data-sm-y="31" data-sm-w="38" data-sm-h="16">
                    <div class="market-card-top"><span>08 / Hemat</span><i class="bi bi-tag-fill"></i></div>
                    <div class="market-card-art"><i class="bi bi-tag-fill"></i></div>
                    <div class="market-card-bottom"><h3>Harga Bersahabat</h3><p>Belanja tetap hemat</p></div><span class="market-card-target"></span>
                </article>
            </div>

            <div class="market-stack-hint" aria-hidden="true">
                <span>Gulir untuk melihat pilihan</span>
                <i class="bi bi-arrow-down"></i>
            </div>
            <div class="market-stack-progress" aria-hidden="true"><span></span></div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section">
        <div class="container">
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-number">24/7</div>
                    <div class="stat-label">Siap Melayani</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number" data-count="6" data-suffix="+" aria-label="6+">6+</div>
                    <div class="stat-label">Metode Pembayaran</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number" data-count="100" data-suffix="%" aria-label="100%">100%</div>
                    <div class="stat-label">Transaksi Tercatat</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number" data-count="1" data-suffix="" aria-label="1">1</div>
                    <div class="stat-label">Belanja Lebih Mudah</div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section" id="community">
        <div class="container">
            <div class="cta-box">
                <h2 class="cta-title">Siap berbelanja lebih mudah?</h2>
                <p class="cta-desc">
                    Masuk ke akun Anda untuk menikmati layanan {{ config('app.name', 'SABU JAYA MART') }}.
                </p>
                <a href="{{ route('register') }}" class="btn-hero-primary" style="position: relative; z-index: 1;">
                    <i class="bi bi-lightning-charge"></i>
                    Mulai Sekarang
                </a>
            </div>
        </div>
    </section>

    <footer class="site-footer">
        <div class="container footer-inner">
            <a href="{{ route('home') }}" class="footer-brand">
                <i class="bi bi-lightning-charge-fill" aria-hidden="true"></i>
                <span>{{ config('app.name', 'SABU JAYA MART') }}</span>
            </a>
            <nav class="footer-nav" aria-label="Navigasi footer">
                <a href="#features">Layanan</a>
                <a href="#showcase">Pilihan Produk</a>
                <a href="#community">Tentang Kami</a>
            </nav>
            <span class="footer-copy">&copy; {{ date('Y') }} {{ config('app.name', 'SABU JAYA MART') }}. Hak cipta dilindungi.</span>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @vite('resources/js/welcome-3d.js')
    <script>
        const navToggle = document.querySelector('.nav-toggle');
        const navLinks = document.querySelector('.nav-links');
        const navbar = document.querySelector('.navbar-modern');

        const setNavigationOpen = (isOpen) => {
            if (!navToggle || !navLinks) return;
            navLinks.classList.toggle('is-open', isOpen);
            document.body.classList.toggle('mobile-nav-active', isOpen);
            navToggle.setAttribute('aria-expanded', String(isOpen));
            navToggle.setAttribute('aria-label', isOpen ? 'Close navigation' : 'Open navigation');
            navToggle.querySelector('i').className = isOpen ? 'bi bi-x-lg' : 'bi bi-list';
        };

        if (navToggle && navLinks) {
            navToggle.addEventListener('click', () => {
                setNavigationOpen(navToggle.getAttribute('aria-expanded') !== 'true');
            });

            navLinks.querySelectorAll('a').forEach((link) => {
                link.addEventListener('click', () => setNavigationOpen(false));
            });

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') setNavigationOpen(false);
            });

            window.addEventListener('resize', () => {
                if (window.innerWidth > 1024) setNavigationOpen(false);
            });
        }

        const updateNavbar = () => {
            navbar?.classList.toggle('is-scrolled', window.scrollY > 40);
        };
        window.addEventListener('scroll', updateNavbar, { passive: true });
        updateNavbar();

        const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
        const heroBackgroundVideo = document.querySelector('.hero-video-background video');
        const heroVisual = document.querySelector('.hero-visual');
        const heroProducts = Array.from(document.querySelectorAll('.hero-product'));

        if (reducedMotion.matches) heroBackgroundVideo?.pause();

        const resetHeroTilt = () => {
            heroVisual?.style.removeProperty('--hero-tilt-x');
            heroVisual?.style.removeProperty('--hero-tilt-y');
            heroProducts.forEach((product) => {
                product.style.removeProperty('--hero-light-x');
                product.style.removeProperty('--hero-light-y');
            });
        };

        if (heroVisual && window.matchMedia('(pointer: fine)').matches && !reducedMotion.matches) {
            heroVisual.addEventListener('pointermove', (event) => {
                const bounds = heroVisual.getBoundingClientRect();
                const x = (event.clientX - bounds.left) / bounds.width;
                const y = (event.clientY - bounds.top) / bounds.height;

                heroVisual.style.setProperty('--hero-tilt-x', `${(x - .5) * 7}deg`);
                heroVisual.style.setProperty('--hero-tilt-y', `${(.5 - y) * 6}deg`);
                heroProducts.forEach((product) => {
                    product.style.setProperty('--hero-light-x', `${x * 100}%`);
                    product.style.setProperty('--hero-light-y', `${y * 100}%`);
                });
            }, { passive: true });

            heroVisual.addEventListener('pointerleave', resetHeroTilt);
        }

        reducedMotion.addEventListener('change', () => {
            if (reducedMotion.matches) {
                resetHeroTilt();
                heroBackgroundVideo?.pause();
            } else if (heroBackgroundVideo) {
                heroBackgroundVideo.play().catch((error) => {
                    console.warn('Welcome background video could not resume playback.', error);
                });
            }
        });

        document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
            anchor.addEventListener('click', (event) => {
                const hash = anchor.getAttribute('href');
                if (!hash || hash === '#') return;
                const target = document.getElementById(hash.slice(1));
                if (!target) return;
                event.preventDefault();
                target.scrollIntoView({ behavior: reducedMotion.matches ? 'auto' : 'smooth', block: 'start' });
            });
        });

        const flightJourney = document.querySelector('.flight-journey');

        if (reducedMotion.matches) {
            document.querySelector('.flight-journey')?.remove();
        }

        if (flightJourney && !reducedMotion.matches) {
            const flightStops = [
                { progress: 0, x: 74, y: 31, angle: -12 },
                { progress: 0.34, x: 32, y: 59, angle: 8 },
                { progress: 0.68, x: 72, y: 39, angle: -16 },
                { progress: 1, x: 35, y: 68, angle: 7 },
            ];
            let flightFrame = 0;

            const updateFlightPosition = () => {
                const maxScroll = document.documentElement.scrollHeight - window.innerHeight;
                const progress = maxScroll > 0 ? window.scrollY / maxScroll : 0;
                const nextStop = flightStops.findIndex((stop) => stop.progress >= progress);
                const endIndex = nextStop === -1 ? flightStops.length - 1 : nextStop;
                const start = flightStops[Math.max(0, endIndex - 1)];
                const end = flightStops[endIndex];
                const segment = end.progress === start.progress
                    ? 0
                    : (progress - start.progress) / (end.progress - start.progress);
                const ease = segment * segment * (3 - 2 * segment);
                const interpolate = (key) => start[key] + (end[key] - start[key]) * ease;

                flightJourney.style.setProperty('--flight-x', `${interpolate('x')}%`);
                flightJourney.style.setProperty('--flight-y', `${interpolate('y')}%`);
                flightJourney.style.setProperty('--flight-angle', `${interpolate('angle')}deg`);
                flightFrame = 0;
            };

            window.addEventListener('scroll', () => {
                if (!flightFrame) {
                    flightFrame = window.requestAnimationFrame(updateFlightPosition);
                }
            }, { passive: true });

            updateFlightPosition();
        }

        if (!reducedMotion.matches && 'IntersectionObserver' in window) {
            document.body.classList.add('motion-ready');

            const revealObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                        const counter = entry.target.querySelector('[data-count]');
                        if (counter && !counter.dataset.animated) {
                            counter.dataset.animated = 'true';
                            const end = Number(counter.dataset.count);
                            const suffix = counter.dataset.suffix || '';
                            const startTime = performance.now();
                            const duration = 900;
                            counter.textContent = `0${suffix}`;

                            const animateCounter = (now) => {
                                const progress = Math.min((now - startTime) / duration, 1);
                                const easedProgress = 1 - Math.pow(1 - progress, 3);
                                counter.textContent = `${Math.round(end * easedProgress)}${suffix}`;
                                if (progress < 1) window.requestAnimationFrame(animateCounter);
                            };

                            window.requestAnimationFrame(animateCounter);
                        }
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.15 });

            document.querySelectorAll('.feature-card, .stat-item, .cta-box, .story-reveal').forEach((element) => {
                revealObserver.observe(element);
            });
        }

        const stackSection = document.querySelector('.market-stack-section');
        const stackStage = document.querySelector('.market-stack-stage');
        const stackCards = Array.from(document.querySelectorAll('.market-stack-card'));
        const stackHeading = document.querySelector('.market-stack-heading');
        const stackHint = document.querySelector('.market-stack-hint');
        const stackProgress = document.querySelector('.market-stack-progress span');
        const smallStack = window.matchMedia('(max-width: 767px)');
        let stackFrame = 0;
        let pointerX = 0;
        let pointerY = 0;

        const clamp = (value, min, max) => Math.min(max, Math.max(min, value));
        const mix = (from, to, amount) => from + (to - from) * amount;
        const smoothStep = (value) => value * value * (3 - 2 * value);

        const updateStack = () => {
            if (!stackSection || !stackStage || reducedMotion.matches) {
                stackFrame = 0;
                return;
            }

            const scrollRange = Math.max(1, stackSection.offsetHeight - window.innerHeight);
            const progress = clamp(-stackSection.getBoundingClientRect().top / scrollRange, 0, 1);
            const scatterProgress = smoothStep(clamp((progress - 0.12) / 0.76, 0, 1));
            const copyProgress = smoothStep(clamp((progress - 0.27) / 0.38, 0, 1));
            const compact = smallStack.matches;
            const width = window.innerWidth;
            const height = window.innerHeight;

            stackCards.forEach((card, index) => {
                const styles = window.getComputedStyle(card);
                const startX = Number.parseFloat(styles.getPropertyValue('--stack-x')) || 0;
                const startY = Number.parseFloat(styles.getPropertyValue('--stack-y')) || 0;
                const startRotate = Number.parseFloat(styles.getPropertyValue('--stack-rotate')) || 0;
                const endX = Number(card.dataset[compact ? 'smX' : 'x']);
                const endY = Number(card.dataset[compact ? 'smY' : 'y']);
                const endRotate = compact ? 0 : Number(card.dataset.rotate);
                const endWidth = compact
                    ? width * Number(card.dataset.smW) / 100
                    : clamp(width * Number(card.dataset.w) / 100, 170, 290);
                const endHeight = compact
                    ? height * Number(card.dataset.smH) / 100
                    : clamp(height * Number(card.dataset.h) / 100, 205, 300);
                const pileWidth = compact ? Math.min(width * 0.48, 210) : clamp(width * 0.21, 170, 290);
                const pileHeight = compact ? Math.min(height * 0.21, 190) : clamp(height * 0.3, 205, 300);
                const depth = scatterProgress * (index / Math.max(1, stackCards.length - 1));
                const parallaxX = compact ? 0 : pointerX * 1.7 * depth;
                const parallaxY = compact ? 0 : pointerY * 1.4 * depth;
                const tiltX = compact ? 0 : pointerY * -5 * depth;
                const tiltY = compact ? 0 : pointerX * 7 * depth;
                const x = mix(startX, endX, scatterProgress) - parallaxX;
                const y = mix(startY, endY, scatterProgress) - parallaxY;
                const rotation = mix(startRotate, endRotate, scatterProgress);
                const scale = mix(0.84, 1, scatterProgress);

                card.style.width = `${mix(pileWidth, endWidth, scatterProgress)}px`;
                card.style.height = `${mix(pileHeight, endHeight, scatterProgress)}px`;
                card.style.zIndex = String(index + 1);
                card.style.setProperty('--stack-light-x', `${50 + pointerX * 32}%`);
                card.style.setProperty('--stack-light-y', `${20 + pointerY * 28}%`);
                card.style.transform = `translate3d(calc(-50% + ${x}vw), calc(-50% + ${y}vh), ${depth * 125}px) rotateX(${tiltX}deg) rotateY(${tiltY}deg) rotateZ(${rotation}deg) scale(${scale})`;
            });

            if (stackHeading) {
                stackHeading.style.opacity = String(copyProgress);
                stackHeading.style.transform = `translate(-50%, calc(-50% + ${mix(12, 0, copyProgress)}px)) scale(${mix(0.94, 1, copyProgress)})`;
            }
            if (stackHint) stackHint.style.opacity = String(1 - clamp(progress / 0.18, 0, 1));
            if (stackProgress) stackProgress.style.transform = `scaleX(${progress})`;

            stackFrame = 0;
        };

        const requestStackUpdate = () => {
            if (!stackFrame && !reducedMotion.matches) {
                stackFrame = window.requestAnimationFrame(updateStack);
            }
        };

        if (stackSection && stackCards.length) {
            updateStack();
            window.addEventListener('scroll', requestStackUpdate, { passive: true });
            window.addEventListener('resize', requestStackUpdate);
            smallStack.addEventListener('change', requestStackUpdate);
            reducedMotion.addEventListener('change', () => {
                if (reducedMotion.matches) {
                    stackCards.forEach((card) => {
                        card.removeAttribute('style');
                    });
                    stackHeading?.removeAttribute('style');
                    stackHint?.removeAttribute('style');
                    stackProgress?.removeAttribute('style');
                } else {
                    updateStack();
                }
            });

            if (window.matchMedia('(pointer: fine)').matches && !reducedMotion.matches) {
                window.addEventListener('pointermove', (event) => {
                    pointerX = (event.clientX / window.innerWidth) * 2 - 1;
                    pointerY = (event.clientY / window.innerHeight) * 2 - 1;
                    requestStackUpdate();
                }, { passive: true });
                document.addEventListener('pointerleave', () => {
                    pointerX = 0;
                    pointerY = 0;
                    requestStackUpdate();
                });
            }
        }
    </script>
</body>
</html>