    </main>

    <!-- AdSense 底部广告 -->
    <div class="ad-slot">
        <ins class="adsbygoogle"
             style="display:block"
             data-ad-client="ca-pub-4485249374604824"
             data-ad-slot=""
             data-ad-format="auto"
             data-full-width-responsive="true"></ins>
    </div>
    <script>(adsbygoogle = window.adsbygoogle || []).push({});</script>

    <footer class="site-footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-col">
                    <h4>关于 GoDaily</h4>
                    <p>GoDaily 致力于发现和整理最优质的AI工具，为用户提供全面的AI工具导航服务。</p>
                </div>
                <div class="footer-col">
                    <h4>快速链接</h4>
                    <ul>
                        <li><a href="<?= SITE_URL ?>/index.php">首页</a></li>
                        <li><a href="<?= SITE_URL ?>/tools.php">AI工具库</a></li>
                        <li><a href="<?= SITE_URL ?>/news.php">AI资讯</a></li>
                        <li><a href="<?= SITE_URL ?>/submit.php">提交工具</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>分类</h4>
                    <ul>
                        <li><a href="<?= SITE_URL ?>/tools.php?category=llm">大语言模型</a></li>
                        <li><a href="<?= SITE_URL ?>/tools.php?category=image">AI图像</a></li>
                        <li><a href="<?= SITE_URL ?>/tools.php?category=video">AI视频</a></li>
                        <li><a href="<?= SITE_URL ?>/tools.php?category=coding">编程辅助</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>联系我们</h4>
                    <p>有事联系：<br><a href="mailto:contact@993899.com">contact@993899.com</a></p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?= date('Y') ?> GoDaily - 每日AI工具导航. All rights reserved.</p>
                <p>
                    精选AI工具，服务于创作者 &nbsp;|&nbsp;
                    <a href="https://beian.miit.gov.cn/" target="_blank" rel="nofollow" style="color:rgba(255,255,255,0.5)">宁ICP备2024005816号</a>
                </p>
                <?php
                $links = $db ? $db->query("SELECT name, url FROM friend_links WHERE status=1 ORDER BY sort_order")->fetchAll() : [];
                if (!empty($links)):
                ?>
                <div class="friend-links" style="margin-top:12px;">
                    <span style="color:rgba(255,255,255,0.4)">友情链接:</span>
                    <?php foreach ($links as $link): ?>
                        <a href="<?= $link['url'] ?>" target="_blank" style="color:rgba(255,255,255,0.7);margin:0 8px;"><?= htmlspecialchars($link['name']) ?></a>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </footer>
    <script src="<?= SITE_URL ?>/assets/js/main.js"></script>
    <script>
        // Mobile menu toggle
        function toggleMobileMenu() {
            const nav = document.getElementById('mobileNav');
            nav.classList.toggle('show');
        }
        
        // Close mobile menu when clicking outside
        document.addEventListener('click', function(e) {
            const nav = document.getElementById('mobileNav');
            const btn = document.querySelector('.mobile-menu-btn');
            if (nav && nav.classList.contains('show') && !nav.contains(e.target) && !btn.contains(e.target)) {
                nav.classList.remove('show');
            }
        });
        
        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth' });
                }
            });
        });
    </script>
</body>
</html>
