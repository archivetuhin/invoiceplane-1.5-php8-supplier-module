<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * InvoicePlane
 *
 * @author		InvoicePlane Developers & Contributors
 * @copyright	Copyright (c) 2012 - 2018 InvoicePlane.com
 * @license		https://invoiceplane.com/license.txt
 * @link		https://invoiceplane.com
 */

/**
 * @param object $client
 * @return string
 */
function format_supplier($supplier)
{
    if ($supplier->supplier_surname != "") {
        return $supplier->supplier_name . " " . $supplier->supplier_surname;
    }

    return $supplier->supplier_name;
}

/**
 * @param string $gender
 * @return string
 */
function format_supplier_gender($gender)
{
    if ($gender == 0) {
        return trans('gender_male');
    }

    if ($gender == 1) {
        return trans('gender_female');
    }

    return trans('gender_other');
}
