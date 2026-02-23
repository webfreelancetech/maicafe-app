<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Mai Cafe')</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f5f1eb;
            color: #3d2817;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* ── Header ── */
        .legal-header {
            background: #fff;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .legal-header-inner {
            max-width: 860px;
            margin: 0 auto;
            padding: 18px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .legal-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            color: #8b6f47;
            font-size: 22px;
            font-weight: 800;
        }
        .legal-logo i {
            font-size: 20px;
        }
        .legal-back-btn {
            display: flex;
            align-items: center;
            gap: 7px;
            text-decoration: none;
            color: #8b6f47;
            font-size: 14px;
            font-weight: 500;
            padding: 8px 16px;
            border: 1.5px solid #8b6f47;
            border-radius: 8px;
            transition: all 0.2s;
        }
        .legal-back-btn:hover {
            background: #8b6f47;
            color: #fff;
        }

        /* ── Page Hero ── */
        .legal-hero {
            background: linear-gradient(135deg, #8b6f47 0%, #6b5233 100%);
            color: #fff;
            padding: 48px 20px 40px;
            text-align: center;
        }
        .legal-hero-icon {
            width: 64px;
            height: 64px;
            background: rgba(255,255,255,0.15);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
            font-size: 28px;
        }
        .legal-hero h1 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
        }
        .legal-hero p {
            font-size: 14px;
            opacity: 0.85;
            max-width: 500px;
            margin: 0 auto;
            line-height: 1.6;
        }
        .legal-updated {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-top: 14px;
            background: rgba(255,255,255,0.15);
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }

        /* ── Content ── */
        .legal-body {
            flex: 1;
            max-width: 860px;
            margin: 0 auto;
            width: 100%;
            padding: 36px 20px;
        }

        /* Table of contents */
        .toc {
            background: #fff;
            border-radius: 12px;
            padding: 24px 28px;
            margin-bottom: 32px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            border-left: 4px solid #8b6f47;
        }
        .toc h3 {
            font-size: 14px;
            font-weight: 600;
            color: #8b6f47;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 14px;
        }
        .toc ol {
            padding-left: 18px;
        }
        .toc ol li {
            margin-bottom: 6px;
        }
        .toc ol li a {
            color: #3d2817;
            text-decoration: none;
            font-size: 14px;
            transition: color 0.2s;
        }
        .toc ol li a:hover {
            color: #8b6f47;
        }

        /* Sections */
        .legal-section {
            background: #fff;
            border-radius: 12px;
            padding: 28px 28px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        .legal-section-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 16px;
            padding-bottom: 14px;
            border-bottom: 1px solid #f0ebe3;
        }
        .section-num {
            width: 32px;
            height: 32px;
            background: linear-gradient(135deg, #8b6f47, #6b5233);
            color: #fff;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 700;
            flex-shrink: 0;
        }
        .legal-section h2 {
            font-size: 17px;
            font-weight: 600;
            color: #3d2817;
        }
        .legal-section p {
            font-size: 14px;
            line-height: 1.8;
            color: #5a4030;
            margin-bottom: 12px;
        }
        .legal-section p:last-child {
            margin-bottom: 0;
        }
        .legal-section ul,
        .legal-section ol {
            padding-left: 20px;
            margin-bottom: 12px;
        }
        .legal-section ul li,
        .legal-section ol li {
            font-size: 14px;
            line-height: 1.8;
            color: #5a4030;
            margin-bottom: 4px;
        }
        .legal-section strong {
            color: #3d2817;
            font-weight: 600;
        }
        .legal-section a {
            color: #8b6f47;
            text-decoration: none;
            font-weight: 500;
        }
        .legal-section a:hover {
            text-decoration: underline;
        }

        /* Highlight box */
        .info-box {
            background: #fdf8f2;
            border: 1px solid #e8d9c5;
            border-radius: 10px;
            padding: 16px 18px;
            margin: 14px 0;
            display: flex;
            gap: 12px;
            align-items: flex-start;
        }
        .info-box i {
            color: #8b6f47;
            margin-top: 2px;
            flex-shrink: 0;
        }
        .info-box p {
            margin: 0;
            font-size: 13.5px;
        }

        /* ── Footer ── */
        .legal-footer {
            background: #3d2817;
            color: #c4a882;
            padding: 28px 20px;
            margin-top: auto;
        }
        .legal-footer-inner {
            max-width: 860px;
            margin: 0 auto;
            text-align: center;
        }
        .legal-footer-links {
            display: flex;
            justify-content: center;
            gap: 24px;
            flex-wrap: wrap;
            margin-bottom: 16px;
        }
        .legal-footer-links a {
            color: #c4a882;
            text-decoration: none;
            font-size: 13px;
            transition: color 0.2s;
        }
        .legal-footer-links a:hover,
        .legal-footer-links a.active {
            color: #fff;
        }
        .legal-footer p {
            font-size: 12px;
            opacity: 0.7;
        }

        @media (max-width: 600px) {
            .legal-hero h1 { font-size: 22px; }
            .legal-section { padding: 20px 16px; }
            .toc { padding: 18px 16px; }
            .legal-header-inner { padding: 14px 16px; }
            .legal-logo span { display: none; }
        }
    </style>
</head>
<body>

    <!-- Header -->
    <header class="legal-header">
        <div class="legal-header-inner">
            <a href="{{ route('home') }}" class="legal-logo">
                <i class="fas fa-utensils"></i>
                <span>Mai Cafe</span>
            </a>
            <a href="{{ route('home') }}" class="legal-back-btn">
                <i class="fas fa-arrow-left"></i> Back to Home
            </a>
        </div>
    </header>

    <!-- Hero -->
    <div class="legal-hero">
        <div class="legal-hero-icon">
            <i class="fas @yield('hero-icon', 'fa-file-alt')"></i>
        </div>
        <h1>@yield('page-title')</h1>
        <p>@yield('page-subtitle')</p>
        <div class="legal-updated">
            <i class="fas fa-calendar-alt"></i>
            Last updated: @yield('last-updated')
        </div>
    </div>

    <!-- Body -->
    <main class="legal-body">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="legal-footer">
        <div class="legal-footer-inner">
            <div class="legal-footer-links">
                <a href="{{ route('home') }}"><i class="fas fa-home"></i> Home</a>
                <a href="{{ route('privacy-policy') }}" class="{{ request()->routeIs('privacy-policy') ? 'active' : '' }}">
                    <i class="fas fa-shield-alt"></i> Privacy Policy
                </a>
                <a href="{{ route('terms-and-conditions') }}" class="{{ request()->routeIs('terms-and-conditions') ? 'active' : '' }}">
                    <i class="fas fa-file-contract"></i> Terms & Conditions
                </a>
            </div>
            <p>&copy; {{ date('Y') }} Mai Cafe. All rights reserved.</p>
        </div>
    </footer>

</body>
</html>
