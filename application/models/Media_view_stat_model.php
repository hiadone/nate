<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Post View Click Log model class
 *
 * Copyright (c) CIBoard <www.ciboard.co.kr>
 *
 * @author CIBoard (develop@ciboard.co.kr)
 */

class Media_view_stat_model extends CB_Model
{

    /**
     * 테이블명
     */
    public $_table = 'media_view_stat';

    /**
     * 사용되는 테이블의 프라이머리키
     */
    public $primary_key = 'mvs_id'; // 사용되는 테이블의 프라이머리키

    function __construct()
    {
        parent::__construct();
    }


    public function get_list($limit = '', $offset = '', $where = '', $like = '', $findex = '', $forder = '', $sfield = '', $skeyword = '', $sop = 'OR')
    {
        $select = 'media_view_stat.*,post.post_id,post.post_title';
        $join[] = array('table' => 'post', 'on' => 'media_view_stat.post_id = post.post_id', 'type' => 'inner');
        $result = $this->_get_list_common($select, $join, $limit, $offset, $where, $like, $findex, $forder, $sfield, $skeyword, $sop);

        return $result;
    }


    public function get_post_group_list($start_date = '', $end_date = '', $brd_id = 0)
    {
        if (empty($start_date) OR empty($end_date)) {
            return false;
        }
       
        $this->db->select('media_view_stat.*,post.post_id,post.post_title', false);
        
        
       
        $this->db->where('left(mvs_datetime, 10) >=', $start_date);
        $this->db->where('left(mvs_datetime, 10) <=', $end_date);
       
        $brd_id = (int) $brd_id;
        if ($brd_id) {
            $this->db->where('post.brd_id', $brd_id);
        }
        
        
        $this->db->join('post', 'media_view_stat.post_id = post.post_id', 'inner');

        $this->db->group_by('media_view_stat.post_id');
        //$this->db->order_by('ps_datetime', $orderby);
        $qry = $this->db->get($this->_table);
        $result = $qry->result_array();

        return $result;
    }


    public function get_view_stat_count($type = 'd', $start_date = '', $end_date = '', $brd_id = 0, $orderby = 'asc', $skey = '', $multi_code = '')
    {
        if (empty($start_date) OR empty($end_date)) {
            return false;
        }
        $left = ($type === 'y') ? 4 : ($type === 'm' ? 7 : 10);
        if (strtolower($orderby) !== 'desc') $orderby = 'asc';
        if($type==='domain'){
            $this->db->select('SUM(mvs_cnt) as mvs_cnt, mvs_referrer as day ', false);
        } else if($type==='week'){
            $this->db->select('SUM(mvs_cnt) as mvs_cnt, WEEKDAY(mvs_datetime) as day ', false);
        } elseif($type==='h'){
            $this->db->select('SUM(mvs_cnt) as mvs_cnt, mid(mvs_datetime,12,2) as day ', false);
        } else {
            $this->db->select('SUM(mvs_cnt) as mvs_cnt, left(mvs_datetime, ' . $left . ') as day ', false);
        }
        
        
        $this->db->where('left(mvs_datetime, 10) >=', $start_date);
        $this->db->where('left(mvs_datetime, 10) <=', $end_date);
        
        $brd_id = (int) $brd_id;
        if ($brd_id) {
            $this->db->where('post.brd_id', $brd_id);
        }

        if (!empty($skey)) {

            if(is_array($skey))
                $this->db->where_in('media_view_stat.post_id', $skey);
            else 
                $this->db->where('media_view_stat.post_id', $skey);
        }

        if (!empty($multi_code)) {

            
            $this->db->where('media_view_stat.mvs_code', $multi_code);
        }
        
        $this->db->join('post', 'media_view_stat.post_id = post.post_id', 'inner');

        $this->db->group_by('day');
        if($type==='domain') $this->db->order_by('mvs_cnt', $orderby);

        $qry = $this->db->get($this->_table);
        $result = $qry->result_array();

        return $result;
    }

    

    public function migration()
    {   
        
        // $qry = $this->db->query('SELECT A.post_id,A.brd_id,A.sc_datetime,A.sc_referrer,A.sc_cnt,B.sc_cnt from (SELECT post_id,brd_id,count(*) as sc_cnt, left(sfd_datetime, 13) as sc_datetime,sfd_referrer as sc_referrer from cb_shortcut_file_download_log group by post_id,left(sfd_datetime, 13),sfd_referrer) A LEFT OUTER JOIN (SELECT post_id,brd_id,count(*) as sc_cnt, left(mlc_datetime, 13) as sc_datetime from cb_shortcut_view_click_log group by post_id,left(mlc_datetime, 13)) B on A.post_id=B.post_id and A.sc_datetime = B.sc_datetime  
        //     UNION
        //     SELECT A.post_id,A.brd_id,A.sc_datetime,A.sc_referrer,A.sc_cnt,B.sc_cnt from (SELECT post_id,brd_id,count(*) as sc_cnt, left(sfd_datetime, 13) as sc_datetime,sfd_referrer as sc_referrer from cb_shortcut_file_download_log group by post_id,left(sfd_datetime, 13),sfd_referrer) A RIGHT OUTER JOIN (SELECT post_id,brd_id,count(*) as sc_cnt, left(mlc_datetime, 13) as sc_datetime from cb_shortcut_view_click_log group by post_id,left(mlc_datetime, 13)) B on A.post_id=B.post_id and A.sc_datetime = B.sc_datetime  
        //     ');

        $qry = $this->db->query('SELECT A.post_id,A.brd_id,A.mvs_datetime,A.mvs_cnt,A.mvs_code from (SELECT post_id,brd_id,count(*) * 50 as mvs_cnt, left(mvl_datetime, 13) as mvs_datetime,mvl_code as mvs_code from cb_media_view_log group by post_id,left(mvl_datetime, 13),mvl_code) A ');

        $result = $qry->result_array();
        

        return $result;
    }
}


