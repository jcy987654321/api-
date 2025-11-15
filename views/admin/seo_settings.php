<section class="page-header">
    <h1>SEO Settings</h1>
    <p>Configure metadata, sitemap, and RSS settings</p>
</section>

<?php if ($success): ?>
    <div class="success-message">
        ✓ SEO settings saved successfully!
    </div>
<?php endif; ?>

<section class="seo-settings">
    <form method="POST" action="/admin/seo-settings" class="settings-form">
        
        <div class="settings-section">
            <h2>Site Information</h2>
            
            <div class="form-group">
                <label for="site_name">Site Name</label>
                <input type="text" id="site_name" name="site_name" 
                       value="<?= htmlspecialchars($settings['site_name'] ?? '') ?>" required>
            </div>
            
            <div class="form-group">
                <label for="site_description">Site Description</label>
                <textarea id="site_description" name="site_description" rows="3" required><?= htmlspecialchars($settings['site_description'] ?? '') ?></textarea>
                <small class="form-help">Used for meta description and social media</small>
            </div>
            
            <div class="form-group">
                <label for="site_url">Site URL</label>
                <input type="url" id="site_url" name="site_url" 
                       value="<?= htmlspecialchars($settings['site_url'] ?? '') ?>" required>
                <small class="form-help">Base URL of your website (include https://)</small>
            </div>
            
            <div class="form-group">
                <label for="site_author">Site Author</label>
                <input type="text" id="site_author" name="site_author" 
                       value="<?= htmlspecialchars($settings['site_author'] ?? '') ?>">
            </div>
            
            <div class="form-group">
                <label for="twitter_handle">Twitter Handle</label>
                <input type="text" id="twitter_handle" name="twitter_handle" 
                       value="<?= htmlspecialchars($settings['twitter_handle'] ?? '') ?>" 
                       placeholder="@yourhandle">
                <small class="form-help">Include @ symbol for Twitter Card optimization</small>
            </div>
        </div>
        
        <div class="settings-section">
            <h2>SEO Metadata</h2>
            
            <div class="form-group">
                <label for="default_title">Default Page Title</label>
                <input type="text" id="default_title" name="default_title" 
                       value="<?= htmlspecialchars($settings['default_title'] ?? '') ?>" required>
            </div>
            
            <div class="form-group">
                <label for="default_description">Default Meta Description</label>
                <textarea id="default_description" name="default_description" rows="3" required><?= htmlspecialchars($settings['default_description'] ?? '') ?></textarea>
                <small class="form-help">Used when page-specific description is not available</small>
            </div>
            
            <div class="form-group">
                <label for="default_keywords">Default Keywords</label>
                <input type="text" id="default_keywords" name="default_keywords" 
                       value="<?= htmlspecialchars($settings['default_keywords'] ?? '') ?>">
                <small class="form-help">Comma-separated keywords for SEO</small>
            </div>
            
            <div class="form-group">
                <label for="custom_keywords">Custom Keywords</label>
                <input type="text" id="custom_keywords" name="custom_keywords" 
                       value="<?= htmlspecialchars($settings['custom_keywords'] ?? '') ?>">
                <small class="form-help">Additional keywords that will be added to all pages</small>
            </div>
            
            <div class="form-group">
                <label for="meta_image">Default Meta Image</label>
                <input type="text" id="meta_image" name="meta_image" 
                       value="<?= htmlspecialchars($settings['meta_image'] ?? '/assets/images/og-default.jpg') ?>">
                <small class="form-help">Path to default Open Graph image (1200x630px recommended)</small>
            </div>
            
            <div class="form-group">
                <label for="twitter_card_type">Twitter Card Type</label>
                <select id="twitter_card_type" name="twitter_card_type">
                    <option value="summary" <?= ($settings['twitter_card_type'] ?? '') === 'summary' ? 'selected' : '' ?>>Summary</option>
                    <option value="summary_large_image" <?= ($settings['twitter_card_type'] ?? '') === 'summary_large_image' ? 'selected' : '' ?>>Summary with Large Image</option>
                    <option value="app" <?= ($settings['twitter_card_type'] ?? '') === 'app' ? 'selected' : '' ?>>App</option>
                    <option value="player" <?= ($settings['twitter_card_type'] ?? '') === 'player' ? 'selected' : '' ?>>Player</option>
                </select>
            </div>
        </div>
        
        <div class="settings-section">
            <h2>Sitemap Settings</h2>
            
            <div class="form-group">
                <label for="sitemap_cache_duration">Cache Duration (seconds)</label>
                <input type="number" id="sitemap_cache_duration" name="sitemap_cache_duration" 
                       value="<?= htmlspecialchars($settings['sitemap_cache_duration'] ?? '3600') ?>" min="60">
                <small class="form-help">How long to cache the sitemap (3600 = 1 hour)</small>
            </div>
            
            <div class="form-group">
                <label>
                    <input type="checkbox" name="sitemap_ping_engines" value="true" 
                           <?= ($settings['sitemap_ping_engines'] ?? 'true') === 'true' ? 'checked' : '' ?>>
                    Ping search engines after sitemap generation
                </label>
                <small class="form-help">Automatically notify Google and Bing when sitemap is updated</small>
            </div>
        </div>
        
        <div class="settings-section">
            <h2>RSS Feed Settings</h2>
            
            <div class="form-group">
                <label for="rss_cache_duration">Cache Duration (seconds)</label>
                <input type="number" id="rss_cache_duration" name="rss_cache_duration" 
                       value="<?= htmlspecialchars($settings['rss_cache_duration'] ?? '1800') ?>" min="60">
                <small class="form-help">How long to cache the RSS feed (1800 = 30 minutes)</small>
            </div>
            
            <div class="form-group">
                <label for="rss_items_per_feed">Items per Feed</label>
                <input type="number" id="rss_items_per_feed" name="rss_items_per_feed" 
                       value="<?= htmlspecialchars($settings['rss_items_per_feed'] ?? '50') ?>" min="1" max="100">
                <small class="form-help">Maximum number of items to include in RSS feed</small>
            </div>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Save Settings</button>
            <a href="/admin/dashboard" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</section>

<style>
.success-message {
    background: #d1fae5;
    border: 1px solid #a7f3d0;
    color: #065f46;
    padding: 1rem;
    border-radius: 0.5rem;
    margin-bottom: 2rem;
}

.settings-form {
    max-width: 800px;
}

.settings-section {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 0.75rem;
    padding: 2rem;
    margin-bottom: 2rem;
}

.settings-section h2 {
    color: #1f2937;
    margin-bottom: 1.5rem;
    border-bottom: 2px solid #f3f4f6;
    padding-bottom: 0.5rem;
}

.form-actions {
    display: flex;
    gap: 1rem;
    margin-top: 2rem;
}

input[type="checkbox"] {
    margin-right: 0.5rem;
}
</style>