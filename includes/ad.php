<?php
/**
 * AdSense 广告位组件
 */
function ad_slot($style = 'default') {
    $styles = [
        'default' => 'display:inline-block;width:728px;height:90px;',
        'rectangle' => 'display:inline-block;width:336px;height:280px;',
        'in-article' => 'display:block;width:300px;height:250px;margin:0 auto;',
        'auto' => 'display:block;width:100%;max-width:728px;margin:0 auto;',
    ];
    $css = $styles[$style] ?? $styles['default'];
    return '<div class="ad-slot" style="' . $css . '"><ins class="adsbygoogle"
         style="' . $css . '"
         data-ad-client="ca-pub-4485249374604824"
         data-ad-slot=""
         data-ad-format="auto"
         data-full-width-responsive="true"></ins></div>';
}

function ad_inline() {
    return '<div style="text-align:center;margin:32px 0;"><ins class="adsbygoogle"
     style="display:inline-block;width:728px;height:90px"
     data-ad-client="ca-pub-4485249374604824"
     data-ad-slot=""
     data-ad-format="auto"
     data-full-width-responsive="true"></ins></div>';
}
