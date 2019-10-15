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
class Stat extends CB_Controller
{
    

    /**
     * 페이지 이동시 필요한 정보입니다
     */

    public $pagedir ='stat';
    public $name_key = '';
    public $id_key = '';
    public $param='';

    /**
     * 모델을 로딩합니다
     */
    protected $models = array('Media_view_stat','Media_view_log','Media_click_stat','Media_click_log','Board', 'Post','Post_link','Post_meta','Post_extra_vars','Member_group_member','Member_group');   
    

    /**
     * 헬퍼를 로딩합니다
     */
    protected $helpers = array('form', 'array');

    function __construct()

    {
        parent::__construct();

        /**
         * 라이브러리를 로딩합니다
         */
        $this->load->library(array('pagination', 'querystring','accesslevel'));

        /**
         * 로그인이 필요한 페이지입니다
         */
        $this->param =& $this->querystring;
        
    }

    /**
     * 목록을 가져오는 메소드입니다
     */
    public function lists($brd_key='b-a-1')
    {

        
        // 이벤트 라이브러리를 로딩합니다
        $eventname = 'event_stat_media_index';
        $this->load->event($eventname);

        $view = array();
        $view['view'] = array();

        // 이벤트가 존재하면 실행합니다
        $view['view']['event']['before'] = Events::trigger('before', $eventname);
        
        /**
         * 페이지에 숫자가 아닌 문자가 입력되거나 1보다 작은 숫자가 입력되면 에러 페이지를 보여줍니다.
         */

        $view['view']['list'] = $list = $this->_get_list($brd_key);
        $view['view']['board_key'] = element('brd_key', element('board', $list));
        $view['view']['listall_url'] = site_url($this->pagedir.'/lists/'.$brd_key);
        

        // stat_count_board ++
        

        $view['view']['is_admin'] = $is_admin = $this->member->is_admin(
            array(
                'board_id' => element('brd_id', element('board', $list)),
                'group_id' => element('bgr_id', element('board', $list)),
            )
        );

        // 이벤트가 존재하면 실행합니다
        $view['view']['event']['before_layout'] = Events::trigger('before_layout', $eventname);

        

        $page_title = $this->cbconfig->item('site_meta_title_main');
        $meta_description = $this->cbconfig->item('site_meta_description_main');
        $meta_keywords = $this->cbconfig->item('site_meta_keywords_main');
        $meta_author = $this->cbconfig->item('site_meta_author_main');
        $page_name = $this->cbconfig->item('site_page_name_main');

        $layoutconfig = array(
            'path' => 'stat',
            'layout' => 'layout',
            'skin' => 'lists',
            'layout_dir' => $this->cbconfig->item('layout_main'),
            'mobile_layout_dir' => $this->cbconfig->item('mobile_layout_main'),
            'use_sidebar' => $this->cbconfig->item('sidebar_main'),
            'use_mobile_sidebar' => $this->cbconfig->item('mobile_sidebar_main'),
            'skin_dir' => 'bootstrap',
            'mobile_skin_dir' => 'bootstrap',
            'page_title' => $page_title,
            'meta_description' => $meta_description,
            'meta_keywords' => $meta_keywords,
            'meta_author' => $meta_author,
            'page_name' => $page_name,
            'page_url' => 'stat/lists',
        );

        $view['layout'] = $this->managelayout->front($layoutconfig, $this->cbconfig->get_device_view_type());
        $this->data = $view;
        $this->layout = element('layout_skin_file', element('layout', $view));
        $this->view = element('view_skin_file', element('layout', $view));
    }

    
    public function view_log($brd_key,$export = '')
    {
        
        // 이벤트 라이브러리를 로딩합니다
        $eventname = 'event_stat_media_index';
        $this->load->event($eventname);

        if (empty($brd_key)) {
            show_404();
        }
        $mem_id = (int) $this->member->item('mem_id');
        

        $view = array();
        $view['view'] = array();

        // 이벤트가 존재하면 실행합니다
        $view['view']['event']['before'] = Events::trigger('before', $eventname);

        $view['view']['is_admin'] = $is_admin = $this->member->is_admin();


        /**
         * 페이지에 숫자가 아닌 문자가 입력되거나 1보다 작은 숫자가 입력되면 에러 페이지를 보여줍니다.
         */
        
        $page = (((int) $this->input->get('page')) > 0) ? ((int) $this->input->get('page')) : 1;
        $findex = $this->input->get('findex') ? $this->input->get('findex') : $this->Media_view_log_model->primary_key;
        $forder = $this->input->get('forder', null, 'desc');
        $sfield = $this->input->get('sfield', null, '');
        $skeyword = $this->input->get('skeyword', null, '');
        
        $per_page=admin_listnum();
        $offset = ($page - 1) * $per_page;

        if($export === 'excel'){
            $per_page='';
            $offset='';
        }
        /**
         * 게시판 목록에 필요한 정보를 가져옵니다.
         */
        $this->Media_view_log_model->allow_search_field = array('post.post_title', 'post.post_id'); // 검색이 가능한 필드
        $this->Media_view_log_model->search_field_equal = array('post.post_id'); // 검색중 like 가 아닌 = 검색을 하는 필드
        $this->Media_view_log_model->allow_order_field = array('mvl_id'); // 정렬이 가능한 필드

        $where = array();

        

        $where_in=array();
        if ($brdid = (int) $this->input->get('brd_id')) {
            $where['post.brd_id'] = $brdid;
        } else $where['post.brd_id'] =  $this->board->item_key('brd_id', $brd_key);

        if($this->input->get('post_id')) $where['post_id'] = $this->input->get('post_id');

        if ($is_admin === false) {
            
            $where['post.mem_id'] = $mem_id;
        }
        
        


        if(!empty($this->input->get('post_id_', null, 0))) {
            $where_in['post_id'] = $this->input->get('post_id_');

            if(!empty($where_in['post_id'][0])){
                $post['extravars'] = $this->Post_extra_vars_model->get_all_meta($where_in['post_id'][0]);

                
                if(!empty($post['extravars']['campaign_multi']))
                $view['view']['campaign_multi'] = $post['extravars']['campaign_multi'];
                
            }
            

        }
        
        
        if(!empty($this->input->get('multi_code',null))) $where['mvl_code'] = $this->input->get('multi_code',null);



        $result = $this->Media_view_log_model->get_list($per_page, $offset, $where, '', $findex, $forder, $sfield, $skeyword,'',$where_in);
        $list_num = $result['total_rows'] - ($page - 1) * $per_page;
        if (element('list', $result)) {
            foreach (element('list', $result) as $key => $val) {
                
                $brd_key = $this->board->item_id('brd_key', element('brd_id', $val));
                $result['list'][$key]['post_url'] = post_url($brd_key, element('post_id', $val));
                $result['list'][$key]['post_link'] = $this->Post_link_model->get_one(element('pln_id', $val));
                $result['list'][$key]['display_datetime'] = display_datetime(
                    element('mvl_datetime', $val)
                );
                $result['list'][$key]['display_ip'] = element('mvl_ip', $val);
                $result['list'][$key]['referrer'] = element('mvl_referrer', $val);

                $result['list'][$key]['member_group_name'] = '';

                $where = array(
                    'mem_id' => element('post_mem_id', $val),
                );
                
                $member_group = $this->Member_group_member_model->get('', '', $where, '', 0, 'mgm_id', 'ASC');
                if ($member_group && is_array($member_group)) {

                    

                    foreach ($member_group as $gkey => $gval) {
                        $item = $this->Member_group_model->item(element('mgr_id', $gval));
                        if ($result['list'][$key]['member_group_name']) {
                            $result['list'][$key]['member_group_name'] .= ', ';
                        }
                        $result['list'][$key]['member_group_name'] .= element('mgr_title', $item);
                    }


                }

                if (element('mvl_useragent', $val)) {
                    $userAgent = get_useragent_info(element('mvl_useragent', $val));
                    $result['list'][$key]['browsername'] = $userAgent['browsername'];
                    $result['list'][$key]['browserversion'] = $userAgent['browserversion'];
                    $result['list'][$key]['os'] = $userAgent['os'];
                    $result['list'][$key]['engine'] = $userAgent['engine'];
                }
                $result['list'][$key]['num'] = $list_num--;
            }
        }
        $result['get_member_group_post_list']= $this->get_member_group_post_list($brd_key);

        $view['view']['data'] = $result;

        // $view['view']['boardlist'] = $this->Board_model->get_board_list();

        /**
         * primary key 정보를 저장합니다
         */
        $view['view']['primary_key'] = $this->Media_view_log_model->primary_key;

        /**
         * 페이지네이션을 생성합니다
         */

        $config['base_url'] = site_url($this->pagedir).'/view_log/'.$brd_key . '?' . $this->param->replace('page');
        $config['total_rows'] = $result['total_rows'];
        $config['per_page'] = $per_page;
        $this->pagination->initialize($config);
        $view['view']['paging'] = $this->pagination->create_links();
        $view['view']['page'] = $page;

        /**
         * 쓰기 주소, 삭제 주소등 필요한 주소를 구합니다
         */
        $search_option = array('post.post_title' => '제목');
        $view['view']['skeyword'] = ($sfield && array_key_exists($sfield, $search_option)) ? $skeyword : '';
        $view['view']['search_option'] = search_option($search_option, $sfield);
        $view['view']['listall_url'] = site_url($this->pagedir.'/view_log/'.$brd_key);
        $view['view']['list_delete_url'] = site_url($this->pagedir . '/listdelete/?' . $this->param->output());

        // 이벤트가 존재하면 실행합니다
        $view['view']['event']['before_layout'] = Events::trigger('before_layout', $eventname);

        /**
         * 레이아웃을 정의합니다
         */
        if ($export === 'excel') {
            
            header('Content-type: application/vnd.ms-excel');
            header('Content-Disposition: attachment; filename=mobusi_' . cdate('Y_m_d') . '.xls');
            echo $this->load->view('/stat/bootstrap_'.$brd_key.'/reallist_excel', $view, true);

        } else {
            $page_title = $this->cbconfig->item('site_meta_title_main');
            $meta_description = $this->cbconfig->item('site_meta_description_main');
            $meta_keywords = $this->cbconfig->item('site_meta_keywords_main');
            $meta_author = $this->cbconfig->item('site_meta_author_main');
            $page_name = $this->cbconfig->item('site_page_name_main');

            $layoutconfig = array(
                'path' => 'stat',
                'layout' => 'layout',
                'skin' => 'reallist',
                'layout_dir' => $this->cbconfig->item('layout_main'),
                'mobile_layout_dir' => $this->cbconfig->item('mobile_layout_main'),
                'use_sidebar' => $this->cbconfig->item('sidebar_main'),
                'use_mobile_sidebar' => $this->cbconfig->item('mobile_sidebar_main'),
                'skin_dir' => 'bootstrap',
                'mobile_skin_dir' => 'bootstrap',
                'page_title' => $page_title,
                'meta_description' => $meta_description,
                'meta_keywords' => $meta_keywords,
                'meta_author' => $meta_author,
                'page_name' => $page_name,
                'page_url' => 'stat/lists',
            );

            $view['layout'] = $this->managelayout->front($layoutconfig, $this->cbconfig->get_device_view_type());
            $this->data = $view;
            $this->layout = element('layout_skin_file', element('layout', $view));
            $this->view = element('view_skin_file', element('layout', $view));
        }
    }

    public function click_log($brd_key,$export = '')
    {
        
        // 이벤트 라이브러리를 로딩합니다
        $eventname = 'event_stat_media_index';
        $this->load->event($eventname);

        if (empty($brd_key)) {
            show_404();
        }
        
        $view = array();
        $view['view'] = array();

        // 이벤트가 존재하면 실행합니다
        $view['view']['event']['before'] = Events::trigger('before', $eventname);

        $view['view']['is_admin'] = $is_admin = $this->member->is_admin();


        /**
         * 페이지에 숫자가 아닌 문자가 입력되거나 1보다 작은 숫자가 입력되면 에러 페이지를 보여줍니다.
         */
        
        $page = (((int) $this->input->get('page')) > 0) ? ((int) $this->input->get('page')) : 1;
        $findex = $this->input->get('findex') ? $this->input->get('findex') : $this->Media_click_log_model->primary_key;
        $forder = $this->input->get('forder', null, 'desc');
        $sfield = $this->input->get('sfield', null, '');
        $skeyword = $this->input->get('skeyword', null, '');

        $per_page=admin_listnum();
        $offset = ($page - 1) * $per_page;

        if($export === 'excel'){
            $per_page='';
            $offset='';
        }

        /**
         * 게시판 목록에 필요한 정보를 가져옵니다.
         */
        $this->Media_click_log_model->allow_search_field = array('post.post_title', 'post.post_id','media_file_download_log.sfd_referrer','cmall_item.cit_name'); // 검색이 가능한 필드
        $this->Media_click_log_model->search_field_equal = array('post.post_id'); // 검색중 like 가 아닌 = 검색을 하는 필드
        $this->Media_click_log_model->allow_order_field = array('mcl_id'); // 정렬이 가능한 필드

        $where = array();
        $where_in=array();


        if ($brdid = (int) $this->input->get('brd_id')) {
            $where['post.brd_id'] = $brdid;
        } else $where['post.brd_id'] =  $this->board->item_key('brd_id', $brd_key);

        $mem_id = (int) $this->member->item('mem_id');

        if ($is_admin === false) {
            $where['post.mem_id'] = $mem_id;
        } 
        
        
        
        if(!empty($this->input->get('post_id_', null, 0))) {
            $where_in['post_id'] = $this->input->get('post_id_');            
            if(!empty($where_in['post_id'][0])){
                $post['extravars'] = $this->Post_extra_vars_model->get_all_meta($where_in['post_id'][0]);

                
                if(!empty($post['extravars']['campaign_multi']))
                $view['view']['campaign_multi'] = $post['extravars']['campaign_multi'];
                
            }
            

        }

        if(!empty($this->input->get('multi_code',null))) $where['mcl_code'] = $this->input->get('multi_code',null);


        $result = $this->Media_click_log_model->get_list($per_page, $offset, $where, '', $findex, $forder, $sfield, $skeyword,'',$where_in);
        $list_num = $result['total_rows'] - ($page - 1) * $per_page;
        if (element('list', $result)) {
            foreach (element('list', $result) as $key => $val) {
                
                
                $result['list'][$key]['post_url'] = post_url($this->board->item_id('brd_key', element('brd_id', $val)), element('post_id', $val));
                $result['list'][$key]['display_datetime'] = display_datetime(
                    element('mcl_datetime', $val)
                );

                $result['list'][$key]['display_ip'] = element('mcl_ip', $val);
                $result['list'][$key]['referrer'] = element('mcl_referrer', $val);
                $result['list'][$key]['member_group_name'] = '';

                $where = array(
                    'mem_id' => element('post_mem_id', $val),
                );
                
                $member_group = $this->Member_group_member_model->get('', '', $where, '', 0, 'mgm_id', 'ASC');
                if ($member_group && is_array($member_group)) {

                    

                    foreach ($member_group as $gkey => $gval) {
                        $item = $this->Member_group_model->item(element('mgr_id', $gval));
                        if ($result['list'][$key]['member_group_name']) {
                            $result['list'][$key]['member_group_name'] .= ', ';
                        }
                        $result['list'][$key]['member_group_name'] .= element('mgr_title', $item);
                    }


                }

                if (element('mcl_useragent', $val)) {
                    $userAgent = get_useragent_info(element('mcl_useragent', $val));
                    $result['list'][$key]['browsername'] = $userAgent['browsername'];
                    $result['list'][$key]['browserversion'] = $userAgent['browserversion'];
                    $result['list'][$key]['os'] = $userAgent['os'];
                    $result['list'][$key]['engine'] = $userAgent['engine'];
                }
                $result['list'][$key]['num'] = $list_num--;
            }
        }
        $result['get_member_group_post_list']= $this->get_member_group_post_list($brd_key);

        $view['view']['data'] = $result;

        // $view['view']['boardlist'] = $this->Board_model->get_board_list();

        /**
         * primary key 정보를 저장합니다
         */
        $view['view']['primary_key'] = $this->Media_click_log_model->primary_key;

        /**
         * 페이지네이션을 생성합니다
         */

        $config['base_url'] = site_url($this->pagedir).'/click_log/'.$brd_key . '?' . $this->param->replace('page');
        $config['total_rows'] = $result['total_rows'];
        $config['per_page'] = $per_page;
        $this->pagination->initialize($config);
        $view['view']['paging'] = $this->pagination->create_links();
        $view['view']['page'] = $page;

        /**
         * 쓰기 주소, 삭제 주소등 필요한 주소를 구합니다
         */
        $search_option = array('post.post_title' => '제목' ,'cmall_item.cit_name' => '상품명');
        $view['view']['skeyword'] = ($sfield && array_key_exists($sfield, $search_option)) ? $skeyword : '';
        $view['view']['search_option'] = search_option($search_option, $sfield);
        $view['view']['listall_url'] = site_url($this->pagedir.'/click_log/'.$brd_key);
        $view['view']['list_delete_url'] = site_url($this->pagedir . '/listdelete/?' . $this->param->output());

        // 이벤트가 존재하면 실행합니다
        $view['view']['event']['before_layout'] = Events::trigger('before_layout', $eventname);

        /**
         * 레이아웃을 정의합니다
         */
        if ($export === 'excel') {
            
            header('Content-type: application/vnd.ms-excel');
            header('Content-Disposition: attachment; filename=mobusi_' . cdate('Y_m_d') . '.xls');
            echo $this->load->view('/stat/bootstrap_'.$brd_key.'/reallist_excel', $view, true);

        } else {
            $page_title = $this->cbconfig->item('site_meta_title_main');
            $meta_description = $this->cbconfig->item('site_meta_description_main');
            $meta_keywords = $this->cbconfig->item('site_meta_keywords_main');
            $meta_author = $this->cbconfig->item('site_meta_author_main');
            $page_name = $this->cbconfig->item('site_page_name_main');

            $layoutconfig = array(
                'path' => 'stat',
                'layout' => 'layout',
                'skin' => 'reallist',
                'layout_dir' => $this->cbconfig->item('layout_main'),
                'mobile_layout_dir' => $this->cbconfig->item('mobile_layout_main'),
                'use_sidebar' => $this->cbconfig->item('sidebar_main'),
                'use_mobile_sidebar' => $this->cbconfig->item('mobile_sidebar_main'),
                'skin_dir' => 'bootstrap',
                'mobile_skin_dir' => 'bootstrap',
                'page_title' => $page_title,
                'meta_description' => $meta_description,
                'meta_keywords' => $meta_keywords,
                'meta_author' => $meta_author,
                'page_name' => $page_name,
                'page_url' => 'stat/lists',
            );

            $view['layout'] = $this->managelayout->front($layoutconfig, $this->cbconfig->get_device_view_type());
            $this->data = $view;
            $this->layout = element('layout_skin_file', element('layout', $view));
            $this->view = element('view_skin_file', element('layout', $view));
        }
    }
    

    public function graph($brd_key,$export = '')
    {
     
        // 이벤트 라이브러리를 로딩합니다
        $eventname = 'event_stat_media_graph';
        $this->load->event($eventname);

        
        if (empty($brd_key)) {
            show_404();
        }

        
        $view = array();
        $view['view'] = array();

        // 이벤트가 존재하면 실행합니다
        $view['view']['event']['before'] = Events::trigger('before', $eventname);
        $view['view']['is_admin'] = $is_admin = $this->member->is_admin();
        
        $datetype = empty($this->input->get('datetype')) ?  'd' : $this->input->get('datetype');
        
        $start_date = $this->input->get('start_date') ? $this->input->get('start_date') : cdate('Y-m-d', strtotime('-1 months'));;
        $end_date = $this->input->get('end_date') ? $this->input->get('end_date') : cdate('Y-m-d');
        if ($datetype === 'y' OR $datetype === 'm') {
            $start_year = substr($start_date, 0, 4);
            $end_year = substr($end_date, 0, 4);
        }
        if ($datetype === 'm') {
            $start_month = substr($start_date, 5, 2);
            $end_month = substr($end_date, 5, 2);
            $start_year_month = $start_year * 12 + $start_month;
            $end_year_month = $end_year * 12 + $end_month;
        }

        $view['view']['start_date'] = $start_date;
        $view['view']['end_date'] = $end_date;
        $view['view']['datetype'] = $datetype;

        if ($datetype === 'h' && $this->input->get('datetime')) {
            $start_date = $this->input->get('datetime') ? $this->input->get('datetime') : cdate('Y-m-d');
            $end_date = cdate('Y-m-d', strtotime($start_date));
        }
       
        $orderby = (strtolower($this->input->get('orderby')) === 'desc') ? 'desc' : 'asc';
        if($datetype==='domain') $orderby='desc';

        $brd_id = $this->input->get('brd_id', null, $this->board->item_key('brd_id', $brd_key));
        
        
        $this->Media_view_stat_model->allow_search_field = array('mcs_id', 'post.post_title'); // 검색이 가능한 필드
        $this->Media_view_stat_model->search_field_equal = array('mcs_id', 'post.post_title'); // 검색중 like 가 

         if(!empty($this->input->get('post_id_', null, 0))) {
            $where_in['post_id'] = $this->input->get('post_id_');            
            if(!empty($where_in['post_id'][0])){
                $post['extravars'] = $this->Post_extra_vars_model->get_all_meta($where_in['post_id'][0]);

                
                if(!empty($post['extravars']['campaign_multi']))
                $view['view']['campaign_multi'] = $post['extravars']['campaign_multi'];
                
            }
            

        }
        if(!empty($this->input->get('post_id_', null, 0))) {
            $this->load->model('Post_link_model');            
            $linkwhere = array(
                'post_id' => element(0,$this->input->get('post_id_'))
            );
            $view['view']['link'] = $this->Post_link_model
                ->get_list('', '', $linkwhere, '',  'pln_id', 'ASC');

        }
        

        $where=array();
        $skey=array();
        if ($is_admin === false) {
            $mem_id = (int) $this->member->item('mem_id');
            $where['post.mem_id'] = $mem_id;

            
            $where['post.brd_id'] = $this->board->item_key('brd_id', $brd_key);  
            
            $result = $this->Post_model
            ->get_post_list('', '', $where);

            if (element('list', $result)) {
                foreach (element('list', $result) as $key => $val) {
                    $skey[] = element('post_id',$val);
                }
            }
            $result='';
        }

        $skey_ = $this->input->get('post_id_', null, $skey);
        

        if(!empty($this->input->get('post_id_', null, 0))) {
            $where_in['post_id'] = $this->input->get('post_id_');

            if(!empty($where_in['post_id'][0])){
                $post['extravars'] = $this->Post_extra_vars_model->get_all_meta($where_in['post_id'][0]);

                
                if(!empty($post['extravars']['campaign_multi']))
                $view['view']['campaign_multi'] = $post['extravars']['campaign_multi'];
                
            }
            

        }

        $multi_code='';
       if(!empty($this->input->get('multi_code',null))) $multi_code = $this->input->get('multi_code',null);

       
        $result = $this->Media_view_stat_model->get_view_stat_count($datetype, $start_date, $end_date, $brd_id, $orderby, $skey_,$multi_code);

        
        
        $week_korean = array('월', '화', '수', '목', '금', '토', '일');
        $sum_count = 0;
        $hit_sum_count = 0;
        $hit_sum_count_sub = array();
        $arr = array();
        $max = 0;

        if ($result && is_array($result)) {
            foreach ($result as $key => $value) {

                $s=element('day', $value);
                if ($s === '-'){
                    if($datetype==='domain') $s = '직접';
                    else $s = element('day', $value);
                }

                
                if ( ! isset($arr[$s])) {
                    $arr[$s]['mvs_cnt'] = 0;
                    $arr[$s]['mcs_cnt'] = 0;
                }
                $arr[$s]['mvs_cnt'] += element('mvs_cnt', $value);
                // $arr[$s]['hit_cnt'] += element('mvs_cnt', $value);
                if ($arr[$s]['mvs_cnt'] > $max) {
                    $max = $arr[$s]['mvs_cnt'];
                }
                $sum_count += element('mvs_cnt', $value);
                // $hit_sum_count += element('mvs_cnt', $value);
            }
        }


        
            
            $this->Media_click_stat_model->allow_search_field = array('mcs_id', 'post.post_title'); // 검색이 가능한 필드
            $this->Media_click_stat_model->search_field_equal = array('mcs_id', 'post.post_title'); // 검색중 like 가 

            $skey = $this->input->get('post_id_', null, '');
            

            $result = $this->Media_click_stat_model->get_click_stat_count($datetype, $start_date, $end_date, $brd_id, $orderby, $skey_, $multi_code);
            
            
            

            if ($result && is_array($result)) {
                foreach ($result as $key => $value) {

                    $s=element('day', $value); 
                    if ($s === '-'){
                        if($datetype==='domain') $s = '직접';
                        else $s = element('day', $value);
                    }

                    
                    if ( ! isset($arr[$s])) {
                        $arr[$s]['pln_cnt'] = array();
                        $arr[$s]['mcs_cnt'] = 0;
                    }

                     if(!empty($this->input->get('post_id_', null, 0))) {
                        if(isset($arr[$s]['pln_cnt'][element('pln_id', $value)]))
                            $arr[$s]['pln_cnt'][element('pln_id', $value)] += element('mcs_cnt', $value);
                        else 
                            $arr[$s]['pln_cnt'][element('pln_id', $value)] = element('mcs_cnt', $value);

                        if(isset($hit_sum_count_sub[element('pln_id', $value)]))
                            $hit_sum_count_sub[element('pln_id', $value)] += element('mcs_cnt', $value);
                        else 
                            $hit_sum_count_sub[element('pln_id', $value)] = element('mcs_cnt', $value);

                    }
                    if(isset($arr[$s]['pln_cnt_sum']))
                        $arr[$s]['pln_cnt_sum'] += element('mcs_cnt', $value);
                    else 
                        $arr[$s]['pln_cnt_sum'] = element('mcs_cnt', $value);

                    $arr[$s]['mcs_cnt'] += element('mcs_cnt', $value);
                    // if ($arr[$s]['cnt'] > $max) {
                    //     $max = $arr[$s]['cnt'];
                    // }
                    
                    
                    $hit_sum_count += element('mcs_cnt', $value);
                }
            }
        
        $result = array();
        $i = 0;
        $save_count = -1;
        $tot_count = 0;

        if (count($arr)) {
            foreach ($arr as $key => $value) {
                if(!empty($arr[$key]['mvs_cnt'])) $count = (int) $arr[$key]['mvs_cnt'];
                else $count = 0;

                if(!empty($arr[$key]['mcs_cnt'])) $hit_count = (int) $arr[$key]['mcs_cnt'];
                else $hit_count = 0;
                
                $result[$key]['count'] = $count;
                $result[$key]['hit_count'] = $hit_count;

                if($datetype!=='domain'){
                    $i++;
                    if ($save_count !== $hit_count) {
                        $no = $i;
                        $save_count = $hit_count;
                    }
                    $result[$key]['no'] = $no;

                    $result[$key]['key'] = $key;
                    if($hit_sum_count)  $rate = ($hit_count / $hit_sum_count * 100);
                    else $rate=0;
                    $result[$key]['rate'] = $rate;
                    $s_rate = number_format($rate, 1);
                    $result[$key]['s_rate'] = $s_rate;

                    if($max) $bar = (int)($hit_count / $max * 100);
                    else $bar = 0;
                } else {
                    $i++;
                    if ($save_count !== $count) {
                        $no = $i;
                        $save_count = $count;
                    }
                    $result[$key]['no'] = $no;
                    
                    $result[$key]['key'] = $key;
                    if($sum_count)  $rate = ($count / $sum_count * 100);
                    else $rate=0;
                    $result[$key]['rate'] = $rate;
                    $s_rate = number_format($rate, 1);
                    $result[$key]['s_rate'] = $s_rate;

                    if($max) $bar = (int)($count / $max * 100);
                    else $bar = 0;
                }
                $result[$key]['pln_cnt'] = element('pln_cnt',$value);
                $result[$key]['pln_cnt_sum'] = element('pln_cnt_sum',$value);
                
                $result[$key]['bar'] = $bar;
            }
            $view['view']['max_value'] = $max;
            $view['view']['sum_count'] = $sum_count;
            $view['view']['hit_sum_count'] = $hit_sum_count;
            $view['view']['hit_sum_count_sub'] = $hit_sum_count_sub;
            $view['view']['week_korean'] = $week_korean;
        }

        if ($datetype === 'y') {
            for ($i = $start_year; $i <= $end_year; $i++) {
                if( ! isset($result[$i])) $result[$i] = '';
            }
        } elseif ($datetype === 'm') {
            for ($i = $start_year_month; $i <= $end_year_month; $i++) {
                $year = floor($i / 12);
                if ($year * 12 == $i) $year--;
                $month = sprintf("%02d", ($i - ($year * 12)));
                $date = $year . '-' . $month;
                if( ! isset($result[$date])) $result[$date] = '';
            }
        } elseif ($datetype === 'd') {
            $date = $start_date;
            while ($date <= $end_date) {
                if( ! isset($result[$date])) $result[$date] = '';
                $date = cdate('Y-m-d', strtotime($date) + 86400);
            }
        } elseif ($datetype === 'h') {

            $date = $start_date;
            $i=0;
            while ($date < cdate('Y-m-d',strtotime($end_date))) {
            $i++;
            if($i > 24)  break;
                if( ! isset($result[cdate('H', strtotime($date))])) $result[cdate('H', strtotime($date))] = '';
                $date = cdate('Y-m-d His', strtotime($date) + 3600);
            }
        } elseif ($datetype === 'i') {

            $date = $start_date;
            $i=0;
            while ($date < cdate('Y-m-d',strtotime($end_date))) {
            $i++;
            if($i > 30)  break;
                if( ! isset($result[cdate('H', strtotime($date))])) $result[cdate('H', strtotime($date))] = '';
                $date = cdate('Y-m-d His', strtotime($date) + 3600);
                echo $date."<br>";
            }
        }


        if($datetype!=='domain'){
            if ($orderby === 'desc') {
                krsort($result);
            } else {
                ksort($result);
            }
        }
        $view['view']['list'] = $result;

        
        

        // $view['view']['boardlist'] = $this->Board_model->get_board_list();
        
        
        

            
        


        $view['view']['get_member_group_post_list'] = $this->get_member_group_post_list($brd_key);
        // $search_option = array('post.post_title' => '언론사명', 'post.post_md' => 'MD 코드');
        // $view['view']['skeyword'] = ($sfield && array_key_exists($sfield, $search_option)) ? $skeyword : '';
        // $view['view']['search_option'] = search_option($search_option, $sfield);

        // 이벤트가 존재하면 실행합니다
        $view['view']['event']['before_layout'] = Events::trigger('before_layout', $eventname);

        if ($export === 'excel') {
            
            header('Content-type: application/vnd.ms-excel');
            header('Content-Disposition: attachment; filename=캠페인_' . cdate('Y_m_d') . '.xls');
            echo $this->load->view('/stat/bootstrap/graph_excel', $view, true);

        } else {
            /**
             * 레이아웃을 정의합니다
             */

            $page_title = $this->cbconfig->item('site_meta_title_main');
            $meta_description = $this->cbconfig->item('site_meta_description_main');
            $meta_keywords = $this->cbconfig->item('site_meta_keywords_main');
            $meta_author = $this->cbconfig->item('site_meta_author_main');
            $page_name = $this->cbconfig->item('site_page_name_main');

            $layoutconfig = array(
                'path' => 'stat',
                'layout' => 'layout',
                'skin' => 'graph',
                'layout_dir' => $this->cbconfig->item('layout_main'),
                'mobile_layout_dir' => $this->cbconfig->item('mobile_layout_main'),
                'use_sidebar' => $this->cbconfig->item('sidebar_main'),
                'use_mobile_sidebar' => $this->cbconfig->item('mobile_sidebar_main'),
                'skin_dir' => 'bootstrap',
                'mobile_skin_dir' => 'bootstrap',
                'page_title' => $page_title,
                'meta_description' => $meta_description,
                'meta_keywords' => $meta_keywords,
                'meta_author' => $meta_author,
                'page_name' => $page_name,
                'page_url' => 'stat/lists',
            );
            $view['layout'] = $this->managelayout->front($layoutconfig, $this->cbconfig->get_device_view_type());
            $this->data = $view;
            $this->layout = element('layout_skin_file', element('layout', $view));
            $this->view = element('view_skin_file', element('layout', $view));
        }
    }

    /**
     * 목록 페이지에서 선택삭제를 하는 경우 실행되는 메소드입니다
     */
    public function listdelete($brd_key)
    {
        // 이벤트 라이브러리를 로딩합니다
        $eventname = 'event_stat_media_listdelete';
        $this->load->event($eventname);

        if (empty($brd_key)) {
            show_404();
        }

        $this->set_init($brd_key);

        // 이벤트가 존재하면 실행합니다
        Events::trigger('before', $eventname);

        /**
         * 체크한 게시물의 삭제를 실행합니다
         */
        if ($this->input->post('chk') && is_array($this->input->post('chk'))) {
            foreach ($this->input->post('chk') as $val) {
                if ($val) {
                    $this->{ucfirst($this->click_stat_model)}->delete($val);
                    $this->{ucfirst($this->click_stat_model)}->delete($val);
                }
            }
        }

        // 이벤트가 존재하면 실행합니다
        Events::trigger('after', $eventname);

        /**
         * 삭제가 끝난 후 목록페이지로 이동합니다
         */
        $this->session->set_flashdata(
            'message',
            '정상적으로 삭제되었습니다'
        );
        
        $redirecturl = site_url($this->pagedir . '?' . $this->param->output());

        redirect($redirecturl);
    }

    /**
     * 오래된 캠페인로그삭제 페이지입니다
     */
    public function cleanlog($brd_key)
    {

        // 이벤트 라이브러리를 로딩합니다
        $eventname = 'event_stat_media_cleanlog';
        $this->load->event($eventname);

        if (empty($brd_key)) {
            show_404();
        }

        

        $view = array();
        $view['view'] = array();

        // 이벤트가 존재하면 실행합니다
        $view['view']['event']['before'] = Events::trigger('before', $eventname);

        $view['view']['is_admin'] = $is_admin = $this->member->is_admin();
        /**
         * Validation 라이브러리를 가져옵니다
         */
        $this->load->library('form_validation');

        /**
         * 전송된 데이터의 유효성을 체크합니다
         */
        $config = array(
            array(
                'field' => 'day',
                'label' => '기간',
                'rules' => 'trim|required|numeric|is_natural',
            ),
        );
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

            if ($this->input->post('criterion') && ($this->input->post('day') || (int)$this->input->post('day') === 0)) {
                $deletewhere = array(
                    'mvs_datetime <=' => $this->input->post('criterion'),
                );

                $this->Media_view_stat_model->delete_where($deletewhere);

                $deletewhere = array(
                    'mcs_datetime <=' => $this->input->post('criterion'),
                );

                $this->Media_click_stat_model->delete_where($deletewhere);
                $view['view']['alert_message'] = '총 ' . number_format($this->input->post('log_count')) . ' 건의 ' . $this->input->post('day') . '일 이상된 로그가 모두 삭제되었습니다';
            } else {
                $criterion = cdate('Y-m-d H:i:s', ctimestamp() - $this->input->post('day') * 24 * 60 * 60);
                $countwhere = array(
                    'mvs_datetime <=' => $criterion,
                );
                $log_count = $this->Media_view_stat_model->count_by($countwhere);

                $countwhere = array(
                    'mcs_datetime <=' => $criterion,
                );
                $log_count += $this->Media_click_stat_model->count_by($countwhere);

                $view['view']['criterion'] = $criterion;
                $view['view']['day'] = $this->input->post('day');
                $view['view']['log_count'] = $log_count;
                if ($log_count > 0) {
                    $view['view']['msg'] = '총 ' . number_format($log_count) . ' 건의 ' . $this->input->post('day') . '일 이상된 캠페인로그가 발견되었습니다. 이를 모두 삭제하시겠습니까?';
                } else {
                    $view['view']['alert_message'] = $this->input->post('day') . '일 이상된 캠페인로그가 발견되지 않았습니다';
                }
            }
        }

        // 이벤트가 존재하면 실행합니다
        $view['view']['event']['before_layout'] = Events::trigger('before_layout', $eventname);

        /**
         * 레이아웃을 정의합니다
         */

        $page_title = $this->cbconfig->item('site_meta_title_main');
        $meta_description = $this->cbconfig->item('site_meta_description_main');
        $meta_keywords = $this->cbconfig->item('site_meta_keywords_main');
        $meta_author = $this->cbconfig->item('site_meta_author_main');
        $page_name = $this->cbconfig->item('site_page_name_main');

        $layoutconfig = array(
            'path' => 'stat',
            'layout' => 'layout',
            'skin' => 'cleanlog',
            'layout_dir' => $this->cbconfig->item('layout_main'),
            'mobile_layout_dir' => $this->cbconfig->item('mobile_layout_main'),
            'use_sidebar' => $this->cbconfig->item('sidebar_main'),
            'use_mobile_sidebar' => $this->cbconfig->item('mobile_sidebar_main'),
            'skin_dir' => 'bootstrap',
            'mobile_skin_dir' => 'bootstrap',
            'page_title' => $page_title,
            'meta_description' => $meta_description,
            'meta_keywords' => $meta_keywords,
            'meta_author' => $meta_author,
            'page_name' => $page_name,
            'page_url' => 'stat/lists',
        );
        $view['layout'] = $this->managelayout->front($layoutconfig, $this->cbconfig->get_device_view_type());
        $this->data = $view;
        $this->layout = element('layout_skin_file', element('layout', $view));
        $this->view = element('view_skin_file', element('layout', $view));

    }

    


    public function _get_list($brd_key, $from_view = '')
    {


        // 이벤트 라이브러리를 로딩합니다
        $eventname = 'event_board_post_get_list';
        $this->load->event($eventname);

        $view = array();
        $view['view'] = array();

        // 이벤트가 존재하면 실행합니다
        $view['view']['event']['before'] = Events::trigger('list_before', $eventname);

        $return = array();
        $board = $this->_get_board($brd_key);
        $mem_id = (int) $this->member->item('mem_id');

        $alertmessage = $this->member->is_member()
            ? '회원님은 이 게시판 목록을 볼 수 있는 권한이 없습니다'
            : '비회원은 이 게시판에 접근할 권한이 없습니다.\\n\\n회원이시라면 로그인 후 이용해 보십시오';

        $check = array(
            'group_id' => element('bgr_id', $board),
            'board_id' => element('brd_id', $board),
        );
        $this->accesslevel->check(
            element('access_list', $board),
            element('access_list_level', $board),
            element('access_list_group', $board),
            $alertmessage,
            $check
        );
        

        if (element('use_personal', $board) && $this->member->is_member() === false) {
            alert('이 게시판은 1:1 게시판입니다. 비회원은 접근할 수 없습니다');
            return false;
        }
        $skindir = ($this->cbconfig->get_device_view_type() === 'mobile')
            ? (element('board_mobile_skin', $board) ? element('board_mobile_skin', $board)
            : element('board_skin', $board)) : element('board_skin', $board);

        $skinurl = base_url( VIEW_DIR . 'board/' . $skindir);

        $view['view']['is_admin'] = $is_admin = $this->member->is_admin(
            array(
                'board_id' => element('brd_id', $board),
                'group_id' => element('bgr_id', $board)
            )
        );

        /**
         * 페이지에 숫자가 아닌 문자가 입력되거나 1보다 작은 숫자가 입력되면 에러 페이지를 보여줍니다.
         */
        
        $page = (((int) $this->input->get('page')) > 0) ? ((int) $this->input->get('page')) : 1;
        $order_by_field = element('order_by_field', $board)
            ? element('order_by_field', $board)
            : 'post_num, post_reply';

        $findex = $this->input->get('findex', null, $order_by_field);
        $sfield = $sfieldchk = $this->input->get('sfield', null, '');
        if ($sfield === 'post_both') {
            $sfield = array('post_title', 'post_content');
        }
        $skeyword = $this->input->get('skeyword', null, '');
        if ($this->cbconfig->get_device_view_type() === 'mobile') {
            $per_page = element('mobile_list_count', $board)
                ? (int) element('mobile_list_count', $board) : 10;
        } else {
            $per_page = element('list_count', $board)
                ? (int) element('list_count', $board) : 20;
        }
        $offset = ($page - 1) * $per_page;

        $this->Post_model->allow_search_field = array('post_id', 'post_title', 'post_content', 'post_both', 'post_category', 'post_userid', 'post_nickname'); // 검색이 가능한 필드
        $this->Post_model->search_field_equal = array('post_id', 'post_userid', 'post_nickname'); // 검색중 like 가 아닌 = 검색을 하는 필드

        // 이벤트가 존재하면 실행합니다
        $view['view']['event']['step1'] = Events::trigger('list_step1', $eventname);

        /**
         * 상단에 공지사항 부분에 필요한 정보를 가져옵니다.
         */

        $except_all_notice= false;
        if (element('except_all_notice', $board)
            && $this->cbconfig->get_device_view_type() !== 'mobile') {
            $except_all_notice = true;
        }
        if (element('mobile_except_all_notice', $board)
            && $this->cbconfig->get_device_view_type() === 'mobile') {
            $except_all_notice = true;
        }
        $use_subject_style = ($this->cbconfig->get_device_view_type() === 'mobile')
            ? element('use_mobile_subject_style', $board)
            : element('use_subject_style', $board);
        $use_sideview = ($this->cbconfig->get_device_view_type() === 'mobile')
            ? element('use_mobile_sideview', $board)
            : element('use_sideview', $board);
        $use_sideview_icon = ($this->cbconfig->get_device_view_type() === 'mobile')
            ? element('use_mobile_sideview_icon', $board)
            : element('use_sideview_icon', $board);
        $list_date_style = ($this->cbconfig->get_device_view_type() === 'mobile')
            ? element('mobile_list_date_style', $board)
            : element('list_date_style', $board);
        $list_date_style_manual = ($this->cbconfig->get_device_view_type() === 'mobile')
            ? element('mobile_list_date_style_manual', $board)
            : element('list_date_style_manual', $board);

        if (element('use_gallery_list', $board)) {
            $this->load->model('Post_file_model');

            $board['gallery_cols'] = $gallery_cols
                = ($this->cbconfig->get_device_view_type() === 'mobile')
                ? element('mobile_gallery_cols', $board)
                : element('gallery_cols', $board);

            $board['gallery_image_width'] = $gallery_image_width
                = ($this->cbconfig->get_device_view_type() === 'mobile')
                ? element('mobile_gallery_image_width', $board)
                : element('gallery_image_width', $board);

            $board['gallery_image_height'] = $gallery_image_height
                = ($this->cbconfig->get_device_view_type() === 'mobile')
                ? element('mobile_gallery_image_height', $board)
                : element('gallery_image_height', $board);

            $board['gallery_percent'] = floor( 102 / $board['gallery_cols']) - 2;
        }

        if (element('use_category', $board)) {
            $this->load->model('Board_category_model');
            $board['category'] = $this->Board_category_model
                ->get_all_category(element('brd_id', $board));
        }

        $noticeresult = $this->Post_model
            ->get_notice_list(element('brd_id', $board), $except_all_notice, $sfield, $skeyword);
        if ($noticeresult) {
            foreach ($noticeresult as $key => $val) {

                $notice_brd_key = $this->board->item_id('brd_key', element('brd_id', $val));
                $noticeresult[$key]['post_url'] = post_url($notice_brd_key, element('post_id', $val));

                $noticeresult[$key]['meta'] = $meta
                    = $this->Post_meta_model->get_all_meta(element('post_id', $val));


                if ($this->cbconfig->get_device_view_type() === 'mobile') {
                    $noticeresult[$key]['title'] = element('mobile_subject_length', $board)
                        ? cut_str(element('post_title', $val), element('mobile_subject_length', $board))
                        : element('post_title', $val);
                } else {
                    $noticeresult[$key]['title'] = element('subject_length', $board)
                        ? cut_str(element('post_title', $val), element('subject_length', $board))
                        : element('post_title', $val);
                }
                if (element('post_del', $val)) {
                    $noticeresult[$key]['title'] = '게시물이 삭제 되었습니다';
                }

                if (element('mem_id', $val) >= 0) {
                    $noticeresult[$key]['display_name'] = display_username(
                        element('post_userid', $val),
                        element('post_nickname', $val),
                        ($use_sideview_icon ? element('mem_icon', $val) : ''),
                        ($use_sideview ? 'Y' : 'N')
                    );
                } else {
                    $noticeresult[$key]['display_name'] = '익명사용자';
                }
                $noticeresult[$key]['display_datetime'] = display_datetime(element('post_datetime', $val), $list_date_style, $list_date_style_manual);
                $noticeresult[$key]['category'] = '';
                if (element('use_category', $board) && element('post_category', $val)) {
                        $noticeresult[$key]['category']
                            = $this->Board_category_model
                            ->get_category_info(element('brd_id', $val), element('post_category', $val));
                }
                if ($this->param->output()) {
                    $noticeresult[$key]['post_url'] .= '?' . $this->param->output();
                }
                $noticeresult[$key]['title_color'] = $use_subject_style
                    ? element('post_title_color', $meta) : '';
                $noticeresult[$key]['title_font'] = $use_subject_style
                    ? element('post_title_font', $meta) : '';
                $noticeresult[$key]['title_bold'] = $use_subject_style
                    ? element('post_title_bold', $meta) : '';
                $noticeresult[$key]['is_mobile'] = (element('post_device', $val) === 'mobile') ? true : false;
            }
        }
        /**
         * 게시판 목록에 필요한 정보를 가져옵니다.
         */
        $where_in='';
        $where = array(
            'brd_id' => $this->board->item_key('brd_id', $brd_key),
        );
        $where['post_del <>'] = 2;
        if (element('except_notice', $board)
            && $this->cbconfig->get_device_view_type() !== 'mobile') {
            $where['post_notice'] = 0;
        }
        if (element('mobile_except_notice', $board)
            && $this->cbconfig->get_device_view_type() === 'mobile') {
            $where['post_notice'] = 0;
        }
        if (element('use_personal', $board) && $is_admin === false) {
            $where['post.mem_id'] = $mem_id;
        }

        $where_in = $this->input->get('post_id_', null, '');

        $category_id = (int) $this->input->get('category_id');
        if (empty($category_id) OR $category_id < 1) {
            $category_id = '';
        }
        $result = $this->Post_model
            ->get_post_list($per_page, $offset, $where, $category_id, $findex, $sfield, $skeyword,'',$where_in);
    
        $list_num = $result['total_rows'] - ($page - 1) * $per_page;
        if (element('list', $result)) {
            foreach (element('list', $result) as $key => $val) {
                $result['list'][$key]['post_url'] = post_url(element('brd_key', $board), element('post_id', $val));

                $result['list'][$key]['meta'] = $meta
                    = $this->Post_meta_model
                    ->get_all_meta(element('post_id', $val));

                $result['list'][$key]['extravars'] = $this->Post_extra_vars_model->get_all_meta(element('post_id', $val));

                if ($this->cbconfig->get_device_view_type() === 'mobile') {
                    $result['list'][$key]['title'] = element('mobile_subject_length', $board)
                        ? cut_str(element('post_title', $val), element('mobile_subject_length', $board))
                        : element('post_title', $val);
                } else {
                    $result['list'][$key]['title'] = element('subject_length', $board)
                        ? cut_str(element('post_title', $val), element('subject_length', $board))
                        : element('post_title', $val);
                }
                if (element('post_del', $val)) {
                    $result['list'][$key]['title'] = '게시물이 삭제 되었습니다';
                }
                $is_blind = (element('blame_blind_count', $board) > 0 && element('post_blame', $val) >= element('blame_blind_count', $board)) ? true : false;
                if ($is_blind) {
                    $result['list'][$key]['title'] = '신고가 접수된 게시글입니다.';
                }

                if (element('mem_id', $val) >= 0) {
                    $result['list'][$key]['display_name'] = display_username(
                        element('post_userid', $val),
                        element('post_nickname', $val),
                        ($use_sideview_icon ? element('mem_icon', $val) : ''),
                        ($use_sideview ? 'Y' : 'N')
                    );
                } else {
                    $result['list'][$key]['display_name'] = '익명사용자';
                }

                $result['list'][$key]['display_datetime'] = display_datetime(
                    element('post_datetime', $val),
                    $list_date_style,
                    $list_date_style_manual
                );
                $result['list'][$key]['category'] = '';
                if (element('use_category', $board) && element('post_category', $val)) {
                    $result['list'][$key]['category']
                        = $this->Board_category_model
                        ->get_category_info(element('brd_id', $val), element('post_category', $val));
                }
                if ($this->param->output()) {
                    $result['list'][$key]['post_url'] .= '?' . $this->param->output();
                }
                $result['list'][$key]['num'] = $list_num--;
                $result['list'][$key]['is_hot'] = false;

                $hot_icon_day = ($this->cbconfig->get_device_view_type() === 'mobile')
                    ? element('mobile_hot_icon_day', $board)
                    : element('hot_icon_day', $board);

                $hot_icon_hit = ($this->cbconfig->get_device_view_type() === 'mobile')
                    ? element('mobile_hot_icon_hit', $board)
                    : element('hot_icon_hit', $board);

                if ($hot_icon_day && ( ctimestamp() - strtotime(element('post_datetime', $val)) <= $hot_icon_day * 86400)) {
                    if ($hot_icon_hit && $hot_icon_hit <= element('post_hit', $val)) {
                        $result['list'][$key]['is_hot'] = true;
                    }
                }
                $result['list'][$key]['is_new'] = false;
                $new_icon_hour = ($this->cbconfig->get_device_view_type() === 'mobile')
                    ? element('mobile_new_icon_hour', $board)
                    : element('new_icon_hour', $board);

                if ($new_icon_hour && ( ctimestamp() - strtotime(element('post_datetime', $val)) <= $new_icon_hour * 3600)) {
                    $result['list'][$key]['is_new'] = true;
                }

                $result['list'][$key]['title_color'] = ($use_subject_style && element('post_title_color', $meta)) ? element('post_title_color', $meta) : '';
                $result['list'][$key]['title_font'] = ($use_subject_style && element('post_title_font', $meta)) ? element('post_title_font', $meta) : '';
                $result['list'][$key]['title_bold'] = ($use_subject_style && element('post_title_bold', $meta)) ? element('post_title_bold', $meta) : '';
                $result['list'][$key]['is_mobile'] = (element('post_device', $val) === 'mobile') ? true : false;

                $result['list'][$key]['thumb_url'] = '';
                $result['list'][$key]['origin_image_url'] = '';
                if (element('use_gallery_list', $board)) {
                    if (element('post_image', $val)) {
                        $filewhere = array(
                            'post_id' => element('post_id', $val),
                            'pfi_is_image' => 1,
                        );
                        $file = $this->Post_file_model
                            ->get_one('', '', $filewhere, '', '', 'pfi_id', 'ASC');
                        $result['list'][$key]['thumb_url'] = thumb_url('post', element('pfi_filename', $file), $gallery_image_width, $gallery_image_height);
                        $result['list'][$key]['origin_image_url'] = thumb_url('post', element('pfi_filename', $file));
                    } else {
                        $thumb_url = get_post_image_url(element('post_content', $val), $gallery_image_width, $gallery_image_height);
                        $result['list'][$key]['thumb_url'] = $thumb_url
                            ? $thumb_url
                            : thumb_url('', '', $gallery_image_width, $gallery_image_height);

                        $result['list'][$key]['origin_image_url'] = $thumb_url;
                    }
                } else {
                    $this->load->model('Post_file_model');

                    if (element('post_image', $val)) {
                        $filewhere = array(
                            'post_id' => element('post_id', $val),
                            'pfi_is_image' => 1,
                        );
                        $file = $this->Post_file_model
                            ->get_one('', '', $filewhere, '', '', 'pfi_id', 'ASC');
                        $result['list'][$key]['thumb_url'] = thumb_url('post', element('pfi_filename', $file));
                        $result['list'][$key]['origin_image_url'] = thumb_url('post', element('pfi_filename', $file));
                    } else {
                        $thumb_url = get_post_image_url(element('post_content', $val));
                        $result['list'][$key]['thumb_url'] = $thumb_url
                            ? $thumb_url
                            : thumb_url('', '');

                        $result['list'][$key]['origin_image_url'] = $thumb_url;
                    }


                }


                $result['list'][$key]['pln_url'] ='';

                if (element('post_link_count', $val)) {
                    $this->load->model('Post_link_model');
                    $linkwhere = array(
                        'post_id' => element('post_id', $val),
                    );
                    $link = $this->Post_link_model
                        ->get('', '', $linkwhere, '', '', 'pln_id', 'ASC');
                    if ($link && is_array($link)) {
                            $result['list'][$key]['pln_url'] = $link;
                    }
                }

                $result['list'][$key]['member_group_name'] = '';

                $where = array(
                    'mem_id' => element('mem_id', $val),
                );
                
                $member_group = $this->Member_group_member_model->get('', '', $where, '', 0, 'mgm_id', 'ASC');
                if ($member_group && is_array($member_group)) {

                    

                    foreach ($member_group as $gkey => $gval) {
                        $item = $this->Member_group_model->item(element('mgr_id', $gval));
                        if ($result['list'][$key]['member_group_name']) {
                            $result['list'][$key]['member_group_name'] .= ', ';
                        }
                        $result['list'][$key]['member_group_name'] .= element('mgr_title', $item);
                    }


                }
            }
        }



        $result['get_member_group_post_list']= $this->get_member_group_post_list($brd_key);
        $return['data'] = $result;
        $return['notice_list'] = $noticeresult;
        if (empty($from_view)) {
            $board['headercontent'] = ($this->cbconfig->get_device_view_type() === 'mobile')
                ? element('mobile_header_content', $board)
                : element('header_content', $board);
        }
        $board['footercontent'] = ($this->cbconfig->get_device_view_type() === 'mobile')
            ? element('mobile_footer_content', $board)
            : element('footer_content', $board);

        $board['cat_display_style'] = ($this->cbconfig->get_device_view_type() === 'mobile')
            ? element('mobile_category_display_style', $board)
            : element('category_display_style', $board);

        $return['board'] = $board;

        $return['point_info'] = '';
        if ($this->cbconfig->item('use_point')
            && element('use_point', $board)
            && element('use_point_info', $board)) {

            $point_info = '';
            if (element('point_write', $board)) {
                $point_info .= '원글작성 : ' . element('point_write', $board) . '<br />';
            }
            if (element('point_comment', $board)) {
                $point_info .= '댓글작성 : ' . element('point_comment', $board) . '<br />';
            }
            if (element('point_fileupload', $board)) {
                $point_info .= '파일업로드 : ' . element('point_fileupload', $board) . '<br />';
            }
            if (element('point_filedownload', $board)) {
                $point_info .= '파일다운로드 : ' . element('point_filedownload', $board) . '<br />';
            }
            if (element('point_filedownload_uploader', $board)) {
                $point_info .= '파일다운로드시업로더에게 : ' . element('point_filedownload_uploader', $board) . '<br />';
            }
            if (element('point_read', $board)) {
                $point_info .= '게시글조회 : ' . element('point_read', $board) . '<br />';
            }
            if (element('point_post_like', $board)) {
                $point_info .= '원글추천함 : ' . element('point_post_like', $board) . '<br />';
            }
            if (element('point_post_dislike', $board)) {
                $point_info .= '원글비추천함 : ' . element('point_post_dislike', $board) . '<br />';
            }
            if (element('point_post_liked', $board)) {
                $point_info .= '원글추천받음 : ' . element('point_post_liked', $board) . '<br />';
            }
            if (element('point_post_disliked', $board)) {
                $point_info .= '원글비추천받음 : ' . element('point_post_disliked', $board) . '<br />';
            }
            if (element('point_comment_like', $board)) {
                $point_info .= '댓글추천함 : ' . element('point_comment_like', $board) . '<br />';
            }
            if (element('point_comment_dislike', $board)) {
                $point_info .= '댓글비추천함 : ' . element('point_comment_dislike', $board) . '<br />';
            }
            if (element('point_comment_liked', $board)) {
                $point_info .= '댓글추천받음 : ' . element('point_comment_liked', $board) . '<br />';
            }
            if (element('point_comment_disliked', $board)) {
                $point_info .= '댓글비추천받음 : ' . element('point_comment_disliked', $board) . '<br />';
            }

            $return['point_info'] = $point_info;
        }

        // 이벤트가 존재하면 실행합니다
        $view['view']['event']['step2'] = Events::trigger('list_step2', $eventname);


        /**
         * primary key 정보를 저장합니다
         */
        $return['primary_key'] = $this->Post_model->primary_key;

        $highlight_keyword = '';
        if ($skeyword) {
            if ( ! $this->session->userdata('skeyword_' . $skeyword)) {
                $sfieldarray = array(
                    'post_title',
                    'post_content',
                    'post_both',
                );
                if (in_array($sfieldchk, $sfieldarray)) {
                    $this->load->model('Search_keyword_model');
                    $searchinsert = array(
                        'sek_keyword' => $skeyword,
                        'sek_datetime' => cdate('Y-m-d H:i:s'),
                        'sek_ip' => $this->input->ip_address(),
                        'mem_id' => $mem_id,
                    );
                    $this->Search_keyword_model->insert($searchinsert);
                    $this->session->set_userdata(
                        'skeyword_' . $skeyword,
                        1
                    );
                }
            }
            $key_explode = explode(' ', $skeyword);
            if ($key_explode) {
                foreach ($key_explode as $seval) {
                    if ($highlight_keyword) {
                        $highlight_keyword .= ',';
                    }
                    $highlight_keyword .= '\'' . html_escape($seval) . '\'';
                }
            }
        }
        $return['highlight_keyword'] = $highlight_keyword;

        /**
         * 페이지네이션을 생성합니다
         */
        $config['base_url'] = board_url($brd_key) . '?' . $this->param->replace('page');
        $config['total_rows'] = $result['total_rows'];
        $config['per_page'] = $per_page;
        if ($this->cbconfig->get_device_view_type() === 'mobile') {
            $config['num_links'] = element('mobile_page_count', $board)
                ? element('mobile_page_count', $board) : 3;
        } else {
            $config['num_links'] = element('page_count', $board)
                ? element('page_count', $board) : 5;
        }
        $this->pagination->initialize($config);
        $return['paging'] = $this->pagination->create_links();
        $return['page'] = $page;

        /**
         * 쓰기 주소, 삭제 주소등 필요한 주소를 구합니다
         */
        $search_option = array(
            'post_title' => '제목',
        );
        $return['search_option'] = search_option($search_option, $sfield);
        if ($skeyword) {
            $return['list_url'] = board_url(element('brd_key', $board));
            $return['search_list_url'] = board_url(element('brd_key', $board) . '?' . $this->param->output());
        } else {
            $return['list_url'] = board_url(element('brd_key', $board) . '?' . $this->param->output());
            $return['search_list_url'] = '';
        }

        $check = array(
            'group_id' => element('bgr_id', $board),
            'board_id' => element('brd_id', $board),
        );
        $can_write = $this->accesslevel->is_accessable(
            element('access_write', $board),
            element('access_write_level', $board),
            element('access_write_group', $board),
            $check
        );

        $return['write_url'] = '';
        if ($can_write === true) {
            $return['write_url'] = write_url($brd_key);
        } elseif ($this->cbconfig->get_device_view_type() !== 'mobile' && element('always_show_write_button', $board)) {
            $return['write_url'] = 'javascript:alert(\'비회원은 글쓰기 권한이 없습니다.\\n\\n회원이시라면 로그인 후 이용해 보십시오.\');';
        } elseif ($this->cbconfig->get_device_view_type() === 'mobile' && element('mobile_always_show_write_button', $board)) {
            $return['write_url'] = 'javascript:alert(\'비회원은 글쓰기 권한이 없습니다.\\n\\n회원이시라면 로그인 후 이용해 보십시오.\');';
        }

        $return['list_delete_url'] = site_url('postact/listdelete/' . $brd_key . '?' . $this->param->output());

        return $return;
        
    }

    /**
     * board, board_meta 정보를 얻습니다
     */
    public function _get_board($brd_key)
    {
        $board_id = $this->board->item_key('brd_id', $brd_key);
        if (empty($board_id)) {
            show_404();
        }
        $board = $this->board->item_all($board_id);
        return $board;
    }

    public function get_member_group_post_list($brd_key='')
    {


        $mem_id = (int) $this->member->item('mem_id');
        
        $is_admin = $this->member->is_admin();

        $last = $this->uri->total_segments();
        $last_string = $this->uri->segment($last);
        
        $mgr_id=array('shortcut'=>1,'mobusi'=>1,'selfcert_ad'=>2,'linkmine'=>5,'tenping' => 6,'linkasia' => 7 ,'nomal_campaign' => 8,'viashare' => 10,'multiple' => 1);
        $where = array();
        $where['post_del <>'] = 2;
        $where['post_notice'] = 0;
        
        $where['brd_id'] = $this->board->item_key('brd_id', $brd_key);

        if ($is_admin === false) {
            $where['post.mem_id'] = $mem_id;
        }

        $category_id = (int) $this->input->get('category_id');
        if (empty($category_id) OR $category_id < 1) {
            $category_id = '';
        }
        $sfield = $sfieldchk = $this->input->get('sfield', null, '');
        $skeyword = $this->input->get('skeyword', null, '');

        $result = $this->Post_model
            ->get_post_list('','', $where, $category_id, '', $sfield, $skeyword);
        $member_group_post_list='';
        if (element('list', $result)) {
            foreach (element('list', $result) as $key => $val) {
        
           
                $member_group_name='';
                $where = array(
                    'mem_id' => element('mem_id', $val),
                    'mgr_id' => element($last_string, $mgr_id),
                );
                

                $this->load->model('Member_group_member_model');
                $member_group = $this->Member_group_member_model->get('', '', $where, '', 0, 'mgm_id', 'ASC');
                if ($member_group && is_array($member_group)) {

                    $this->load->model('Member_group_model');

                    foreach ($member_group as $gkey => $gval) {
                        $item = $this->Member_group_model->item(element('mgr_id', $gval));
                        if ($member_group_name) {
                            $member_group_name.= ', ';
                        }
                        $member_group_name .= element('mgr_title', $item);
                        $member_group_post_list['member_group_name_list'][element('mgr_id', $item)]=$member_group_name;
                        $member_group_post_list['search_list'][element('mgr_id', $item)][element('post_id', $val)] = array('mgr_title' => $member_group_name,'post_title' => element('post_title', $val));
                    }


                }
            }
        }
        return $member_group_post_list;
    }

    
}
