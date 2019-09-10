<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Bannerclick class
 *
 * Copyright (c) CIBoard <www.ciboard.co.kr>
 *
 * @author CIBoard (develop@ciboard.co.kr)
 */

/**
 * 관리자>페이지설정>배너 클릭 controller 입니다.
 */
class Cron extends CB_Controller {

 

    protected $models = array('Media_view_stat','Media_view_log','Media_click_stat','Media_click_log');   

    function __construct()
    {
        parent::__construct();

        /**
         * 라이브러리를 로딩합니다
         */
        

        /**
         * 로그인이 필요한 페이지입니다
         */
        
    }

    


    public function media_migration(){

        
        //이벤트 라이브러리를 로딩합니다

        $result = $this->Media_click_stat_model
            ->migration();
        
        
         foreach($result as $value){
            
             $this->Media_click_stat_model->replace($value);
         }

         $result = $this->Media_view_stat_model
            ->migration();
        
        
         foreach($result as $value){
           
             $this->Media_view_stat_model->replace($value);
         }

        $criterion = cdate('Y-m-d 00:00:00', strtotime('-3 day'));

        $deletewhere = array(
                    'mlc_datetime <=' => $criterion,
                );

        $result = $this->Media_view_log_model->delete_where($deletewhere);

        $deletewhere = array(
                    'mfd_datetime <=' => $criterion,
                );

        $result = $this->Media_click_log_model->delete_where($deletewhere);

        echo $result;
    }

    

    public function import(){

        $eventname = 'event_admin_cmall_cmallitem_import';
        $this->load->event($eventname);

        $this->load->model(array('Cmall_item_model'));

        // 이벤트가 존재하면 실행합니다
        Events::trigger('before', $eventname);
        $date = new DateTime('-3 day');
        
        $url = 'http://open.api.tingle.kr/item/list?mode=createddate&date='.$date->format('Ymd').'&X-OAPI-KEY=cd5a1d6d54bb0376fac5be8cf90090c5';
        
        $data = array();

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 0);      
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        curl_close($ch);

        $obj = json_decode($result,true);


        foreach($obj as $value){


            $ppl_result = $this->Cmall_item_model->get_one('','',array('cit_key' => element('pplID',$value)));
            if($ppl_result)             
                $url = 'http://nate.newspopcon.com/postact/import_write/'.element('cit_id',$ppl_result);
            else 
                $url = 'http://nate.newspopcon.com/postact/import_write';

            

            
            $data = array(
                    'cit_key' => element('pplID',$value),
                    'cit_price' => element('price',$value),
                    'cit_name' => element('productItem',$value),
                    'cit_summary' => element('productNickname',$value),
                    'programName' => element('programName',$value),
                    'actorName' => element('actorName',$value),
                    'roleName' => element('roleName',$value),
                    'productBrand' => element('productBrand',$value),
                    'productModel' => element('productModel',$value),
                    'productNickname' => element('productNickname',$value),
                    'minPrice' => element('minPrice',$value),
                    'maxPrice' => element('maxPrice',$value),
                    'pplCategory' => element('pplCategory',$value),
                    'orgChannelID' => element('orgChannelID',$value),               
                    'cit_status' => 1,
                    'cit_shopping_url' => element('shopping_url',$value),               
                    );

            


            if (!empty(element(0,element('goods_img',$value))) && $fileinfo = getimagesize(element(0,element('goods_img',$value)))) {
                $img_src_array= explode('/', element(0,element('goods_img',$value)));
                $imageName = config_item('uploads_dir').'/cmallitem/'.end($img_src_array);

                # 이미지 다운로드
                $imageFile = $this->extract_html(element(0,element('goods_img',$value)));

                # 파일 생성 후 저장
                $filetemp = fopen($imageName, 'w');
                fwrite($filetemp, $imageFile['content']);
                fclose($filetemp); // Closing file handle
                
                if ($filetemp) {
                    $data['cit_file_1'] = curl_file_create($imageName, $fileinfo['2'], $imageName);
                    if(element('cit_file_1',$ppl_result))
                        $data['cit_file_1_del'] = true;

                    
                }
            }

            if (!empty(element(0,element('img',$value))) && $fileinfo = getimagesize(element(0,element('img',$value)))) {
                $img_src_array= explode('/', element(0,element('img',$value)));
                $imageName = config_item('uploads_dir').'/cmallitem/'.end($img_src_array);

                # 이미지 다운로드
                $imageFile = $this->extract_html(element(0,element('img',$value)));

                # 파일 생성 후 저장
                $filetemp = fopen($imageName, 'w');
                fwrite($filetemp, $imageFile['content']);
                fclose($filetemp); // Closing file handle

                if ($filetemp) {
                    $data['cit_file_2'] = curl_file_create($imageName, $fileinfo['2'], $imageName);
                    if(element('cit_file_2',$ppl_result))
                        $data['cit_file_2_del'] = true;
                    
                }

            }


            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, sizeof($data));
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $result = curl_exec($ch);
            curl_close($ch);
            echo $result;
            echo "<br>";
            
            break;
        }


        
    }




    function extract_html($url, $proxy='', $proxy_userpwd='') {


        $response = array();
        $response['code']='';
        $response['message']='';
        $response['status']=false;  
        
        $agent = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.1) Gecko/20061204 Firefox/2.0.0.1';
        
        // Some websites require referrer
        $host = parse_url($url, PHP_URL_HOST);
        $scheme = parse_url($url, PHP_URL_SCHEME);
        $referrer = $scheme . '://' . $host; 
        
        $curl = curl_init();
        
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt ($curl, CURLOPT_POST, 0);
        curl_setopt($curl, CURLOPT_URL, $url);
        // curl_setopt($curl, CURLOPT_PROXY, $proxy);
        // curl_setopt($curl, CURLOPT_PROXYUSERPWD, $proxy_userpwd);
        curl_setopt($curl, CURLOPT_USERAGENT, $agent);
        curl_setopt($curl, CURLOPT_REFERER, $referrer);
        
        // if ( !file_exists(COOKIE_FILENAME) || !is_writable(COOKIE_FILENAME) ) {
        //     $response['status']=false;
        //     $response['message']='Cookie file is missing or not writable.';
        //     return $response;
        // }
        
        // curl_setopt($curl, CURLOPT_COOKIESESSION, 0);
        // curl_setopt($curl, CURLOPT_COOKIEFILE, COOKIE_FILENAME);
        // curl_setopt($curl, CURLOPT_COOKIEJAR, COOKIE_FILENAME);
        
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);
        
        // allow to crawl https webpages
        curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);
        curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
        
        // the download speed must be at least 1 byte per second
        curl_setopt($curl,CURLOPT_LOW_SPEED_LIMIT, 1);
        
        // if the download speed is below 1 byte per second for more than 30 seconds curl will give up
        curl_setopt($curl,CURLOPT_LOW_SPEED_TIME, 30);
        
        $content = curl_exec($curl);
        
        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        
        $response['code'] = $code;
        
        if ($content === false) {
            $response['status'] = false;
            $response['content'] = curl_error($curl);
        }
        else{
            $response['status'] = true;
            $response['content'] = $content;
        }
        
        curl_close($curl);
        
        return $response;
        
    }
}
