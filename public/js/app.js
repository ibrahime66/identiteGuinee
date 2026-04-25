// IdentiGuinée - Main JavaScript File

document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Initialize popovers
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });

    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        var alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            var bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);

    // Form validation
    var forms = document.querySelectorAll('.needs-validation');
    Array.prototype.slice.call(forms).forEach(function(form) {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });

    // Loading states for buttons
    var submitButtons = document.querySelectorAll('button[type="submit"]');
    submitButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            if (button.form.checkValidity()) {
                button.disabled = true;
                button.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Chargement...';
                
                // Re-enable after 10 seconds (fallback)
                setTimeout(function() {
                    button.disabled = false;
                    button.innerHTML = button.getAttribute('data-original-text') || 'Soumettre';
                }, 10000);
            }
        });
    });

    // Store original button text
    submitButtons.forEach(function(button) {
        button.setAttribute('data-original-text', button.innerHTML);
    });

    // Smooth scrolling for anchor links
    var anchorLinks = document.querySelectorAll('a[href^="#"]');
    anchorLinks.forEach(function(link) {
        link.addEventListener('click', function(e) {
            var target = document.querySelector(link.getAttribute('href'));
            if (target) {
                e.preventDefault();
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Copy to clipboard functionality
    var copyButtons = document.querySelectorAll('.copy-to-clipboard');
    copyButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            var text = button.getAttribute('data-copy-text');
            navigator.clipboard.writeText(text).then(function() {
                var originalText = button.innerHTML;
                button.innerHTML = '<i class="fas fa-check"></i> Copié!';
                button.classList.add('btn-success');
                button.classList.remove('btn-outline-secondary');
                
                setTimeout(function() {
                    button.innerHTML = originalText;
                    button.classList.remove('btn-success');
                    button.classList.add('btn-outline-secondary');
                }, 2000);
            });
        });
    });

    // Dynamic form field validation
    var requiredFields = document.querySelectorAll('[required]');
    requiredFields.forEach(function(field) {
        field.addEventListener('blur', function() {
            if (field.value.trim() === '') {
                field.classList.add('is-invalid');
            } else {
                field.classList.remove('is-invalid');
                field.classList.add('is-valid');
            }
        });
    });

    // Phone number formatting
    var phoneFields = document.querySelectorAll('input[type="tel"]');
    phoneFields.forEach(function(field) {
        field.addEventListener('input', function(e) {
            var value = e.target.value.replace(/\s/g, '');
            var formattedValue = value.replace(/(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})/, '$1 $2 $3 $4 $5');
            e.target.value = formattedValue;
        });
    });

    // Date picker initialization (if using flatpickr or similar)
    var dateFields = document.querySelectorAll('input[type="date"]');
    dateFields.forEach(function(field) {
        // Set max date to today for birth dates
        if (field.name.includes('birth')) {
            field.max = new Date().toISOString().split('T')[0];
        }
    });

    // File upload preview
    var fileInputs = document.querySelectorAll('input[type="file"]');
    fileInputs.forEach(function(input) {
        input.addEventListener('change', function(e) {
            var file = e.target.files[0];
            if (file) {
                var fileSize = (file.size / 1024 / 1024).toFixed(2);
                var fileName = file.name;
                
                // Display file info
                var fileInfo = document.createElement('div');
                fileInfo.className = 'mt-2 p-2 bg-light rounded';
                fileInfo.innerHTML = '<small><strong>' + fileName + '</strong> (' + fileSize + ' MB)</small>';
                
                // Remove previous file info
                var previousInfo = input.parentNode.querySelector('.file-info');
                if (previousInfo) {
                    previousInfo.remove();
                }
                
                fileInfo.classList.add('file-info');
                input.parentNode.appendChild(fileInfo);
            }
        });
    });

    // Search functionality
    var searchInputs = document.querySelectorAll('.search-input');
    searchInputs.forEach(function(input) {
        var searchTimeout;
        input.addEventListener('input', function(e) {
            clearTimeout(searchTimeout);
            var searchTerm = e.target.value.toLowerCase();
            
            searchTimeout = setTimeout(function() {
                var target = document.querySelector(e.target.getAttribute('data-search-target'));
                if (target) {
                    var items = target.querySelectorAll('.searchable-item');
                    items.forEach(function(item) {
                        var text = item.textContent.toLowerCase();
                        if (text.includes(searchTerm)) {
                            item.style.display = '';
                        } else {
                            item.style.display = 'none';
                        }
                    });
                }
            }, 300);
        });
    });

    // Print functionality
    var printButtons = document.querySelectorAll('.print-button');
    printButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            window.print();
        });
    });

    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Ctrl+P for print
        if (e.ctrlKey && e.key === 'p') {
            e.preventDefault();
            window.print();
        }
        
        // Escape to close modals
        if (e.key === 'Escape') {
            var modals = document.querySelectorAll('.modal.show');
            modals.forEach(function(modal) {
                var bsModal = bootstrap.Modal.getInstance(modal);
                if (bsModal) {
                    bsModal.hide();
                }
            });
        }
    });

    // Progress bar animation
    var progressBars = document.querySelectorAll('.progress-bar');
    progressBars.forEach(function(bar) {
        var width = bar.getAttribute('aria-valuenow');
        setTimeout(function() {
            bar.style.width = width + '%';
        }, 100);
    });

    // Table sorting
    var sortableTables = document.querySelectorAll('.sortable-table');
    sortableTables.forEach(function(table) {
        var headers = table.querySelectorAll('th[data-sort]');
        headers.forEach(function(header) {
            header.style.cursor = 'pointer';
            header.addEventListener('click', function() {
                var sortBy = header.getAttribute('data-sort');
                var tbody = table.querySelector('tbody');
                var rows = Array.from(tbody.querySelectorAll('tr'));
                
                rows.sort(function(a, b) {
                    var aVal = a.querySelector('td[data-' + sortBy + ']').textContent;
                    var bVal = b.querySelector('td[data-' + sortBy + ']').textContent;
                    return aVal.localeCompare(bVal);
                });
                
                tbody.innerHTML = '';
                rows.forEach(function(row) {
                    tbody.appendChild(row);
                });
            });
        });
    });

    // Lazy loading images
    var lazyImages = document.querySelectorAll('img[data-src]');
    if ('IntersectionObserver' in window) {
        var imageObserver = new IntersectionObserver(function(entries, observer) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    var img = entry.target;
                    img.src = img.getAttribute('data-src');
                    img.removeAttribute('data-src');
                    imageObserver.unobserve(img);
                }
            });
        });
        
        lazyImages.forEach(function(img) {
            imageObserver.observe(img);
        });
    }

    // Console welcome message
    console.log('%c🇬🇳 IdentiGuinée - Plateforme Nationale d\'Identité Numérique', 'font-size: 16px; font-weight: bold; color: #3498db;');
    console.log('%cVersion 1.0.0 - MIABE Hackathon 2026', 'font-size: 12px; color: #7f8c8d;');
});

// Utility functions
window.IdentiGuinée = {
    showLoading: function(element) {
        element.disabled = true;
        element.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Chargement...';
    },
    
    hideLoading: function(element, originalText) {
        element.disabled = false;
        element.innerHTML = originalText;
    },
    
    showAlert: function(message, type = 'info') {
        var alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-' + type + ' alert-dismissible fade show';
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        var container = document.querySelector('.container');
        container.insertBefore(alertDiv, container.firstChild);
        
        setTimeout(function() {
            alertDiv.remove();
        }, 5000);
    },
    
    formatCurrency: function(amount) {
        return new Intl.NumberFormat('fr-GN', {
            style: 'currency',
            currency: 'GNF'
        }).format(amount);
    },
    
    formatDate: function(date) {
        return new Intl.DateTimeFormat('fr-GN', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        }).format(new Date(date));
    }
};
