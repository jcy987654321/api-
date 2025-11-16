import 'core-js/stable';
import 'regenerator-runtime/runtime';
import './polyfills';
import './vendor';
import './public-layout';

if (typeof window !== 'undefined') {
  window.initCharts = () => import('./chart-loader');
  window.initSyntaxHighlighter = () => import('./syntax-highlighter');
  window.initMediaPreview = () => import('./media-preview');
}

if (import.meta.env.DEV) {
  console.log('Development mode enabled');
}
