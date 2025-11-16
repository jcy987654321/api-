if (typeof window !== 'undefined' && typeof Element !== 'undefined') {
  if (!Element.prototype.matches) {
    Element.prototype.matches =
      Element.prototype.msMatchesSelector ||
      Element.prototype.webkitMatchesSelector;
  }

  if (!Element.prototype.closest) {
    Element.prototype.closest = function (s) {
      let el = this;
      do {
        if (Element.prototype.matches.call(el, s)) return el;
        el = el.parentElement || el.parentNode;
      } while (el !== null && el.nodeType === 1);
      return null;
    };
  }

  if (typeof window.CustomEvent !== 'function') {
    function CustomEvent(event, params) {
      params = params || { bubbles: false, cancelable: false, detail: null };
      const evt = document.createEvent('CustomEvent');
      evt.initCustomEvent(event, params.bubbles, params.cancelable, params.detail);
      return evt;
    }
    window.CustomEvent = CustomEvent;
  }

  if (!('IntersectionObserver' in window)) {
    const observer = {
      observe: () => {},
      unobserve: () => {},
      disconnect: () => {}
    };
    window.IntersectionObserver = function() {
      return observer;
    };
  }
}
