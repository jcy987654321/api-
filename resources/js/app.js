import './bootstrap';
import $ from 'jquery';
import 'jquery-pjax';
import 'bootstrap';
import { Chart } from 'chart.js/auto';

// Initialize PJAX
$(document).pjax('a[data-pjax]', '#pjax-container', {
    timeout: 5000
});

// PJAX event handlers
$(document).on('pjax:send', function() {
    // Show loading indicator
    $('body').css('cursor', 'wait');
});

$(document).on('pjax:complete', function() {
    // Hide loading indicator
    $('body').css('cursor', 'default');
    
    // Re-initialize any scripts that need to run after PJAX
    initializeCharts();
});

// Initialize tooltips and popovers
$(function () {
    $('[data-toggle="tooltip"]').tooltip();
    $('[data-toggle="popover"]').popover();
});

// Chart initialization
function initializeCharts() {
    // User growth chart
    const chartElement = document.getElementById('userGrowthChart');
    if (chartElement) {
        const ctx = chartElement.getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartElement.dataset.labels ? JSON.parse(chartElement.dataset.labels) : [],
                datasets: [{
                    label: 'New Users',
                    data: chartElement.dataset.data ? JSON.parse(chartElement.dataset.data) : [],
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }
}

// Initialize on page load
$(document).ready(function() {
    initializeCharts();
});
