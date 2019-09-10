<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Cmall channel model class
 *
 * Copyright (c) CIBoard <www.ciboard.co.kr>
 *
 * @author CIBoard (develop@ciboard.co.kr)
 */

class Cmall_channel_model extends CB_Model
{

    /**
     * 테이블명
     */
    public $_table = 'cmall_channel';

    /**
     * 사용되는 테이블의 프라이머리키
     */
    public $primary_key = 'cch_id'; // 사용되는 테이블의 프라이머리키

    function __construct()
    {
        parent::__construct();
    }


    public function get_all_channel()
    {
        $cachename = 'cmall-channel-all';
        if ( ! $result = $this->cache->get($cachename)) {
            $return = $this->get($primary_value = '', $select = '', $where = '', $limit = '', $offset = 0, $findex = 'cch_order', $forder = 'asc');
            if ($return) {
                foreach ($return as $key => $value) {
                    $result[$value['cch_parent']][] = $value;
                }
                $this->cache->save($cachename, $result);
            }
        }
        return $result;
    }


    public function get_channel_info($cch_id = 0)
    {
        $cch_id = (int) $cch_id;
        if (empty($cch_id) OR $cch_id < 1) {
            return;
        }
        $cachename = 'cmall-channel-detail';
        if ( ! $result = $this->cache->get($cachename)) {
            $return = $this->get($primary_value = '', $select = '', $where = '', $limit = '', $offset = 0, $findex = 'cch_order', $forder = 'asc');
            if ($return) {
                foreach ($return as $key => $value) {
                    $result[$value['cch_id']] = $value;
                }
                $this->cache->save($cachename, $result);
            }
        }
        return isset($result[$cch_id]) ? $result[$cch_id] : '';
    }


    public function get_channel($cit_id = 0)
    {
        $cit_id = (int) $cit_id;
        if (empty($cit_id) OR $cit_id < 1) {
            return;
        }

        $this->db->select('cmall_channel.*');
        $this->db->join('cmall_channel_rel', 'cmall_channel.cch_id = cmall_channel_rel.cch_id', 'inner');
        $this->db->where(array('cmall_channel_rel.cit_id' => $cit_id));
        $this->db->order_by('cch_order', 'asc');
        $qry = $this->db->get($this->_table);
        $result = $qry->result_array();

        return $result;
    }
}
