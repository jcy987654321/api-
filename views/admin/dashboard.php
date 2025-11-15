<section class="page-header">
    <h1>Admin Dashboard</h1>
    <p>Manage your API management system</p>
</section>

<section class="admin-stats">
    <h2>System Overview</h2>
    <div class="stats-grid">
        <div class="stat-card">
            <h3><?= number_format($stats['total_apis']) ?></h3>
            <p>Total APIs</p>
        </div>
        <div class="stat-card">
            <h3><?= number_format($stats['active_apis']) ?></h3>
            <p>Active APIs</p>
        </div>
        <div class="stat-card">
            <h3><?= number_format($stats['total_announcements']) ?></h3>
            <p>Total Announcements</p>
        </div>
        <div class="stat-card">
            <h3><?= number_format($stats['published_announcements']) ?></h3>
            <p>Published Announcements</p>
        </div>
        <div class="stat-card">
            <h3><?= number_format($stats['total_feedback']) ?></h3>
            <p>Total Feedback</p>
        </div>
        <div class="stat-card">
            <h3><?= number_format($stats['new_feedback']) ?></h3>
            <p>New Feedback</p>
        </div>
    </div>
</section>

<section class="admin-actions">
    <h2>Quick Actions</h2>
    <div class="action-grid">
        <div class="action-card">
            <h3>SEO Settings</h3>
            <p>Configure metadata, sitemap, and RSS settings</p>
            <a href="/admin/seo-settings" class="btn btn-primary">Manage SEO</a>
        </div>
        
        <div class="action-card">
            <h3>Sitemap</h3>
            <p>Regenerate sitemap and ping search engines</p>
            <button class="btn btn-secondary" onclick="regenerateSitemap()">Regenerate Now</button>
        </div>
        
        <div class="action-card">
            <h3>RSS Feed</h3>
            <p>Regenerate RSS feed with latest content</p>
            <button class="btn btn-secondary" onclick="regenerateRss()">Regenerate Now</button>
        </div>
        
        <div class="action-card">
            <h3>View Site</h3>
            <p>Visit the public website</p>
            <a href="/" class="btn btn-secondary" target="_blank">Open Site</a>
        </div>
    </div>
</section>

<section class="admin-links">
    <h2>Management</h2>
    <div class="links-grid">
        <a href="/admin/apis" class="link-card">
            <h3>APIs</h3>
            <p>Manage API listings and documentation</p>
        </a>
        
        <a href="/admin/announcements" class="link-card">
            <h3>Announcements</h3>
            <p>Create and manage announcements</p>
        </a>
        
        <a href="/admin/feedback" class="link-card">
            <h3>Feedback</h3>
            <p>Review user feedback and suggestions</p>
        </a>
        
        <a href="/admin/settings" class="link-card">
            <h3>Settings</h3>
            <p>Configure system settings and preferences</p>
        </a>
    </div>
</section>

<script>
function regenerateSitemap() {
    const btn = event.target;
    const originalText = btn.textContent;
    
    btn.textContent = 'Regenerating...';
    btn.disabled = true;
    
    fetch('/admin/sitemap/regenerate', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            btn.textContent = '✓ Regenerated';
            btn.classList.add('success');
        } else {
            btn.textContent = '✗ Failed';
            btn.classList.add('error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        btn.textContent = '✗ Error';
        btn.classList.add('error');
    })
    .finally(() => {
        setTimeout(() => {
            btn.textContent = originalText;
            btn.disabled = false;
            btn.classList.remove('success', 'error');
        }, 3000);
    });
}

function regenerateRss() {
    const btn = event.target;
    const originalText = btn.textContent;
    
    btn.textContent = 'Regenerating...';
    btn.disabled = true;
    
    fetch('/admin/rss/regenerate', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            btn.textContent = '✓ Regenerated';
            btn.classList.add('success');
        } else {
            btn.textContent = '✗ Failed';
            btn.classList.add('error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        btn.textContent = '✗ Error';
        btn.classList.add('error');
    })
    .finally(() => {
        setTimeout(() => {
            btn.textContent = originalText;
            btn.disabled = false;
            btn.classList.remove('success', 'error');
        }, 3000);
    });
}
</script>

<style>
.admin-stats .stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
    margin-bottom: 3rem;
}

.stat-card {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 0.75rem;
    padding: 1.5rem;
    text-align: center;
}

.stat-card h3 {
    font-size: 2rem;
    color: #3b82f6;
    margin-bottom: 0.5rem;
}

.stat-card p {
    color: #6b7280;
    font-weight: 500;
}

.admin-actions .action-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
    margin-bottom: 3rem;
}

.action-card {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 0.75rem;
    padding: 1.5rem;
}

.action-card h3 {
    color: #1f2937;
    margin-bottom: 0.5rem;
}

.action-card p {
    color: #6b7280;
    margin-bottom: 1rem;
}

.admin-links .links-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1rem;
}

.link-card {
    display: block;
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 0.75rem;
    padding: 1.5rem;
    text-decoration: none;
    color: inherit;
    transition: all 0.2s;
}

.link-card:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    transform: translateY(-2px);
}

.link-card h3 {
    color: #1f2937;
    margin-bottom: 0.5rem;
}

.link-card p {
    color: #6b7280;
}

.btn.success {
    background: #10b981;
    color: white;
}

.btn.error {
    background: #ef4444;
    color: white;
}
</style>