<section class="page-header">
    <h1>Feedback</h1>
    <p>Share your thoughts, suggestions, and help us improve our platform</p>
</section>

<section class="feedback-form">
    <div class="form-container">
        <?php if (!empty($errors)): ?>
            <div class="error-messages">
                <h3>Please fix the following errors:</h3>
                <ul>
                    <?php foreach ($errors as $field => $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" action="/feedback" class="feedback-form-form">
            <div class="form-group">
                <label for="name">Name *</label>
                <input 
                    type="text" 
                    id="name" 
                    name="name" 
                    value="<?= htmlspecialchars($old_input['name'] ?? '') ?>" 
                    required
                    class="<?= isset($errors['name']) ? 'error' : '' ?>"
                >
            </div>

            <div class="form-group">
                <label for="email">Email *</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    value="<?= htmlspecialchars($old_input['email'] ?? '') ?>" 
                    required
                    class="<?= isset($errors['email']) ? 'error' : '' ?>"
                >
                <small class="form-help">We'll never share your email with anyone else.</small>
            </div>

            <div class="form-group">
                <label for="type">Feedback Type</label>
                <select id="type" name="type">
                    <option value="general" <?= ($old_input['type'] ?? '') === 'general' ? 'selected' : '' ?>>General Feedback</option>
                    <option value="bug" <?= ($old_input['type'] ?? '') === 'bug' ? 'selected' : '' ?>>Bug Report</option>
                    <option value="feature" <?= ($old_input['type'] ?? '') === 'feature' ? 'selected' : '' ?>>Feature Request</option>
                    <option value="api" <?= ($old_input['type'] ?? '') === 'api' ? 'selected' : '' ?>>API Suggestion</option>
                    <option value="other" <?= ($old_input['type'] ?? '') === 'other' ? 'selected' : '' ?>>Other</option>
                </select>
            </div>

            <div class="form-group">
                <label for="message">Message *</label>
                <textarea 
                    id="message" 
                    name="message" 
                    rows="6" 
                    required
                    class="<?= isset($errors['message']) ? 'error' : '' ?>"
                ><?= htmlspecialchars($old_input['message'] ?? '') ?></textarea>
                <small class="form-help">Please provide as much detail as possible to help us understand your feedback.</small>
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary">Send Feedback</button>
                <button type="reset" class="btn btn-secondary">Clear Form</button>
            </div>
        </form>
    </div>
</section>

<section class="contact-info">
    <div class="contact-methods">
        <h2>Other Ways to Reach Us</h2>
        <div class="contact-grid">
            <div class="contact-item">
                <h3>GitHub Issues</h3>
                <p>Report bugs or request features on our GitHub repository.</p>
                <a href="#" class="btn btn-secondary">View on GitHub</a>
            </div>
            <div class="contact-item">
                <h3>Twitter</h3>
                <p>Follow us for updates and quick questions.</p>
                <a href="https://twitter.com/<?= htmlspecialchars(str_replace('@', '', \App\Config\Settings::get('site.twitter_handle'))) ?>" class="btn btn-secondary" target="_blank" rel="noopener">Follow on Twitter</a>
            </div>
            <div class="contact-item">
                <h3>Documentation</h3>
                <p>Check our documentation for guides and API references.</p>
                <a href="/docs" class="btn btn-secondary">View Documentation</a>
            </div>
        </div>
    </div>
</section>