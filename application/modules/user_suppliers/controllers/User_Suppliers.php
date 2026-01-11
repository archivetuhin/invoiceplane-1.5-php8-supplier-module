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
 * Class User_suppliers
 */
class User_Suppliers extends Admin_Controller
{
    /**
     * Custom_Values constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('users/mdl_users');
        $this->load->model('suppliers/mdl_suppliers');
        $this->load->model('user_suppliers/mdl_user_suppliers');
    }

    public function index()
    {
        redirect('users');
    }

    /**
     * @param null $id
     */
    public function user($id = null)
    {
        if ($this->input->post('btn_cancel')) {
            redirect('users');
        }

        $user = $this->mdl_users->get_by_id($id);

        if (empty($user)) {
            redirect('users');
        }

        $user_suppliers = $this->mdl_user_suppliers->assigned_to($id)->get()->result();

        $this->layout->set('user', $user);
        $this->layout->set('user_suppliers', $user_suppliers);
        $this->layout->set('id', $id);
        $this->layout->buffer('content', 'user_suppliers/field');
        $this->layout->render();
    }

    /**
     * @param null $user_id
     */
    public function create($user_id = null)
    {
        if (!$user_id) {
            redirect('custom_values');
        }

        if ($this->input->post('btn_cancel')) {
            redirect('user_suppliers/field/' . $user_id);
        }

        if ($this->mdl_user_suppliers->run_validation()) {
            
            if ($this->input->post('user_all_suppliers')) {
                $users_id = array($user_id);
                
                $this->mdl_user_suppliers->set_all_suppliers_user($users_id);
                
                $user_update = array(
                    'user_all_suppliers' => 1
                );
                
            } else {
                $user_update = array(
                    'user_all_suppliers' => 0
                );
                
               $this->mdl_user_suppliers->save(); 
            }
            
            $this->db->where('user_id',$user_id);
            $this->db->update('ip_users',$user_update);
            
            redirect('user_suppliers/user/' . $user_id);
        }

        $user = $this->mdl_users->get_by_id($user_id);
        $suppliers = $this->mdl_suppliers->get_not_assigned_to_user($user_id);

        $this->layout->set('id', $user_id);
        $this->layout->set('user', $user);
        $this->layout->set('suppliers', $suppliers);
        $this->layout->buffer('content', 'user_suppliers/new');
        $this->layout->render();
    }

    /**
     * @param integer $user_supplier_id
     */
    public function delete($user_supplier_id)
    {
        $ref = $this->mdl_user_suppliers->get_by_id($user_supplier_id);

        $this->mdl_user_suppliers->delete($user_supplier_id);
        redirect('user_suppliers/user/' . $ref->user_id);
    }

}
