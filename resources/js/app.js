import './bootstrap';
import $ from 'jquery';
import 'jquery-pjax';

// Initialize PJAX
$(document).pjax('a[data-pjax]', '#pjax-container', {
    timeout: 5000
});

// PJAX event handlers
$(document).on('pjax:send', function() {
    // Loading indicator on
});

$(document).on('pjax:complete', function() {
    // Loading indicator off
});
