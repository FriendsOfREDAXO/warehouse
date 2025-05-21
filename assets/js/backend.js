$(document).on("rex:ready", function(warehouse, container) {

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
