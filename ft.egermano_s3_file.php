<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if (!class_exists('S3')) require_once 'S3.php';

class Egermano_s3_file_ft extends EE_Fieldtype {

	var $info = array(
		'name'		=> 'Egermano S3 File',
		'version'	=> '0.1'
	);
		
	function install()
	{
		// Parans to auth in aws S3
		return array(
			'aws_access_key'	=> 'Your AWS Access Key here',
			'aws_secret_key'	=> 'Your AWS Secret Key here',
			'aws_bucket'	=> 'Name of your bucket',
			'aws_path' => 'Path upload target'
		);
	}
	
	function display_global_settings()
	{
		$val = array_merge($this->settings, $_POST);

		$form = form_label('AWS Access Key', 'aws_access_key').NBS.form_input('aws_access_key', $val['aws_access_key']);
		$form .= form_label('AWS Secret Key', 'aws_secret_key').NBS.form_input('aws_secret_key', $val['aws_secret_key']);
		$form .= form_label('AWS Bucket', 'aws_bucket').NBS.form_input('aws_bucket', $val['aws_bucket']);

		return $form;
	}
	
	function save_global_settings()
	{
		return array_merge($this->settings, $_POST);
	}
	
	function display_settings($data)
	{	
		$form = '<table class="mainTable" border="0" cellspacing="0" cellpadding="0"><thead><tr><th width="40%">Custom Field Options</th><th></th></tr></thead><tbody>';
		$form .= '<tr class="even"><td>'.form_label('AWS Path', 'aws_path').'</td><td>'.form_input('aws_path', isset($data['aws_path']) ? $data['aws_path'] : $this->settings['aws_path']).'</td></tr>';
		$form .= '</tbody></table>';

		return $form;

	}
	
	function save_settings($data)
	{
		$data = array(
			'aws_path' => $this->EE->input->post('aws_path')
		);
		
		return $data;
	}
	
	// --------------------------------------------------------------------
	
	function display_field($data)
	{
		$r = '<div class="file_set egermano_s3_file">';
		if(!empty($data)){
			$r .= '<div><b>File: </b>'.$data.'</div></br>';
		}
		
		$r .= form_upload($this->field_name, '', 'onChange="document.getElementsByName(\''.$this->field_name.'\')[1].value = \'\'"');
		$r .= form_hidden($this->field_name, $data, "id='$this->field_name'");
		$r .= '</div>';
		
		return '<br/>'.$r;
	}
	
	function save($data){
		$value = $this->EE->input->post($this->field_name);
		if(empty($value))
		{

			$file_name = $_FILES[$this->field_name]['name'];
			$file_type = $_FILES[$this->field_name]['type'];
			$file_size = $_FILES[$this->field_name]['size'];
			$file_tmp_name = $_FILES[$this->field_name]['tmp_name'];
			$error = $_FILES[$this->field_name]['error'];
			
			$has_path = !empty($this->settings['aws_path']);
			
			// Instantiate the class S3
			$s3 = new S3($this->settings['aws_access_key'], $this->settings['aws_secret_key']);
			
			
			if($has_path)
			{
				$info = $s3->putObjectString(file_get_contents($_FILES[$this->field_name]['tmp_name'], true) , $this->settings['aws_bucket'], $this->settings['aws_path'].'/'.$file_name, S3::ACL_PUBLIC_READ);
				return 'http://s3.amazonaws.com/'.$this->settings['aws_bucket'].'/'.$this->settings['aws_path'].'/'.$file_name;
			}
			else
			{
				$info = $s3->putObjectString(file_get_contents($_FILES[$this->field_name]['tmp_name'], true) , $this->settings['aws_bucket'], $file_name, S3::ACL_PUBLIC_READ);
				return 'http://s3.amazonaws.com/'.$this->settings['aws_path'].'/'.$file_name;
			}
			
		}
		else
		{
			return $value;
		}
	}
	
	function replace_tag($data, $params = array(), $tagdata = FALSE)
	{
		return $data;
	}
	
	function replace_image($data, $params = array(), $tagdata = FALSE)
	{
		$image = "<img src='$data' />";
		return $image;
	}
	
	function replace_audio($data, $params = array(), $tagdata = FALSE)
	{
		$image = "<audio src='$data' controls='controls'></audio>";
		return $image;
	}
	
	function replace_video($data, $params = array(), $tagdata = FALSE)
	{
		$image = "<img src='$data' />";
		return $image;
	}
	
}
// END Egermano_s3_file_ft class

/* End of file ft.egermano_s3_file.php */
/* Location: ./system/expressionengine/third_party/google_maps/ft.egermano_s3_file.php */