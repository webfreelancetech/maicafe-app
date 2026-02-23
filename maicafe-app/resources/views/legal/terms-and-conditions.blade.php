@extends('layouts.legal')

@section('title', 'Terms & Conditions — Mai Cafe')
@section('hero-icon', 'fa-file-contract')
@section('page-title', 'Terms & Conditions')
@section('page-subtitle', 'Please read these terms carefully before using our app or placing an order with us.')
@section('last-updated', 'February 2026')

@section('content')

    <!-- Table of Contents -->
    <div class="toc">
        <h3><i class="fas fa-list"></i> &nbsp;Contents</h3>
        <ol>
            <li><a href="#section-1">Acceptance of Terms</a></li>
            <li><a href="#section-2">Use of Our Services</a></li>
            <li><a href="#section-3">Account Registration</a></li>
            <li><a href="#section-4">Ordering & Payment</a></li>
            <li><a href="#section-5">Cancellations & Refunds</a></li>
            <li><a href="#section-6">Loyalty Points & Rewards</a></li>
            <li><a href="#section-7">Intellectual Property</a></li>
            <li><a href="#section-8">Prohibited Conduct</a></li>
            <li><a href="#section-9">Limitation of Liability</a></li>
            <li><a href="#section-10">Governing Law</a></li>
            <li><a href="#section-11">Changes to Terms</a></li>
            <li><a href="#section-12">Contact Us</a></li>
        </ol>
    </div>

    <div class="info-box">
        <i class="fas fa-info-circle"></i>
        <p>These Terms & Conditions ("Terms") govern your use of the Mai Cafe mobile application and website (collectively, "Services"). By accessing or using our Services, you agree to be bound by these Terms. If you do not agree, please do not use our Services.</p>
    </div>

    <!-- Section 1 -->
    <div class="legal-section" id="section-1">
        <div class="legal-section-header">
            <div class="section-num">1</div>
            <h2>Acceptance of Terms</h2>
        </div>
        <p>By downloading, installing, or using the Mai Cafe app or website, you confirm that you:</p>
        <ul>
            <li>Are at least 18 years of age, or have the consent of a parent or legal guardian</li>
            <li>Have read and understood these Terms and our <a href="{{ route('privacy-policy') }}">Privacy Policy</a></li>
            <li>Agree to comply with these Terms in full</li>
        </ul>
        <p>If you are using our Services on behalf of a business or organisation, you confirm that you have authority to bind that entity to these Terms.</p>
    </div>

    <!-- Section 2 -->
    <div class="legal-section" id="section-2">
        <div class="legal-section-header">
            <div class="section-num">2</div>
            <h2>Use of Our Services</h2>
        </div>
        <p>Mai Cafe provides an online ordering platform that allows customers to browse our menu, place food and beverage orders, and track their order status. Our Services are available for:</p>
        <ul>
            <li><strong>Pickup orders</strong> — collect your order from any of our store locations</li>
            <li><strong>Delivery orders</strong> — have your order delivered to a specified address (where available)</li>
            <li><strong>Pay-at-counter orders</strong> — place your order in advance and pay at the counter upon arrival</li>
        </ul>
        <p>We reserve the right to modify, suspend, or discontinue any part of our Services at any time without prior notice. We will not be liable to you or any third party for any such modification, suspension, or discontinuation.</p>
    </div>

    <!-- Section 3 -->
    <div class="legal-section" id="section-3">
        <div class="legal-section-header">
            <div class="section-num">3</div>
            <h2>Account Registration</h2>
        </div>
        <p>To place an order, you must create an account with Mai Cafe. When registering, you agree to:</p>
        <ul>
            <li>Provide accurate, current, and complete information</li>
            <li>Verify your email address via the one-time password (OTP) sent to you</li>
            <li>Keep your password secure and not share it with others</li>
            <li>Notify us immediately of any unauthorised use of your account</li>
            <li>Be responsible for all activity that occurs under your account</li>
        </ul>
        <p>We reserve the right to suspend or terminate accounts that violate these Terms, contain false information, or are used for fraudulent purposes.</p>
        <div class="info-box">
            <i class="fas fa-user-times"></i>
            <p><strong>Account deletion:</strong> You may delete your account at any time from the app's settings. Account deletion is a soft-delete — your data is retained for legal and financial compliance purposes but your account will be fully deactivated. You can re-register with the same email address at any time.</p>
        </div>
    </div>

    <!-- Section 4 -->
    <div class="legal-section" id="section-4">
        <div class="legal-section-header">
            <div class="section-num">4</div>
            <h2>Ordering & Payment</h2>
        </div>
        <p><strong>Placing an order</strong></p>
        <p>When you place an order through our app, you are making an offer to purchase the selected items at the listed prices. An order is only confirmed once you receive a confirmation notification and your payment has been successfully processed.</p>

        <p><strong>Pricing</strong></p>
        <ul>
            <li>All prices are displayed inclusive of applicable taxes</li>
            <li>Delivery charges (if applicable) are calculated and displayed at checkout before payment</li>
            <li>We reserve the right to update prices at any time without prior notice</li>
            <li>In the event of a pricing error, we will notify you and you may choose to proceed at the correct price or cancel the order</li>
        </ul>

        <p><strong>Payment methods</strong></p>
        <ul>
            <li><strong>Online payment</strong> — secured by Shift4, a PCI DSS compliant payment processor</li>
            <li><strong>Pay at counter</strong> — payment collected in-store by our staff</li>
        </ul>

        <p><strong>Payment security</strong></p>
        <p>All online payments are processed securely through Shift4. Your card details are tokenised client-side and are never stored on our servers. We store only a transaction reference for reconciliation purposes.</p>

        <p><strong>Failed payments</strong></p>
        <p>If your payment fails, your order will remain in a pending state. You may retry payment or cancel the order. We will not charge you for failed transactions. If a charge was made in error, please contact us immediately.</p>
    </div>

    <!-- Section 5 -->
    <div class="legal-section" id="section-5">
        <div class="legal-section-header">
            <div class="section-num">5</div>
            <h2>Cancellations & Refunds</h2>
        </div>
        <p><strong>Cancellations</strong></p>
        <ul>
            <li>You may cancel an order only while it is in <strong>Pending</strong> or <strong>Awaiting Payment</strong> status</li>
            <li>Once an order moves to <strong>Confirmed</strong> or <strong>Preparing</strong> status, it cannot be cancelled as preparation has begun</li>
            <li>To cancel, use the "Cancel Order" option in the order detail screen of the app</li>
        </ul>

        <p><strong>Refunds</strong></p>
        <ul>
            <li>Refunds for cancelled orders will be processed to the original payment method within 5–10 business days, depending on your bank</li>
            <li>If you received an incorrect or unsatisfactory order, please contact us within 24 hours with details and photos where possible</li>
            <li>Refunds are issued at our discretion and we may offer a replacement, store credit, or full/partial refund depending on the circumstances</li>
            <li>We do not issue refunds for change of mind after an order has been confirmed</li>
        </ul>

        <div class="info-box">
            <i class="fas fa-clock"></i>
            <p>For urgent order issues, please contact the store directly or reach us via email. We aim to resolve all complaints within 48 hours.</p>
        </div>
    </div>

    <!-- Section 6 -->
    <div class="legal-section" id="section-6">
        <div class="legal-section-header">
            <div class="section-num">6</div>
            <h2>Loyalty Points & Rewards</h2>
        </div>
        <p>Mai Cafe operates a loyalty programme that rewards customers for completed orders. The following terms apply:</p>
        <ul>
            <li>Loyalty points are awarded only on successfully completed and paid orders</li>
            <li>Points have no cash value and cannot be transferred, sold, or exchanged for cash</li>
            <li>Points may be forfeited if your account is inactive for more than 12 months</li>
            <li>We reserve the right to modify, suspend, or discontinue the loyalty programme at any time</li>
            <li>Tier status (Bronze, Silver, Gold) is determined by your cumulative spend over a rolling 12-month period</li>
            <li>Fraudulent accumulation of loyalty points will result in immediate account termination</li>
        </ul>
    </div>

    <!-- Section 7 -->
    <div class="legal-section" id="section-7">
        <div class="legal-section-header">
            <div class="section-num">7</div>
            <h2>Intellectual Property</h2>
        </div>
        <p>All content on the Mai Cafe platform — including the logo, branding, product images, menu descriptions, app design, and written content — is the property of Mai Cafe or its licensors and is protected by applicable intellectual property laws.</p>
        <p>You may not:</p>
        <ul>
            <li>Copy, reproduce, modify, or distribute any of our content without prior written consent</li>
            <li>Use our brand name, logo, or trademarks in any way that could cause confusion or damage to our reputation</li>
            <li>Reverse engineer, decompile, or extract the source code of our mobile application</li>
        </ul>
    </div>

    <!-- Section 8 -->
    <div class="legal-section" id="section-8">
        <div class="legal-section-header">
            <div class="section-num">8</div>
            <h2>Prohibited Conduct</h2>
        </div>
        <p>You agree not to use our Services to:</p>
        <ul>
            <li>Place fraudulent or fake orders</li>
            <li>Attempt to gain unauthorised access to our systems or other users' accounts</li>
            <li>Scrape, crawl, or extract data from our platform using automated tools</li>
            <li>Submit false information, including fake reviews or complaints</li>
            <li>Engage in any activity that disrupts or interferes with our Services</li>
            <li>Use our platform for any unlawful purpose</li>
            <li>Abuse our refund or cancellation policy</li>
        </ul>
        <p>Violation of these prohibitions may result in immediate account suspension, termination, and/or legal action.</p>
    </div>

    <!-- Section 9 -->
    <div class="legal-section" id="section-9">
        <div class="legal-section-header">
            <div class="section-num">9</div>
            <h2>Limitation of Liability</h2>
        </div>
        <p>To the fullest extent permitted by law:</p>
        <ul>
            <li>Our Services are provided "as is" and "as available" without warranties of any kind, express or implied</li>
            <li>We do not guarantee that the app will be available at all times or free from errors</li>
            <li>We are not liable for any indirect, incidental, special, or consequential damages arising from your use of our Services</li>
            <li>Our total liability to you for any claim arising from your use of our Services shall not exceed the value of the order to which the claim relates</li>
        </ul>
        <p>Nothing in these Terms limits our liability for death or personal injury caused by our negligence, fraud, or any other liability that cannot be excluded by law.</p>
    </div>

    <!-- Section 10 -->
    <div class="legal-section" id="section-10">
        <div class="legal-section-header">
            <div class="section-num">10</div>
            <h2>Governing Law</h2>
        </div>
        <p>These Terms are governed by and construed in accordance with the laws of <strong>[Your Jurisdiction]</strong>. Any disputes arising from or relating to these Terms or your use of our Services shall be subject to the exclusive jurisdiction of the courts of <strong>[Your Jurisdiction]</strong>.</p>
        <p>If any provision of these Terms is found to be invalid or unenforceable, the remaining provisions will continue in full force and effect.</p>
    </div>

    <!-- Section 11 -->
    <div class="legal-section" id="section-11">
        <div class="legal-section-header">
            <div class="section-num">11</div>
            <h2>Changes to Terms</h2>
        </div>
        <p>We reserve the right to update these Terms at any time. When we do, we will:</p>
        <ul>
            <li>Update the "Last updated" date at the top of this page</li>
            <li>Notify registered users via email or in-app notification for material changes</li>
        </ul>
        <p>Your continued use of our Services after any changes to these Terms constitutes your acceptance of the updated Terms. If you do not agree with the updated Terms, you must stop using our Services.</p>
    </div>

    <!-- Section 12 -->
    <div class="legal-section" id="section-12">
        <div class="legal-section-header">
            <div class="section-num">12</div>
            <h2>Contact Us</h2>
        </div>
        <p>If you have any questions about these Terms or wish to report a violation, please get in touch:</p>
        <ul>
            <li><strong>Email:</strong> <a href="mailto:legal@maicafe.com">legal@maicafe.com</a></li>
            <li><strong>Address:</strong> Mai Cafe, [Your Business Address]</li>
            <li><strong>Phone:</strong> [Your Contact Number]</li>
        </ul>
        <p>We aim to respond to all enquiries within 5 business days.</p>
    </div>

@endsection
