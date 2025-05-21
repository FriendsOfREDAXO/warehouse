<?php
/* Dieses Modul wird automatisch vom Addon `warehouse` aktualisiert. Es wird empfohlen, keine Änderungen vorzunehmen, da diese nicht updatesicher sind. */
?>
<fieldset>
    <div class="form-group">
        <label class="control-label">Fragment wählen</label>
        <div class="">
            <select class="form-control" required="required" name="REX_INPUT_VALUE[10]">
                <?php
                $fragment_files = glob(rex_path::addon('warehouse', 'fragments/warehouse/') . '*.php');
                ?>
                <option value=""><?= rex_i18n::msg('warehouse.module.select.default'); ?></option>
                <?php
                foreach ($fragment_files as $fragment_file) {
                    if('REX_VALUE[10]' === basename($fragment_file)) {
                        echo '<option value="' . basename($fragment_file) . '" selected>' . basename($fragment_file) . '</option>';
                        continue;
                    };
                    echo '<option value="' . basename($fragment_file) . '">' . basename($fragment_file) . '</option>';
                }
                ?>
            </select>
        </div>
    </div>
</fieldset>
