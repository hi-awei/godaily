<?php
/**
 * 我的收藏页
 */
require_once 'includes/db.php';
require_once 'includes/functions.php';

$pageTitle = '我的收藏';
$pageDesc = '我收藏的AI工具 - GoDaily';

require_once 'templates/header.php';
?>

<div class="container">
    <div class="breadcrumb">
        <a href="index.php">首页</a> &gt;
        <span>我的收藏</span>
    </div>

    <div class="page-header">
        <h1>我的收藏</h1>
        <p class="page-desc">你收藏的AI工具都在这里</p>
    </div>

    <div class="favorites-container" id="favoritesContainer">
        <p class="loading">加载中...</p>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    loadFavorites();
});

function getUserHash() {
    // 使用与服务端一致的算法生成用户标识
    // 客户端无法获取IP，所以使用浏览器指纹
    var canvas = document.createElement('canvas');
    var ctx = canvas.getContext('2d');
    ctx.textBaseline = 'top';
    ctx.font = '14px Arial';
    ctx.fillText('GoDaily', 2, 2);
    var fp = canvas.toDataURL().slice(-50);
    var ua = navigator.userAgent;
    var lang = navigator.language;
    return sha256(ua + lang + fp);
}

// 简易SHA256（实际用服务端生成的hash更可靠）
function sha256(str) {
    // 用服务端API获取
    return null;
}

function loadFavorites() {
    fetch('api.php?action=favorites')
        .then(function(r) { return r.json(); })
        .then(function(d) {
            var container = document.getElementById('favoritesContainer');
            if (!d.success || d.favorites.length === 0) {
                container.innerHTML = '<div class="empty-state"><p>还没有收藏任何工具</p><a href="tools.php" class="btn-primary">去发现好工具</a></div>';
                return;
            }
            
            var html = '<div class="favorites-grid">';
            d.favorites.forEach(function(tool) {
                html += '<a href="tool.php?slug=' + tool.slug + '" class="favorite-card">';
                html += '<div class="favorite-icon">';
                if (tool.icon && (tool.icon.startsWith('http') || tool.icon.startsWith('assets') || tool.icon.startsWith('/'))) {
                    html += '<img src="' + tool.icon + '" alt="' + escapeHtml(tool.name) + '" onerror="this.style.display=\'none\';this.nextElementSibling.style.display=\'flex\';">';
                    html += '<div class="tool-logo-placeholder" style="display:none;">' + tool.name.charAt(0) + '</div>';
                } else if (tool.icon) {
                    html += '<span class="tool-icon-emoji">' + tool.icon + '</span>';
                } else {
                    html += '<div class="tool-logo-placeholder">' + tool.name.charAt(0) + '</div>';
                }
                html += '</div>';
                html += '<div class="favorite-info">';
                html += '<h3>' + escapeHtml(tool.name) + '</h3>';
                html += '<p>' + escapeHtml(tool.tagline || '') + '</p>';
                html += '<span class="favorite-category">' + escapeHtml(tool.category) + '</span>';
                html += '</div>';
                html += '</a>';
            });
            html += '</div>';
            container.innerHTML = html;
        })
        .catch(function() {
            document.getElementById('favoritesContainer').innerHTML = '<p class="error">加载失败，请刷新重试</p>';
        });
}

function escapeHtml(text) {
    var div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
</script>

<style>
.page-header { margin: 24px 0; }
.page-header h1 { font-size: 24px; margin-bottom: 8px; }
.page-desc { color: var(--text-light); }

.favorites-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 16px; }
.favorite-card { 
    display: flex; align-items: center; gap: 14px; padding: 16px; 
    background: white; border-radius: var(--radius); box-shadow: var(--shadow);
    transition: all 0.2s; text-decoration: none; color: inherit;
}
.favorite-card:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
.favorite-icon { width: 48px; height: 48px; flex-shrink: 0; display: flex; align-items: center; justify-content: center; border-radius: 12px; background: var(--bg); overflow: hidden; }
.favorite-icon img { width: 100%; height: 100%; object-fit: contain; }
.favorite-info h3 { font-size: 15px; margin-bottom: 4px; }
.favorite-info p { font-size: 13px; color: var(--text-light); margin-bottom: 6px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.favorite-category { font-size: 11px; color: var(--primary); background: var(--bg); padding: 2px 8px; border-radius: 10px; }

.empty-state { text-align: center; padding: 60px 20px; }
.empty-state p { color: var(--text-light); margin-bottom: 16px; }
.error { text-align: center; color: #e53935; padding: 20px; }
.loading { text-align: center; color: var(--text-light); padding: 20px; }
</style>

<?php require_once 'templates/footer.php'; ?>
