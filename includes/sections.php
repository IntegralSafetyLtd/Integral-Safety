<?php
/**
 * Section Rendering Functions
 * Supports custom styling: bg_color, text_color, heading_color, bg_image, bg_opacity, layout_type
 */

/**
 * Get sections for a page/service/training
 */
function getSections($pageType, $pageId) {
    return dbFetchAll(
        "SELECT * FROM page_sections WHERE page_type = ? AND page_id = ? AND is_active = 1 ORDER BY sort_order ASC",
        [$pageType, $pageId]
    );
}

/**
 * Build inline style string from section data
 */
function buildSectionStyle($data, $includeText = true) {
    $styles = [];

    if (!empty($data['bg_color']) && $data['bg_color'] !== 'transparent') {
        $styles[] = "background-color: {$data['bg_color']}";
    }

    if ($includeText && !empty($data['text_color']) && $data['text_color'] !== 'transparent') {
        $styles[] = "color: {$data['text_color']}";
    }

    return implode('; ', $styles);
}

/**
 * Build heading style
 */
function buildHeadingStyle($data) {
    if (!empty($data['heading_color']) && $data['heading_color'] !== 'transparent') {
        return "color: {$data['heading_color']}";
    }
    return '';
}

function buildTextStyle($data) {
    if (!empty($data['text_color']) && $data['text_color'] !== 'transparent') {
        return "color: {$data['text_color']}";
    }
    return '';
}

/**
 * Check if section has background image
 */
function hasBackgroundImage($data) {
    return !empty($data['bg_image']);
}

/**
 * Render a section wrapper with background image support
 */
function renderSectionStart($data, $defaultBg = 'bg-white', $additionalClasses = '') {
    $hasBgImage = hasBackgroundImage($data);
    $bgOpacity = isset($data['bg_opacity']) ? (int)$data['bg_opacity'] / 100 : 1;
    $overlayOpacity = 1 - $bgOpacity;

    $bgColor = !empty($data['bg_color']) ? $data['bg_color'] : '#ffffff';
    $textStyle = !empty($data['text_color']) && $data['text_color'] !== 'transparent'
        ? "color: {$data['text_color']};"
        : '';

    if ($hasBgImage): ?>
        <div class="relative rounded-2xl overflow-hidden mb-8 <?= $additionalClasses ?>" style="<?= $textStyle ?>">
            <!-- Background Image -->
            <div class="absolute inset-0 bg-cover bg-center"
                 style="background-image: url('<?= e($data['bg_image']) ?>'); opacity: <?= $bgOpacity ?>;"></div>
            <!-- Color Overlay -->
            <div class="absolute inset-0" style="background-color: <?= e($bgColor) ?>; opacity: <?= $overlayOpacity ?>;"></div>
            <!-- Content -->
            <div class="relative z-10 p-8">
    <?php else:
        $style = buildSectionStyle($data);
        $bgClass = empty($data['bg_color']) ? $defaultBg : '';
    ?>
        <div class="<?= $bgClass ?> rounded-2xl p-8 shadow-sm mb-8 <?= $additionalClasses ?>" style="<?= e($style) ?>">
    <?php endif;
}

/**
 * Render section end
 */
function renderSectionEnd($data) {
    if (hasBackgroundImage($data)) {
        echo '</div></div>';
    } else {
        echo '</div>';
    }
}

/**
 * Render a section based on its type
 */
function renderSection($section) {
    $data = json_decode($section['section_data'], true) ?: [];
    $type = $section['section_type'];

    switch ($type) {
        case 'page_header':
            renderPageHeaderSection($data);
            break;
        case 'hero':
            renderHeroSection($data);
            break;
        case 'text':
            renderTextSection($data);
            break;
        case 'text_image':
            renderTextImageSection($data);
            break;
        case 'image':
            renderImageSection($data);
            break;
        case 'checklist':
            renderChecklistSection($data);
            break;
        case 'process_steps':
            renderProcessStepsSection($data);
            break;
        case 'faq':
            renderFaqSection($data);
            break;
        case 'benefits':
            renderBenefitsSection($data);
            break;
        case 'stats':
            renderStatsSection($data);
            break;
        case 'cta':
            renderCtaSection($data);
            break;
        case 'cards':
            renderCardsSection($data);
            break;
    }
}

function renderPageHeaderSection($data) {
    $headingStyle = buildHeadingStyle($data);
    $textStyle = buildTextStyle($data);
    $imagePosition = $data['image_position'] ?? 'right';
    $hasImage = !empty($data['image']) && $imagePosition !== 'none';
    $contentWidth = isset($data['content_width']) ? (int)$data['content_width'] : 50;
    $imageWidth = 100 - $contentWidth;
    $gridStyle = "grid-template-columns: {$contentWidth}fr {$imageWidth}fr;";

    // Get phone number for secondary button if it's a tel: link
    $phone = getSetting('contact_phone', '');

    renderSectionStart($data, 'py-16 md:py-20 bg-white');
    ?>
    <div class="max-w-6xl mx-auto px-6">
        <div class="<?= $hasImage ? 'grid lg:grid-cols-2 gap-12 items-center' : '' ?>" style="<?= $hasImage ? $gridStyle : '' ?>">
            <?php if ($imagePosition === 'left' && $hasImage): ?>
            <div class="hidden lg:block">
                <img src="<?= e($data['image']) ?>" alt="<?= e($data['title'] ?? '') ?>" class="w-full h-[360px] object-cover rounded-2xl shadow-xl">
            </div>
            <?php endif; ?>

            <div class="<?= $hasImage ? 'max-w-xl' : '' ?>">
                <?php if (!empty($data['breadcrumb'])): ?>
                <p class="text-orange-500 font-semibold text-sm uppercase tracking-wider mb-4"><?= e($data['breadcrumb']) ?></p>
                <?php endif; ?>

                <h1 class="font-heading text-4xl md:text-5xl font-bold text-navy-900 mb-6" style="<?= e($headingStyle) ?>">
                    <?= e($data['title'] ?? '') ?>
                </h1>

                <?php if (!empty($data['description'])): ?>
                <p class="text-gray-600 text-lg leading-relaxed mb-8" style="<?= e($textStyle) ?>">
                    <?= e($data['description']) ?>
                </p>
                <?php endif; ?>

                <?php if (!empty($data['show_cta'])): ?>
                <?php
                $btn1Target = !empty($data['button1_newtab']) ? ' target="_blank" rel="noopener noreferrer"' : '';
                $btn2Target = !empty($data['button2_newtab']) ? ' target="_blank" rel="noopener noreferrer"' : '';
                ?>
                <div class="flex flex-wrap gap-4">
                    <?php if (!empty($data['button1_text'])): ?>
                    <a href="<?= e($data['button1_url'] ?? '/contact') ?>"<?= $btn1Target ?> class="bg-orange-500 text-white px-6 py-3 rounded-lg font-semibold hover:bg-orange-600 transition-colors shadow-md inline-flex items-center gap-2">
                        <?= e($data['button1_text']) ?>
                        <?php if (!empty($data['button1_newtab'])): ?>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                        <?php endif; ?>
                    </a>
                    <?php endif; ?>
                    <?php if (!empty($data['button2_text'])): ?>
                    <a href="<?= e($data['button2_url'] ?? '#') ?>"<?= $btn2Target ?> class="border-2 border-navy-800 text-navy-800 px-6 py-3 rounded-lg font-semibold hover:bg-navy-800 hover:text-white transition-colors inline-flex items-center gap-2">
                        <?php if (strpos($data['button2_url'] ?? '', 'tel:') === 0): ?>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                        <?php elseif (!empty($data['button2_newtab'])): ?>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                        <?php endif; ?>
                        <?= e($data['button2_text']) ?>
                    </a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>

            <?php if ($imagePosition === 'right' && $hasImage): ?>
            <div class="hidden lg:block">
                <img src="<?= e($data['image']) ?>" alt="<?= e($data['title'] ?? '') ?>" class="w-full h-[360px] object-cover rounded-2xl shadow-xl">
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php
    renderSectionEnd($data);
}

function renderHeroSection($data) {
    $headingStyle = buildHeadingStyle($data);
    $imagePosition = $data['image_position'] ?? 'right';
    $hasImage = !empty($data['image']);
    $contentWidth = isset($data['content_width']) ? (int)$data['content_width'] : 50;
    $imageWidth = 100 - $contentWidth;

    // Calculate grid column classes based on width percentages
    $contentCols = round($contentWidth / 100 * 12);
    $imageCols = 12 - $contentCols;
    $gridStyle = "grid-template-columns: {$contentWidth}% {$imageWidth}%;";

    renderSectionStart($data, '', '');
    ?>
    <div class="<?= $hasImage ? 'grid gap-8 items-center' : '' ?>" style="<?= $hasImage ? $gridStyle : '' ?>">
        <?php if ($imagePosition === 'left' && $hasImage): ?>
        <div style="grid-column: 1;">
            <img src="<?= e($data['image']) ?>" alt="<?= e($data['heading'] ?? '') ?>" class="rounded-xl shadow-lg w-full">
        </div>
        <div style="grid-column: 2;">
        <?php else: ?>
        <div>
        <?php endif; ?>
            <h2 class="font-heading text-2xl md:text-3xl font-bold text-navy-900 mb-4" style="<?= e($headingStyle) ?>">
                <?= e($data['heading'] ?? '') ?>
            </h2>
            <?php if (!empty($data['subheading'])): ?>
            <p class="text-gray-600 text-lg leading-relaxed mb-6">
                <?= e($data['subheading']) ?>
            </p>
            <?php endif; ?>

            <?php if (!empty($data['show_cta'])): ?>
            <?php
            $btn1Target = !empty($data['button1_newtab']) ? ' target="_blank" rel="noopener noreferrer"' : '';
            $btn2Target = !empty($data['button2_newtab']) ? ' target="_blank" rel="noopener noreferrer"' : '';
            ?>
            <div class="flex flex-wrap gap-4">
                <?php if (!empty($data['button1_text'])): ?>
                <a href="<?= e($data['button1_url'] ?? '/contact') ?>"<?= $btn1Target ?> class="inline-flex items-center gap-2 px-6 py-3 bg-orange-500 text-white font-semibold rounded-lg hover:bg-orange-600 transition-colors">
                    <?= e($data['button1_text']) ?>
                    <?php if (!empty($data['button1_newtab'])): ?>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                    <?php endif; ?>
                </a>
                <?php endif; ?>
                <?php if (!empty($data['button2_text'])): ?>
                <a href="<?= e($data['button2_url'] ?? '#') ?>"<?= $btn2Target ?> class="inline-flex items-center gap-2 px-6 py-3 border-2 border-navy-800 text-navy-800 font-semibold rounded-lg hover:bg-navy-800 hover:text-white transition-colors">
                    <?php if (strpos($data['button2_url'] ?? '', 'tel:') === 0): ?>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                    <?php elseif (!empty($data['button2_newtab'])): ?>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                    <?php endif; ?>
                    <?= e($data['button2_text']) ?>
                </a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>

        <?php if ($imagePosition === 'right' && $hasImage): ?>
        <div>
            <img src="<?= e($data['image']) ?>" alt="<?= e($data['heading'] ?? '') ?>" class="rounded-xl shadow-lg w-full">
        </div>
        <?php endif; ?>
    </div>
    <?php
    renderSectionEnd($data);
}

function renderTextSection($data) {
    $headingStyle = buildHeadingStyle($data);
    renderSectionStart($data, 'bg-white');
    ?>
    <?php if (!empty($data['heading'])): ?>
    <h3 class="font-heading text-xl font-bold text-navy-900 mb-4" style="<?= e($headingStyle) ?>"><?= e($data['heading']) ?></h3>
    <?php endif; ?>
    <div class="prose prose-gray max-w-none">
        <?= $data['content'] ?? '' ?>
    </div>
    <?php
    renderSectionEnd($data);
}

function renderTextImageSection($data) {
    $headingStyle = buildHeadingStyle($data);
    $layout = $data['layout_type'] ?? ($data['image_position'] === 'left' ? 'image-left' : 'image-right');
    $hasImage = !empty($data['image']);
    $contentWidth = isset($data['content_width']) ? (int)$data['content_width'] : 50;
    $imageWidth = 100 - $contentWidth;
    $gridStyle = "grid-template-columns: {$contentWidth}% {$imageWidth}%;";

    renderSectionStart($data, 'bg-white');

    if ($layout === 'text-only' || !$hasImage): ?>
        <?php if (!empty($data['heading'])): ?>
        <h3 class="font-heading text-xl font-bold text-navy-900 mb-4" style="<?= e($headingStyle) ?>"><?= e($data['heading']) ?></h3>
        <?php endif; ?>
        <div class="prose prose-gray">
            <?= $data['content'] ?? '' ?>
        </div>
    <?php elseif ($layout === 'image-top'): ?>
        <img src="<?= e($data['image']) ?>" alt="<?= e($data['heading'] ?? '') ?>" class="w-full h-48 object-cover rounded-xl mb-6">
        <?php if (!empty($data['heading'])): ?>
        <h3 class="font-heading text-xl font-bold text-navy-900 mb-4" style="<?= e($headingStyle) ?>"><?= e($data['heading']) ?></h3>
        <?php endif; ?>
        <div class="prose prose-gray">
            <?= $data['content'] ?? '' ?>
        </div>
    <?php elseif ($layout === 'full-width'): ?>
        <?php if (!empty($data['heading'])): ?>
        <h3 class="font-heading text-xl font-bold text-navy-900 mb-4" style="<?= e($headingStyle) ?>"><?= e($data['heading']) ?></h3>
        <?php endif; ?>
        <div class="prose prose-gray mb-6">
            <?= $data['content'] ?? '' ?>
        </div>
        <img src="<?= e($data['image']) ?>" alt="<?= e($data['heading'] ?? '') ?>" class="w-full rounded-xl shadow-lg">
    <?php else: ?>
        <div class="grid gap-8 items-center" style="<?= e($gridStyle) ?>">
            <?php if ($layout === 'image-left'): ?>
            <div>
                <img src="<?= e($data['image']) ?>" alt="<?= e($data['heading'] ?? '') ?>" class="rounded-xl shadow-lg w-full">
            </div>
            <?php endif; ?>

            <div>
                <?php if (!empty($data['heading'])): ?>
                <h3 class="font-heading text-xl font-bold text-navy-900 mb-4" style="<?= e($headingStyle) ?>"><?= e($data['heading']) ?></h3>
                <?php endif; ?>
                <div class="prose prose-gray">
                    <?= $data['content'] ?? '' ?>
                </div>
            </div>

            <?php if ($layout === 'image-right'): ?>
            <div>
                <img src="<?= e($data['image']) ?>" alt="<?= e($data['heading'] ?? '') ?>" class="rounded-xl shadow-lg w-full">
            </div>
            <?php endif; ?>
        </div>
    <?php endif;

    renderSectionEnd($data);
}

function renderImageSection($data) {
    if (empty($data['image'])) return;
    renderSectionStart($data, '', '');
    ?>
    <img src="<?= e($data['image']) ?>" alt="<?= e($data['alt_text'] ?? '') ?>" class="rounded-2xl shadow-lg w-full">
    <?php if (!empty($data['caption'])): ?>
    <p class="text-center text-gray-500 text-sm mt-3"><?= e($data['caption']) ?></p>
    <?php endif; ?>
    <?php
    renderSectionEnd($data);
}

function renderChecklistSection($data) {
    $items = $data['items'] ?? [];
    if (empty($items)) return;

    $headingStyle = buildHeadingStyle($data);
    renderSectionStart($data, 'bg-white');
    ?>
    <?php if (!empty($data['heading'])): ?>
    <h3 class="font-heading text-xl font-bold text-navy-900 mb-2" style="<?= e($headingStyle) ?>"><?= e($data['heading']) ?></h3>
    <?php endif; ?>
    <?php if (!empty($data['intro'])): ?>
    <p class="text-gray-600 mb-6"><?= e($data['intro']) ?></p>
    <?php endif; ?>

    <div class="grid md:grid-cols-2 gap-3">
        <?php foreach ($items as $item): ?>
        <div class="flex items-start gap-3">
            <div class="w-5 h-5 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                <svg class="w-3 h-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <span class="text-gray-700"><?= e($item) ?></span>
        </div>
        <?php endforeach; ?>
    </div>
    <?php
    renderSectionEnd($data);
}

function renderProcessStepsSection($data) {
    $steps = $data['steps'] ?? [];
    if (empty($steps)) return;

    $headingStyle = buildHeadingStyle($data);
    renderSectionStart($data, 'bg-white');
    ?>
    <?php if (!empty($data['heading'])): ?>
    <h3 class="font-heading text-xl font-bold text-navy-900 mb-2" style="<?= e($headingStyle) ?>"><?= e($data['heading']) ?></h3>
    <?php endif; ?>
    <?php if (!empty($data['intro'])): ?>
    <p class="text-gray-600 mb-6"><?= e($data['intro']) ?></p>
    <?php endif; ?>

    <div class="space-y-6">
        <?php foreach ($steps as $index => $step): ?>
        <div class="flex gap-4">
            <div class="w-10 h-10 bg-orange-500 rounded-full flex items-center justify-center flex-shrink-0 text-white font-bold">
                <?= $index + 1 ?>
            </div>
            <div class="flex-1 pt-1">
                <h4 class="font-semibold text-navy-900 mb-1"><?= e($step['title'] ?? '') ?></h4>
                <p class="text-gray-600"><?= e($step['description'] ?? '') ?></p>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php
    renderSectionEnd($data);
}

function renderFaqSection($data) {
    $items = $data['items'] ?? [];
    if (empty($items)) return;

    $headingStyle = buildHeadingStyle($data);
    renderSectionStart($data, 'bg-white');
    ?>
    <?php if (!empty($data['heading'])): ?>
    <h3 class="font-heading text-xl font-bold text-navy-900 mb-6" style="<?= e($headingStyle) ?>"><?= e($data['heading']) ?></h3>
    <?php endif; ?>

    <div class="space-y-4">
        <?php foreach ($items as $item): ?>
        <details class="group border border-gray-200 rounded-xl">
            <summary class="flex items-center justify-between p-5 cursor-pointer hover:bg-gray-50 rounded-xl">
                <span class="font-semibold text-navy-900 pr-4"><?= e($item['question'] ?? '') ?></span>
                <svg class="w-5 h-5 text-gray-400 group-open:rotate-180 transition-transform flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </summary>
            <div class="px-5 pb-5 text-gray-600">
                <?= e($item['answer'] ?? '') ?>
            </div>
        </details>
        <?php endforeach; ?>
    </div>
    <?php
    renderSectionEnd($data);
}

function renderBenefitsSection($data) {
    $items = $data['items'] ?? [];
    if (empty($items)) return;

    $headingStyle = buildHeadingStyle($data);

    // Use custom colors or default navy gradient
    $hasBgImage = hasBackgroundImage($data);
    $hasCustomBg = !empty($data['bg_color']);

    if (!$hasBgImage && !$hasCustomBg): ?>
    <div class="bg-gradient-to-br from-navy-800 to-navy-900 rounded-2xl p-8 text-white mb-8">
    <?php else:
        renderSectionStart($data, 'bg-gradient-to-br from-navy-800 to-navy-900', 'text-white');
    endif; ?>

    <?php if (!empty($data['heading'])): ?>
    <h3 class="font-heading text-xl font-bold mb-6" style="<?= e($headingStyle) ?>"><?= e($data['heading']) ?></h3>
    <?php endif; ?>

    <div class="grid md:grid-cols-2 gap-3">
        <?php foreach ($items as $item): ?>
        <div class="flex items-start gap-3">
            <svg class="w-5 h-5 text-orange-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            <span class="text-white/90"><?= e($item) ?></span>
        </div>
        <?php endforeach; ?>
    </div>

    <?php
    if (!$hasBgImage && !$hasCustomBg) {
        echo '</div>';
    } else {
        renderSectionEnd($data);
    }
}

function renderStatsSection($data) {
    $items = $data['items'] ?? [];
    if (empty($items)) return;

    // Stats have their own layout, but still support background
    $hasBgImage = hasBackgroundImage($data);
    $hasCustomBg = !empty($data['bg_color']);

    if ($hasBgImage || $hasCustomBg) {
        renderSectionStart($data, '');
    }
    ?>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-6 <?= (!$hasBgImage && !$hasCustomBg) ? 'mb-8' : '' ?>">
        <?php foreach ($items as $item): ?>
        <div class="bg-white rounded-xl p-6 text-center shadow-sm">
            <div class="font-heading text-3xl font-bold text-orange-500"><?= e($item['number'] ?? '') ?></div>
            <div class="text-gray-600 text-sm mt-1"><?= e($item['label'] ?? '') ?></div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php
    if ($hasBgImage || $hasCustomBg) {
        renderSectionEnd($data);
    }
}

function renderCtaSection($data) {
    $style = $data['style'] ?? 'orange';
    $headingStyle = buildHeadingStyle($data);

    // Check for custom styling
    $hasBgImage = hasBackgroundImage($data);
    $hasCustomBg = !empty($data['bg_color']);

    if ($hasBgImage || $hasCustomBg) {
        renderSectionStart($data, '', 'text-center text-white');
    } else {
        $bgClass = $style === 'navy' ? 'bg-navy-800' : 'bg-orange-500';
    ?>
    <div class="<?= $bgClass ?> rounded-2xl p-8 text-center text-white mb-8">
    <?php } ?>

    <?php if (!empty($data['heading'])): ?>
    <h3 class="font-heading text-2xl font-bold mb-3" style="<?= e($headingStyle) ?>"><?= e($data['heading']) ?></h3>
    <?php endif; ?>
    <?php if (!empty($data['content'])): ?>
    <p class="text-white/90 mb-6 max-w-xl mx-auto"><?= e($data['content']) ?></p>
    <?php endif; ?>
    <?php if (!empty($data['button_text'])): ?>
    <a href="<?= e($data['button_link'] ?? '/contact') ?>" class="inline-block bg-white text-<?= $style === 'navy' ? 'navy-800' : 'orange-600' ?> px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition-colors">
        <?= e($data['button_text']) ?>
    </a>
    <?php endif; ?>

    <?php
    if ($hasBgImage || $hasCustomBg) {
        renderSectionEnd($data);
    } else {
        echo '</div>';
    }
}

function renderCardsSection($data) {
    $cards = $data['cards'] ?? [];
    if (empty($cards)) return;

    $headingStyle = buildHeadingStyle($data);
    $hasBgImage = hasBackgroundImage($data);
    $hasCustomBg = !empty($data['bg_color']);

    if ($hasBgImage || $hasCustomBg) {
        renderSectionStart($data, '');
    } else {
        echo '<div class="mb-8">';
    }
    ?>
    <?php if (!empty($data['heading'])): ?>
    <h3 class="font-heading text-xl font-bold text-navy-900 mb-6" style="<?= e($headingStyle) ?>"><?= e($data['heading']) ?></h3>
    <?php endif; ?>

    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($cards as $card): ?>
        <div class="bg-white rounded-xl p-6 shadow-sm">
            <?php if (!empty($card['icon'])): ?>
            <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center mb-4">
                <?= getIcon($card['icon'], 'w-6 h-6 text-orange-500') ?>
            </div>
            <?php endif; ?>
            <h4 class="font-semibold text-navy-900 mb-2"><?= e($card['title'] ?? '') ?></h4>
            <p class="text-gray-600 text-sm"><?= e($card['description'] ?? '') ?></p>
        </div>
        <?php endforeach; ?>
    </div>
    <?php
    if ($hasBgImage || $hasCustomBg) {
        renderSectionEnd($data);
    } else {
        echo '</div>';
    }
}
