// Copy-to-clipboard function for warehouse address data
function data_copy() {
    var textToCopy = this.textContent.trim();
    
    if (navigator.clipboard && window.isSecureContext) {
        // Modern clipboard API
        navigator.clipboard.writeText(textToCopy).then(function() {
            showCopyFeedback(this, 'Kopiert!');
        }.bind(this), function() {
            // Fallback for clipboard API failure
            fallbackCopyMethod.call(this, textToCopy);
        }.bind(this));
    } else {
        // Fallback for older browsers
        fallbackCopyMethod.call(this, textToCopy);
    }
}

// Fallback copy method for browsers without clipboard API
function fallbackCopyMethod(textToCopy) {
    var tempInput = document.createElement('textarea');
    tempInput.value = textToCopy;
    tempInput.style.position = 'fixed';
    tempInput.style.left = '-9999px';
    document.body.appendChild(tempInput);
    tempInput.select();
    
    try {
        var successful = document.execCommand('copy');
        if (successful) {
            showCopyFeedback(this, 'Kopiert!');
        } else {
            showCopyFeedback(this, 'Kopieren fehlgeschlagen', true);
        }
    } catch (err) {
        showCopyFeedback(this, 'Kopieren nicht unterstützt', true);
    }
    
    document.body.removeChild(tempInput);
}

// Show feedback after copy operation
function showCopyFeedback(element, message, isError = false) {
    var originalText = element.title || '';
    var originalBg = element.style.backgroundColor;
    
    // Set feedback styling
    element.style.backgroundColor = isError ? '#f8d7da' : '#d4edda';
    element.style.transition = 'background-color 0.3s ease';
    element.title = message;
    
    // Reset after 1 second
    setTimeout(function() {
        element.style.backgroundColor = originalBg;
        element.title = originalText;
    }, 1000);
}

$(document).on("rex:ready", function(warehouse, container) {

    // Initialize copy functionality for existing elements
    document.querySelectorAll('div[data-warehouse-copy]').forEach(function(el) {
        el.addEventListener('click', data_copy);
    });

    // Warte auf QuickNavigation -  Event an das Formular erst nach 2 Sekunden
    setTimeout(function() {

        // Fügt den Submit-Event-Listener zum Formular mit der ID "warehouse_search" hinzu
        $('#warehouse_search').on('submit', function(e) {
            // Verhindert das Absenden des Formulars
            e.preventDefault();

            // Sendet eine AJAX-Anfrage
            $.ajax({
                url: '/', // URL, an die die Anfrage gesendet wird
                type: 'GET', // Methode der Anfrage
                data: {
                    "rex-api-call": 'warehouse_search', // Daten, die an den Server gesendet werden
                    "q": $('input[name="q"]').val() // Wert des Eingabefelds
                },
                success: function(response) {
                    // Erstellt ein leeres table-Element und fügt Klassen hinzu
                    var table = $('<table></table>');
                    table.addClass('table table-striped table-hover');
                    // Erstellt ein leeres tbody-Element
                    var tbody = $('<tbody></tbody>');

                    // Durchläuft jedes Element im Antwortobjekt
                    $.each(response, function(key, value) {
                        // Erstellt ein neues tr-Element und fügt das gewünschte HTML hinzu
                        var tr = $('<tr></tr>');
                        var td1 = $('<td></td>').text(value['id']);
                        var td2 = $('<td></td>').append($(value['icon']));
                        var td4 = $('<td></td>').append($(value['url']));
                        var td5 = $('<td></td>').text(value['details']);
                        tr.append(td1);
                        tr.append(td2);
                        tr.append(td4);
                        tr.append(td5);

                        // Fügt das tr-Element zum tbody-Element hinzu
                        tbody.append(tr);
                        // Fügt das tbody-Element zum table-Element hinzu
                        table.append(tbody);
                    });

                    // Fügt das table-Element in das div mit der ID "warehouseSearchResults" ein
                    $('#warehouseSearchResults').html(table);

                    // Fügt den Click-Event-Listener zu allen neuen div-Elementen mit dem data-warehouse-copy Attribut hinzu
                    document.querySelectorAll('div[data-warehouse-copy]').forEach(function(el) {
                        el.addEventListener('click', data_copy)
                    })

                },
                error: function() {
                    // Zeigt eine Fehlermeldung an, wenn ein Fehler auftritt
                    $('#warehouseSearchResults').html('Ein Fehler ist aufgetreten.');
                }
            });
        });

    }, 2000);

});
