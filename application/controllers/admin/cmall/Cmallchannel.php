<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Cmallchannel class
 *
 * Copyright (c) CIBoard <www.ciboard.co.kr>
 *
 * @author CIBoard (develop@ciboard.co.kr)
 */

/**
 * 관리자>컨텐츠몰관리>분류관리 controller 입니다.
 */
class Cmallchannel extends CB_Controller
{

    /**
     * 관리자 페이지 상의 현재 디렉토리입니다
     * 페이지 이동시 필요한 정보입니다
     */
    public $pagedir = 'cmall/cmallchannel';

    /**
     * 모델을 로딩합니다
     */
    protected $models = array('Cmall_channel');

    /**
     * 이 컨트롤러의 메인 모델 이름입니다
     */
    protected $modelname = 'Cmall_channel_model';

    /**
     * 헬퍼를 로딩합니다
     */
    protected $helpers = array('form', 'array', 'cmall');

    function __construct()
    {
        parent::__construct();

        /**
         * 라이브러리를 로딩합니다
         */
        $this->load->library(array('querystring', 'cmalllib'));
    }

    /**
     * 분류관리를 가져오는 메소드입니다
     */
    public function index()
    {
        // 이벤트 라이브러리를 로딩합니다
        $eventname = 'event_admin_cmall_cmallchannel_index';
        $this->load->event($eventname);

        $view = array();
        $view['view'] = array();

        // 이벤트가 존재하면 실행합니다
        $view['view']['event']['before'] = Events::trigger('before', $eventname);

        $primary_key = $this->{$this->modelname}->primary_key;

        /**
         * Validation 라이브러리를 가져옵니다
         */
        $this->load->library('form_validation');

        /**
         * 전송된 데이터의 유효성을 체크합니다
         */
        if ($this->input->post('type') === 'add') {
            $config = array(
                array(
                    'field' => 'cch_parent',
                    'label' => '상위채널',
                    'rules' => 'trim',
                ),
                array(
                    'field' => 'cch_value',
                    'label' => '채널명',
                    'rules' => 'trim|required',
                ),
                array(
                    'field' => 'cch_order',
                    'label' => '정렬순서',
                    'rules' => 'trim|numeric|is_natural',
                ),
            );
        } else {
            $config = array(
                array(
                    'field' => 'cch_id',
                    'label' => '채널아이디',
                    'rules' => 'trim|required',
                ),
                array(
                    'field' => 'cch_value',
                    'label' => '채널명',
                    'rules' => 'trim|required',
                ),
                array(
                    'field' => 'cch_order',
                    'label' => '정렬순서',
                    'rules' => 'trim|numeric|is_natural',
                ),
            );
        }
        $this->form_validation->set_rules($config);


        /**
         * 유효성 검사를 하지 않는 경우, 또는 유효성 검사에 실패한 경우입니다.
         * 즉 글쓰기나 수정 페이지를 보고 있는 경우입니다
         */
        if ($this->form_validation->run() === false) {

            // 이벤트가 존재하면 실행합니다
            $view['view']['event']['formrunfalse'] = Events::trigger('formrunfalse', $eventname);

        } else {
            /**
             * 유효성 검사를 통과한 경우입니다.
             * 즉 데이터의 insert 나 update 의 process 처리가 필요한 상황입니다
             */

            // 이벤트가 존재하면 실행합니다
            $view['view']['event']['formruntrue'] = Events::trigger('formruntrue', $eventname);

            if ($this->input->post('type') === 'add') {

                $cch_order = $this->input->post('cch_order') ? $this->input->post('cch_order') : 0;
                $insertdata = array(
                    'cch_value' => $this->input->post('cch_value', null, ''),
                    'cch_parent' => $this->input->post('cch_parent', null, 0),
                    'cch_order' => $cch_order,
                );
                $this->Cmall_channel_model->insert($insertdata);
                $this->cache->delete('cmall-channel-all');
                $this->cache->delete('cmall-channel-detail');

                $view['view']['alert_message'] = '채널 설정이 저장되었습니다';

                redirect(admin_url($this->pagedir), 'refresh');

            }
            if ($this->input->post('type') === 'modify') {

                $cch_order = $this->input->post('cch_order') ? $this->input->post('cch_order') : 0;
                $updatedata = array(
                    'cch_value' => $this->input->post('cch_value', null, ''),
                    'cch_order' => $cch_order,
                );
                $this->Cmall_channel_model->update($this->input->post('cch_id'), $updatedata);
                $this->cache->delete('cmall-channel-all');
                $this->cache->delete('cmall-channel-detail');

                $view['view']['alert_message'] = '채널 정보가 수정되었습니다';

                redirect(admin_url($this->pagedir), 'refresh');

            }
        }

        $getdata = $this->Cmall_channel_model->get_all_channel();
        $view['view']['data'] = $getdata;

        /**
         * primary key 정보를 저장합니다
         */
        $view['view']['primary_key'] = $primary_key;

        // 이벤트가 존재하면 실행합니다
        $view['view']['event']['before_layout'] = Events::trigger('before_layout', $eventname);

        /**
         * 어드민 레이아웃을 정의합니다
         */
        $layoutconfig = array('layout' => 'layout', 'skin' => 'index');
        $view['layout'] = $this->managelayout->admin($layoutconfig, $this->cbconfig->get_device_view_type());
        $this->data = $view;
        $this->layout = element('layout_skin_file', element('layout', $view));
        $this->view = element('view_skin_file', element('layout', $view));
    }

    /**
     * 채널 삭제
     */
    public function delete($cch_id = 0)
    {
        // 이벤트 라이브러리를 로딩합니다
        $eventname = 'event_admin_cmall_cmallchannel_delete';
        $this->load->event($eventname);

        // 이벤트가 존재하면 실행합니다
        Events::trigger('before', $eventname);

        /**
         * 프라이머리키에 숫자형이 입력되지 않으면 에러처리합니다
         */
        $cch_id = (int) $cch_id;
        if (empty($cch_id) OR $cch_id < 1) {
            show_404();
        }

        $this->Cmall_channel_model->delete($cch_id);
        $this->cache->delete('cmall-channel-all');
        $this->cache->delete('cmall-channel-detail');

        // 이벤트가 존재하면 실행합니다
        Events::trigger('after', $eventname);

        /**
         * 삭제가 끝난 후 목록페이지로 이동합니다
         */
        $this->session->set_flashdata(
            'message',
            '정상적으로 삭제되었습니다'
        );
        $param =& $this->querystring;
        $redirecturl = admin_url($this->pagedir . '?' . $param->output());

        redirect($redirecturl);
    }

    /**
     * 채널 가져오기
     */
    public function import()
    {
        // 이벤트 라이브러리를 로딩합니다
        $eventname = 'event_admin_cmall_cmallchannel_import';
        $this->load->event($eventname);

        // 이벤트가 존재하면 실행합니다
        Events::trigger('before', $eventname);

        /**
         * 프라이머리키에 숫자형이 입력되지 않으면 에러처리합니다
         */
        

        $url = 'http://open.api.tingle.kr/channel?X-OAPI-KEY=cd5a1d6d54bb0376fac5be8cf90090c5';
        $data = array();

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 0);      
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        curl_close($ch);

        $obj = json_decode($result,true);
        
        
        
        foreach($obj as $value){
        
            $cch_num_cnt=$this->Cmall_channel_model->count_by(array('cch_num'=> element('channelID',$value)));
            if($cch_num_cnt) continue;

            
                
                $insertdata = array(
                    'cch_value' => element('channelName',$value),
                    'cch_parent' => 0,
                    'cch_order' => 0,
                    'cch_num' => element('channelID',$value),
                );
                $this->Cmall_channel_model->insert($insertdata);

            


        }

        // 이벤트가 존재하면 실행합니다
        Events::trigger('after', $eventname);

        /**
         * 삭제가 끝난 후 목록페이지로 이동합니다
         */
        $this->session->set_flashdata(
            'message',
            '정상적으로 가져오기가되었습니다'
        );
        $param =& $this->querystring;
        $redirecturl = admin_url($this->pagedir . '?' . $param->output());

        redirect($redirecturl);
    }
}
