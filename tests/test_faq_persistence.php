<?php
require_once __DIR__ . '/../backend/config/database.php';
require_once __DIR__ . '/../backend/config/functions.php';

echo "\n================================================================\n";
echo "MURLI CREATIONS FAQ PERSISTENCE & MULTI-PAGE SYNC TEST SUITE\n";
echo "================================================================\n\n";

$testFaqs = [
    [
        "q" => "How are the delicate mirrors and clay reliefs protected during shipping?",
        "a" => "Every artwork is protected with multi-layer high-density cushioning, moisture-resistant film, and heavy-duty reinforced packaging to ensure 100% safe transit across India."
    ],
    [
        "q" => "Are these authentic handmade Lippan artworks?",
        "a" => "Yes, each artwork is 100% hand-sculpted by Master Craftsman Murli Prasad in our Delhi studio, using traditional clay relief paste, genuine glass mirrors, and premium wooden backings."
    ],
    [
        "q" => "Test Question 3: Can I request custom sizes?",
        "a" => "Yes, custom dimensions are available upon request via our WhatsApp helpline."
    ]
];

// Simulate saving FAQs via save_settings
$settings = get_site_settings();
$normalized = [];
foreach ($testFaqs as $fq) {
    $normalized[] = [
        'question' => $fq['q'],
        'answer' => $fq['a'],
        'q' => $fq['q'],
        'a' => $fq['a']
    ];
}
$settings['faqs'] = $normalized;
$saved1 = save_site_settings($settings);

$hp = get_homepage_content();
$hp['faqs'] = $normalized;
$saved2 = save_homepage_content($hp);

if ($saved1 && $saved2) {
    echo "✅ PASS: FAQs saved to both site_settings and homepage_content\n";
} else {
    echo "❌ FAIL: FAQs save failed\n";
    exit(1);
}

// Verify site_settings reload
$reloadedSettings = get_site_settings();
$faqsCount = count($reloadedSettings['faqs'] ?? []);
if ($faqsCount === 3) {
    echo "✅ PASS: Reloaded site_settings has 3 FAQs\n";
} else {
    echo "❌ FAIL: Reloaded site_settings has $faqsCount FAQs\n";
    exit(1);
}

// Verify homepage reload
$reloadedHp = get_homepage_content();
$hpFaqsCount = count($reloadedHp['faqs'] ?? []);
if ($hpFaqsCount === 3) {
    echo "✅ PASS: Reloaded homepage_content has 3 FAQs\n";
} else {
    echo "❌ FAIL: Reloaded homepage_content has $hpFaqsCount FAQs\n";
    exit(1);
}

// Verify rendering output on contact.php
ob_start();
$_SERVER['PHP_SELF'] = '/contact.php';
include __DIR__ . '/../frontend/contact.php';
$contactHtml = ob_get_clean();

if (str_contains($contactHtml, 'Test Question 3: Can I request custom sizes?')) {
    echo "✅ PASS: contact.php successfully rendered the updated FAQ\n";
} else {
    echo "❌ FAIL: contact.php did not contain updated FAQ\n";
    exit(1);
}

// Verify rendering output on index.php
ob_start();
$_SERVER['PHP_SELF'] = '/index.php';
include __DIR__ . '/../frontend/index.php';
$indexHtml = ob_get_clean();

if (str_contains($indexHtml, 'Test Question 3: Can I request custom sizes?')) {
    echo "✅ PASS: index.php (Homepage) successfully rendered the updated FAQ\n";
} else {
    echo "❌ FAIL: index.php did not contain updated FAQ\n";
    exit(1);
}

echo "\n================================================================\n";
echo "SUMMARY: 5 PASSED, 0 FAILED - FAQS WORK ON BOTH CONTACT & HOME!\n";
echo "================================================================\n";
