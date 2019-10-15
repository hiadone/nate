<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Post Link model class
 *
 * Copyright (c) CIBoard <www.ciboard.co.kr>
 *
 * @author CIBoard (develop@ciboard.co.kr)
 */

class Post_link_model extends CB_Model
{

	/**
	 * 테이블명
	 */
	public $_table = 'post_link';

	/**
	 * 사용되는 테이블의 프라이머리키
	 */
	public $primary_key = 'pln_id'; // 사용되는 테이블의 프라이머리키

	function __construct()
	{
		parent::__construct();
	}


	public function get_list($limit = '', $offset = '', $where = '', $like = '', $findex = '', $forder = '', $sfield = '', $skeyword = '', $sop = 'OR')
    {
        $select = 'post_link.*,cmall_item.cit_summary';
        $join[] = array('table' => 'cmall_item', 'on' => 'post_link.pln_url = cmall_item.cit_shopping_url', 'type' => 'inner');
        $result = $this->_get_list_common($select, $join, $limit, $offset, $where, $like, $findex, $forder, $sfield, $skeyword, $sop);

        return $result;
    }
}
