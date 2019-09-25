<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Post View Click Log model class
 *
 * Copyright (c) CIBoard <www.ciboard.co.kr>
 *
 * @author CIBoard (develop@ciboard.co.kr)
 */

class Media_view_log_model extends CB_Model
{

    /**
     * 테이블명
     */
    public $_table = 'media_view_log';

    /**
     * 사용되는 테이블의 프라이머리키
     */
    public $primary_key = 'mvl_id'; // 사용되는 테이블의 프라이머리키

    function __construct()
    {
        parent::__construct();
    }


    public function get_admin_list($limit = '', $offset = '', $where = '', $like = '', $findex = '', $forder = '', $sfield = '', $skeyword = '', $sop = 'OR')
    {
        $select = 'media_view_log.*, post.mem_id as post_mem_id, post.post_userid, post.post_nickname,
            post.brd_id, post.post_datetime, post.post_hit, post.post_secret, post.post_title, post_link.*';
        $join[] = array('table' => 'post_link', 'on' => 'media_view_log.pln_id = post_link.pln_id', 'type' => 'inner');
        $join[] = array('table' => 'post', 'on' => 'post_link.post_id = post.post_id', 'type' => 'inner');
        $result = $this->_get_list_common($select, $join, $limit, $offset, $where, $like, $findex, $forder, $sfield, $skeyword, $sop);

        return $result;
    }


    public function get_list($limit = '', $offset = '', $where = '', $like = '', $findex = '', $forder = '', $sfield = '', $skeyword = '', $sop = 'OR',$where_in = '')
    {
        if(!empty($where_in)) $where_in_['media_view_log.'.key($where_in)]=$where_in[key($where_in)];
        else $where_in_='';
        $select = 'media_view_log.*, post.mem_id as post_mem_id, post.post_userid, post.post_nickname,
            post.brd_id, post.post_datetime, post.post_hit, post.post_secret, post.post_title';        
        $join[] = array('table' => 'post', 'on' => 'media_view_log.post_id = post.post_id', 'type' => 'inner');
        $result = $this->_get_list_common($select, $join, $limit, $offset, $where, $like, $findex, $forder, $sfield, $skeyword, $sop,$where_in_);

        return $result;
    }


    public function get_view_click_count($type = 'd', $start_date = '', $end_date = '', $brd_id = 0, $orderby = 'asc')
    {
        if (empty($start_date) OR empty($end_date)) {
            return false;
        }
        $left = ($type === 'y') ? 4 : ($type === 'm' ? 7 : 10);
        if (strtolower($orderby) !== 'desc') $orderby = 'asc';

        $this->db->select('count(*) as cnt, left(mvl_datetime, ' . $left . ') as day ', false);
        $this->db->where('left(mvl_datetime, 10) >=', $start_date);
        $this->db->where('left(mvl_datetime, 10) <=', $end_date);
        $brd_id = (int) $brd_id;
        if ($brd_id) {
            $this->db->where('brd_id', $brd_id);
        }
        $this->db->group_by('day');
        $this->db->order_by('mvl_datetime', $orderby);
        $qry = $this->db->get($this->_table);
        $result = $qry->result_array();

        return $result;
    }

    
}
