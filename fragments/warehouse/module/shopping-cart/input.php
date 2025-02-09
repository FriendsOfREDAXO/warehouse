<?php /* UK . 370 . Warehouse Cart Page - Input - Id: 13 */ ?>

<?php
$selectvals = [1=>'Eins',2=>'Zwei',3=>'Drei',4=>'Vier'];
$REX_VAL2 = rex_var::toArray("REX_VALUE[2]");
if (!$REX_VAL2) {
    $REX_VAL2 = [];
}
?>

<select class="form-control sort_select" id="for_value-2" type="text" multiple="multiple">
    <?php foreach ($selectvals as $k=>$v) : ?>
        <option value="<?= $k ?>" <?= in_array($k,$REX_VAL2) ? 'selected' : '' ?>><?= $v ?></option>
    <?php endforeach ?>
</select>

<div id="val2_container">
    <table class="sortable table table-striped">
        <tbody>
            <?php foreach ($REX_VAL2 as $k) : ?>
            <tr data-val="<?= $k ?>"><td><input type="hidden" name="REX_INPUT_VALUE[2][]" value="<?= $k ?>"><?= $selectvals[$k] ?></td><td><a class="weg">X</a></td></tr>
            <?php endforeach ?>
        </tbody>
    </table>
</div>


<script>
    $(function() {
        $('body').on('change','.sort_select',function() {
            $(this).closest('select').find('option').each(function() {
                if ($(this).prop('selected')) {
                    if (!$("#val2_container tbody").find('[data-val="'+$(this).val()+'"]').length) {
                        $('#val2_container tbody').append('<tr data-val="'+$(this).val()+'"><td><input type="hidden" name="REX_INPUT_VALUE[2][]" value="'+$(this).val()+'">'+$(this).html()+'</td><td><a class="weg">X</a></td></tr>');
                    }
                } else {
                    $('#val2_container tbody tr[data-val="'+$(this).val()+'"]').remove();
                }
            });
        });
        $('table.sortable tbody').sortable();
        $('#val2_container').on('click','.weg',function() {
            val = $(this).closest('tr').data('val');
            $('.sort_select option[value="'+val+'"]').prop('selected',false);
            $(this).closest('tr').remove();
        });
    })
</script>


