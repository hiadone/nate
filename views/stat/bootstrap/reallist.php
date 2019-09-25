<?php $this->managelayout->add_css(element('view_skin_url', $layout) . '/css/style.css'); ?>
<div class="box">
    <div class="box-table">
        <?php
        echo show_alert_message($this->session->flashdata('message'), '<div class="alert alert-auto-close alert-dismissible alert-info"><button type="button" class="close alertclose" >&times;</button>', '</div>');
        $attributes = array('class' => 'form-inline', 'name' => 'flist', 'id' => 'flist');
        echo form_open(current_full_url(), $attributes);

        ?>
        <input type="hidden" name="findex" value="<?php echo html_escape($this->input->get('findex')); ?>" />
        <?php  echo '<h4 class="highlight mb20">미디어 log</h4>'; ?>
            <div class="box-table-header">
                <ul class="nav nav-pills">
                    <li role="presentation" ><a href="<?php echo site_url($this->pagedir. '/lists/b-a-1'); ?>">캠페인 목록</a></li>
                    <li role="presentation" class="active"><a href="<?php echo site_url($this->pagedir . '/view_log/b-a-1?'.$this->param->output()); ?>">실시간 리스트</a></li>
                    <li role="presentation"><a href="<?php echo site_url($this->pagedir . '/graph/b-a-1?'.$this->param->output()); ?>">기간별 그래프</a></li>
                    <?php if (element('is_admin', $view)) { ?>
                    <li role="presentation"><a href="<?php echo site_url($this->pagedir . '/cleanlog/b-a-1?'.$this->param->output()); ?>">로그삭제</a></li>
                    <?php } ?>
                </ul>
                <?php
                ob_start();
                ?>
                    <div class="btn-group pull-right" role="group" aria-label="...">
                        <a href="<?php echo element('listall_url', $view).'?'.$this->param->replace('post_id_[]') ?>" class="btn btn-outline btn-default btn-sm">전체목록</a>
                        <!-- <button type="button" class="btn btn-outline btn-default btn-sm btn-list-delete btn-list-selected disabled" data-list-delete-url = "<?php echo element('list_delete_url', $view); ?>" >선택삭제</button> -->
                    </div>
                <?php
                $buttons = ob_get_contents();
                ob_end_flush();

                ?>

                <div class="pull-right mr10">
                    <ul class="nav nav-tabs clearfix">
                        <li role="presentation" <?php if ($this->uri->segment(2) === 'view_log') { ?>class="active" <?php } ?>><a href="<?php echo site_url($this->pagedir . '/view_log/b-a-1?'.$this->param->output()); ?>" >미디어 view log</a></li>
                        <li role="presentation" <?php if ($this->uri->segment(2) === 'click_log') { ?>class="active" <?php } ?>><a href="<?php echo site_url($this->pagedir . '/click_log/b-a-1?'.$this->param->output()); ?>">미디어 click log</a></li>

                    </ul>
                </div>
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

            <?php
            if(element('campaign_multi', $view)){
                $multi_code = explode(',',element('campaign_multi', $view));
            
            ?>
            <div class="col-md-12 mb20">
                <ul class="nav nav-tabs ">
                    <li role="presentation" <?php if ( ! $this->input->get('multi_code')) { ?>class="active" <?php } ?>><a href="<?php echo current_url().'?'.$this->param->replace('multi_code');?>&multi_code=">매체 전체</a></li>
                    <?php
                    
                    if ($multi_code) {
                        foreach ($multi_code as $mkey => $mval) {
                            if(empty($mval)) continue;
                    ?>
                        <li role="presentation" <?php if ($this->input->get('multi_code') === $mval) { ?>class="active" <?php } ?>><a href="<?php echo current_url().'?'.$this->param->replace('multi_code').'&multi_code='.$mval; ?>"><?php echo html_escape($mval); ?></a></li>
                    <?php
                        }
                    }
                    ?>
                </ul>
            </div>
            <?php } ?>
            <div class="row">전체 : <?php echo element('total_rows', element('data', $view), 0); ?>건</div>
            <div class="table-responsive">
                <table class="table table-hover table-striped table-bordered">
                    <thead>
                        <tr>
                            <th><a href="<?php echo element('pl_id', element('sort', $view)); ?>">번호</a></th>
                            <th>제목</th>                            
                            <th>일시</th>
                            <th>IP</th>
                            <th>OS</th>
                            <th>referrer</th>
                            <!-- <th><input type="checkbox" name="chkall" id="chkall" /></th> -->
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    if (element('list', element('data', $view))) {
                        foreach (element('list', element('data', $view)) as $result) {

                    ?>
                        <tr>
                            <td><?php echo number_format(element('num', $result)); ?></td>
                            <td><?php echo html_escape(element('post_title', $result)); ?><a href="<?php echo goto_url(element('post_url', $result)); ?>" target="_blank"><span class="fa fa-external-link"></span></a></td>
                            
                            <td><?php echo display_datetime(element('display_datetime', $result), 'full'); ?></td>
                            <!-- <td><?php echo element('pl_hit', $result) ? element('pl_hit', $result) : ''; ?></td> -->
                            
                            <td><?php echo element('display_ip', $result); ?></td>
                            <td><?php echo element('os', $result); ?></td>
                            <td><?php echo element('referrer', $result); ?></td>
                            <!-- <td><input type="checkbox" name="chk[]" class="list-chkbox" value="<?php echo element(element('primary_key', $view), $result); ?>" /></td> -->
                        </tr>
                    <?php
                        }
                    }
                    if ( ! element('list', element('data', $view))) {
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
                <?php echo element('paging', $view); ?>
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
                        <?php echo element('search_option', $view); ?>
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
                    if (element('search_list', element('get_member_group_post_list', element('data', $view)))){
                        $checkbox_content=""; 
                        $i=0;
                        
                        foreach(element('search_list', element('get_member_group_post_list', element('data', $view))) as $key => $value){
                            
                            $checked="";
                            foreach($value as $key_ =>$value_){
                                if(empty($this->input->get('post_id_')) || in_array($key_,$this->input->get('post_id_'))) $checked='checked';
                            }
                            $checkbox_content.= '<div class="form-group">';
                            $checkbox_content.= '<label for="'.$key.'" class="col-sm-2 control-label checkbox-inline" style="color:#ec5956;text-align:center;font-weight:bold;"><input type="checkbox"  data-chkkey="chk'.$key.'" name="chkall" id="'.$key.'" '.$checked.'/>'.html_escape(element($key,element('member_group_name_list', element('get_member_group_post_list', element('data', $view))))).'</label>';
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