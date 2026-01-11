<?php
$cv = $this->controller->view_data['custom_values'];
?>

<script type="text/javascript">
    $(function () {
        $("#supplier_country").select2({
            placeholder: "<?php _trans('country'); ?>",
            allowClear: true
        });
    });
</script>

<form method="post">

    <input type="hidden" name="<?php echo $this->config->item('csrf_token_name'); ?>"
           value="<?php echo $this->security->get_csrf_hash() ?>">

    <div id="headerbar">
        <h1 class="headerbar-title"><?php _trans('supplier_form'); ?></h1>
        <?php $this->layout->load_view('layout/header_buttons'); ?>
    </div>

    <div id="content">

        <?php $this->layout->load_view('layout/alerts'); ?>

        <input class="hidden" name="is_update" type="hidden"
            <?php if ($this->mdl_suppliers->form_value('is_update')) {
                echo 'value="1"';
            } else {
                echo 'value="0"';
            } ?>
        >

        <div class="row">
            <div class="col-xs-12 col-sm-6">

                <div class="panel panel-default">
                    <div class="panel-heading form-inline clearfix">
                        <?php _trans('personal_information'); ?>

                        <div class="pull-right">
                            <label for="supplier_active" class="control-label">
                                <?php _trans('active_supplier'); ?>
                                <input id="supplier_active" name="supplier_active" type="checkbox" value="1"
                                    <?php if ($this->mdl_suppliers->form_value('supplier_active') == 1
                                        || !is_numeric($this->mdl_suppliers->form_value('supplier_active'))
                                    ) {
                                        echo 'checked="checked"';
                                    } ?>>
                            </label>
                        </div>
                    </div>

                    <div class="panel-body">

                        <div class="form-group">
                            <label for="supplier_name">
                                <?php _trans('supplier_name'); ?>
                            </label>
                            <input id="supplier_name" name="supplier_name" type="text" class="form-control"
                                   autofocus
                                   value="<?php echo $this->mdl_suppliers->form_value('supplier_name', true); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="supplier_surname">
                                <?php _trans('supplier_surname_optional'); ?>
                            </label>
                            <input id="supplier_surname" name="supplier_surname" type="text" class="form-control"
                                   value="<?php echo $this->mdl_suppliers->form_value('supplier_surname', true); ?>">
                        </div>

                        <div class="form-group no-margin">
                            <label for="supplier_language">
                                <?php _trans('language'); ?>
                            </label>
                            <select name="supplier_language" id="supplier_language" class="form-control simple-select">
                                <option value="system">
                                    <?php _trans('use_system_language') ?>
                                </option>
                                <?php foreach ($languages as $language) {
                                    $supplier_lang = $this->mdl_suppliers->form_value('supplier_language');
                                    ?>
                                    <option value="<?php echo $language; ?>"
                                        <?php check_select($supplier_lang, $language) ?>>
                                        <?php echo ucfirst($language); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                    </div>
                </div>

            </div>
        </div>

        <div class="row">
            <div class="col-xs-12 col-sm-6">
                <div class="panel panel-default">

                    <div class="panel-heading">
                        <?php _trans('address'); ?>
                    </div>

                    <div class="panel-body">
                        <div class="form-group">
                            <label for="supplier_address_1"><?php _trans('street_address'); ?></label>

                            <div class="controls">
                                <input type="text" name="supplier_address_1" id="supplier_address_1" class="form-control"
                                       value="<?php echo $this->mdl_suppliers->form_value('supplier_address_1', true); ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="supplier_address_2"><?php _trans('street_address_2'); ?></label>

                            <div class="controls">
                                <input type="text" name="supplier_address_2" id="supplier_address_2" class="form-control"
                                       value="<?php echo $this->mdl_suppliers->form_value('supplier_address_2', true); ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="supplier_city"><?php _trans('city'); ?></label>

                            <div class="controls">
                                <input type="text" name="supplier_city" id="supplier_city" class="form-control"
                                       value="<?php echo $this->mdl_suppliers->form_value('supplier_city', true); ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="supplier_state"><?php _trans('state'); ?></label>

                            <div class="controls">
                                <input type="text" name="supplier_state" id="supplier_state" class="form-control"
                                       value="<?php echo $this->mdl_suppliers->form_value('supplier_state', true); ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="supplier_zip"><?php _trans('zip_code'); ?></label>

                            <div class="controls">
                                <input type="text" name="supplier_zip" id="supplier_zip" class="form-control"
                                       value="<?php echo $this->mdl_suppliers->form_value('supplier_zip', true); ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="supplier_country"><?php _trans('country'); ?></label>

                            <div class="controls">
                                <select name="supplier_country" id="supplier_country" class="form-control">
                                    <option value=""><?php _trans('none'); ?></option>
                                    <?php foreach ($countries as $cldr => $country) { ?>
                                        <option value="<?php echo $cldr; ?>"
                                            <?php check_select($selected_country, $cldr); ?>
                                        ><?php echo $country ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                        <!-- Custom Fields -->
                        <?php foreach ($custom_fields as $custom_field): ?>
                            <?php if ($custom_field->custom_field_location != 1) {
                                continue;
                            } ?>
                            <?php print_field($this->mdl_suppliers, $custom_field, $cv); ?>
                        <?php endforeach; ?>
                    </div>

                </div>

            </div>
            <div class="col-xs-12 col-sm-6">

                <div class="panel panel-default">

                    <div class="panel-heading">
                        <?php _trans('contact_information'); ?>
                    </div>

                    <div class="panel-body">
                        <div class="form-group">
                            <label for="supplier_phone"><?php _trans('phone_number'); ?></label>

                            <div class="controls">
                                <input type="text" name="supplier_phone" id="supplier_phone" class="form-control"
                                       value="<?php echo $this->mdl_suppliers->form_value('supplier_phone', true); ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="supplier_fax"><?php _trans('fax_number'); ?></label>

                            <div class="controls">
                                <input type="text" name="supplier_fax" id="supplier_fax" class="form-control"
                                       value="<?php echo $this->mdl_suppliers->form_value('supplier_fax', true); ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="supplier_mobile"><?php _trans('mobile_number'); ?></label>

                            <div class="controls">
                                <input type="text" name="supplier_mobile" id="supplier_mobile" class="form-control"
                                       value="<?php echo $this->mdl_suppliers->form_value('supplier_mobile', true); ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="supplier_email"><?php _trans('email_address'); ?></label>

                            <div class="controls">
                                <input type="text" name="supplier_email" id="supplier_email" class="form-control"
                                       value="<?php echo $this->mdl_suppliers->form_value('supplier_email', true); ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="supplier_web"><?php _trans('web_address'); ?></label>

                            <div class="controls">
                                <input type="text" name="supplier_web" id="supplier_web" class="form-control"
                                       value="<?php echo $this->mdl_suppliers->form_value('supplier_web', true); ?>">
                            </div>
                        </div>

                        <!-- Custom fields -->
                        <?php foreach ($custom_fields as $custom_field): ?>
                            <?php if ($custom_field->custom_field_location != 2) {
                                continue;
                            } ?>
                            <?php print_field($this->mdl_suppliers, $custom_field, $cv); ?>
                        <?php endforeach; ?>
                    </div>

                </div>

            </div>
        </div>

        <div class="row">
            <div class="col-xs-12 col-sm-6">

                <div class="panel panel-default">

                    <div class="panel-heading">
                        <?php _trans('personal_information'); ?>
                    </div>

                    <div class="panel-body">
                        <div class="form-group">
                            <label for="supplier_gender"><?php _trans('gender'); ?></label>

                            <div class="controls">
                                <select name="supplier_gender" id="supplier_gender"
                                	class="form-control simple-select" data-minimum-results-for-search="Infinity">
                                    <?php
                                    $genders = array(
                                        trans('gender_male'),
                                        trans('gender_female'),
                                        trans('gender_other'),
                                    );
foreach ($genders as $key => $val) { ?>
                                        <option value=" <?php echo $key; ?>" <?php check_select($key, $this->mdl_suppliers->form_value('supplier_gender')) ?>>
                                            <?php echo $val; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group has-feedback">
                            <label for="supplier_birthdate"><?php _trans('birthdate'); ?></label>
                            <?php
                            $bdate = $this->mdl_suppliers->form_value('supplier_birthdate');
if ($bdate && $bdate != "0000-00-00") {
    $bdate = date_from_mysql($bdate);
} else {
    $bdate = '';
}
?>
                            <div class="input-group">
                                <input type="text" name="supplier_birthdate" id="supplier_birthdate"
                                       class="form-control datepicker"
                                       value="<?php _htmlsc($bdate); ?>">
                                <span class="input-group-addon">
                                <i class="fa fa-calendar fa-fw"></i>
                            </span>
                            </div>
                        </div>

                        <?php if ($this->mdl_settings->setting('sumex') == '1'): ?>

                            <div class="form-group">
                                <label for="supplier_avs"><?php _trans('sumex_ssn'); ?></label>
                                <?php $avs = $this->mdl_suppliers->form_value('supplier_avs'); ?>
                                <div class="controls">
                                    <input type="text" name="supplier_avs" id="supplier_avs" class="form-control"
                                           value="<?php echo htmlspecialchars(format_avs($avs), ENT_COMPAT); ?>">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="supplier_insurednumber"><?php _trans('sumex_insurednumber'); ?></label>
                                <?php $insuredNumber = $this->mdl_suppliers->form_value('supplier_insurednumber'); ?>
                                <div class="controls">
                                    <input type="text" name="supplier_insurednumber" id="supplier_insurednumber"
                                           class="form-control"
                                           value="<?php echo htmlentities($insuredNumber, ENT_COMPAT); ?>">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="supplier_veka"><?php _trans('sumex_veka'); ?></label>
                                <?php $veka = $this->mdl_suppliers->form_value('supplier_veka'); ?>
                                <div class="controls">
                                    <input type="text" name="supplier_veka" id="supplier_veka" class="form-control"
                                           value="<?php echo htmlentities($veka, ENT_COMPAT); ?>">
                                </div>
                            </div>

                        <?php endif; ?>

                        <!-- Custom fields -->
                        <?php foreach ($custom_fields as $custom_field): ?>
                            <?php if ($custom_field->custom_field_location != 3) {
                                continue;
                            } ?>
                            <?php print_field($this->mdl_suppliers, $custom_field, $cv); ?>
                        <?php endforeach; ?>
                    </div>

                </div>

            </div>
            <div class="col-xs-12 col-sm-6">

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <?php _trans('tax_information'); ?>
                    </div>

                    <div class="panel-body">
                        <div class="form-group">
                            <label for="supplier_vat_id"><?php _trans('vat_id'); ?></label>

                            <div class="controls">
                                <input type="text" name="supplier_vat_id" id="supplier_vat_id" class="form-control"
                                       value="<?php echo $this->mdl_suppliers->form_value('supplier_vat_id', true); ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="supplier_tax_code"><?php _trans('tax_code'); ?></label>

                            <div class="controls">
                                <input type="text" name="supplier_tax_code" id="supplier_tax_code" class="form-control"
                                       value="<?php echo $this->mdl_suppliers->form_value('supplier_tax_code', true); ?>">
                            </div>
                        </div>

                        <!-- Custom fields -->
                        <?php foreach ($custom_fields as $custom_field): ?>
                            <?php if ($custom_field->custom_field_location != 4) {
                                continue;
                            } ?>
                            <?php print_field($this->mdl_suppliers, $custom_field, $cv); ?>
                        <?php endforeach; ?>
                    </div>

                </div>

            </div>
        </div>
        <?php if ($custom_fields): ?>
            <div class="row">
                <div class="col-xs-12 col-md-6">

                    <div class="panel panel-default">

                        <div class="panel-heading">
                            <?php _trans('custom_fields'); ?>
                        </div>

                        <div class="panel-body">
                            <?php foreach ($custom_fields as $custom_field): ?>
                                <?php if ($custom_field->custom_field_location != 0) {
                                    continue;
                                }
                                print_field($this->mdl_suppliers, $custom_field, $cv);
                                ?>
                            <?php endforeach; ?>
                        </div>

                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</form>
