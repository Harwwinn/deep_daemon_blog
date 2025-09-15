<?php
// Include the header to maintain site navigation and styling
require_once 'homepage/_header.php';
?>

<!-- ======================================================= -->
<!-- ========= COOKIE POLICY PAGE CONTENT ================== -->
<!-- ======================================================= -->
<div class="bg-white py-12">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto prose prose-lg text-gray-800">
            <h1>Cookie Policy</h1>
            <p class="lead">Last updated: <?php echo date('F j, Y'); ?></p>

            <p>Welcome to Deepdaemon. This Cookie Policy explains what cookies are, how we use them on our website, and your choices regarding them.</p>

            <h2>What Are Cookies?</h2>
            <p>A cookie is a small text file that a website saves on your computer or mobile device when you visit the site. It enables the website to remember your actions and preferences (such as login, language, font size, and other display preferences) over a period of time, so you don’t have to keep re-entering them whenever you come back to the site or browse from one page to another.</p>

            <h2>How Do We Use Cookies?</h2>
            <p>On our site, we use several types of cookies for different purposes:</p>

            <h4>1. Strictly Necessary Cookies</h4>
            <p>These cookies are essential for you to browse the website and use its features, such as accessing secure areas of the site. Without these cookies, services you have asked for, like logging into your account, cannot be provided.</p>
            <ul>
                <li><strong>Session Cookies (PHPSESSID):</strong> We use this cookie to identify you and maintain your logged-in state as you navigate through protected areas like your dashboard. This cookie is deleted when you close your browser.</li>
            </ul>

            <h4>2. Performance and Analytics Cookies (Future Use)</h4>
            <p>These cookies collect information about how visitors use our website, for instance, which pages visitors go to most often. All information these cookies collect is aggregated and therefore anonymous. It is only used to improve how our website works. We may use services like Google Analytics in the future.</p>

            <h4>3. Advertising Cookies (Future Use)</h4>
            <p>Should we decide to display advertisements, we and our advertising partners (such as Google AdSense) may use cookies to show you advertising that is more relevant to you and your interests. These cookies remember that you have visited our site and may share this information with other organizations, such as advertisers.</p>

            <h2>Your Choices Regarding Cookies</h2>
            <p>You have several options to control or limit how we and our partners use cookies:</p>
            <ul>
                <li><strong>Browser Settings:</strong> Most web browsers allow you to control cookies through their settings. You can set your browser to notify you when you receive a cookie, giving you the chance to decide whether to accept it. You can also block all cookies, but this may affect the functionality of many websites, including ours (for example, you will not be able to log in).</li>
                <li><strong>Opt-Out Tools:</strong> For advertising cookies, you can use opt-out tools provided by organizations such as the <a href="http://optout.aboutads.info/" target="_blank" rel="noopener">Digital Advertising Alliance</a>.</li>
            </ul>

            <h2>Changes to Our Cookie Policy</h2>
            <p>We may update this Cookie Policy from time to time. We will notify you of any changes by posting the new policy on this page. You are advised to review this policy periodically for any changes.</p>

            <h2>Contact Us</h2>
            <p>If you have any questions about our use of cookies, you can contact us through our <a href="/homepage/contact.php">contact page</a>.</p>
        </div>
    </div>
</div>

<?php
// Include the footer
require_once 'homepage/_footer.php';
?>