<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Guest_system extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->library('session');
		$this->load->database(); //- load database
		$this->load->model('db_functions_model'); //- load database model

		$this->load->model('util_function_model'); //- load utilities model
		$this->util_function_model->check_valid_access('GUEST-SYSTEM'); //-check valid access

		$this->load->library('pagination'); //call pagination library
		$this->load->library('table'); //- library for generate table
		$this->load->helper('string');
		$this->load->helper('date');
		$this->load->helper('url');

		$this->load->library('Dynamic_menu');

		$this->output->set_header('Last-Modified: ' . gmdate("D, d M Y H:i:s") . ' GMT');
		$this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
		$this->output->set_header('Pragma: no-cache');
		$this->output->set_header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	}

	public function index($offset=0)
	{
		$data['base']			= $this->config->item('base_url');
		$data['images_folder']	= $this->config->item('images');
		$data['images_icon']	= $this->config->item('images_icon');
		$data['js']				= $this->config->item('js');
		$data['css']			= $this->config->item('css');
		$data['author']			= $this->config->item('author');
		$data['site_title']		= $this->config->item('site_title');
		$data['page_heading']	= "Guest System";

		$data['display_info']=$this->session->flashdata('msg_to_show');
		$admin_id=$this->session->userdata('admin_id');
		$admin_name=$this->session->userdata('admin_name');
		$admin_username=$this->session->userdata('admin_username');
		$contact_level=$this->session->userdata('admin_level');
		//========================================================
		$data['admin_name']=$admin_name;

		//============ CHECK PRIV FOR SECURITY ========================
		$val_read=$this->util_function_model->check_priv('GUEST-SYSTEM-INPUT',$admin_id,'read_val'); //-check valid access for read
		$val_write=$this->util_function_model->check_priv('GUEST-SYSTEM-INPUT',$admin_id,'write_val'); //-check valid access for write

		$data['priv_edit']=false;
		if($this->util_function_model->check_priv('GUEST-SYSTEM-INPUT',$admin_id)){//- check privilege for input guest system
			$data['priv_edit']=true;
		}

		//========= SECURITY POST LISTING ================
		$data['cmb_security_post']="<select id=\"select_security_post\" name=\"select_security_post\" data-placeholder=\"Select Security Post...\" class=\"chosen-select\" style=\"width:200px;\">";
		$data['cmb_security_post'].="<option value=\"0\" selected=\"selected\">-</option>";
		$ssql="
				select guest_mt_id,security_post_name from guest_system_mt where post_gate = 1
				";
		$view_dep = $this->db->query($ssql);
		$table_data=$view_dep->result();
		if($table_data){
			foreach ($table_data as $row)
			{
				$guest_mt_id=$row->guest_mt_id;
				$security_post_name=$row->security_post_name;
				$data['cmb_security_post'].="<option value=\"$guest_mt_id\">$security_post_name</option>";
			}
		}

		$data['cmb_security_post'].="</select>";

		$data['cmb_security_post_out']="<select id=\"select_security_post_out\" name=\"select_security_post_out\" data-placeholder=\"Select Security Post...\" class=\"chosen-select\" style=\"width:200px;\">";
		$data['cmb_security_post_out'].="<option value=\"0\" selected=\"selected\">-</option>";
		$ssql = "select guest_mt_id, security_post_name from guest_system_mt where post_gate = 0";
		$view_dep = $this->db->query($ssql);
		$table_data = $view_dep->result();
		if($table_data) {
			foreach ($table_data as $row)
			{
				$guest_mt_id=$row->guest_mt_id;
				$security_post_name=$row->security_post_name;
				$data['cmb_security_post_out'].="<option value=\"$guest_mt_id\">$security_post_name</option>";
			}
		}

		$data['cmb_security_post_out'].="</select>";
		//========================================================

		$data['dyn_menu']=$this->load->view('templates/dyn_menu',$data,true);
		$this->load->view('templates/header',$data);
		$this->load->view('guest_system/input',$data);
		$this->load->view('templates/footer');
	}

	public function access_in($pos=0){
		$data['base']			= $this->config->item('base_url');
		$data['images_folder']	= $this->config->item('images');
		$data['images_icon']	= $this->config->item('images_icon');
		$data['js']				= $this->config->item('js');
		$data['css']			= $this->config->item('css');
		$data['author']			= $this->config->item('author');
		$data['site_title']		= $this->config->item('site_title');
		$data['page_heading']	= "Guest In";

		$data['display_info']=$this->session->flashdata('msg_to_show');
		$admin_id=$this->session->userdata('admin_id');
		$admin_name=$this->session->userdata('admin_name');
		$admin_username=$this->session->userdata('admin_username');
		$contact_level=$this->session->userdata('admin_level');
		//========================================================
		$data['admin_name']=$admin_name;

		//============ CHECK PRIV FOR SECURITY ========================
		$val_read=$this->util_function_model->check_priv('GUEST-SYSTEM',$admin_id,'read_val'); //-check valid access for read
		$val_write=$this->util_function_model->check_priv('GUEST-SYSTEM',$admin_id,'write_val'); //-check valid access for write
		if($contact_level==1){
			$val_write=TRUE;
		}

		$data['priv_edit']=$val_write;
		$data['val']="new";
		//http://<ACCOUNTT>:<PWD>@<IPADDRESS>/ISAPI/Streaming/channels/1601/picture?videoResolutionWidth=1280&videoResolutionHeight=720
		//hikvision

		$img='';//"http://admin:viovera789@192.168.1.64/ISAPI/Streaming/channels/1/picture";
		$img_hide='';//"http://192.168.1.64/ISAPI/Streaming/channels/1/picture";
		$data['cctv']=$img;
		$data['cctv_hide']=$img_hide;

		//========= SECURITY POST LISTING ================
		$data['security_post']=$pos;
		//========================================================

		$data['dyn_menu']=$this->load->view('templates/dyn_menu',$data,true);
		$this->load->view('templates/header',$data);
		$this->load->view('guest_system/guest_in',$data);
		$this->load->view('templates/footer');
	}

	public function access_out(){
		$data['base']			= $this->config->item('base_url');
		$data['images_folder']	= $this->config->item('images');
		$data['images_icon']	= $this->config->item('images_icon');
		$data['js']				= $this->config->item('js');
		$data['css']			= $this->config->item('css');
		$data['author']			= $this->config->item('author');
		$data['site_title']		= $this->config->item('site_title');
		$data['page_heading']	= "Guest Out";

		$data['display_info']=$this->session->flashdata('msg_to_show');
		$admin_id=$this->session->userdata('admin_id');
		$admin_name=$this->session->userdata('admin_name');
		$admin_username=$this->session->userdata('admin_username');
		$contact_level=$this->session->userdata('admin_level');
		//========================================================
		$data['admin_name']=$admin_name;

		//============ CHECK PRIV FOR SECURITY ========================
		$val_read=$this->util_function_model->check_priv('GUEST-SYSTEM',$admin_id,'read_val'); //-check valid access for read
		$val_write=$this->util_function_model->check_priv('GUEST-SYSTEM',$admin_id,'write_val'); //-check valid access for write
		if($contact_level==1){
			$val_write=TRUE;
		}

		$data['priv_edit']=$val_write;

		isset($_POST["txt_card_no"]) ? $card_no=strip_slashes(strip_quotes($_POST["txt_card_no"])) : $card_no=0;
		$card_no=trim($card_no);
		isset($_POST["proc_val"]) ? $proc_val=strip_slashes(strip_quotes($_POST["proc_val"])) : $proc_val='';

		$guest_pic_1='';
		$guest_pic_2='';
		$checkin='';
		$curr_date='';
		$security_post_name='';

		if($proc_val=='out' && $card_no!=''){
			$datestring = "%Y-%m-%d %H:%i:%s";
			$time = time();
			$curr_date= mdate($datestring, $time);

			$guest_dt_id=0;
			$this->db->select('guest_dt_id,guest_pic_1,guest_pic_2,checkin,(select t2.security_post_name from guest_system_mt t2 where guest_mt_id_in = t2.guest_mt_id) as security_post_name');
			$this->db->where('card_no', $card_no);
			$this->db->where('guest_status', 0);
			$this->db->limit(1,0);
			$this->db->from('guest_system_dt t1');
			$query_guest = $this->db->get();
			foreach ($query_guest->result() as $row_guest)
			{
				$guest_dt_id=$row_guest->guest_dt_id;
				$guest_pic_1=$row_guest->guest_pic_1;
				$guest_pic_2=$row_guest->guest_pic_2;
				$checkin=$this->util_function_model->ddate($row_guest->checkin);
				$security_post_name=$row_guest->security_post_name;
			}

			$data_arr = array(
				'checkout' => $curr_date,
				'guest_status' => 1
      );
			$this->db->where('guest_dt_id', $guest_dt_id);
			$this->db->update('guest_system_dt', $data_arr);
		}
	//echo $guest_pic_1;
	//die("");
		$data['guest_pic_1']=$guest_pic_1;
		$data['guest_pic_2']=$guest_pic_2;
		$data['checkin']=$checkin;
		$data['checkout']=$this->util_function_model->ddate($curr_date);
		$data['security_post_name']=$security_post_name;

		$data['dyn_menu']=$this->load->view('templates/dyn_menu',$data,true);
		$this->load->view('templates/header',$data);
		$this->load->view('guest_system/guest_out',$data);
		$this->load->view('templates/footer');
	}

	public function access_out_index_mode($card_no = "", $pos_out = 0) {
		if($card_no != "" ) {

			$this->db->select('card_no')
			->from('guest_system_dt')
			->where('card_no', $card_no)->where('guest_status', 0);

			$checkcardno = $this->db->get();

			if ( !$checkcardno->result() ) {
				$data_info = array('n', 'Maaf, tamu belum gate In.');

			} else {

				$this->db->select('guest_dt_id, guest_pic_1, guest_pic_2,
				checkin, (select t2.security_post_name from guest_system_mt t2
				where guest_mt_id_in = t2.guest_mt_id) as security_post_name');
				$this->db->where('card_no', $card_no);
				$this->db->where('guest_status', 0);
				$this->db->limit(1, 0);
				$this->db->from('guest_system_dt t1');
				$query_guest = $this->db->get();

				$row_guest = $query_guest->row();

				$guest_dt_id = $row_guest->guest_dt_id;
				$guest_pic_1 = $row_guest->guest_pic_1;
				$guest_pic_2 = $row_guest->guest_pic_2;

				$checkin = $this->util_function_model->ddate($row_guest->checkin);
				$security_post_name = $row_guest->security_post_name;

				$this->db->select('ip_address, cctv_user_name, cctv_password');
				$this->db->where('guest_mt_id', $pos_out);
				$this->db->from('guest_system_mt');
				$credential = $this->db->get();
				$credentialdata = $credential->row();

				$cctv_user_name = $credentialdata->cctv_user_name;
				$cctv_password = $credentialdata->cctv_password;
				$ip_address = $credentialdata->ip_address;

				$img_cctv="http://$cctv_user_name:$cctv_password@$ip_address/ISAPI/Streaming/channels/1/picture";
				$nama_file = $card_no.'_out_'.time().'.jpg';
				$direktori = $this->config->item('upload_file')."/guest-system/";

				$target_cctv = $direktori.$nama_file;
				$this->grab_image($img_cctv,$target_cctv);

				$datestring = "%Y-%m-%d %H:%i:%s";
				$curr_date= mdate($datestring, time());

				$data_arr = array(
					'guest_mt_id_out' => $pos_out,
					'guest_pic_out' => $target_cctv,
					'checkout' => $curr_date,

					'guest_status' => 1
      	);
				$this->db->where('guest_dt_id', $guest_dt_id);
				$this->db->update('guest_system_dt', $data_arr);

				$currdateformat = $this->util_function_model->ddate($curr_date);

				$data_info = array('c', 'Selesai, tamu keluar dari pos.',
				$security_post_name, $checkin, $currdateformat,
				$guest_pic_1, $guest_pic_2, $target_cctv);

			}

			array_push($data_info, 'Last array');
			echo json_encode($data_info);
			die("");
		}
	}

	public function control(){
		$data['base']			= $this->config->item('base_url');
		$data['images_folder']	= $this->config->item('images');
		$data['images_icon']	= $this->config->item('images_icon');
		$data['js']				= $this->config->item('js');
		$data['css']			= $this->config->item('css');
		$data['author']			= $this->config->item('author');
		$data['site_title']		= $this->config->item('site_title');
		$data['page_heading']	= "Guest System Control";

		$data['display_info']=$this->session->flashdata('msg_to_show');
		$admin_id=$this->session->userdata('admin_id');
		$admin_name=$this->session->userdata('admin_name');
		$contact_level=$this->session->userdata('admin_level');
		//========================================================

		$data['admin_name']=$admin_name;

		//============ CHECK PRIV FOR SECURITY ========================
		$val_read=$this->util_function_model->check_priv('GUEST-SYSTEM-CONTROL',$admin_id,'read_val'); //-check valid access for read
		$val_write=$this->util_function_model->check_priv('GUEST-SYSTEM-CONTROL',$admin_id,'write_val'); //-check valid access for write
		if($contact_level==1){
			$val_write=TRUE;
		}

		$data['val_write']=$val_write;
		$where="";

		/*generate tables*/
		$this->load->model('table_model'); //- load table model

		/**** styling the table ****/
		//- you can describe your own template here by use the variable of $tmpl_table
		$tmpl_table='';//- used default style
		$this->table_model->set_template($tmpl_table); //- set template
		$this->table->set_heading( 'No.','Security Post','Post Gate',
		'CCTV IP Address','Action'); //- create the header of table

		$row_no=1;
		$sSQL="
			SELECT
				t1.guest_mt_id, t1.security_post_name, t1.post_gate,
				t1.ip_address, t1.cctv_user_name, t1.cctv_password
			FROM
				guest_system_mt t1
			$where
			";
		$view_dep = $this->db->query($sSQL);
		$table_data=$view_dep->result();
		if($table_data){
			foreach ($table_data as $row)
			{
				$guest_mt_id=$row->guest_mt_id;
				$security_post_name=$row->security_post_name;
				$ip_address=$row->ip_address;

				$post_gate = $row->post_gate;

				$icon_delete='';
				$icon_detail='';

				if($val_write){
					$icon_delete="<img src=\"".$data['images_icon']."/icons/delete.png\" title=\"Delete\" onclick=\"confirm_delete($guest_mt_id,'$security_post_name')\">&nbsp;&nbsp;";
					$icon_detail="<img src=\"".$data['images_icon']."/icons/edit.png\" title=\"Edit\" id=\"btn_dialog\" onclick=\"javascript:window.location.assign('".$data['base']."/guest_system/control_detail/$guest_mt_id')\">&nbsp;&nbsp;";
				}
				$action_button="<div align=\"left\">$icon_detail$icon_delete</div>";

				$post_gateCaption = ($post_gate) ? "In" : "Out";

			    $data_arr = array(
			    	$row_no.".",
			    	$security_post_name,
						$post_gateCaption,

			    	$ip_address,
						$action_button //- action
	        );
			    $this->table->add_row($data_arr);
			    $row_no++;
			}

			$data['display_table']=$this->table->generate();
		}
		else
		{
			$data['display_table']=$this->util_function_model->info_msg("You don't have any data to show.");
		}

		$this->table->clear();
		/*end tables*/
		//========================================================

		$data['dyn_menu']=$this->load->view('templates/dyn_menu',$data,true);
		$this->load->view('templates/header',$data);
		$this->load->view('guest_system/control',$data);
		$this->load->view('templates/footer');
	}

	public function control_detail($rid){
		$data['base']			= $this->config->item('base_url');
		$data['images_folder']	= $this->config->item('images');
		$data['js']				= $this->config->item('js');
		$data['css']			= $this->config->item('css');
		$data['author']			= $this->config->item('author');
		$data['site_title']		= $this->config->item('site_title');
		$data['page_heading']	= "Edit Guest System Control";

		$data['val']="edit";
		$data['rid']=$rid;

		$admin_id=$this->session->userdata('admin_id');
		$admin_name=$this->session->userdata('admin_name');
		$contact_level=$this->session->userdata('admin_level');
		//========================================================

		$data['admin_name']=$admin_name;
		$data['contact_level']=$contact_level;
		$data['priv_edit']=false;
		$val_write=$this->util_function_model->check_priv('GUEST-SYSTEM-CONTROL',$admin_id,'write_val'); //-check valid access for write
		if($val_write){
			$data['priv_edit']=true;
		}

		$this->db->select('security_post_name,post_gate,ip_address,cctv_user_name,cctv_password');
		$this->db->where('guest_mt_id', $rid);
		$this->db->from('guest_system_mt');
		$query = $this->db->get();
		$table_data=$query->result();
		$arr=0;
		if($table_data){
			foreach ($table_data as $row)
			{
				$data['security_post_name']=$row->security_post_name;
				$data['post_gate'] = $row->post_gate;
				$data['ip_address']=$row->ip_address;
				$data['cctv_user_name']=$row->cctv_user_name;
				$data['cctv_password']=$row->cctv_password;
			}
		}

		$data['dyn_menu']=$this->load->view('templates/dyn_menu',$data,true);
		$this->load->view('templates/header',$data);
		$this->load->view('guest_system/control_tab',$data);
		$this->load->view('templates/footer');
	}

	public function control_new(){
		$data['base']			= $this->config->item('base_url');
		$data['images_folder']	= $this->config->item('images');
		$data['js']				= $this->config->item('js');
		$data['css']			= $this->config->item('css');
		$data['author']			= $this->config->item('author');
		$data['site_title']		= $this->config->item('site_title');
		$data['page_heading']	= "Add New Guest System Control";

		$data['val']="new";

		$admin_id=$this->session->userdata('admin_id');
		$admin_name=$this->session->userdata('admin_name');
		$contact_level=$this->session->userdata('admin_level');
		//========================================================

		$data['admin_name']=$admin_name;

		$data['priv_edit']=false;
		if($this->util_function_model->check_priv('GUEST-SYSTEM-CONTROL',$admin_id)){//- check privilege for edit user
			$data['priv_edit']=true;
		}

		$data['rid']=0;
		$data['security_post_name']='';
		$data['post_gate'] = '';

		$data['ip_address']='';
		$data['cctv_user_name']='';
		$data['cctv_password']='';

		$data['dyn_menu']=$this->load->view('templates/dyn_menu',$data,true);
		$this->load->view('templates/header',$data);
		$this->load->view('guest_system/control_tab',$data);
		$this->load->view('templates/footer');
	}

	public function control_fnl(){
		//- init all variables
		$base = $this->config->item('base_url');
		isset($_POST["proc_val"]) ? $proc_val=strip_slashes(strip_quotes($_POST["proc_val"])) : $proc_val="";
		isset($_POST["rid"]) ? $rid=strip_slashes(strip_quotes($_POST["rid"])) : $rid=0;

		$datestring = "%Y-%m-%d %H:%i:%s";
		$time = time();
		$curr_date= mdate($datestring, $time);

		$admin_id=$this->session->userdata('admin_id');

		isset($_POST["txt_post_name"]) ? $post_name=strip_slashes(strip_quotes($_POST["txt_post_name"])) : $post_name="";
		isset($_POST["seleGate"]) ? $seleGate= $_POST["seleGate"] : $seleGate='';
		isset($_POST["txt_cctv_ip"]) ? $cctv_ip=strip_slashes(strip_quotes($_POST["txt_cctv_ip"])) : $cctv_ip="";
		isset($_POST["txt_cctv_username"]) ? $cctv_username=strip_slashes(strip_quotes($_POST["txt_cctv_username"])) : $cctv_username='';
		isset($_POST["txt_cctv_password"]) ? $cctv_password=strip_slashes(strip_quotes($_POST["txt_cctv_password"])) : $cctv_password='';
		isset($_POST["is_ajax"]) ? $is_ajax=strip_slashes(strip_quotes($_POST["is_ajax"])) : $is_ajax=0;

		$data_info=array();

		if($is_ajax == 1){

			if($proc_val == "proc_new"){
				$data_arr = array(
	               'security_post_name' => $post_name,
								 'post_gate' => $seleGate,
	               'ip_address' => $cctv_ip,

	        'cctv_user_name' => $cctv_username,
					'cctv_password' => $cctv_password,
					'create_by' => $admin_id,

						'create_time' => $curr_date,
						'update_by' => $admin_id,
						'update_time' => $curr_date
	      );
				$this->db->insert('guest_system_mt', $data_arr);

				//==== INSERT TO ACTION LOG ===========================
				$this->util_function_model->insert_to_action_log($admin_id,
				'GUEST-SYSTEM-CONTROL',$proc_val);
				//=====================================================
				$msg = "Operation successful! New Guest System Control has been added";
				$url = $base.'/guest_system/control';
	            array_push($data_info, $msg, $url);
	            echo json_encode($data_info);
	            die("");
			}

			if($proc_val=="proc_edit"){
				$data_arr = array(
	               'security_post_name' => $post_name,
								 'post_gate' => $seleGate,
	               'ip_address' => $cctv_ip,
	               'cctv_user_name' => $cctv_username,
					'cctv_password' => $cctv_password,
					'update_by' => $admin_id,
					'update_time' => $curr_date
	      );
				$this->db->where('guest_mt_id', $rid);
				$this->db->update('guest_system_mt', $data_arr);

	            //==== INSERT TO ACTION LOG ===========================
				$this->util_function_model->insert_to_action_log($admin_id,'GUEST-SYSTEM-CONTROL',$proc_val);
				//=====================================================

							$status_msg="Operation successful! Guest System Control has been updated";
							$msg_to_show=$this->util_function_model->info_msg($status_msg);
					        $this->session->set_flashdata('msg_to_show', $msg_to_show);
					        $url=$base.'/guest_system'.'/control';

				            array_push($data_info, $status_msg, $url);
				            echo json_encode($data_info);
				            die("");
			}
		}

		if($proc_val=="proc_delete"){
			$ssql="delete from guest_system_mt where guest_mt_id = $rid";
			$delete_data = $this->db->query($ssql);

			//==== INSERT TO ACTION LOG ===========================
			$this->util_function_model->insert_to_action_log($admin_id,'GUEST-SYSTEM-CONTROL',$proc_val);
			//=====================================================

			$status_msg="Operation successful! Guest System Control $post_name has been deleted";
			$msg_to_show=$this->util_function_model->info_msg($status_msg);
	        $this->session->set_flashdata('msg_to_show', $msg_to_show);
	        redirect('guest_system/control', 'refresh');
		}
	}

	protected function grab_image($image_url, $image_file){
		//harus disetting di camera:
		//menu securty/authentication >> web authentication pilih digest/basic

		$fp = fopen ($image_file, 'w+');              // open file handle

    	$ch = curl_init($image_url);
	    // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // enable if you want
	    curl_setopt($ch, CURLOPT_FILE, $fp);          // output to file
	    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	    curl_setopt($ch, CURLOPT_TIMEOUT, 1000);      // some large value to allow curl to run for a long time
	    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
	     curl_setopt($ch, CURLOPT_VERBOSE, true);   // Enable this line to see debug prints
	    curl_exec($ch);

	    curl_close($ch);                              // closing curl handle
	    fclose($fp);
	    /*
		$ch = curl_init($image_url);
		$fp = fopen($image_file, 'wb');
		curl_setopt($ch, CURLOPT_FILE, $fp);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_exec($ch);
		curl_close($ch);
		fclose($fp);
		*/
	}

	public function guest_system_fnl($card_no='', $post_id=0) {

		$this->db->select('card_no')
		->from('guest_system_dt')

		->where('card_no', $card_no)
		->where('guest_status', 0);

		$querystring = $this->db->get();

		if( $querystring->result() ) {
			$data_info=array('exist', "Tamu belum gate Out, masih di dalam.");

		} else {
			$ip_address='';
			$cctv_user_name='';
			$cctv_password='';

			$dest='';

			$datestring = "%Y-%m-%d %H:%i:%s";
			$time = time();
			$curr_date= mdate($datestring, $time);

			$admin_id=$this->session->userdata('admin_id');
			$direktori = $this->config->item('upload_file')."/guest-system/";

			//-KTP
			$nama_file = $card_no.'_ktp_'.time().'.jpg';
			//$direktori = 'C:/VMS/guest-system/';
			$target_id = $direktori.$nama_file;
			move_uploaded_file($_FILES['webcam']['tmp_name'], $target_id);

			$this->db->select('ip_address, cctv_user_name, cctv_password');
			$this->db->where('guest_mt_id', $post_id);
			$this->db->from('guest_system_mt');

			$query = $this->db->get();
			$cre = $query->row();

			$ip_address = $cre->ip_address;
			$cctv_user_name = $cre->cctv_user_name;
			$cctv_password = $cre->cctv_password;

			//-CCTV
			$img_cctv="http://$cctv_user_name:$cctv_password@$ip_address/ISAPI/Streaming/channels/1/picture";
			//header("Content-Type: image/jpeg");
			//$img_cctv="http://192.168.1.212/ISAPI/Streaming/channels/1/picture";
			$nama_file = $card_no.'_in_'.time().'.jpg';
			//$direktori = 'C:/VMS/guest-system/';
			$target_cctv = $direktori.$nama_file;
			$this->grab_image($img_cctv, $target_cctv);

			$data = array(
				'guest_mt_id_in' => $post_id,
				'card_no' => $card_no,
				'guest_pic_1' => $target_id,

				'guest_pic_2' => $target_cctv,
				'guest_dest' => $dest,
				'guest_status' => 0,

				'checkin' => $curr_date,
				'create_by' => $admin_id,
				'create_time' => $curr_date
			);
			$this->db->insert('guest_system_dt', $data);
			$data_info = array('add', "&#9745; Tamu baru berhasil disimpan.", $target_cctv);

/*
		$msg_to_show = $this->util_function_model->info_msg($status_msg);
		$this->session->set_flashdata('msg_to_show', $msg_to_show);*/
		}

		array_push($data_info, 'guest-fnl');
		echo json_encode($data_info);
		die("");
	}
	/*
	public function reload_cctv($reload_status=0,$post_id=0){

		//isset($_POST["reload_status"]) ? $reload_status=strip_slashes(strip_quotes($_POST["reload_status"])) : $reload_status=0;
		//isset($_POST["post_id"]) ? $post_id=strip_slashes(strip_quotes($_POST["post_id"])) : $post_id=0;

		$ip_address='';
		$cctv_user_name='';
		$cctv_password='';

		$this->db->select('ip_address,cctv_user_name,cctv_password');
		$this->db->where('guest_mt_id',$post_id);
		$this->db->from('guest_system_mt');
		$query = $this->db->get();
		$table_data=$query->result();
		if($table_data){
			foreach ($table_data as $row)
			{
				$ip_address=$row->ip_address;
				$cctv_user_name=$row->cctv_user_name;
				$cctv_password=$row->cctv_password;
			}
		}

		$data_info=array();
		$img='';
		$time_=time();
		if($reload_status==1){
			header("Content-Type: image/jpeg");
			$img="http://$cctv_user_name:$cctv_password@$ip_address/ISAPI/Streaming/channels/1/picture?second=$time_";
			//$img="http://$ip_address/ISAPI/Streaming/channels/1/picture?second=$time_";
		 	readfile($img);
		}

		//$data_info[0]=$img;
        //echo json_encode($data_info);
        //die("");
	}
	*/

	public function reload_cctv($reload_status=0,$post_id=0){
		$ip_address='';
		$cctv_user_name='';
		$cctv_password='';

		$this->db->select('ip_address,cctv_user_name,cctv_password');
		$this->db->where('guest_mt_id',$post_id);
		$this->db->from('guest_system_mt');
		$query = $this->db->get();
		$ip_unm_pwd=$query->row();

		$ip_address=$ip_unm_pwd->ip_address;
		$cctv_user_name=$ip_unm_pwd->cctv_user_name;
		$cctv_password=$ip_unm_pwd->cctv_password;

		 //$img="192.168.1.212:80/snapshot.cgi?user=admin&pwd=viovera789";
		 $img="http://$cctv_user_name:$cctv_password@$ip_address/ISAPI/Streaming/channels/1/picture";
		 header ('content-type: image/jpeg');
		 readfile($img);
	}

	public function guest_staying_in() {
		$data['page_heading'] = "GSI Reports";

		$data['author']			= $this->config->item('author');
		$data['images_folder']	= $this->config->item('images');
		$data['base']			= $this->config->item('base_url');

		$data['css']			= $this->config->item('css');
		$data['admin_name']=$this->session->userdata('admin_name');

		$data['js']				= $this->config->item('js');
		$data['site_title']		= $this->config->item('site_title');

		$this->load->model('table_model'); //- load table model

		/**** styling the table ****/
		//- you can describe your own template here by use the variable of $tmpl_table
		$tmpl_table='';//- used default style
		$this->table_model->set_template($tmpl_table); //- set template
		$this->table->set_heading('No.', 'KTP Capture',
		'Waktu Masuk', 'IN Photo', 'Lama Waktu'); //- create the header of table

		$guest_tb = $this->db->select("checkin, guest_pic_1, guest_pic_2")
		->where("guest_status", 0)

		->order_by("checkin", 'desc')
		->get("guest_system_dt")->result();

		if( $guest_tb ) {

			$lastdatetime = new DateTime();
			$row_no = 1;

			foreach ( $guest_tb as $row ) {

				$chkindatetime = new DateTime($row->checkin);

				$interval = $lastdatetime->diff($chkindatetime);

				if( $interval->format('%a') ) {
					$elapsed = $interval->format('%a hari');

					if( $interval->format('%h') ) {

						$hourfrmt = $interval->format(' %h jam');
						$elapsed .= "<em class=\"text-dark\">$hourfrmt</em>";
					} else {

						$elapsed .= " lalu";
					}

				} else if( $interval->format('%h') ) {
					$elapsed = $interval->format('%h jam lalu');

				} else if( $interval->format('%i') ) {
					$elapsed = $interval->format('%i menit lalu');

				} else if( $interval->format('%s') ) {
					$elapsed = $interval->format('%s detik lalu');

				}

				$chkin = strtotime($row->checkin);
				$checkindatedif = [
					'data' => $elapsed,
					'data-order' => $chkin
				];

				$chkindate = date('d-F Y', $chkin);
				$chkintime = date('H:i:s', $chkin);

				$chkindatetm = [
					'data-order' => $row->checkin,
					'data' => $chkindate." <em class=\"text-gray\">$chkintime</em>"
				];

				$this->table->add_row(
					$row_no.".",
					"<a data-fancybox=\"guest\" href=\"".".$row->guest_pic_1\" class=\"fncboxphotos\">
					<img src=\"../assets/dist/img/via-logo.png\" width=\"39\">
					</a>
					<figcaption><p>Waktu Masuk: $chkindate"." pukul "."$chkintime</p></figcaption>",
					$chkindatetm,
					"<a data-fancybox=\"guest\" href=\"".".$row->guest_pic_2\" class=\"fncboxphotos\">
					<img src=\"../assets/dist/img/via-logo.png\" width=\"39\">
					</a>
					<figcaption><p>Waktu Masuk: $chkindate"." pukul "."$chkintime</p></figcaption>",
					$checkindatedif
				);

				$row_no++;
			}
			$data['display_table']=$this->table->generate();

		}
		else{
			$data['display_table']=$this->util_function_model->info_msg("empty data");

		}

		$data['dyn_menu']=$this->load->view('templates/dyn_menu', $data, true);

		$this->load->view('templates/header', $data);
		$this->load->view('reports/guest_staying_in', $data);
		$this->load->view('templates/footer');
	}

	public function guest_log() {

			$data['base']			= $this->config->item('base_url');
			$data['images_folder']	= $this->config->item('images');
			$data['images_icon']	= $this->config->item('images_icon');

			$data['js']				= $this->config->item('js');
			$data['css']			= $this->config->item('css');
			$data['author']			= $this->config->item('author');

			$data['site_title']		= $this->config->item('site_title');
			$data['page_heading']	= "Reports - Guests Log";

			$data['display_info']=$this->session->flashdata('msg_to_show');
			$admin_id=$this->session->userdata('admin_id');
			$admin_name=$this->session->userdata('admin_name');

			$contact_level=$this->session->userdata('admin_level');
			//========================================================

			$data['admin_name']=$admin_name;

			//============ CHECK PRIV FOR SECURITY ========================
			$val_read=$this->util_function_model->check_priv('RPT-GUEST-LOG',$admin_id,'read_val'); //-check valid access for read
			$val_write=$this->util_function_model->check_priv('RPT-GUEST-LOG',$admin_id,'write_val'); //-check valid access for write

			if($contact_level == 1) {
				$val_write = TRUE;
			}

			$data['priv_edit']=$val_write;

			$this->load->model('table_model'); //- load table model

			$row_no = 1;

			/**** styling the table ****/
			//- you can describe your own template here by use the variable of $tmpl_table
			$tmpl_table='';//- used default style
			$this->table_model->set_template($tmpl_table); //- set template
			$this->table->set_heading('No.','Kartu Tamu','KTP Capture',
			'Waktu Masuk','Waktu Keluar'); //- create the header of table

			$guest_tb = $this->db->select("checkin, checkout, guest_pic_1, card_no")
			->from("guest_system_dt")
			->order_by("checkin", 'desc')
			->get()->result();

			if( $guest_tb ) {
				foreach ($guest_tb as $row) {

					$chkin = strtotime($row->checkin);
					$chkindate = date('d-F Y', $chkin);
					$chkintime = date('H:i:s', $chkin);

					$chkindatetm = [
						'data-order' => $row->checkin,
						'data' => $chkindate." <em class=\"text-gray\">$chkintime</em>"
					];

					$chkout = strtotime($row->checkout);

					if( $chkout > 0 ) {
						$chkoutdate = date('d-F Y', $chkout);
						$chkouttime = date('H:i:s', $chkout);

						$dateoutcaption = $chkoutdate." pukul ".$chkouttime;
						$chkoutdatetm = [
						'data' => $chkoutdate." <em class=\"text-gray\">$chkouttime</em>",
						'data-order' => $row->checkout
						];

					} else {
						$dateoutcaption = 'Tamu belum keluar';
						$chkoutdatetm = "<span class=\"text-primary\">Tamu belum keluar</span>";
					}

					$this->table->add_row(
						$row_no.".",
						$row->card_no,
						"<a data-fancybox=\"ktp\" href=\"".".$row->guest_pic_1\" class=\"imgs\">
						<img src=\"../assets/dist/img/via-logo.png\" width=\"39\">
						</a>
						<figcaption>
						<p class=\"font-weight-bold\">Nomor Kartu Tamu: $row->card_no</p>
						<p>Waktu Masuk: $chkindate"." pukul "."$chkintime</p>
						<p>Waktu Keluar: $dateoutcaption</p>
						</figcaption>",
						$chkindatetm,
						$chkoutdatetm
					);

					$row_no++;
				}
				$data['display_table']=$this->table->generate();
			}
			else{
				$data['display_table']=$this->util_function_model->info_msg("no data");
			}

			$data['dyn_menu']=$this->load->view('templates/dyn_menu', $data, true);

			$this->load->view('templates/header',$data);
			$this->load->view('reports/guest_log',$data);
			$this->load->view('templates/footer');
	}

}
