<?php $this->load->helper('country'); ?>

<span class="supplier-address-street-line">
    <?php echo($supplier->supplier_address_1 ? htmlsc($supplier->supplier_address_1) . '<br>' : ''); ?>
</span>
<span class="supplier-address-street-line">
    <?php echo($supplier->supplier_address_2 ? htmlsc($supplier->supplier_address_2) . '<br>' : ''); ?>
</span>
<span class="supplier-adress-town-line">
    <?php echo($supplier->supplier_city ? htmlsc($supplier->supplier_city) . ' ' : ''); ?>
    <?php echo($supplier->supplier_state ? htmlsc($supplier->supplier_state) . ' ' : ''); ?>
    <?php echo($supplier->supplier_zip ? htmlsc($supplier->supplier_zip) : ''); ?>
</span>
<span class="supplier-adress-country-line">
    <?php echo($supplier->supplier_country ? '<br>' . get_country_name(trans('cldr'), $supplier->supplier_country) : ''); ?>
</span>
