<fieldset class="form-horizontal">
    <legend>Artikeldaten</legend>
    <div class="form-group">
        <label class="col-sm-2 control-label" for="text">Artikel-Nr.</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" name="REX_INPUT_VALUE[1]" value="REX_VALUE[1]">
        </div>
    </div>    
    <div class="form-group">
        <label class="col-sm-2 control-label" for="text">Bezeichnung</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" name="REX_INPUT_VALUE[2]" value="REX_VALUE[2]">
        </div>
    </div>    
    <div class="form-group">
        <label class="col-sm-2 control-label" for="text">Preis</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" name="REX_INPUT_VALUE[3]" value="REX_VALUE[3]" pattern='[0-9]+(\.[0-9][0-9]?)?' placeholder="3.65">
            <p class="help-block">Preis mit Punkt als Dezimalzeichen und ohne Währungssymbol in der Form <code>3.65</code> eintragen.</p>
        </div>
    </div>    
    <input type="hidden" name="REX_INPUT_VALUE[20]" value="warehouse_single">
</fieldset>
