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
 * Class Mdl_User_suppliers
 */
class Mdl_User_suppliers extends MY_Model
{
    public $table = 'ip_user_suppliers';
    public $primary_key = 'ip_user_suppliers.user_supplier_id';

    public function default_select()
    {
        $this->db->select('ip_user_suppliers.*, ip_users.user_name, ip_suppliers.supplier_name, ip_suppliers.supplier_surname');
    }

    public function default_join()
    {
        $this->db->join('ip_users', 'ip_users.user_id = ip_user_suppliers.user_id');
        $this->db->join('ip_suppliers', 'ip_suppliers.supplier_id = ip_user_suppliers.supplier_id');
    }

    public function default_order_by()
    {
        $this->db->order_by('ip_suppliers.supplier_name', 'ACS');
    }

    /**
     * @return array
     */
    public function validation_rules()
    {
        return array(
            'user_id' => array(
                'field' => 'user_id',
                'label' => trans('user'),
                'rules' => 'required'
            ),
            'supplier_id' => array(
                'field' => 'supplier_id',
                'label' => trans('supplier'),
                'rules' => 'required'
            ),
        );
    }

    /**
     * @param $user_id
     * @return $this
     */
    public function assigned_to($user_id)
    {
        $this->filter_where('ip_user_suppliers.user_id', $user_id);
        return $this;
    }
    
    /**
    * 
    * @param array $users_id
    */
    public function set_all_suppliers_user($users_id)
    {
        $this->load->model('suppliers/mdl_suppliers');
        
        for ($x = 0; $x < count($users_id); $x++) {
            $suppliers = $this->mdl_suppliers->get_not_assigned_to_user($users_id[$x]);
            
            for ($i = 0; $i < count($suppliers); $i++) {
                $user_supplier = array(
                    'user_id' => $users_id[$x],
                    'supplier_id' => $suppliers[$i]->supplier_id
                );
                
                $this->db->insert('ip_user_suppliers', $user_supplier);
            }
        }
    }
    
    public function get_users_all_suppliers()
    {
        $this->load->model('users/mdl_users');
        $users = $this->mdl_users->where('user_all_suppliers', 1)->get()->result();
        
        $new_users = array();
        
        for ($i = 0; $i < count($users); $i++) {
            array_push($new_users, $users[$i]->user_id);
        }
        
        $this->set_all_suppliers_user($new_users);
    }
}
