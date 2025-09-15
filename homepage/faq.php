<?php
// Include the header
require_once __DIR__ . '/_header.php';

// --- FAQ DATA ---
// Keeping the Q&A in an array makes the HTML much cleaner and easier to manage.
$faq_items = [
    [
        'question' => 'What is the main purpose of this blog?',
        'answer' => 'Deepdaemon is a platform dedicated to sharing high-quality knowledge about technology, web development, and scientific innovation. Our mission is to link scientific research with industrial solutions to generate cutting-edge technology and high-impact human capital.'
    ],
    [
        'question' => 'Who can create articles on this platform?',
        'answer' => 'Currently, only registered users who have been granted author or administrator privileges by the site owner can create and publish articles. This ensures a high standard of quality and expertise in our content.'
    ],
    [
        'question' => 'How can I become a collaborator?',
        'answer' => 'We are always looking for passionate experts to join our team. If you are interested in contributing, please reach out to us through our <a href="contact.php" class="text-primary-600 hover:underline">Contact Page</a> with a brief introduction and links to your previous work or profile.'
    ],
    [
        'question' => 'How do you handle user data and privacy?',
        'answer' => 'We take user privacy very seriously. We collect minimal personal data required for account functionality, such as your name and email. All passwords are securely hashed, and we never sell your data to third parties. For full details, please read our <a href="/homepage/privacy-policy.php" class="text-primary-600 hover:underline">Privacy Policy</a>.'
    ],
    [
        'question' => 'What technologies were used to build this website?',
        'answer' => 'This website was built from scratch using a combination of modern web technologies, including PHP for the backend logic, MySQL for the database, and Tailwind CSS for a utility-first approach to styling. Interactive elements are powered by the lightweight JavaScript framework, Alpine.js.'
    ]
];
?>

<!-- ======================================================= -->
<!-- ========= FAQ PAGE CONTENT ============================ -->
<!-- ======================================================= -->
<div class="bg-white py-12">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto">

            <!-- Page Header -->
            <div class="text-center">
                <h1 class="text-4xl font-extrabold tracking-tight text-gray-900 sm:text-5xl">
                    Frequently Asked Questions
                </h1>
                <p class="mt-4 text-xl text-gray-600">
                    Have a question? Find your answer here. If you can't find what you're looking for, feel free to contact us.
                </p>
            </div>

            <!-- Accordion Section -->
            <div class="mt-12 space-y-4" x-data="{ activeAccordion: null }">
                <?php foreach ($faq_items as $index => $item): ?>
                    <!-- Single Accordion Item -->
                    <div class="border border-gray-200 rounded-lg">
                        <h2>
                            <!-- Accordion Button -->
                            <button type="button" 
                                    class="flex items-center justify-between w-full p-6 text-left font-semibold text-lg text-gray-800"
                                    @click="activeAccordion = (activeAccordion === <?php echo $index; ?>) ? null : <?php echo $index; ?>">
                                <span><?php echo htmlspecialchars($item['question']); ?></span>
                                <!-- Arrow Icon (rotates based on state) -->
                                <svg class="h-6 w-6 transform transition-transform duration-200" 
                                     :class="{ 'rotate-180': activeAccordion === <?php echo $index; ?> }"
                                     xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                  <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                </svg>
                            </button>
                        </h2>
                        <!-- Accordion Panel (Content) -->
                        <div x-show="activeAccordion === <?php echo $index; ?>" 
                             x-collapse 
                             class="px-6 pb-6 prose">
                            <p><?php echo $item['answer']; // Using echo directly to render the HTML links ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<?php
// Include the footer
require_once __DIR__ . '/_footer.php';
?>