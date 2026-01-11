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
 * Class Mdl_suppliers
 */
class Mdl_suppliers extends Response_Model
{
    public $table = 'ip_suppliers';
    public $primary_key = 'ip_suppliers.supplier_id';
    public $date_created_field = 'supplier_date_created';
    public $date_modified_field = 'supplier_date_modified';

    public function default_select()
    {
        $this->db->select(
            'SQL_CALC_FOUND_ROWS ' . $this->table . '.*, ' .
            'CONCAT(' . $this->table . '.supplier_name, " ", ' . $this->table . '.supplier_surname) as supplier_fullname'
            , false);
    }

    public function default_order_by()
    {
        $this->db->order_by('ip_suppliers.supplier_name');
    }

    public function validation_rules()
    {
        return array(
            'supplier_name' => array(
                'field' => 'supplier_name',
                'label' => trans('supplier_name'),
                'rules' => 'required'
            ),
            'supplier_surname' => array(
                'field' => 'supplier_surname',
                'label' => trans('supplier_surname')
            ),
            'supplier_active' => array(
                'field' => 'supplier_active'
            ),
            'supplier_language' => array(
                'field' => 'supplier_language',
                'label' => trans('language'),
                'rules' => 'trim'
            ),
            'supplier_address_1' => array(
                'field' => 'supplier_address_1'
            ),
            'supplier_address_2' => array(
                'field' => 'supplier_address_2'
            ),
            'supplier_city' => array(
                'field' => 'supplier_city'
            ),
            'supplier_state' => array(
                'field' => 'supplier_state'
            ),
            'supplier_zip' => array(
                'field' => 'supplier_zip'
            ),
            'supplier_country' => array(
                'field' => 'supplier_country',
                'rules' => 'trim'
            ),
            'supplier_phone' => array(
                'field' => 'supplier_phone'
            ),
            'supplier_fax' => array(
                'field' => 'supplier_fax'
            ),
            'supplier_mobile' => array(
                'field' => 'supplier_mobile'
            ),
            'supplier_email' => array(
                'field' => 'supplier_email'
            ),
            'supplier_web' => array(
                'field' => 'supplier_web'
            ),
            'supplier_vat_id' => array(
                'field' => 'supplier_vat_id'
            ),
            'supplier_tax_code' => array(
                'field' => 'supplier_tax_code'
            ),
            // SUMEX
            'supplier_birthdate' => array(
                'field' => 'supplier_birthdate',
                'rules' => 'callback_convert_date'
            ),
            'supplier_gender' => array(
                'field' => 'supplier_gender'
            ),
            'supplier_avs' => array(
                'field' => 'supplier_avs',
                'label' => trans('sumex_ssn'),
                'rules' => 'callback_fix_avs'
            ),
            'supplier_insurednumber' => array(
                'field' => 'supplier_insurednumber',
                'label' => trans('sumex_insurednumber')
            ),
            'supplier_veka' => array(
                'field' => 'supplier_veka',
                'label' => trans('sumex_veka')
            ),
        );
    }

    /**
     * @param int $amount
     * @return mixed
     */
    function get_latest($amount = 10)
    {
        return $this->mdl_suppliers
            ->where('supplier_active', 1)
            ->order_by('supplier_id', 'DESC')
            ->limit($amount)
            ->get()
            ->result();
    }

    /**
     * @param $input
     * @return string
     */
    function fix_avs($input)
    {
        if ($input != "") {
            if (preg_match('/(\d{3})\.(\d{4})\.(\d{4})\.(\d{2})/', $input, $matches)) {
                return $matches[1] . $matches[2] . $matches[3] . $matches[4];
            } else if (preg_match('/^\d{13}$/', $input)) {
                return $input;
            }
        }

        return "";
    }

    function convert_date($input)
    {
        $this->load->helper('date_helper');

        if ($input == '') {
            return '';
        }

        return date_to_mysql($input);
    }

    public function db_array()
    {
        $db_array = parent::db_array();

        if (!isset($db_array['supplier_active'])) {
            $db_array['supplier_active'] = 0;
        }

        return $db_array;
    }

    /**
     * @param int $id
     */
    public function delete($id)
    {
        parent::delete($id);

        $this->load->helper('orphan');
        delete_orphans();
    }

    /**
     * Returns supplier_id of existing supplier
     *
     * @param $supplier_name
     * @return int|null
     */
    public function supplier_lookup($supplier_name)
    {
        $supplier = $this->mdl_suppliers->where('supplier_name', $supplier_name)->get();

        if ($supplier->num_rows()) {
            $supplier_id = $supplier->row()->supplier_id;
        } else {
            $db_array = array(
                'supplier_name' => $supplier_name
            );

            $supplier_id = parent::save(null, $db_array);
        }

        return $supplier_id;
    }

    public function with_total()
    {
        $this->filter_select('IFnull((SELECT SUM(invoice_total) FROM ip_invoice_amounts WHERE invoice_id IN (SELECT invoice_id FROM ip_invoices WHERE ip_invoices.supplier_id = ip_suppliers.supplier_id)), 0) AS supplier_invoice_total', false);
        return $this;
    }

    public function with_total_paid()
    {
        $this->filter_select('IFnull((SELECT SUM(invoice_paid) FROM ip_invoice_amounts WHERE invoice_id IN (SELECT invoice_id FROM ip_invoices WHERE ip_invoices.supplier_id = ip_suppliers.supplier_id)), 0) AS supplier_invoice_paid', false);
        return $this;
    }

    public function with_total_balance()
    {
        $this->filter_select('IFnull((SELECT SUM(invoice_balance) FROM ip_invoice_amounts WHERE invoice_id IN (SELECT invoice_id FROM ip_invoices WHERE ip_invoices.supplier_id = ip_suppliers.supplier_id)), 0) AS supplier_invoice_balance', false);
        return $this;
    }

    public function is_inactive()
    {
        $this->filter_where('supplier_active', 0);
        return $this;
    }

    /**
     * @param $user_id
     * @return $this
     */
    public function get_not_assigned_to_user($user_id)
    {
        $this->load->model('user_suppliers/mdl_user_suppliers');
        $suppliers = $this->mdl_user_suppliers->select('ip_user_suppliers.supplier_id')
            ->assigned_to($user_id)->get()->result();

        $assigned_suppliers = [];
        foreach ($suppliers as $supplier) {
            $assigned_suppliers[] = $supplier->supplier_id;
        }

        if (count($assigned_suppliers) > 0) {
            $this->where_not_in('ip_suppliers.supplier_id', $assigned_suppliers);
        }

        $this->is_active();
        return $this->get()->result();
    }

    public function is_active()
    {
        $this->filter_where('supplier_active', 1);
        return $this;
    }

}
