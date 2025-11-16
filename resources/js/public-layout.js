const PublicLayout = {
  init: function() {
    this.initThemeToggle();
    this.initPJAX();
    this.initScrollControls();
    this.initMobileMenu();
    this.initAccessibility();
  },

  initThemeToggle: function() {
    const themeToggle = document.querySelector('.theme-toggle');
    const html = document.documentElement;
    
    if (!themeToggle) return;

    const getAutoTheme = () => {
      const hour = new Date().getHours();
      return (hour >= 6 && hour < 18) ? 'light' : 'dark';
    };

    const applyTheme = (theme, isOverride = false) => {
      html.setAttribute('data-theme', theme);
      localStorage.setItem('theme', theme);
      
      if (isOverride) {
        localStorage.setItem('themeOverride', 'true');
      }
    };

    const loadTheme = () => {
      const savedTheme = localStorage.getItem('theme');
      const savedOverride = localStorage.getItem('themeOverride');
      
      if (savedOverride === 'true' && savedTheme) {
        applyTheme(savedTheme, false);
      } else {
        applyTheme(getAutoTheme(), false);
      }
    };

    themeToggle.addEventListener('click', () => {
      const currentTheme = html.getAttribute('data-theme');
      const newTheme = currentTheme === 'light' ? 'dark' : 'light';
      applyTheme(newTheme, true);
    });

    loadTheme();

    setInterval(() => {
      const savedOverride = localStorage.getItem('themeOverride');
      if (savedOverride !== 'true') {
        applyTheme(getAutoTheme(), false);
      }
    }, 60000);
  },

  initPJAX: function() {
    if (!$.support.pjax) {
      console.warn('PJAX not supported, falling back to regular navigation');
      return;
    }

    const pjaxContainer = '#pjax-container';
    const pjaxLoader = $('.pjax-loader');

    $(document).pjax('a[data-pjax]', pjaxContainer, {
      timeout: 5000,
      scrollTo: 0,
      push: true,
      replace: false
    });

    $(document).on('pjax:send', function() {
      pjaxLoader.addClass('loading');
      $('body').css('pointer-events', 'none');
    });

    $(document).on('pjax:complete', function() {
      pjaxLoader.removeClass('loading');
      $('body').css('pointer-events', '');
      PublicLayout.updateActiveNavLink();
    });

    $(document).on('pjax:success', function() {
      if (window.gtag) {
        gtag('config', 'GA_MEASUREMENT_ID', {
          'page_path': window.location.pathname
        });
      }
    });

    $(document).on('pjax:error', function(xhr, textStatus, error) {
      console.error('PJAX error:', error);
      pjaxLoader.removeClass('loading');
      $('body').css('pointer-events', '');
      
      if (xhr.status === 404) {
        window.location.href = '/404';
      } else {
        return true;
      }
    });

    $(document).on('pjax:timeout', function(event) {
      event.preventDefault();
      console.warn('PJAX timeout, performing full page reload');
      window.location.href = event.target.href;
    });

    this.updateActiveNavLink();
  },

  updateActiveNavLink: function() {
    const currentPath = window.location.pathname;
    const navLinks = document.querySelectorAll('.nav-link');
    
    navLinks.forEach(link => {
      const linkPath = new URL(link.href, window.location.origin).pathname;
      
      if (linkPath === currentPath || (currentPath !== '/' && currentPath.startsWith(linkPath) && linkPath !== '/')) {
        link.classList.add('active');
      } else {
        link.classList.remove('active');
      }
    });
  },

  initScrollControls: function() {
    const scrollControls = document.querySelector('.scroll-controls');
    const scrollTopBtn = document.querySelector('.scroll-top');
    const scrollBottomBtn = document.querySelector('.scroll-bottom');
    
    if (!scrollControls) return;

    const toggleScrollControls = () => {
      const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
      const scrollHeight = document.documentElement.scrollHeight;
      const clientHeight = document.documentElement.clientHeight;
      const scrollBottom = scrollHeight - scrollTop - clientHeight;

      if (scrollTop > 300 || scrollBottom > 300) {
        scrollControls.classList.add('visible');
      } else {
        scrollControls.classList.remove('visible');
      }

      if (scrollTopBtn) {
        scrollTopBtn.style.display = scrollTop > 300 ? 'flex' : 'none';
      }
      
      if (scrollBottomBtn) {
        scrollBottomBtn.style.display = scrollBottom > 300 ? 'flex' : 'none';
      }
    };

    const smoothScrollTo = (targetPosition) => {
      const startPosition = window.pageYOffset;
      const distance = targetPosition - startPosition;
      const duration = 500;
      let start = null;

      const animation = (currentTime) => {
        if (start === null) start = currentTime;
        const timeElapsed = currentTime - start;
        const progress = Math.min(timeElapsed / duration, 1);
        
        const easeInOutCubic = progress < 0.5
          ? 4 * progress * progress * progress
          : 1 - Math.pow(-2 * progress + 2, 3) / 2;
        
        window.scrollTo(0, startPosition + distance * easeInOutCubic);
        
        if (timeElapsed < duration) {
          requestAnimationFrame(animation);
        }
      };

      requestAnimationFrame(animation);
    };

    if (scrollTopBtn) {
      scrollTopBtn.addEventListener('click', () => {
        smoothScrollTo(0);
      });

      scrollTopBtn.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' || e.key === ' ') {
          e.preventDefault();
          smoothScrollTo(0);
        }
      });
    }

    if (scrollBottomBtn) {
      scrollBottomBtn.addEventListener('click', () => {
        smoothScrollTo(document.documentElement.scrollHeight);
      });

      scrollBottomBtn.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' || e.key === ' ') {
          e.preventDefault();
          smoothScrollTo(document.documentElement.scrollHeight);
        }
      });
    }

    let scrollTimeout;
    window.addEventListener('scroll', () => {
      clearTimeout(scrollTimeout);
      scrollTimeout = setTimeout(toggleScrollControls, 100);
    }, { passive: true });

    toggleScrollControls();
  },

  initMobileMenu: function() {
    const menuToggle = document.querySelector('.mobile-menu-toggle');
    const navbarMenu = document.querySelector('.navbar-menu');
    
    if (!menuToggle || !navbarMenu) return;

    menuToggle.addEventListener('click', () => {
      const isExpanded = menuToggle.getAttribute('aria-expanded') === 'true';
      menuToggle.setAttribute('aria-expanded', !isExpanded);
      navbarMenu.classList.toggle('active');
      
      if (!isExpanded) {
        document.body.style.overflow = 'hidden';
      } else {
        document.body.style.overflow = '';
      }
    });

    document.addEventListener('click', (e) => {
      if (!menuToggle.contains(e.target) && !navbarMenu.contains(e.target)) {
        menuToggle.setAttribute('aria-expanded', 'false');
        navbarMenu.classList.remove('active');
        document.body.style.overflow = '';
      }
    });

    const navLinks = navbarMenu.querySelectorAll('.nav-link');
    navLinks.forEach(link => {
      link.addEventListener('click', () => {
        menuToggle.setAttribute('aria-expanded', 'false');
        navbarMenu.classList.remove('active');
        document.body.style.overflow = '';
      });
    });

    window.addEventListener('resize', () => {
      if (window.innerWidth >= 768) {
        menuToggle.setAttribute('aria-expanded', 'false');
        navbarMenu.classList.remove('active');
        document.body.style.overflow = '';
      }
    });
  },

  initAccessibility: function() {
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Tab') {
        document.body.classList.add('keyboard-nav');
      }
    });

    document.addEventListener('mousedown', () => {
      document.body.classList.remove('keyboard-nav');
    });

    const focusableElements = document.querySelectorAll(
      'a, button, input, select, textarea, [tabindex]:not([tabindex="-1"])'
    );

    focusableElements.forEach(element => {
      element.addEventListener('focus', function() {
        this.classList.add('is-focused');
      });

      element.addEventListener('blur', function() {
        this.classList.remove('is-focused');
      });
    });

    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') {
        const menuToggle = document.querySelector('.mobile-menu-toggle');
        const navbarMenu = document.querySelector('.navbar-menu');
        
        if (navbarMenu && navbarMenu.classList.contains('active')) {
          menuToggle.setAttribute('aria-expanded', 'false');
          navbarMenu.classList.remove('active');
          document.body.style.overflow = '';
          menuToggle.focus();
        }
      }
    });
  }
};

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', () => PublicLayout.init());
} else {
  PublicLayout.init();
}

window.PublicLayout = PublicLayout;

export default PublicLayout;
