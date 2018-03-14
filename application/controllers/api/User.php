<?php

error_reporting(0);
ini_set('display_errors',0);
defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
/** @noinspection PhpIncludeInspection */
require APPPATH . '/libraries/REST_Controller.php';
/**
* This is an example of a few basic user interaction methods you could use
* all done with a hardcoded array
*
* @package         CodeIgniter
* @subpackage      Rest Server
* @category        Controller
* @author          Phil Sturgeon, Chris Kacerguis
* @license         MIT
* @link            https://github.com/chriskacerguis/codeigniter-restserver
*/
class User extends REST_Controller {

	function __construct()
	{
     // Construct the parent class
    parent::__construct();
    $this->load->database();
    $this->load->model('User_model');
    $this->load->helper('url');
    $this->load->helper('form');

 // Ensure you have created the 'limits' table and enabled 'limits' within application/config/rest.php
   $this->methods['users_get']['limit'] = 500; // 500 requests per hour per user/key
   $this->methods['users_post']['limit'] = 100; // 100 requests per hour per user/key
   $this->methods['users_delete']['limit'] = 50; // 50 requests per hour per user/key
 }




    public function registerusers_post(){
        $data=array(
        'name'=>$this->input->post('name'),
        'email'=>$this->input->post('email'),
        'phone_no'=>$this->input->post('phone_no'),
        'password'=>md5($this->input->post('password')),
        );

        $locationdata=array(
        'city_id'=>$this->input->post('city_id'),
        'postal_code'=>$this->input->post('postal_code'),
        'street_name'=>$this->input->post('street_name'),
        'street_number'=>$this->input->post('street_number'),
        'lat'=>$this->input->post('lat'),
        'lng'=>$this->input->post('lng')
        );

        $checkemail=$this->User_model->select_data('email','tbl_customer',array('email'=>$data['email']));
        if (empty($checkemail)) {
            $location=$this->User_model->insert_data('tbl_location',$locationdata);
            $data['location_id']=$location;
            $users=$this->User_model->insert_data('tbl_customer',$data);
            $userdata=$this->User_model->select_data('email,name,phone_no,location_id','tbl_customer',array('customer_id'=>$users));
            $result = array(
            "controller" => "User",
            "action" => "registerusers",
            "ResponseCode" => true,
            "MessageWhatHappen" => "Users has been succesfully registered.",
            "response"=>$userdata
            );
        }
        else{
            $result = array(
            "controller" => "User",
            "action" => "registerusers",
            "ResponseCode" => false,
            "MessageWhatHappen" => "Email already exists."
            );
        }
        $this->set_response($result, REST_Controller::HTTP_OK);  
    }

    public function login_post(){
        $email=$this->input->post('email');
        $password=md5($this->input->post('password'));
        $type=$this->input->post('type');
        if ($type==1) {
            
            $checkemail=$this->User_model->select_data('email,name,phone_no,location_id','tbl_customer',array('email'=>$email,'password'=>$password));
            if (!empty($checkemail)) {
                $responseresult= $checkemail;/*successfully logined*/
            }
            else{
                $responseresult= 1;/*email or password is wrong*/
            }
        }
        elseif($type==2){ 
            $checkemail=$this->User_model->select_data('email,name,phone_no,location_id','tbl_customer',array('email'=>$email));
            if (!empty($checkemail)) {
                $responseresult= $checkemail;
            }
            else{
                $responseresult= 2;/*email does not exists*/
            }
        }

        if ($responseresult!=1 && $responseresult!=2 ) {
            $result = array(
            "controller" => "User",
            "action" => "login",
            "ResponseCode" => true,
            "MessageWhatHappen" => "Users succesfully logged in.",
            "response"=>$responseresult
            ); 
        }
        elseif ($responseresult==2) {
            $result = array(
            "controller" => "User",
            "action" => "login",
            "ResponseCode" => false,
            "MessageWhatHappen" => "Email doesn't exists."
            ); 
        }
        elseif($responseresult==1){
            $result = array(
            "controller" => "User",
            "action" => "login",
            "ResponseCode" => false,
            "MessageWhatHappen" => "Email or password is wrong."
            ); 
        }
        else{
            $result = array(
            "controller" => "User",
            "action" => "login",
            "ResponseCode" => false,
            "MessageWhatHappen" => "Something went wrong."
            ); 
        }
        $this->set_response($result, REST_Controller::HTTP_OK);  
    } 

    public function getallcountries_get(){
        $countriesdata=$this->db->get('tbl_country')->result();
        if (!empty($countriesdata)) {
            $result = array(
            "controller" => "User",
            "action" => "getallcountries",
            "ResponseCode" => true,
            "MessageWhatHappen" => "Your data shows succesfully.",
            "response"=>$countriesdata
            );
        }
        elseif (empty($countriesdata)) {
            $result = array(
            "controller" => "User",
            "action" => "getallcountries",
            "ResponseCode" => false,
            "MessageWhatHappen" => "No data exist in table."
            );
        }
        else{
            $result = array(
            "controller" => "User",
            "action" => "getallcountries",
            "ResponseCode" => false,
            "MessageWhatHappen" => "Something went wrong."
            );
        }
        $this->set_response($result, REST_Controller::HTTP_OK);  
    }

    public function getcitycountryid_post(){
        $country_id=$this->input->post('country_id');
        if (empty($country_id)) {
            $result=$this->requiredresponse();    
        }else
        {
            $citydata=$this->db->get_where('tbl_city', array('country_id' => $country_id))->result();
            if (!empty($citydata)) {
                $result = array(
                "controller" => "User",
                "action" => "getcitycountryid",
                "ResponseCode" => true,
                "MessageWhatHappen" => "Your data shows succesfully.",
                "response"=>$citydata
                );
            }
            elseif (empty($citydata)) {
                $result = array(
                "controller" => "User",
                "action" => "getcitycountryid",
                "ResponseCode" => false,
                "MessageWhatHappen" => "No data exist in table."
                );
            }
            else{
                $result = array(
                "controller" => "User",
                "action" => "getcitycountryid",
                "ResponseCode" => false,
                "MessageWhatHappen" => "Something went wrong."
                );
            }
        }
        $this->set_response($result, REST_Controller::HTTP_OK);  
    }

    public function availabilitydetail_post(){
        $availability_id=$this->input->post('availability_id');
        if (empty($availability_id)) {
            $result=$this->requiredresponse();    
        }else
        {
            $this->db->select('*');
            $this->db->from('tbl_employee');
            $this->db->join('tbl_location', 'tbl_location.location_id = tbl_employee.location_id');
            $this->db->where('tbl_employee.availability_id',$availability_id);
            $availabilitydata = $this->db->get()->result();

            if (!empty($availabilitydata)) {
                $result = array(
                "controller" => "User",
                "action" => "availabilitydetail",
                "ResponseCode" => true,
                "MessageWhatHappen" => "Your data shows succesfully.",
                "response"=>$availabilitydata
                );
            }
            elseif (empty($availabilitydata)) {
                $result = array(
                "controller" => "User",
                "action" => "availabilitydetail",
                "ResponseCode" => false,
                "MessageWhatHappen" => "No data exist in table."
                );
            }
            else{
                $result = array(
                "controller" => "User",
                "action" => "availabilitydetail",
                "ResponseCode" => false,
                "MessageWhatHappen" => "Something went wrong."
                );
            }
        }
        $this->set_response($result, REST_Controller::HTTP_OK);
    }


    public function booking_post(){
        $bookingdata=array(
        'customer_id'=>$this->input->post('customer_id'),
        'employee_id'=>$this->input->post('employee_id'),
        'booking_date'=>$this->input->post('booking_date'),
        'booking_time'=>$this->input->post('booking_time'),
        'no_of_hours'=>$this->input->post('no_of_hours'),
        );

        $paymentdata=array(
        'netprice'=>$this->input->post('netprice'),
        'vat'=>$this->input->post('vat'),
        'total'=>$this->input->post('total')
        );
        $payment=$this->User_model->insert_data('tbl_payment',$paymentdata);

        $bookingdata['payment_id']=$payment;

        $booking=$this->User_model->insert_data('tbl_booking',$bookingdata);

        if (!empty($payment) && !empty($booking)) {
            $result = array(
            "controller" => "User",
            "action" => "booking",
            "ResponseCode" => true,
            "MessageWhatHappen" => "Your booking scheduled succesfully.",
            ); 
        }
        else{
            $result = array(
            "controller" => "User",
            "action" => "booking",
            "ResponseCode" => false,
            "MessageWhatHappen" => "Something went wrong.",
            );
        }
        $this->set_response($result, REST_Controller::HTTP_OK);
    }


    public function availability_post(){
        $date=$this->input->post('date');
        if (empty($date)) {
            $result=$this->requiredresponse();    
        }else
        {
            $this->db->select('*');
            $this->db->from('tbl_availability');
            $this->db->join('tbl_employee', 'tbl_employee.availability_id = tbl_availability.availability_id');
            $this->db->join('tbl_hour', 'tbl_hour.hour_id = tbl_availability.hour_id');
            $this->db->join('tbl_location', 'tbl_location.location_id = tbl_employee.location_id');
            $this->db->where('tbl_availability.date',$date);
            $availabilitydata = $this->db->get()->result();

            if (!empty($availabilitydata)) {
                $result = array(
                "controller" => "User",
                "action" => "availability",
                "ResponseCode" => true,
                "MessageWhatHappen" => "Your data shows succesfully.",
                "response"=>$availabilitydata
                );
            }
            elseif (empty($availabilitydata)) {
                $result = array(
                "controller" => "User",
                "action" => "availability",
                "ResponseCode" => false,
                "MessageWhatHappen" => "No data exist in table."
                );
            }
            else{
                $result = array(
                "controller" => "User",
                "action" => "getallcountries",
                "ResponseCode" => false,
                "MessageWhatHappen" => "Something went wrong."
                );
            }
        }
        $this->set_response($result, REST_Controller::HTTP_OK); 

    }

    public function requiredresponse(){
        $result = array(
        "controller" => "User",
        "action" => "requiredfield",
        "ResponseCode" => false,
        "MessageWhatHappen" => "Please fill required field."
        );
        return $result;
    }
}