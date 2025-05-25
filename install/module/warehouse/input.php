<?php
/* Dieses Modul wird automatisch vom Addon `warehouse` aktualisiert. Es wird empfohlen, keine Änderungen vorzunehmen, da diese nicht updatesicher sind. */

use FriendsOfRedaxo\Warehouse\Warehouse;

?>
<fieldset>
	<div class="form-group">
		<label
			class="control-label"><?= rex_i18n::msg('warehouse.module.title'); ?></label>
		<div class="">
			<input class="form-control" type="text" name="REX_INPUT_VALUE[1]" value="REX_VALUE[1]">
		</div>
	</div>

	<div class="form-group">
		<label
			class="control-label"><?= rex_i18n::msg('warehouse.module.description'); ?></label>
		<div class="">
			<textarea
				<?= Warehouse::getConfig('editor') ? Warehouse::getConfig('editor') : 'class="form-control"' ?> rows="6" name="REX_INPUT_VALUE[2]">REX_VALUE[2]</textarea>
		</div>
	</div>
</fieldset>
<fieldset>
	<div class="form-group">
		<label class="control-label">Fragment</label>
		<div class="">
			<select class="form-control" required="required" name="REX_INPUT_VALUE[10]">
				<?php
                $fragment_files = glob(rex_path::addon('warehouse', 'fragments/warehouse/bootstrap5/') . '*.php');
?>
				<option value="">
					<?= rex_i18n::msg('warehouse.module.select.default'); ?>
				</option>
				<?php
foreach ($fragment_files as $fragment_file) {
    if ('REX_VALUE[10]' === basename($fragment_file)) {
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
