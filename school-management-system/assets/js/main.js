// Main JavaScript file for common functionality

$(document).ready(function() {
    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();
    
    // Confirm before destructive actions
    $('.confirm-action').on('click', function(e) {
        if (!confirm($(this).data('confirm-message') || 'Are you sure?')) {
            e.preventDefault();
        }
    });
    
    // AJAX form submission handler
    $('.ajax-form').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        const button = form.find('button[type="submit"]');
        const buttonText = button.text();
        
        button.prop('disabled', true).text('Processing...');
        
        $.ajax({
            url: form.attr('action'),
            type: form.attr('method'),
            data: form.serialize(),
            success: function(response) {
                if (response.success) {
                    if (response.redirect) {
                        window.location.href = response.redirect;
                    } else {
                        form.find('.alert').remove();
                        form.prepend('<div class="alert alert-success">' + response.message + '</div>');
                        if (form.hasClass('reset-on-success')) {
                            form[0].reset();
                        }
                    }
                } else {
                    form.find('.alert').remove();
                    form.prepend('<div class="alert alert-error">' + response.message + '</div>');
                }
            },
            error: function() {
                form.find('.alert').remove();
                form.prepend('<div class="alert alert-error">An error occurred. Please try again.</div>');
            },
            complete: function() {
                button.prop('disabled', false).text(buttonText);
            }
        });
    });
    
    // Auto-calculate averages
    $('.calculate-average').on('change', function() {
        const container = $(this).closest('.average-container');
        const inputs = container.find('input[type="number"]');
        let total = 0;
        let count = 0;
        
        inputs.each(function() {
            const value = parseFloat($(this).val());
            if (!isNaN(value)) {
                total += value;
                count++;
            }
        });
        
        if (count > 0) {
            const average = total / count;
            container.find('.average-result').text(average.toFixed(2));
        } else {
            container.find('.average-result').text('N/A');
        }
    });
});