// Copy-to-clipboard function for warehouse address data
function data_copy() {
    const textToCopy = this.textContent.trim();
    
    if (navigator.clipboard && window.isSecureContext) {
        // Modern clipboard API
        navigator.clipboard.writeText(textToCopy).then(() => {
            showCopyFeedback(this, 'Kopiert!');
        }, () => {
            // Fallback for clipboard API failure
            fallbackCopyMethod.call(this, textToCopy);
        });
    } else {
        // Fallback for older browsers
        fallbackCopyMethod.call(this, textToCopy);
    }
}

// Fallback copy method for browsers without clipboard API
function fallbackCopyMethod(textToCopy) {
    const tempInput = document.createElement('textarea');
    tempInput.value = textToCopy;
    tempInput.style.position = 'fixed';
    tempInput.style.left = '-9999px';
    document.body.appendChild(tempInput);
    tempInput.select();
    
    try {
        const successful = document.execCommand('copy');
        showCopyFeedback(this, successful ? 'Kopiert!' : 'Kopieren fehlgeschlagen', !successful);
    } catch (err) {
        showCopyFeedback(this, 'Kopieren nicht unterstützt', true);
    }
    
    document.body.removeChild(tempInput);
}

// Show feedback after copy operation
function showCopyFeedback(element, message, isError = false) {
    const originalTitle = element.title || '';
    const originalBg = element.style.backgroundColor;
    
    // Set feedback styling
    element.style.backgroundColor = isError ? '#f8d7da' : '#d4edda';
    element.style.transition = 'background-color 0.3s ease';
    element.title = message;
    
    // Reset after 1 second
    setTimeout(() => {
        element.style.backgroundColor = originalBg;
        element.title = originalTitle;
    }, 1000);
}

$(document).on("rex:ready", function(warehouse, container) {

    // Initialize copy functionality for existing elements
    document.querySelectorAll('div[data-warehouse-copy]').forEach(el => {
        el.addEventListener('click', data_copy);
    });

    // Wait for QuickNavigation - Attach event to form after 2 seconds
    setTimeout(() => {

        // Add submit event listener to warehouse_search form
        $('#warehouse_search').on('submit', function(e) {
            // Prevent form submission
            e.preventDefault();

            // Send AJAX request
            $.ajax({
                url: '/',
                type: 'GET',
                data: {
                    "rex-api-call": 'warehouse_search',
                    "q": $('input[name="q"]').val()
                },
                success: function(response) {
                    // Create table element with classes
                    const table = $('<table></table>').addClass('table table-striped table-hover');
                    const tbody = $('<tbody></tbody>');

                    // Iterate through response items
                    $.each(response, function(key, value) {
                        // Create table row with data
                        const tr = $('<tr></tr>');
                        const td1 = $('<td></td>').text(value['id']);
                        const td2 = $('<td></td>').append($(value['icon']));
                        const td4 = $('<td></td>').append($(value['url']));
                        const td5 = $('<td></td>').text(value['details']);
                        tr.append(td1, td2, td4, td5);

                        // Add row to tbody
                        tbody.append(tr);
                    });

                    // Add tbody to table and insert into results div
                    table.append(tbody);
                    $('#warehouseSearchResults').html(table);

                    // Re-initialize copy functionality for new elements
                    document.querySelectorAll('div[data-warehouse-copy]').forEach(el => {
                        el.addEventListener('click', data_copy);
                    });

                },
                error: function() {
                    // Show error message
                    $('#warehouseSearchResults').html('Ein Fehler ist aufgetreten.');
                }
            });
        });

    }, 2000);

});
