import Prism from 'prismjs';
import 'prismjs/themes/prism-tomorrow.css';

import 'prismjs/components/prism-javascript';
import 'prismjs/components/prism-typescript';
import 'prismjs/components/prism-jsx';
import 'prismjs/components/prism-tsx';
import 'prismjs/components/prism-css';
import 'prismjs/components/prism-scss';
import 'prismjs/components/prism-json';
import 'prismjs/components/prism-php';
import 'prismjs/components/prism-python';
import 'prismjs/components/prism-bash';
import 'prismjs/components/prism-sql';
import 'prismjs/components/prism-markdown';
import 'prismjs/components/prism-yaml';

import 'prismjs/plugins/line-numbers/prism-line-numbers';
import 'prismjs/plugins/line-numbers/prism-line-numbers.css';
import 'prismjs/plugins/toolbar/prism-toolbar';
import 'prismjs/plugins/toolbar/prism-toolbar.css';
import 'prismjs/plugins/copy-to-clipboard/prism-copy-to-clipboard';

export function highlightAll() {
  Prism.highlightAll();
}

export function highlightElement(element) {
  if (element) {
    Prism.highlightElement(element);
  }
}

export function highlightCode(code, language = 'javascript') {
  return Prism.highlight(code, Prism.languages[language] || Prism.languages.javascript, language);
}

export function initSyntaxHighlighting() {
  document.querySelectorAll('pre code').forEach((block) => {
    const pre = block.parentElement;
    
    if (!pre.classList.contains('line-numbers')) {
      pre.classList.add('line-numbers');
    }
    
    highlightElement(block);
  });
}

if (typeof window !== 'undefined') {
  window.Prism = Prism;
}

export { Prism };

export default {
  Prism,
  highlightAll,
  highlightElement,
  highlightCode,
  initSyntaxHighlighting
};
