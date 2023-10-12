<?php

/**
 * Load All S3 related actions
 *
 * @class   ACOOFMF_S3
 *
 * It is used to divide functionality of amazon s3 connection into different parts
 */

if (!defined('ABSPATH')) {
    exit;
}

// Libraries
use Aws\S3\S3Client;
use Aws\Exception\AwsException;
use Aws\S3\MultipartUploader;
use Aws\Exception\MultipartUploadException;

class ACOOFMF_S3
{
    
    /**
     * The version number.
     *
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $version;

    /**
     * The token.
     *
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $token;

    /**
     * The main plugin file.
     *
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $file;

    /**
     * The main plugin directory.
     *
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $dir;

    /**
     * The plugin assets directory.
     *
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $assets_dir;

    /**
     * The plugin Configuration.
     *
     * @var     array
     * @access  protected
     * @since   1.0.0
     */

    protected $config;

    /**
     * The plugin Settings.
     *
     * @var     array
     * @access  protected
     * @since   1.0.0
     */

    protected $settings;

    /**
     * The plugin assets URL.
     *
     * @var     string
     * @access  public
     * @since   1.0.0
     */


    public $assets_url;
    /**
     * The s3 client.
     *
     * @var     object
     * @access  public
     * @since   1.0.0
     */

    public $acoofm_S3_Client=false;
    /**
     * The plugin hook suffix.
     *
     * @var     array
     * @access  public
     * @since   1.0.0
     */
    public $hook_suffix = array();


    /**
     * Constructor function.
     *
     * @access  public
     * @param string $file plugin start file path.
     * @since   1.0.0
     */
    public function __construct($file = '')
    {
        $this->version = ACOOFM_VERSION;
        $this->token = ACOOFM_TOKEN;
        $this->file = $file;
        $this->dir = dirname($this->file);
        $this->assets_dir = trailingslashit($this->dir) . 'assets';
        $this->assets_url = esc_url(trailingslashit(plugins_url('/assets/', $this->file)));
        $plugin = plugin_basename($this->file);

        $this->config = acoofm_get_option('credentials', []);
        $this->settings = acoofm_get_option('settings', []);

        if (
            isset($this->config['region']) && !empty($this->config['region']) &&
            isset($this->config['access_key']) && !empty($this->config['access_key']) &&
            isset($this->config['secret_key']) && !empty($this->config['secret_key'])
        ) {
            $this->acoofm_S3_Client = new S3Client([
                'version'     => '2006-03-01',
                'region'      => $this->config['region'],
                'use_accelerate_endpoint' => isset($this->config['transfer_accilaration'])
                                                ? $this->config['transfer_accilaration'] : false,
                'use_aws_shared_config_files' => false,
                'credentials' => [
                    'key'    => $this->config['access_key'],
                    'secret' => $this->config['secret_key'],
                ],
            ]);
        }
    }

    /**
     * Verify Credentials
     * @since 1.0.0
     * @return boolean
     */
    public function verify($access_key, $secret_key, $region, $bucket_name, $transfer_accilaration = false)
    {
        if (
            isset($region) && !empty($region) &&
            isset($access_key) && !empty($access_key) &&
            isset($secret_key) && !empty($secret_key) &&
            isset($bucket_name) && !empty($bucket_name)
        ) {
            try {
                $s3Client = new S3Client([
                    'version'     => '2006-03-01',
                    'region'      => $region,
                    'use_accelerate_endpoint' => $transfer_accilaration,
                    'use_aws_shared_config_files' => false,
                    'credentials' => [
                        'key'    => $access_key,
                        'secret' => $secret_key,
                    ],
                ]);

                //Listing all S3 Bucket
                $buckets = $s3Client->listBuckets();
                $bucket_found = false;
                $region_correct = false;
                if ($buckets) {
                    foreach ($buckets['Buckets'] as $bucket) {
                        if ($bucket['Name']==$bucket_name) {
                            $bucket_found = true;
                        }
                    }
                } else {
                    return array('message' => __('No Buckets found', 'offload-media-cloud-storage'), 'code' => 403, 'success' => false);
                }
                if ($bucket_found) {
                    $fileName = "acoofm_verify.txt";
                    $verify_file = fopen('./'.$fileName, "w");
                    $txt = "We are verifying input/output operations in s3\n";
                    fwrite($verify_file, $txt);
                    fclose($verify_file);

                    $upload = $s3Client->putObject([
                        'Bucket' => $bucket_name,
                        'Key'    => $fileName,
                        'Body'   => fopen('./'.$fileName, "r"),
                        'ACL'    => 'public-read', // make file 'public'
                    ]);
                    
                    @unlink($fileName);
                    if ($upload->get('ObjectURL')) {
                        try {
                            $getObject = $s3Client->GetObject([
                                'Bucket' => $bucket_name,
                                'Key'    => $fileName,
                                'SaveAs' => 'acoofm-local-verify.txt'
                            ]);

                            if (file_exists('acoofm-local-verify.txt')) {
                                @unlink('acoofm-local-verify.txt');
                                $s3Client->deleteObject([
                                    'Bucket' => $bucket_name,
                                    'Key'    => $fileName,
                                ]);
                
                                if (!$s3Client->doesObjectExist($bucket_name, $fileName)) {
                                    return array('message' => __('Configuration for AWS/S3 has verified successfully', 'offload-media-cloud-storage'), 'code' => 200, 'success' => true);
                                } else {
                                    return array('message' => __('Bucket has permission issues on deleting the object from bucket, Please check ACL permission as well as policies', 'offload-media-cloud-storage'), 'code' => 403, 'success' => false);
                                }
                            } else {
                                return array('message' => __('Bucket has permission issues on getting the object from bucket, Please check ACL permission as well as policies', 'offload-media-cloud-storage'), 'code' => 403, 'success' => false);
                            }
                        } catch (Aws\S3\Exception\S3Exception $ex) {
                            return array('message' => $ex->getAwsErrorMessage(), 'code' => $ex->getAwsErrorCode(), 'success' => false);
                        }
                    } else {
                        return array('message' => __('Bucket has permission issues on putting object in to bucket, Please check ACL permission as well as policies', 'offload-media-cloud-storage'), 'code' => 403, 'success' => false);
                    }
                } else {
                    return array('message' => __('Bucket Name is incorrect', 'offload-media-cloud-storage'), 'code' => 403, 'success' => false);
                }
            } catch (Aws\S3\Exception\S3Exception $ex) {
                return array('message' => $ex->getAwsErrorMessage() ?? __('Please check the authorization details', 'offload-media-cloud-storage'), 'code' => $ex->getStatusCode() ?? 405, 'success' => false);
            }
        } else {
            return array( 'message' => __('Insufficient Data. Please try again', 'offload-media-cloud-storage'), 'code' =>  405, 'success' => false);
        }
    }

    /**
     * Connect Function To Identify the congfigurations are correct
     * @since 1.0.0
     */
    public function connect()
    {
        if ($this->acoofm_S3_Client) {
            return true;
        }
        return false;
    }

    /**
     * Make Object Private
     * @since 3.0.0
     * 
     */
    public function make_private($key) {
        if(!$key) return false;
        try {
            $this->acoofm_S3_Client->putObjectAcl([
                'Bucket'    => $this->config['bucket_name'],
                'Key'       => $key,
                'ACL'       => 'private'
            ]); 
            return true;
        } catch (Aws\S3\Exception\S3Exception $ex) {
            return false;
        }
        return false;
    }



    /**
     * Make Object Public
     * @since 3.0.0
     * 
     */
    public function make_public($key) {
        if(!$key) return false;
        try {
            $this->acoofm_S3_Client->putObjectAcl([
                'Bucket'    => $this->config['bucket_name'],
                'Key'       => $key,
                'ACL'       => 'public-read'
            ]); 
            return true;
        } catch (Aws\S3\Exception\S3Exception $ex) {
            return false;
        }
        return false;
    }



    /**
     * Check the object exist 
     * @since 1.1.8
     */
    public function is_exist($key) {
        if(!$key) return false;

        if($this->acoofm_S3_Client->doesObjectExist($this->config['bucket_name'], $key)) {
            return true;
        }
        
        return false;
    }

    /**
     * Upload Single
     * @since 1.0.0
     * @return boolean
     */
    public function uploadSingle($media_absolute_path, $media_path, $prefix='')
    {
        $result = array();
        if (
            isset($media_absolute_path) && !empty($media_absolute_path) &&
            isset($media_path) && !empty($media_path)
        ) {
            $file_name = wp_basename( $media_path );
            if ($file_name) {
                $upload_path = acoofm_generate_object_key($file_name, $prefix);
               
                // Decide Multipart upload or normal put object
                if (filesize($media_absolute_path) <= ACOOFM_S3_MULTIPART_UPLOAD_MINIMUM_SIZE) {
                    // Upload a publicly accessible file. The file size and type are determined by the SDK.
                    try {
                        $upload = $this->acoofm_S3_Client->putObject([
                            'Bucket' => $this->config['bucket_name'],
                            'Key'    => $upload_path,
                            'Body'   => fopen($media_absolute_path, 'r'),
                            'ACL'    => 'public-read', // make file 'public'
                        ]);

                        $result = array(
                            'success'   => true,
                            'code'      => 200,
                            'file_url'  => $upload->get('ObjectURL'),
                            'key'       => $upload_path,
                            'Message'   => __('File Uploaded Successfully', 'offload-media-cloud-storage')
                        );
                    } catch (Aws\S3\Exception\S3Exception $e) {
                        $result = array(
                            'success' => false,
                            'code'    => 403,
                            'Message' => $e->getMessage()
                        );
                    }
                } else {
                    $multiUploader = new MultipartUploader($this->acoofm_S3_Client, $media_absolute_path, [
                        'bucket'    => $this->config['bucket_name'],
                        'key'       => $upload_path,
                        'acl'       => 'public-read', // make file 'public'
                    ]);
                    
                    try {
                        do {
                            try {
                                $uploaded = $multiUploader->upload();
                            } catch (MultipartUploadException $e) {
                                $multiUploader = new MultipartUploader($this->acoofm_S3_Client, $media_absolute_path, [
                                    'state' => $e->getState(),
                                ]);
                            }
                        } while (!isset($uploaded));

                        if (isset($uploaded['ObjectURL']) && !empty($uploaded['ObjectURL'])) {
                            $result = array(
                                'success' => true,
                                'code'    => 200,
                                'file_url' => urldecode($uploaded['ObjectURL']),
                                'key'     => $upload_path,
                                'Message' => __('File Uploaded Successfully', 'offload-media-cloud-storage')
                            );
                        } else {
                            $result = array(
                                'success' => false,
                                'code'    => 403,
                                'Message' => __('Something happened while uploading to server', 'offload-media-cloud-storage')
                            );
                        }
                    } catch (MultipartUploadException $e) {
                        $result = array(
                            'success' => false,
                            'code'    => 403,
                            'Message' => $e->getMessage()
                        );
                    }
                }
            } else {
                $result = array(
                    'success' => false,
                    'code'    => 403,
                    'Message' => __('Check the file you are trying to upload. Please try again', 'offload-media-cloud-storage')
                );
            }
        } else {
            $result = array(
                'success' => false,
                'code'    => 405,
                'Message' => __('Insufficient Data. Please try again', 'offload-media-cloud-storage')
            );
        }
        return $result;
    }

    /**
     * Save object to local
     * @since 1.0.0
     */
    public function object_to_local($key, $save_path)
    {
        $path_parts = pathinfo($save_path);
        if (!file_exists($path_parts['dirname'])) {
            mkdir($path_parts['dirname'], 0755, true);
        }

        try {
            $getObject = $this->acoofm_S3_Client->GetObject([
                'Bucket' => $this->config['bucket_name'],
                'Key'    => $key,
                'SaveAs' => $save_path
            ]);
            if (file_exists($save_path)) {
                return true;
            }
        } catch (Aws\S3\Exception\S3Exception $e) {
            return false;
        }
        return false;
    }


    /**
     * Delete Single
     * @since 1.0.0
     * @return boolean
     */
    public function deleteSingle($key)
    {
        $result = array();
        if (isset($key) && !empty($key)) {
            try {
                $this->acoofm_S3_Client->deleteObject([
                    'Bucket' => $this->config['bucket_name'],
                    'Key'    => $key
                ]);

                if (!$this->acoofm_S3_Client->doesObjectExist($this->config['bucket_name'], $key)) {  
                    $result = array(
                        'success' => true,
                        'code'    => 200,
                        'Message' => __('Deleted Successfully', 'offload-media-cloud-storage')
                    );
                } else {
                    $result = array(
                        'success' => false,
                        'code'    => 403,
                        'Message' => __('File not deleted', 'offload-media-cloud-storage')
                    );
                }
            } catch (Aws\S3\Exception\S3Exception $e) {
                $result = array(
                    'success' => false,
                    'code'    => 403,
                    'Message' => $e->getMessage()
                );
            }
        } else {
            $result = array(
                'success' => false,
                'code'    => 405,
                'Message' => __('Insufficient Data. Please try again', 'offload-media-cloud-storage')
            );
        }
        return $result;
    }

    /**
     * get presigned URL
     * @since 1.0.0
     * @return boolean
     */
    public function get_presigned_url($key)
    {
        $result = array();
        if (isset($key) && !empty($key)) {
            try {
                $cmd = $this->acoofm_S3_Client->getCommand('GetObject', [
                    'Bucket' => $this->config['bucket_name'],
                    'Key'    => $key
                ]);

                $expires = isset($this->settings['presigned_expire']) ? $this->settings['presigned_expire'] : 20;

                $request = $this->acoofm_S3_Client->createPresignedRequest($cmd, sprintf('+%s  minutes', $expires));

                if ($presignedUrl = (string)$request->getUri()) {
                    $result = array(
                        'success'   => true,
                        'code'      => 200,
                        'file_url'  => $presignedUrl,
                        'Message'   => __('Got Presigned URL Successfully', 'offload-media-cloud-storage')
                    );
                } else {
                    $result = array(
                        'success' => false,
                        'code'    => 403,
                        'Message' => __('Error getting presigned URL', 'offload-media-cloud-storage')
                    );
                }
            } catch (Aws\S3\Exception\S3Exception $e) {
                $result = array(
                    'success' => false,
                    'code'    => 403,
                    'Message' => $e->getMessage()
                );
            }
        } else {
            $result = array(
                'success' => false,
                'code'    => 405,
                'Message' => __('Insufficient Data. Please try again', 'offload-media-cloud-storage')
            );
        }
        return $result;
    }
}
