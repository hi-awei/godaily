<?php
/**
 * 生成 JSON-LD 结构化数据
 */

// 首页 - WebSite + Organization
function jsonld_homepage() {
    $siteUrl = rtrim(SITE_URL, '/');
    $data = [
        '@context' => 'https://schema.org',
        '@graph' => [
            [
                '@type' => 'WebSite',
                '@id' => $siteUrl . '/#website',
                'url' => $siteUrl,
                'name' => SITE_NAME,
                'description' => SITE_DESC,
                'potentialAction' => [
                    '@type' => 'SearchAction',
                    'target' => $siteUrl . '/tools.php?q={search_term_string}',
                    'query-input' => 'required name=search_term_string'
                ]
            ],
            [
                '@type' => 'Organization',
                '@id' => $siteUrl . '/#organization',
                'name' => SITE_NAME,
                'url' => $siteUrl,
                'logo' => $siteUrl . '/assets/img/logo.png'
            ]
        ]
    ];
    return '<script type="application/ld+json">' . json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>';
}

// 工具详情页 - SoftwareApplication
function jsonld_tool($tool) {
    $siteUrl = rtrim(SITE_URL, '/');
    $data = [
        '@context' => 'https://schema.org',
        '@type' => 'SoftwareApplication',
        'name' => $tool['name'],
        'description' => $tool['tagline'] ?: $tool['description'],
        'url' => $siteUrl . '/tool.php?slug=' . $tool['slug'],
        'applicationCategory' => 'UtilitiesApplication',
        'operatingSystem' => 'Web',
        'offers' => [
            '@type' => 'Offer',
            'price' => strpos(strtolower($tool['pricing'] ?? ''), '免费') !== false ? '0' : '0',
            'priceCurrency' => 'CNY'
        ]
    ];
    if (!empty($tool['icon'])) {
        $data['image'] = (strpos($tool['icon'], 'http') === 0) ? $tool['icon'] : $siteUrl . '/' . $tool['icon'];
    }
    return '<script type="application/ld+json">' . json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>';
}

// 工具列表页 - ItemList
function jsonld_tools_list($tools, $category = null) {
    $siteUrl = rtrim(SITE_URL, '/');
    $items = [];
    $pos = 1;
    foreach ($tools as $t) {
        $items[] = [
            '@type' => 'ListItem',
            'position' => $pos++,
            'url' => $siteUrl . '/tool.php?slug=' . $t['slug'],
            'name' => $t['name']
        ];
        if ($pos > 20) break; // 限制数量
    }
    $data = [
        '@context' => 'https://schema.org',
        '@type' => 'ItemList',
        'name' => $category ? $category . ' - AI工具导航' : 'AI工具导航',
        'numberOfItems' => count($tools),
        'itemListElement' => $items
    ];
    return '<script type="application/ld+json">' . json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>';
}

// 资讯详情页 - Article
function jsonld_article($news) {
    $siteUrl = rtrim(SITE_URL, '/');
    $data = [
        '@context' => 'https://schema.org',
        '@type' => 'Article',
        'headline' => $news['title'],
        'description' => mb_substr(strip_tags($news['content'] ?? $news['summary'] ?? ''), 0, 160),
        'url' => $siteUrl . '/news_detail.php?id=' . $news['id'],
        'datePublished' => $news['created_at'],
        'dateModified' => $news['updated_at'] ?? $news['created_at'],
        'publisher' => [
            '@type' => 'Organization',
            'name' => SITE_NAME,
            'url' => $siteUrl
        ]
    ];
    return '<script type="application/ld+json">' . json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>';
}
