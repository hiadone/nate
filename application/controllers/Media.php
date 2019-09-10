<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Main class
 *
 * Copyright (c) CIBoard <www.ciboard.co.kr>
 *
 * @author CIBoard (develop@ciboard.co.kr)
 */

/**
 * 메인 페이지를 담당하는 controller 입니다.
 */
class Media extends CB_Controller
{

    /**
     * 모델을 로딩합니다
     */
    protected $models = array('Post', 'Post_extra_vars');

    
    
    /**
     * 헬퍼를 로딩합니다
     */
    protected $helpers = array('form', 'array', 'number');

    function __construct()
    {
        parent::__construct();

        /**
         * 라이브러리를 로딩합니다
         */
        $this->load->library(array('querystring', 'board_group'));
    }


    /**
     * media 정보
     */
    public function index($brd_key = 0,$post_id = 0)
    {
        
        if (empty($post_id) || empty($brd_key)) {
            show_404();
        }

       
        //$this->db->cache_on();
        $where = array(
            'post_id' => $post_id,
            'brd_id' => $this->board->item_key('brd_id', $brd_key),
        );

        $post = $this->Post_model->get_one('','',$where);
        

        

        if (element('post_del', $post) > 1) {
            show_404();
        }

        $post['extravars'] = $this->Post_extra_vars_model->get_all_meta(element('post_id', $post));
        
        if(element('campaign_status',element('extravars',$post)) ==='disable') show_404();
        
        
        
        $view['view']['post'] = $post;
        
        
        $view['view']['link'] = $link = array();

        if (element('post_link_count', $post)) {
            $this->load->model('Post_link_model');
            $this->load->model('Cmall_item_model');

            
            $linkwhere = array(
                'post_id' => element('post_id', $post),
            );
            $view['view']['link'] = $link = $this->Post_link_model
                ->get('', '', $linkwhere, '', '', 'pln_id', 'ASC');
            if ($link && is_array($link)) {
                foreach ($link as $key => $value) {

                    $itemwhere = array(
                        'cit_shopping_url' => element('pln_url', $value),
                    );
                    $view['view']['link'][$key]['item']  = $item = $this->Cmall_item_model
                        ->get_one('', '', $itemwhere);

                    if ($item['cit_file_1']) {
                        $view['view']['link'][$key]['item']['cit_file_1'] = site_url(config_item('uploads_dir') . '/cmallitem/' . element('cit_file_1', $item));
                        $view['view']['link'][$key]['item']['thumb_image_url'] = thumb_url('cmailcmallitemtem', element('cit_file_1', $item));                        
                    }
                    $view['view']['link'][$key]['media_click'] = site_url('postact/media_click/' . element('pln_id', $value));
                }
            }
        }


        $view['view']['link_count'] = $link_count = count($link);

        
        //$this->db->cache_off();
        $userAgent = $this->agent->agent_string() ? $this->agent->agent_string() : '';        
        $view['view']['userAgent']=get_useragent_info($userAgent);


        $layoutconfig = array(
            'layout' => 'blank',
            'skin' => 'index',
            'layout_dir' => 'bootstrap',
            'skin_dir' => 'media/'.$brd_key.'/'.$post_id,
            'mobile_skin_dir' => 'media/'.$brd_key.'/'.$post_id,
            'mobile_layout_dir' => 'bootstrap',
            
        );


        $view['layout'] = $this->managelayout->front($layoutconfig, $this->cbconfig->get_device_view_type());
        $this->data = $view;
        
        $this->layout = element('layout_skin_file', element('layout', $view));
        $this->view = element('view_skin_file', element('layout', $view));
        
    }



    // public function iframe($brd_key = 0,$post_id = 0)
    // {
        
    //     if (empty($post_id) || empty($brd_key)) {
    //         show_404();
    //     }
        
       
    //     //$this->db->cache_on();
    //     $where = array(
    //         'post_id' => $post_id,
    //         'brd_id' => $this->board->item_key('brd_id', $brd_key),
    //     );

    //     $post = $this->Post_model->get_one('','',$where);
        

        

    //     if (element('post_del', $post) > 1) {
    //         show_404();
    //     }

    //     $post['extravars'] = $this->Post_extra_vars_model->get_all_meta(element('post_id', $post));
        
    //     if(element('campaign_status',element('extravars',$post)) ==='disable') show_404();
        
        
        
    //     $view['view']['post'] = $post;
        
        
    //     $view['view']['link'] = $link = array();

    //     if (element('post_link_count', $post)) {
    //         $this->load->model('Post_link_model');
    //         $this->load->model('Cmall_item_model');

            
    //         $linkwhere = array(
    //             'post_id' => element('post_id', $post),
    //         );
    //         $view['view']['link'] = $link = $this->Post_link_model
    //             ->get('', '', $linkwhere, '', '', 'pln_id', 'ASC');
    //         if ($link && is_array($link)) {
    //             foreach ($link as $key => $value) {

    //                 $itemwhere = array(
    //                     'cit_shopping_url' => element('pln_url', $value),
    //                 );
    //                 $view['view']['link'][$key]['item']  = $item = $this->Cmall_item_model
    //                     ->get_one('', '', $itemwhere);

    //                 if ($item['cit_file_1']) {
    //                     $view['view']['link'][$key]['item']['cit_file_1'] = site_url(config_item('uploads_dir') . '/cmallitem/' . element('cit_file_1', $item));
    //                     $view['view']['link'][$key]['item']['aws_image_url'] = "https://hiadone.s3.ap-northeast-2.amazonaws.com/".config_item('uploads_dir') . '/cmallitem/' . element('cit_file_1', $item);                                                
    //                 }
    //                 $view['view']['link'][$key]['media_click'] = site_url('postact/media_click/' . element('pln_id', $value));
    //             }
    //         }
    //     }


    //     $view['view']['link_count'] = $link_count = count($link);

        
    //     //$this->db->cache_off();
    //     $userAgent = $this->agent->agent_string() ? $this->agent->agent_string() : '';        
    //     $view['view']['userAgent']=get_useragent_info($userAgent);


    //     $layoutconfig = array(
    //         'layout' => 'blank',
    //         'skin' => 'iframe',
    //         'layout_dir' => 'bootstrap',
    //         'skin_dir' => 'media/'.$brd_key.'/'.$post_id,
    //         'mobile_skin_dir' => 'media/'.$brd_key.'/'.$post_id,
    //         'mobile_layout_dir' => 'bootstrap',
            
    //     );


    //     $view['layout'] = $this->managelayout->front($layoutconfig, $this->cbconfig->get_device_view_type());
    //     $this->data = $view;
        
    //     $this->layout = element('layout_skin_file', element('layout', $view));
    //     $this->view = element('view_skin_file', element('layout', $view));
        
    // }

    // public function script($brd_key = 0,$post_id = 0)
    // {
        
    //     if (empty($post_id) || empty($brd_key)) {
    //         show_404();
    //     }

    //     $this->output->set_content_type('text/javascript');
    //     //$this->db->cache_on();
    //     $where = array(
    //         'post_id' => $post_id,
    //         'brd_id' => $this->board->item_key('brd_id', $brd_key),
    //     );

    //     $post = $this->Post_model->get_one('','',$where);
        

        

    //     if (element('post_del', $post) > 1) {
    //         show_404();
    //     }

    //     $post['extravars'] = $this->Post_extra_vars_model->get_all_meta(element('post_id', $post));
        
    //     if(element('campaign_status',element('extravars',$post)) ==='disable') show_404();
        
        
        
    //     $view['view']['post'] = $post;
        
        
    //     $view['view']['link'] = $link = array();

    //     if (element('post_link_count', $post)) {
    //         $this->load->model('Post_link_model');
    //         $this->load->model('Cmall_item_model');

            
    //         $linkwhere = array(
    //             'post_id' => element('post_id', $post),
    //         );
    //         $view['view']['link'] = $link = $this->Post_link_model
    //             ->get('', '', $linkwhere, '', '', 'pln_id', 'ASC');
    //         if ($link && is_array($link)) {
    //             foreach ($link as $key => $value) {

    //                 $itemwhere = array(
    //                     'cit_shopping_url' => element('pln_url', $value),
    //                 );
    //                 $view['view']['link'][$key]['item']  = $item = $this->Cmall_item_model
    //                     ->get_one('', '', $itemwhere);

    //                 if ($item['cit_file_1']) {
    //                     $view['view']['link'][$key]['item']['cit_file_1'] = site_url(config_item('uploads_dir') . '/cmallitem/' . element('cit_file_1', $item));
    //                     $view['view']['link'][$key]['item']['aws_image_url'] = "https://hiadone.s3.ap-northeast-2.amazonaws.com/".config_item('uploads_dir') . '/cmallitem/' . element('cit_file_1', $item);                        
    //                 }
    //                 $view['view']['link'][$key]['media_click'] = site_url('postact/media_click/' . element('pln_id', $value));
    //             }
    //         }
    //     }


    //     $view['view']['link_count'] = $link_count = count($link);

        
    //     //$this->db->cache_off();
    //     $userAgent = $this->agent->agent_string() ? $this->agent->agent_string() : '';        
    //     $view['view']['userAgent']=get_useragent_info($userAgent);


    //     $layoutconfig = array(
    //         'layout' => 'script',
    //         'skin' => 'script',
    //         'layout_dir' => 'bootstrap',
    //         'skin_dir' => 'media/'.$brd_key.'/'.$post_id,
    //         'mobile_skin_dir' => 'media/'.$brd_key.'/'.$post_id,
    //         'mobile_layout_dir' => 'bootstrap',
            
    //     );


    //     $view['layout'] = $this->managelayout->front($layoutconfig, $this->cbconfig->get_device_view_type());
    //     $this->data = $view;
        
    //     $this->layout = element('layout_skin_file', element('layout', $view));
    //     $this->view = element('view_skin_file', element('layout', $view));
        
    // }
}
