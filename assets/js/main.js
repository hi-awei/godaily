// GoDaily - 前端交互脚本

// 移动端菜单
function toggleMobileMenu() {
    const nav = document.querySelector('.main-nav');
    if (nav) {
        nav.style.display = nav.style.display === 'flex' ? 'none' : 'flex';
        nav.style.flexDirection = 'column';
        nav.style.position = 'absolute';
        nav.style.top = '64px';
        nav.style.left = '0';
        nav.style.right = '0';
        nav.style.background = 'white';
        nav.style.padding = '16px';
        nav.style.borderBottom = '1px solid #e2e8f0';
        nav.style.boxShadow = '0 4px 12px rgba(0,0,0,0.1)';
    }
}

// 投票功能
async function vote(toolId) {
    try {
        const resp = await fetch(`api.php?action=vote&tool_id=${toolId}`);
        const data = await resp.json();
        if (data.success) {
            alert('✅ 投票成功！感谢你的支持');
            location.reload();
        } else {
            alert(data.message || '投票失败');
        }
    } catch (e) {
        alert('网络错误，请重试');
    }
}

// 复制链接
function copyLink() {
    navigator.clipboard.writeText(window.location.href).then(() => {
        alert('链接已复制到剪贴板！');
    }).catch(() => {
        alert('复制失败，请手动复制');
    });
}

// 平滑滚动
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            e.preventDefault();
            target.scrollIntoView({ behavior: 'smooth' });
        }
    });
});

// 搜索框交互
const searchInput = document.querySelector('.hero-search-input');
if (searchInput) {
    searchInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            const query = this.value.trim();
            if (query) {
                window.location.href = `tools.php?q=${encodeURIComponent(query)}`;
            }
        }
    });
}

// 图片懒加载
if ('IntersectionObserver' in window) {
    const lazyImages = document.querySelectorAll('img[data-src]');
    const imageObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                img.removeAttribute('data-src');
                imageObserver.unobserve(img);
            }
        });
    });
    lazyImages.forEach(img => imageObserver.observe(img));
}

// 滚动显示/隐藏导航
let lastScroll = 0;
const header = document.querySelector('.site-header');
if (header) {
    window.addEventListener('scroll', () => {
        const currentScroll = window.pageYOffset;
        if (currentScroll > lastScroll && currentScroll > 100) {
            header.style.transform = 'translateY(-100%)';
        } else {
            header.style.transform = 'translateY(0)';
        }
        lastScroll = currentScroll;
    });
}
