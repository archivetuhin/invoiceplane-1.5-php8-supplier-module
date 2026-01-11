<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * InvoicePlane
 *
 * @author      InvoicePlane Developers & Contributors
 * @copyright   Copyright (c) 2012 - 2018 InvoicePlane.com
 * @license     https://invoiceplane.com/license.txt
 * @link        https://invoiceplane.com
 */

/**
 * Class Ajax
 */
class Ajax extends Admin_Controller
{

    public $ajax_controller = true;

    public function name_query()
    {
        // Load the model & helper
        $this->load->model('suppliers/mdl_suppliers');

        $response = [];

        // Get the post input
        $query = $this->input->get('query');
        $permissiveSearchsuppliers = $this->input->get('permissive_search_suppliers');

        if (empty($query)) {
            echo json_encode($response);
            exit;
        }

        // Search for chars "in the middle" of suppliers names
        $permissiveSearchsuppliers ? $moresuppliersQuery = '%' : $moresuppliersQuery = '';

        // Search for suppliers
        $escapedQuery = $this->db->escape_str($query);
        $escapedQuery = str_replace("%", "", $escapedQuery);
        $suppliers = $this->mdl_suppliers
            ->where('supplier_active', 1)
            ->having('supplier_name LIKE \'' . $moresuppliersQuery . $escapedQuery . '%\'')
            ->or_having('supplier_surname LIKE \'' . $moresuppliersQuery . $escapedQuery . '%\'')
            ->or_having('supplier_fullname LIKE \'' . $moresuppliersQuery . $escapedQuery . '%\'')
            ->order_by('supplier_name')
            ->get()
            ->result();

        foreach ($suppliers as $supplier) {
            $response[] = [
                'id' => $supplier->supplier_id,
                'text' => (format_supplier($supplier)),
            ];
        }

        // Return the results
        echo json_encode($response);
    }

    /**
     * Get the latest suppliers
     */
    public function get_latest()
    {
        // Load the model & helper
        $this->load->model('suppliers/mdl_suppliers');

        $response = [];

        $suppliers = $this->mdl_suppliers
            ->where('supplier_active', 1)
            ->limit(5)
            ->order_by('supplier_date_created')
            ->get()
            ->result();

        foreach ($suppliers as $supplier) {
            $response[] = [
                'id' => $supplier->supplier_id,
                'text' => htmlsc(format_supplier($supplier)),
            ];
        }

        // Return the results
        echo json_encode($response);
    }

    public function save_preference_permissive_search_suppliers()
    {
        $this->load->model('mdl_settings');
        $permissiveSearchsuppliers = $this->input->get('permissive_search_suppliers');

        if (!preg_match('!^[0-1]{1}$!', $permissiveSearchsuppliers)) {
            exit;
        }

        $this->mdl_settings->save('enable_permissive_search_suppliers', $permissiveSearchsuppliers);
    }

    public function save_supplier_note()
    {
        $this->load->model('suppliers/mdl_supplier_notes');

        if ($this->mdl_supplier_notes->run_validation()) {
            $this->mdl_supplier_notes->save();

            $response = [
                'success' => 1,
                'new_token' => $this->security->get_csrf_hash(),
            ];
        } else {
            $this->load->helper('json_error');
            $response = [
                'success' => 0,
                'new_token' => $this->security->get_csrf_hash(),
                'validation_errors' => json_errors(),
            ];
        }

        echo json_encode($response);
    }

    public function load_supplier_notes()
    {
        $this->load->model('suppliers/mdl_supplier_notes');
        $data = [
            'supplier_notes' => $this->mdl_supplier_notes->where('supplier_id',
                $this->input->post('supplier_id'))->get()->result(),
        ];

        $this->layout->load_view('suppliers/partial_notes', $data);
    }

}
