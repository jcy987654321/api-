const MediaPreview = {
  lightboxOverlay: null,
  lightboxContent: null,
  currentIndex: 0,
  mediaItems: [],

  init() {
    this.createLightbox();
    this.attachEventListeners();
    this.initLazyLoading();
  },

  createLightbox() {
    this.lightboxOverlay = document.createElement('div');
    this.lightboxOverlay.className = 'lightbox-overlay';
    this.lightboxOverlay.innerHTML = `
      <div class="lightbox-content">
        <button class="lightbox-close" aria-label="Close preview">&times;</button>
        <button class="lightbox-prev" aria-label="Previous image">&larr;</button>
        <button class="lightbox-next" aria-label="Next image">&rarr;</button>
        <div class="lightbox-media"></div>
        <div class="lightbox-caption"></div>
      </div>
    `;
    document.body.appendChild(this.lightboxOverlay);

    this.lightboxContent = this.lightboxOverlay.querySelector('.lightbox-media');

    this.lightboxOverlay.querySelector('.lightbox-close').addEventListener('click', () => this.close());
    this.lightboxOverlay.querySelector('.lightbox-prev').addEventListener('click', () => this.prev());
    this.lightboxOverlay.querySelector('.lightbox-next').addEventListener('click', () => this.next());
    this.lightboxOverlay.addEventListener('click', (e) => {
      if (e.target === this.lightboxOverlay) {
        this.close();
      }
    });

    document.addEventListener('keydown', (e) => {
      if (!this.lightboxOverlay.classList.contains('active')) return;
      
      if (e.key === 'Escape') this.close();
      if (e.key === 'ArrowLeft') this.prev();
      if (e.key === 'ArrowRight') this.next();
    });
  },

  attachEventListeners() {
    document.addEventListener('click', (e) => {
      const trigger = e.target.closest('[data-lightbox]');
      if (trigger) {
        e.preventDefault();
        this.open(trigger);
      }
    });
  },

  open(element) {
    this.mediaItems = Array.from(document.querySelectorAll(`[data-lightbox="${element.dataset.lightbox}"]`));
    this.currentIndex = this.mediaItems.indexOf(element);
    this.show();
    this.lightboxOverlay.classList.add('active');
    document.body.style.overflow = 'hidden';
  },

  close() {
    this.lightboxOverlay.classList.remove('active');
    document.body.style.overflow = '';
    this.lightboxContent.innerHTML = '';
  },

  show() {
    const item = this.mediaItems[this.currentIndex];
    const mediaUrl = item.href || item.src || item.dataset.src;
    const caption = item.dataset.caption || item.alt || '';

    this.lightboxContent.innerHTML = '';
    
    if (this.isVideo(mediaUrl)) {
      const video = document.createElement('video');
      video.src = mediaUrl;
      video.controls = true;
      video.autoplay = true;
      this.lightboxContent.appendChild(video);
    } else {
      const img = document.createElement('img');
      img.src = mediaUrl;
      img.alt = caption;
      this.lightboxContent.appendChild(img);
    }

    const captionElement = this.lightboxOverlay.querySelector('.lightbox-caption');
    captionElement.textContent = caption;

    const prevBtn = this.lightboxOverlay.querySelector('.lightbox-prev');
    const nextBtn = this.lightboxOverlay.querySelector('.lightbox-next');
    prevBtn.style.display = this.mediaItems.length > 1 ? 'block' : 'none';
    nextBtn.style.display = this.mediaItems.length > 1 ? 'block' : 'none';
  },

  prev() {
    if (this.mediaItems.length <= 1) return;
    this.currentIndex = (this.currentIndex - 1 + this.mediaItems.length) % this.mediaItems.length;
    this.show();
  },

  next() {
    if (this.mediaItems.length <= 1) return;
    this.currentIndex = (this.currentIndex + 1) % this.mediaItems.length;
    this.show();
  },

  isVideo(url) {
    return /\.(mp4|webm|ogg|mov)$/i.test(url);
  },

  initLazyLoading() {
    if ('IntersectionObserver' in window) {
      const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            const img = entry.target;
            if (img.dataset.src) {
              img.src = img.dataset.src;
              img.removeAttribute('data-src');
              img.classList.add('loaded');
              observer.unobserve(img);
            }
          }
        });
      }, {
        rootMargin: '50px 0px',
        threshold: 0.01
      });

      document.querySelectorAll('img[data-src]').forEach(img => {
        imageObserver.observe(img);
      });
    } else {
      document.querySelectorAll('img[data-src]').forEach(img => {
        img.src = img.dataset.src;
        img.removeAttribute('data-src');
      });
    }
  }
};

if (typeof window !== 'undefined') {
  window.MediaPreview = MediaPreview;
}

export default MediaPreview;

export const initMediaPreview = () => MediaPreview.init();
