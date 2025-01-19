$(function () {
    $('.sortable tbody').sortable();
    $('.formbe_table tbody').sortable();

    $("body").on("click", ".sortable .row_add", function () {
        var $tr = $(this).closest("tr");
        var $clone = $tr.clone();
        $tr.after($clone);
    });

    $("body").on("click", ".sortable .row_del", function () {
        if ($(this).closest("tbody").find("tr").length > 1) {
            $(this).closest("tr").remove();
        }
    });

    $('.wh-aufklapp').on('click',function(e) {
        $(this).next('.wh-klappauf').toggleClass('open');
    });

    // Initiale Ausführung
    window.setTimeout(function() {
        warehouse_edittable();
    }, 200);

    function warehouse_edittable() {
        let n = 0;
        $('.edittable.warehouse_relayprice').each(function() {
            n++;
            let $elem = $(this);

            // Sicherstellen dass das Element eine ID hat
            if (!$elem.attr('id')) {
                $elem.attr('id', 'warehouse-table-' + n);
            }

            let the_id = $elem.attr('id');

            // Nur initialisieren wenn noch nicht geschehen
            if (!$elem.hasClass('has_edittable')) {
                $elem.addClass('has_edittable');

                try {
                    window['mytable'+n] = $('#'+the_id).editTable({
                        data: [['']],
                        tableClass: 'inputtable',
                        jsonData: false,
                        headerCols: ["Menge", "Preis"],
                        maxRows: 999,
                        first_row: true,
                        row_template: false,
                        field_templates: false,
                        validate_field: function (col_id, value, col_type, $element) {
                            return true;
                        }
                    });
                } catch(e) {
                    console.error('Error initializing editTable for #' + the_id, e);
                }
            }
        });
    }

    // Rex:ready Event Handler mit Überprüfung
    $(document).on('rex:ready', function() {
        // Kurze Verzögerung um sicherzustellen dass das DOM aktualisiert ist
        requestAnimationFrame(function() {
            warehouse_edittable();
            $('.edittable + table tbody').sortable();
        });

        tools_init($(form_element));
    });


    let wh_checkbox = '.wh-toggle',
        wh_multi_select = '.wh-multi-select',
        wh_multi_open_select = '.wh-multi-select-open',
        wh_currency_input = 'input[data-wh-currency="1"]',
        form_element = '.rex-yform';

    function tools_init($element) {
        checkboxpicker_init($element);
        multiselects_init($element);
        currency_input_init($element);
        currency_submit_init($element);
    }

    function tools_destroy($element) {
        multiselects_destroy($element);
        currency_input_destroy($element);
    }

    function currency_submit_init($element) {
        if ($element.find(wh_currency_input).length) {
            $(form_element).on('submit', function () {
                // $element.find(wh_currency_input).each(function () {
                //     let $dd = $(this).parents('dd'),
                //         $clone = $(this).clone(),
                //         $val = $(this).val().replace(',', '.');
                //     $clone.hide().attr('class', '').val($val);
                //     $clone.appendTo($dd);
                //     $(this).attr('name', '');
                // });
            });
        }
    }

// Event-Handler für das Hinzufügen einer neuen Reihe
    $(document).on('be_table:row-added', function(e, $newRow) {
        // Initialisiere currency inputs für die neue Reihe
        currency_input_init($newRow);
    });

    function currency_input_init($element) {
        // Beide Selektoren kombinieren
        let $allInputs = $element.find(wh_currency_input)
            .add($element.find('td[data-title="Preis"] input[type="text"]'));

        if ($allInputs.length) {
            $allInputs.each(function (index) {
                let $currencySymbol = '€',
                    $input = $(this);
                if ($input.attr('data-currency-symbol')) {
                    $currencySymbol = $input.attr('data-currency-symbol');
                }

                // Nur Input-Group erstellen, wenn sie noch nicht existiert
                if (!$input.parent().find('.input-group').length) {
                    $input.wrap('<div class="input-group"></div>');
                    $input.before('<span class="input-group-addon">' + $currencySymbol + '</span>');
                }

                let $uid = Math.random().toString(16).slice(2) + '_' + index;

                // Stelle sicher, dass der initiale Wert korrekt formatiert ist
                let initialValue = $input.val();
                if (initialValue === '') {
                    initialValue = '0,00';
                } else {
                    // Konvertiere Punkt zu Komma falls nötig
                    initialValue = initialValue.replace('.', ',');
                }
                $input.val(initialValue);

                $input.addClass('autonumeric_' + $uid);

                const autoNumInstance = new AutoNumeric('.autonumeric_' + $uid, {
                    digitGroupSeparator: '.',
                    decimalCharacter: ',',
                    decimalCharacterAlternative: '.',
                    currencySymbol: '',
                    decimalPlaces: 2,
                    minimumValue: '0',
                    outputFormat: 'number',
                    allowDecimalPadding: true,
                    rawValueDivisor: null,
                    onUpdate: function(an) {
                        try {
                            const numericValue = an.getNumber();
                            if (!isNaN(numericValue)) {
                                $input.val(numericValue);
                            }
                        } catch (e) {
                            console.warn('Fehler bei der Wertaktualisierung:', e);
                        }
                    }
                });

                // Vor dem Submit des Forms den unformatierten Wert setzen
                $input.closest('form').on('submit', function() {
                    try {
                        const rawValue = autoNumInstance.getNumber();
                        if (!isNaN(rawValue)) {
                            $input.val(rawValue);
                        }
                    } catch (e) {
                        console.warn('Fehler beim Form-Submit:', e);
                    }
                });
            });
        }
    }

    function currency_input_destroy($element) {
        if ($element.find(wh_currency_input).length) {
            $element.find(wh_currency_input).each(function () {
                let parent_element = $(this).parent(),
                    $dd = $(this).parents('dd'),
                    $classes = $(this).attr("class").split(" "),
                    $myclass;

                $(this).appendTo($dd);
                parent_element.remove();

                for (var i = 0, max = $classes.length; i < max; i++) {
                    let $class = $classes[i].split("_");
                    if ($class[0] === "bscautonumeric") {
                        $myclass = $classes[i];
                        let $val = $(this).val().replace(',', '.');
                        $(this).off().removeClass($myclass);
                        $(this).replaceWith($(this).clone().val($val));
                        break;
                    }
                }
            });
        }
    }

    function multiselects_init($element) {
        if ($element.find(wh_multi_select).length) {
            $element.find(wh_multi_select).each(function () {
                var maxheight = 350,
                    numberdisplay = 3,
                    search = 1,
                    selectall = 1;
                if (typeof $(this).data('max-height') !== 'undefined') {
                    maxheight = $(this).data('max-height');
                }
                if (typeof $(this).data('number-display') !== 'undefined') {
                    numberdisplay = $(this).data('number-display');
                }
                if (typeof $(this).data('search') !== 'undefined') {
                    search = $(this).data('search');
                }
                if (typeof $(this).data('select-all') !== 'undefined') {
                    selectall = $(this).data('select-all');
                }
                $(this).baseconditionMultiselect({
                    includeSelectAllOption: selectall,
                    selectAllText: 'Select all',
                    filterBehavior: 'both',
                    enableFiltering: search,
                    maxHeight: maxheight,
                    numberDisplayed: numberdisplay,
                });
            });
        }
        if ($element.find(wh_multi_open_select).length) {
            $element.find(wh_multi_open_select).each(function () {
                var maxheight = false,
                    search = 1,
                    selectall = 1;
                if (typeof $(this).data('max-height') !== 'undefined') {
                    maxheight = $(this).data('max-height');
                }
                if (typeof $(this).data('search') !== 'undefined') {
                    search = $(this).data('search');
                }
                if (typeof $(this).data('select-all') !== 'undefined') {
                    selectall = $(this).data('select-all');
                }
                $(this).whMultiselect({
                    includeSelectAllOption: selectall,
                    selectAllText: 'Select all',
                    filterBehavior: 'both',
                    enableFiltering: search,
                    maxHeight: maxheight,
                    templates: {
                        button: '',
                        ul: '<ul class="wh-multiselect-container checkbox-list"></ul>',
                        filter: '<li class="wh-multiselect-item filter"><div class="input-group"><span class="input-group-addon"><i class="glyphicon glyphicon-search"></i></span><input class="form-control wh-multiselect-search" type="text"></div></li>',
                        filterClearBtn: '<span class="input-group-btn"><button class="btn btn-default wh-multiselect-clear-filter" type="button"><i class="glyphicon glyphicon-remove-circle"></i></button></span>',
                        li: '<li><a href="javascript:void(0);"><label></label></a></li>',
                        divider: '<li class="wh-multiselect-item divider"></li>',
                        liGroup: '<li class="wh-multiselect-item group"><label class="wh-multiselect-group"></label></li>'
                    }
                });
            });
        }
    }

    function multiselects_destroy($element) {
        multiselect_destroy($element, wh_multi_select);
        multiselect_destroy($element, wh_multi_open_select);
    }

    function multiselect_destroy($element, $select) {
        if ($element.find($select).length) {
            $element.find($select).each(function () {
                var parent_element = $(this).parent();
                $(this).appendTo($(this).parents('dd'));
                parent_element.remove();
            });
        }
    }

    function checkboxpicker_init($element) {
        if ($element.find(wh_checkbox).length) {
            $element.find(wh_checkbox).each(function () {
                $(this).baseconditionToggle();
                var val = 0;
                if ($(this).prop('checked')) {
                    val = 1;
                }

                $(this).parent().parent().append('<input type="hidden" name="' + $(this).attr('name').replace('[1]', '') + '" value="' + val + '"/>');
                $(this).attr('name', '');

                $(this).change(function () {
                    var val = 0;
                    if ($(this).prop('checked')) {
                        val = 1;
                    }
                    $(this).parent().parent().find('input').val(val);
                })
            });
        }
    }
});

