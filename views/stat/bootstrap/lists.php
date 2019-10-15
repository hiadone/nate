<?php $this->managelayout->add_css(element('view_skin_url', $layout) . '/css/style.css'); ?>

<div class="box">
    <div class="box-table">
        <?php
        echo show_alert_message($this->session->flashdata('message'), '<div class="alert alert-auto-close alert-dismissible alert-info"><button type="button" class="close alertclose" >&times;</button>', '</div>');
        $attributes = array('class' => 'form-inline', 'name' => 'flist', 'id' => 'flist');
        echo form_open(current_full_url(), $attributes);
        ?>

        <?php  echo '<h4 class="highlight mb20">미디어 통계</h4>'; ?>
            <div class="box-table-header">
                <ul class="nav nav-pills">
                    <li role="presentation" class="active"><a href="<?php echo site_url($this->pagedir. '/lists/b-a-1?'.$this->param->output()); ?>">캠페인 목록</a></li>
                    <li role="presentation"><a href="<?php echo site_url($this->pagedir . '/view_log/b-a-1?'.$this->param->output()); ?>">실시간 리스트</a></li>
                    <li role="presentation"><a href="<?php echo site_url($this->pagedir . '/graph/b-a-1?'.$this->param->output()); ?>">기간별 그래프</a></li>
                    <?php if (element('is_admin', $view)) { ?>
                    <li role="presentation"><a href="<?php echo site_url($this->pagedir . '/cleanlog/b-a-1?'.$this->param->output()); ?>">로그삭제</a></li>
                    <?php } ?>
                </ul>
                <?php
                ob_start();
                ?>
                    <div class="btn-group pull-right" role="group" aria-label="...">
                        <a href="<?php echo element('listall_url', $view); ?>" class="btn btn-outline btn-default btn-sm">전체목록</a>
                        <!-- <button type="button" class="btn btn-outline btn-default btn-sm btn-list-delete btn-list-selected disabled" data-list-delete-url = "<?php echo element('list_delete_url', $view); ?>" >선택삭제</button> -->
                    </div>
                <?php
                $buttons = ob_get_contents();
                ob_end_flush();
                ?>
                <?php if (element('boardlist', $view)) { ?>
                    <div class="pull-right mr10">
                        <select name="brd_id" class="form-control" onChange="location.href='<?php echo current_url(); ?>?brd_id=' + this.value;">
                            <option value="">전체게시판</option>
                            <?php foreach (element('boardlist', $view) as $key => $value) { ?>
                                <option value="<?php echo element('brd_id', $value); ?>" <?php echo set_select('brd_id', element('brd_id', $value), ($this->input->get('brd_id') === element('brd_id', $value) ? true : false)); ?>><?php echo html_escape(element('brd_name', $value)); ?></option>
                            <?php } ?>
                        </select>
                    </div>
                <?php } ?>
            </div>
            <div class="row">전체 : <?php echo element('total_rows', element('data', $view), 0); ?>건</div>
            <div class="table-responsive">
                <table class="table table-hover table-striped table-bordered">
                    <thead>
                        <tr>
                            <th><a href="<?php echo element('pl_id', element('sort', $view)); ?>">번호</a></th>
                            <th>제목</th>                            
                            <th>URL</th>
                           <!--  <th>캠페인클릭</th> -->
                            <!-- <th>IP</th> -->
                            <th>미디어상태</th>
                            <th>등록일자</th>
                            <th>Action</th>
                            <!-- <th><input type="checkbox" name="chkall" id="chkall" /></th> -->
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    if (element('list', element('data', element('list', $view)))) {
                        foreach (element('list', element('data', element('list', $view))) as $result) {
                            $multi_code =  explode(',', element('campaign_multi', element('extravars',$result)));
                    ?>
                        <tr>
                            <td><?php echo number_format(element('num', $result)); ?></td>
                            <td><?php echo html_escape(element('post_title', $result)); ?><a href="<?php echo goto_url(element('post_url', $result)); ?>" target="_blank"><span class="fa fa-external-link"></span></a></td>
                            <!-- <td><?php echo html_escape(element('member_group_name', $result)); ?></td>
                            <td><img src="<?php echo element('thumb_url', $result); ?>" alt="<?php echo html_escape(element('title', $result)); ?>" title="<?php echo html_escape(element('title', $result)); ?>" class="thumbnail img-responsive px50"  /></td> -->
                            <td><?php 
                                echo '<div><i class="fa fa-link"></i><a href="'.site_url('postact/media_view/'.element('post_id', $result)).'" target="_blank">'.site_url('postact/media_view/'.element('post_id', $result)).'</a></div>';
                                    
                                 ?>
                            </td>
                            
                            <td><?php echo element('campaign_status', element('extravars', $result)); ?></td>
                            <td><?php echo element('display_datetime', $result); ?></td>
                            <td><a href="<?php echo site_url($this->pagedir . '/view_log/b-a-1');?>?post_id_[]=<?php echo element('post_id', $result)?>" class="btn btn-success btn-xs">통계보기</a></td>
                        </tr>
                    <?php
                        }
                    }
                    if (!element('list', element('data', element('list', $view)))) {
                    ?>
                        <tr>
                            <td colspan="11" class="nopost">자료가 없습니다</td>
                        </tr>
                    <?php
                    }
                    ?>
                    </tbody>
                </table>
            </div>
            <div class="box-info">
                <nav><?php echo element('paging', element('list', $view)); ?></nav>
                <div class="pull-left ml20"><?php echo admin_listnum_selectbox();?></div>
                <?php echo $buttons; ?>
            </div>
        <?php echo form_close(); ?>
    </div>
    <form name="fsearch" id="fsearch" action="<?php echo current_full_url(); ?>" method="get">
    <input type="hidden" name="brd_id" value="<?php echo $this->input->get('brd_id')?>">
        <div class="box-search">
            <div class="row">
                <div class="col-md-6 col-md-offset-3">
                    <select class="form-control" name="sfield" >
                        <?php echo element('search_option',element('list', $view)); ?>
                    </select>
                    <div class="input-group">
                        <input type="text" class="form-control" name="skeyword" value="<?php echo html_escape(element('skeyword', $view)); ?>" placeholder="Search for..." />
                        <span class="input-group-btn">
                            <button class="btn btn-default btn-sm" name="search_submit" type="submit">검색!</button>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="box-search">
            <div class="row">
                <div class="col-md-11">
                    <div class="form-horizontal">
                    <?php
                    if (element('search_list', element('get_member_group_post_list', element('data', element('list', $view))))){
                        $checkbox_content=""; 
                        $i=0;
                        
                        foreach(element('search_list', element('get_member_group_post_list', element('data', element('list', $view)))) as $key => $value){
                            
                            $checked="";
                            foreach($value as $key_ =>$value_){
                                if(empty($this->input->get('post_id_')) || in_array($key_,$this->input->get('post_id_'))) $checked='checked';
                            }
                            $checkbox_content.= '<div class="form-group">';
                            $checkbox_content.= '<label for="'.$key.'" class="col-sm-2 control-label checkbox-inline" style="color:#ec5956;text-align:center;font-weight:bold;"><input type="checkbox"  data-chkkey="chk'.$key.'" name="chkall" id="'.$key.'" '.$checked.'/>'.html_escape(element($key,element('member_group_name_list', element('get_member_group_post_list', element('data', element('list', $view)))))).'</label>';
                            $checkbox_content.= '<div class="col-sm-10" style="text-align:left">';

                            foreach($value as $key_ => $value_){

                            $checked="";
                            if(empty($this->input->get('post_id_')) || in_array($key_,$this->input->get('post_id_'))) $checked='checked';
                            $checkbox_content .= '<label for="post_id_' . $i . '" class="checkbox-inline"><input type="checkbox" name="post_id_[]" id="post_id_' . $i . '" value="' . $key_ . '" class="chk'.$key.'" '.$checked.' /> ' . html_escape(element('post_title',$value_)) . ' </label> ';
                            $i++;
                            }
                            $checkbox_content.= '</div>';
                            $checkbox_content.= '</div>';
                        }
                        

                        echo $checkbox_content;
                    }
                    ?>


                    </div>
                </div>
                <div class="col-md-1">
                    <div class="input-group">
                        <span class="input-group-btn">
                            <button class="btn btn-outline btn-primary " name="search_submit" type="submit">적용!</button>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<script>
$(document).on('click', 'input[name="chkall"]', function() {
    var chkkey = $(this).data('chkkey');

    if($(this).is(":checked"))
        $('.'+chkkey).prop('checked', true) ;
    else 
        $('.'+chkkey).prop('checked', false) ;

});

$(document).on('click', '#allchkall', function() {

    if($(this).is(":checked")){
        $('input[name="chkall"]').prop('checked', true) ;
        $('input[name="post_id_[]"]').prop('checked', true) ;
    }
    else {
        $('input[name="chkall"]').prop('checked', false) ;
        $('input[name="post_id_[]"]').prop('checked', false) ;
    }

});
</script>