@extends('layouts.legal')

@section('title', 'Privacy Policy — Mai Cafe')
@section('hero-icon', 'fa-shield-alt')
@section('page-title', 'Privacy Policy')
@section('page-subtitle', 'We are committed to protecting your personal information and being transparent about how we use it.')
@section('last-updated', 'February 2026')

@section('content')

    <!-- Table of Contents -->
    <div class="toc">
        <h3><i class="fas fa-list"></i> &nbsp;Contents</h3>
        <ol>
            <li><a href="#section-1">Information We Collect</a></li>
            <li><a href="#section-2">How We Use Your Information</a></li>
            <li><a href="#section-3">How We Share Your Information</a></li>
            <li><a href="#section-4">Data Retention</a></li>
            <li><a href="#section-5">Your Rights</a></li>
            <li><a href="#section-6">Cookies & Tracking</a></li>
            <li><a href="#section-7">Payment Information</a></li>
            <li><a href="#section-8">Children's Privacy</a></li>
            <li><a href="#section-9">Changes to This Policy</a></li>
            <li><a href="#section-10">Contact Us</a></li>
        </ol>
    </div>

    <div class="info-box">
        <i class="fas fa-info-circle"></i>
        <p>This Privacy Policy describes how <strong>Mai Cafe</strong> ("we", "us", or "our") collects, uses, and shares your personal information when you use our mobile app or website. Please read it carefully before using our services.</p>
    </div>

    <!-- Section 1 -->
    <div class="legal-section" id="section-1">
        <div class="legal-section-header">
            <div class="section-num">1</div>
            <h2>Information We Collect</h2>
        </div>
        <p>We collect information you provide directly to us as well as information generated automatically when you use our services.</p>
        <p><strong>Information you provide:</strong></p>
        <ul>
            <li><strong>Account details</strong> — name, email address, phone number, and password when you register</li>
            <li><strong>Order information</strong> — items ordered, delivery address, order preferences, and special instructions</li>
            <li><strong>Payment details</strong> — processed securely through our payment provider; we do not store full card numbers</li>
            <li><strong>Profile information</strong> — profile photo and any updates you make to your account</li>
            <li><strong>Communications</strong> — messages or feedback you send us via email or support channels</li>
        </ul>
        <p><strong>Information collected automatically:</strong></p>
        <ul>
            <li><strong>Device information</strong> — device type, operating system, and app version</li>
            <li><strong>Usage data</strong> — pages viewed, features used, time spent in the app, and tap/click interactions</li>
            <li><strong>Location data</strong> — only when you grant permission, used to suggest nearby stores</li>
            <li><strong>Log data</strong> — IP address, access times, and error reports</li>
        </ul>
    </div>

    <!-- Section 2 -->
    <div class="legal-section" id="section-2">
        <div class="legal-section-header">
            <div class="section-num">2</div>
            <h2>How We Use Your Information</h2>
        </div>
        <p>We use the information we collect to:</p>
        <ul>
            <li>Create and manage your account</li>
            <li>Process and fulfil your orders</li>
            <li>Send order confirmations, status updates, and receipts</li>
            <li>Send OTP verification codes during registration or password reset</li>
            <li>Provide customer support and respond to your enquiries</li>
            <li>Personalise your experience, including product recommendations</li>
            <li>Administer loyalty points and rewards</li>
            <li>Improve and develop our app and services</li>
            <li>Detect fraud and ensure the security of our platform</li>
            <li>Comply with legal obligations</li>
        </ul>
        <p>We will only send you marketing communications if you have opted in, and you may opt out at any time by contacting us or updating your notification preferences in the app.</p>
    </div>

    <!-- Section 3 -->
    <div class="legal-section" id="section-3">
        <div class="legal-section-header">
            <div class="section-num">3</div>
            <h2>How We Share Your Information</h2>
        </div>
        <p>We do not sell your personal information. We may share it in the following limited circumstances:</p>
        <ul>
            <li><strong>Service providers</strong> — third-party companies that help us operate our platform (e.g., payment processors, email service providers, cloud hosting). These providers are contractually obligated to protect your data.</li>
            <li><strong>Order fulfilment</strong> — we share your name, contact number, and delivery address with the relevant store or delivery partner to complete your order.</li>
            <li><strong>Legal requirements</strong> — when required by law, court order, or governmental authority.</li>
            <li><strong>Business transfers</strong> — in the event of a merger, acquisition, or sale of assets, your data may be transferred as part of that transaction.</li>
        </ul>
        <div class="info-box">
            <i class="fas fa-lock"></i>
            <p>We never sell, rent, or trade your personal information to third parties for their own marketing purposes.</p>
        </div>
    </div>

    <!-- Section 4 -->
    <div class="legal-section" id="section-4">
        <div class="legal-section-header">
            <div class="section-num">4</div>
            <h2>Data Retention</h2>
        </div>
        <p>We retain your personal information for as long as your account is active or as needed to provide you with our services. If you delete your account:</p>
        <ul>
            <li>Your account is immediately deactivated and you will be logged out of all devices</li>
            <li>Your profile data is retained in our systems in a deactivated state to comply with legal and financial record-keeping obligations</li>
            <li>You may re-register using the same email address at any time</li>
            <li>Order history associated with your account may be retained for up to 7 years for tax and audit purposes</li>
        </ul>
        <p>If you would like to request complete erasure of your data, please contact us at the details in Section 10.</p>
    </div>

    <!-- Section 5 -->
    <div class="legal-section" id="section-5">
        <div class="legal-section-header">
            <div class="section-num">5</div>
            <h2>Your Rights</h2>
        </div>
        <p>Depending on your location, you may have the following rights regarding your personal data:</p>
        <ul>
            <li><strong>Access</strong> — request a copy of the personal data we hold about you</li>
            <li><strong>Correction</strong> — request correction of inaccurate or incomplete data</li>
            <li><strong>Erasure</strong> — request deletion of your personal data (subject to legal obligations)</li>
            <li><strong>Restriction</strong> — request that we limit processing of your data in certain circumstances</li>
            <li><strong>Portability</strong> — request a copy of your data in a machine-readable format</li>
            <li><strong>Objection</strong> — object to processing of your data for direct marketing</li>
            <li><strong>Withdraw consent</strong> — where processing is based on your consent, withdraw it at any time</li>
        </ul>
        <p>To exercise any of these rights, please contact us using the details in Section 10. We will respond within 30 days.</p>
    </div>

    <!-- Section 6 -->
    <div class="legal-section" id="section-6">
        <div class="legal-section-header">
            <div class="section-num">6</div>
            <h2>Cookies & Tracking</h2>
        </div>
        <p>Our website uses cookies and similar tracking technologies to improve your browsing experience. These include:</p>
        <ul>
            <li><strong>Essential cookies</strong> — necessary for the site to function (e.g., session management)</li>
            <li><strong>Analytics cookies</strong> — help us understand how visitors use our site so we can improve it</li>
            <li><strong>Preference cookies</strong> — remember your settings and preferences</li>
        </ul>
        <p>Our mobile app does not use browser cookies, but may use local storage and device identifiers for session management and cart persistence.</p>
        <p>You can control cookie settings through your browser settings. Disabling essential cookies may affect the functionality of our website.</p>
    </div>

    <!-- Section 7 -->
    <div class="legal-section" id="section-7">
        <div class="legal-section-header">
            <div class="section-num">7</div>
            <h2>Payment Information</h2>
        </div>
        <p>All payment transactions are processed securely by our payment provider, <strong>Shift4</strong>. We use client-side tokenisation, which means your full card details are sent directly from your device to Shift4's servers and are never stored on our systems.</p>
        <p>We store only:</p>
        <ul>
            <li>A transaction reference ID provided by Shift4</li>
            <li>The last four digits of your card (for display purposes only)</li>
            <li>Payment status (success, failed, pending)</li>
        </ul>
        <p>Shift4 is PCI DSS Level 1 certified. For details on how Shift4 handles your data, please review their privacy policy at <a href="https://www.shift4.com/privacy-policy" target="_blank" rel="noopener noreferrer">shift4.com/privacy-policy</a>.</p>
    </div>

    <!-- Section 8 -->
    <div class="legal-section" id="section-8">
        <div class="legal-section-header">
            <div class="section-num">8</div>
            <h2>Children's Privacy</h2>
        </div>
        <p>Our services are not directed to children under the age of 13. We do not knowingly collect personal information from children. If you believe we have collected information from a child, please contact us immediately and we will delete it promptly.</p>
    </div>

    <!-- Section 9 -->
    <div class="legal-section" id="section-9">
        <div class="legal-section-header">
            <div class="section-num">9</div>
            <h2>Changes to This Policy</h2>
        </div>
        <p>We may update this Privacy Policy from time to time to reflect changes in our practices or for legal, regulatory, or operational reasons. When we make material changes, we will:</p>
        <ul>
            <li>Update the "Last updated" date at the top of this page</li>
            <li>Notify you via email or an in-app notification if the changes are significant</li>
        </ul>
        <p>We encourage you to review this policy periodically. Your continued use of our services after any changes constitutes acceptance of the updated policy.</p>
    </div>

    <!-- Section 10 -->
    <div class="legal-section" id="section-10">
        <div class="legal-section-header">
            <div class="section-num">10</div>
            <h2>Contact Us</h2>
        </div>
        <p>If you have any questions, concerns, or requests regarding this Privacy Policy or how we handle your personal data, please contact us:</p>
        <ul>
            <li><strong>Email:</strong> <a href="mailto:privacy@maicafe.com">privacy@maicafe.com</a></li>
            <li><strong>Address:</strong> Mai Cafe, [Your Business Address]</li>
            <li><strong>Phone:</strong> [Your Contact Number]</li>
        </ul>
        <p>We aim to respond to all privacy-related enquiries within 30 days.</p>
    </div>

@endsection
