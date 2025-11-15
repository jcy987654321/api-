<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    
    <!-- Basic Meta Tags -->
    <title><?= htmlspecialchars($meta['title']) ?></title>
    <meta name="description" content="<?= htmlspecialchars($meta['description']) ?>">
    <meta name="keywords" content="<?= htmlspecialchars($meta['keywords']) ?>">
    <meta name="author" content="<?= htmlspecialchars(\App\Config\Settings::get('site.author')) ?>">
    
    <!-- Canonical URL -->
    <link rel="canonical" href="<?= htmlspecialchars($meta['canonical_url']) ?>">
    
    <!-- Open Graph Tags -->
    <meta property="og:title" content="<?= htmlspecialchars($meta['og_title']) ?>">
    <meta property="og:description" content="<?= htmlspecialchars($meta['og_description']) ?>">
    <meta property="og:image" content="<?= htmlspecialchars($meta['og_image']) ?>">
    <meta property="og:url" content="<?= htmlspecialchars($meta['og_url']) ?>">
    <meta property="og:type" content="<?= htmlspecialchars($meta['og_type']) ?>">
    <meta property="og:site_name" content="<?= htmlspecialchars(\App\Config\Settings::get('site.name')) ?>">
    
    <!-- Twitter Card Tags -->
    <meta name="twitter:card" content="<?= htmlspecialchars($meta['twitter_card']) ?>">
    <meta name="twitter:title" content="<?= htmlspecialchars($meta['twitter_title']) ?>">
    <meta name="twitter:description" content="<?= htmlspecialchars($meta['twitter_description']) ?>">
    <meta name="twitter:image" content="<?= htmlspecialchars($meta['twitter_image']) ?>">
    <meta name="twitter:site" content="<?= htmlspecialchars($meta['twitter_site']) ?>">
    
    <!-- Additional SEO Meta Tags -->
    <meta name="robots" content="index, follow">
    <meta name="googlebot" content="index, follow">
    <meta name="language" content="English">
    <meta name="revisit-after" content="7 days">
    
    <!-- JSON-LD Structured Data -->
    <script type="application/ld+json">
    <?= json_encode($jsonLd, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) ?>
    </script>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    
    <!-- RSS Feed -->
    <link rel="alternate" type="application/rss+xml" title="<?= htmlspecialchars(\App\Config\Settings::get('site.name')) ?> RSS Feed" href="<?= htmlspecialchars(\App\Config\Settings::get('site.url')) ?>/rss.xml">
    
    <!-- CSS -->
    <link rel="stylesheet" href="/assets/css/style.css">
    
    <!-- Preconnect to external domains -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
</head>
<body>
    <header class="site-header">
        <nav class="main-nav">
            <div class="container">
                <a href="/" class="logo">
                    <?= htmlspecialchars(\App\Config\Settings::get('site.name')) ?>
                </a>
                <ul class="nav-links">
                    <li><a href="/">Home</a></li>
                    <li><a href="/apis">APIs</a></li>
                    <li><a href="/announcements">Announcements</a></li>
                    <li><a href="/feedback">Feedback</a></li>
                </ul>
            </div>
        </nav>
    </header>

    <main class="site-main">
        <div class="container">
            <?php include ROOT_PATH . "/views/{$template}.php"; ?>
        </div>
    </main>

    <footer class="site-footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3><?= htmlspecialchars(\App\Config\Settings::get('site.name')) ?></h3>
                    <p><?= htmlspecialchars(\App\Config\Settings::get('site.description')) ?></p>
                </div>
                <div class="footer-section">
                    <h4>Quick Links</h4>
                    <ul>
                        <li><a href="/sitemap.xml">Sitemap</a></li>
                        <li><a href="/rss.xml">RSS Feed</a></li>
                        <li><a href="/robots.txt">Robots.txt</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Resources</h4>
                    <ul>
                        <li><a href="/apis">API Directory</a></li>
                        <li><a href="/announcements">Announcements</a></li>
                        <li><a href="/feedback">Contact Us</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?= date('Y') ?> <?= htmlspecialchars(\App\Config\Settings::get('site.name')) ?>. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- JavaScript -->
    <script src="/assets/js/app.js"></script>
</body>
</html>