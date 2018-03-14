 <?php
 defined('BASEPATH') OR exit('No direct script access allowed');
 class User_model extends CI_Model{
	public function __construct() {
		parent::__construct();
		$this->load->database();
		$this->load->helper('url');

	}

	

		public function insert_data($tbl_name,$data)                                         /* Data insert */
	    {
	      	$this->db->insert($tbl_name, $data);
	       	$insert_id = $this->db->insert_id();
	        return $insert_id;

	    }

	    public function update_data($tbl_name,$data,$where){                                 /* Update data */

	      $this->db->where($where);
	      $this->db->update($tbl_name,$data);

	     return($this->db->affected_rows())?1:0;
	    }

	    public function select_data($selection,$tbl_name,$where=null,$order=null)                   /* Select data with condition*/
		    {
		      if (empty($where)&&empty($order)) {
		      $data_response = $this->db->select($selection)
		           ->from($tbl_name)
		           ->get()->result();
		    }
		    elseif(empty($order)){
		    $data_response =
		    $this->db->select($selection)
		           ->from($tbl_name)
		           ->where($where)
		           ->get()->result();

		    }else{
		    $data_response =
		    $this->db->select($selection)
		           ->from($tbl_name)
		           ->where($where)
		           ->order_by($order)
		           ->get()->result();
		    }
	    return $data_response;

	    }

   



}
