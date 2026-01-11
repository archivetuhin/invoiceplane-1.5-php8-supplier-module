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
 * Class suppliers
 */
class Suppliers extends Admin_Controller
{
    /**
     * suppliers constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('mdl_suppliers');
    }

    public function index()
    {
        // Display active suppliers by default
        redirect('suppliers/status/active');
    }

    /**
     * @param string $status
     * @param int $page
     */
    public function status($status = 'active', $page = 0)
    {

        if (is_numeric(array_search($status, array('active', 'inactive')))) {
            $function = 'is_' . $status;
            $this->mdl_suppliers->$function();
        }

        $this->mdl_suppliers->with_total_balance()->paginate(site_url('suppliers/status/' . $status), $page);
        $suppliers = $this->mdl_suppliers->result();

        $this->layout->set(
            array(
                'records' => $suppliers,
                'filter_display' => true,
                'filter_placeholder' => trans('filter_suppliers'),
                'filter_method' => 'filter_suppliers'
            )
        );


        $this->layout->buffer('content', 'suppliers/index');
        $this->layout->render();
    }

    /**
     * @param null $id
     */
    public function form($id = null)
    {
        if ($this->input->post('btn_cancel')) {
            redirect('suppliers');
        }
        
        $new_supplier = false;
        
        // Set validation rule based on is_update
        if ($this->input->post('is_update') == 0 && $this->input->post('supplier_name') != '') {
            $check = $this->db->get_where('ip_suppliers', array(
                'supplier_name' => $this->input->post('supplier_name'),
                'supplier_surname' => $this->input->post('supplier_surname')
            ))->result();

            if (!empty($check)) {
                $this->session->set_flashdata('alert_error', trans('supplier_already_exists'));
                redirect('suppliers/form');
            } else {
                $new_supplier = true;
            }
        }
        
        if ($this->mdl_suppliers->run_validation()) {
            $id = $this->mdl_suppliers->save($id);
            
            if ($new_supplier) {
                $this->load->model('user_suppliers/mdl_user_suppliers');
                $this->mdl_user_suppliers->get_users_all_suppliers();
            }
            
            $this->load->model('custom_fields/mdl_supplier_custom');
            $result = $this->mdl_supplier_custom->save_custom($id, $this->input->post('custom'));

            if ($result !== true) {
                $this->session->set_flashdata('alert_error', $result);
                $this->session->set_flashdata('alert_success', null);
                redirect('suppliers/form/' . $id);
                return;
            } else {
                redirect('suppliers/view/' . $id);
            }
        }

        if ($id and !$this->input->post('btn_submit')) {
            if (!$this->mdl_suppliers->prep_form($id)) {
                show_404();
            }

            $this->load->model('custom_fields/mdl_supplier_custom');
            $this->mdl_suppliers->set_form_value('is_update', true);

            $supplier_custom = $this->mdl_supplier_custom->where('supplier_id', $id)->get();

            if ($supplier_custom->num_rows()) {
                $supplier_custom = $supplier_custom->row();

                unset($supplier_custom->supplier_id, $supplier_custom->supplier_custom_id);

                foreach ($supplier_custom as $key => $val) {
                    $this->mdl_suppliers->set_form_value('custom[' . $key . ']', $val);
                }
            }
        } elseif ($this->input->post('btn_submit')) {
            if ($this->input->post('custom')) {
                foreach ($this->input->post('custom') as $key => $val) {
                    $this->mdl_suppliers->set_form_value('custom[' . $key . ']', $val);
                }
            }
        }

        $this->load->model('custom_fields/mdl_custom_fields');
        $this->load->model('custom_values/mdl_custom_values');
        $this->load->model('custom_fields/mdl_supplier_custom');

        $custom_fields = $this->mdl_custom_fields->by_table('ip_supplier_custom')->get()->result();
        $custom_values = [];
        foreach ($custom_fields as $custom_field) {
            if (in_array($custom_field->custom_field_type, $this->mdl_custom_values->custom_value_fields())) {
                $values = $this->mdl_custom_values->get_by_fid($custom_field->custom_field_id)->result();
                $custom_values[$custom_field->custom_field_id] = $values;
            }
        }

        $fields = $this->mdl_supplier_custom->get_by_clid($id);

        foreach ($custom_fields as $cfield) {
            foreach ($fields as $fvalue) {
                if ($fvalue->supplier_custom_fieldid == $cfield->custom_field_id) {
                    // TODO: Hackish, may need a better optimization
                    $this->mdl_suppliers->set_form_value(
                        'custom[' . $cfield->custom_field_id . ']',
                        $fvalue->supplier_custom_fieldvalue
                    );
                    break;
                }
            }
        }

        $this->load->helper('country');
        $this->load->helper('custom_values');

        $this->layout->set(
            array(
                'custom_fields' => $custom_fields,
                'custom_values' => $custom_values,
                'countries' => get_country_list(trans('cldr')),
                'selected_country' => $this->mdl_suppliers->form_value('supplier_country') ?: get_setting('default_country'),
                'languages' => get_available_languages(),
            )
        );

        $this->layout->buffer('content', 'suppliers/form');
        $this->layout->render();
    }

    /**
     * @param int $supplier_id
     */
    public function view($supplier_id)
    {
        $this->load->model('suppliers/mdl_supplier_notes');
        $this->load->model('invoices/mdl_invoices');
        $this->load->model('quotes/mdl_quotes');
        $this->load->model('payments/mdl_payments');
        $this->load->model('custom_fields/mdl_custom_fields');
        $this->load->model('custom_fields/mdl_supplier_custom');

        $supplier = $this->mdl_suppliers
            ->with_total()
            ->with_total_balance()
            ->with_total_paid()
            ->where('ip_suppliers.supplier_id', $supplier_id)
            ->get()->row();

        $custom_fields = $this->mdl_supplier_custom->get_by_supplier($supplier_id)->result();

        $this->mdl_supplier_custom->prep_form($supplier_id);

        if (!$supplier) {
            show_404();
        }


        $this->layout->set(
            array(
                'supplier' => $supplier,
                'supplier_notes' => $this->mdl_supplier_notes->where('supplier_id', $supplier_id)->get()->result(),
                'invoices' => $this->mdl_invoices->by_supplier($supplier_id)->limit(20)->get()->result(),
                'quotes' => $this->mdl_quotes->by_supplier($supplier_id)->limit(20)->get()->result(),
                'payments' => $this->mdl_payments->by_supplier($supplier_id)->limit(20)->get()->result(),
                'custom_fields' => $custom_fields,
                'quote_statuses' => $this->mdl_quotes->statuses(),
                'invoice_statuses' => $this->mdl_invoices->statuses()
            )
        );

        $this->layout->buffer(
            array(
                array(
                    'invoice_table',
                    'invoices/partial_invoice_table'
                ),
                array(
                    'quote_table',
                    'quotes/partial_quote_table'
                ),
                array(
                    'payment_table',
                    'payments/partial_payment_table'
                ),
                array(
                    'partial_notes',
                    'suppliers/partial_notes'
                ),
                array(
                    'content',
                    'suppliers/view'
                )
            )
        );

        $this->layout->render();
    }

    /**
     * @param int $supplier_id
     */
    public function delete($supplier_id)
    {
        $this->mdl_suppliers->delete($supplier_id);
        redirect('suppliers');
    }

}
