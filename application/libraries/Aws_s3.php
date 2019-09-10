<?php

/**
 * Amazon S3 Upload PHP class
 *
 * @version 0.1
 */
require_once FCPATH . 'plugin/vendor/autoload.php';

use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;

class Aws_s3 {

    protected $s3_bucket_name;
    protected $s3_folder_name;
    protected $s3_bucket_url;
    protected $s3_access_key;
    protected $s3_secret_key;

    protected $s3Client;

    function __construct()
    {
        $this->CI =& get_instance();
        
        $this->s3_bucket_name = config_item('s3_bucket_name');
        $this->s3_folder_name = config_item('s3_folder_name');
        $this->s3_bucket_url = config_item('s3_bucket_url');
        $this->s3_access_key = config_item('s3_access_key');
        $this->s3_secret_key = config_item('s3_secret_key');

        $this->s3Client = new S3Client(array(
          'region' => 'ap-northeast-2',
          'version' => 'latest',
          'signature' => 'v4',
          'credentials' => array(
            'key'    => $this->s3_access_key,
            'secret' => $this->s3_secret_key
            )         
        ));

    }

    function upload_file($file_path,$file_name,$upload_path)
    {   

        

        

       
        $file_url = $file_path.$file_name;
        $s3_key = $upload_path.$file_name;

        $s3_body = file_get_contents($file_url);

        return $this->s3Client->putObject(array(
          'Bucket' => $this->s3_bucket_name,
          'Key'    => $s3_key,
          'Body'   => $s3_body,
          'ACL'    => 'public-read'
        ));
    }


    function delete_file($file_name)
    {   

        

        

        $s3_key = $file_name;

        return $this->s3Client->deleteObject(array(
          'Bucket' => $this->s3_bucket_name,
          'Key'    => $s3_key
          
        ));
    }

}

