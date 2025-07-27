<?php

use FriendsOfRedaxo\Warehouse\Payment;
use FriendsOfRedaxo\Warehouse\Warehouse;

$options = Payment::getPaymentOptions();

$notices = [];
if ('' != $this->getElement('notice')) {
    $notices[] = rex_i18n::translate($this->getElement('notice'), false);
}
if (isset($this->params['warning_messages'][$this->getId()]) && !$this->params['hide_field_warning_messages']) {
    $notices[] = '<span class="text-warning">' . rex_i18n::translate($this->params['warning_messages'][$this->getId()], false) . '</span>';
}

$notice = '';
if (count($notices) > 0) {
    $notice = '<div class="form-text">' . implode('<br />', $notices) . '</div>';
}

$class_label = '';
$class = '';
$class_group = trim('mb-3 ' . $class . $this->getWarningClass());

if ('' != trim($this->getLabel())) {
    echo '<div class="' . $class_group . '">
	<label class="form-label' . $class_label . '">' . $this->getLabel() . '</label>';
}
foreach ($options as $key => $value) { 
	$inline = (bool) $this->getElement('inline');
	$form_check_class = '' . ($inline ? ' form-check-inline' : '');
?>
	<div class="<?= $form_check_class ?><?= '' == trim($this->getLabel()) ? $this->getWarningClass() : '' ?>">
<?php 
    $attributes = [
        'id' => $this->getFieldId() . '-' . htmlspecialchars($key),
        'name' => $this->getFieldName(),
        'value' => $key,
        'type' => 'radio',
        'class' => '',
    ];

    if ($key == $this->getValue()) {
        $attributes['checked'] = 'checked';
    }

    $input_attributes = $this->getAttributeElements($attributes);
    ?>

<label class="d-block" for="<?= $this->getFieldId() . '-' . htmlspecialchars($key) ?>">
    <div class="card mb-2 d-block width-100">
        <?php if (!empty($value['logo'])): ?>
            <img src="<?= htmlspecialchars($value['logo']) ?>" class="card-img-top" alt="Logo" style="max-height: 80px; object-fit: contain;">
        <?php endif; ?>
        <div class="card-body p-2">
            <h6 class="card-title mb-1"><input class="" <?= implode(' ', $input_attributes) ?> />&nbsp;<?= htmlspecialchars($value['label'] ?? $this->getLabelStyle($value)) ?></h6>
            <?php if (!empty($value['description'])): ?>
                <p class="card-text small mb-0"><?= htmlspecialchars($value['description']) ?></p>
            <?php endif; ?>
        </div>
    </div>
</label>
</div>
<?php
}
?>
<?= $notice; ?>
<?php
if ('' != trim($this->getLabel())) {
    echo '</div>';
}
?>
