<?php
$this->managelayout->add_css(element('view_skin_url', $layout) . '/css/style.css'); 
$this->managelayout->add_css(base_url('assets/css/datepicker3.css'));
$this->managelayout->add_js(base_url('assets/js/bootstrap-datepicker.js'));
$this->managelayout->add_js(base_url('assets/js/bootstrap-datepicker.kr.js'));


$color_array = array('red','orange','yellow','green','blue','purple','pink','brown','gold','silver','black','gray');
 ?>
<div class="box">
    <div class="box-table">
    <?php  echo '<h4 class="highlight mb20">미디어 통계</h4>'; ?>
        <div class="box-table-header">
            <ul class="nav nav-pills">
                    <li role="presentation" ><a href="<?php echo site_url($this->pagedir.'/lists/b-a-1?'.$this->param->output()); ?>'); ?>">캠페인 목록</a></li>
                    <li role="presentation"><a href="<?php echo site_url($this->pagedir . '/view_log/b-a-1?'.$this->param->output()); ?>">실시간 리스트</a></li>
                    <li role="presentation" class="active"><a href="<?php echo site_url($this->pagedir . '/graph/b-a-1?'.$this->param->output()); ?>">기간별 그래프</a></li>
                    <?php if (element('is_admin', $view)) { ?>
                    <li role="presentation"><a href="<?php echo site_url($this->pagedir . '/cleanlog/b-a-1?'.$this->param->output()); ?>">로그삭제</a></li>
                    <?php } ?>
                </ul>
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
            
        <div class="box-table-header">
            <form class="form-inline" name="flist" action="<?php echo current_url(); ?>" method="get" >
                <input type="hidden" name="datetype" value="<?php echo html_escape($this->input->get('datetype')); ?>" />
                <input type="hidden" name="datetime" value="<?php echo html_escape($this->input->get('datetime')); ?>" />
                <input type="hidden" name="sfield" value="<?php echo html_escape($this->input->get('sfield')); ?>" />
                <input type="hidden" name="skeyword" value="<?php echo html_escape($this->input->get('skeyword')); ?>" />
                <?php 
                if($this->input->get('post_id_')){
                    foreach($this->input->get('post_id_') as $value){
                        echo '<input type="hidden" name="post_id_[]" value="'.$value.'" />';
                    }
                }
                 ?>                
                <div class="box-table-button">
                    <?php if (element('boardlist', $view)) { 
                        $brd_name="";
                    ?>
                        <span class="mr10">
                            <select name="brd_id" class="form-control" onChange="location.href='<?php echo current_url(); ?>?brd_id=' + this.value;">
                                <option value="">전체게시판</option>
                                <?php foreach (element('boardlist', $view) as $key => $value) { 
                                    $brd_name[element('brd_id', $value)]=element('brd_name', $value);
                                ?>
                                    <option value="<?php echo element('brd_id', $value); ?>" <?php echo set_select('brd_id', element('brd_id', $value), ($this->input->get('brd_id') === element('brd_id', $value) ? true : false)); ?>><?php echo html_escape(element('brd_name', $value)); ?></option>
                                <?php } ?>
                            </select>
                        </span>
                    <?php } ?>
                    <span class="mr10">
                        기간 : <input type="text" class="form-control input-small datepicker"  name="start_date" value="<?php echo element('start_date', $view); ?>" readonly="readonly" /> - <input type="text" class="form-control input-small datepicker" name="end_date" value="<?php echo element('end_date', $view); ?>" readonly="readonly" />
                    </span>
                    <div class="btn-group" role="group" aria-label="...">
                      <!--   <button type="button" class="btn <?php echo ($this->input->get('datetype') === 'domain') ? 'btn-success' : 'btn-default'; ?> btn-sm" onclick="fdate_submit('domain');">유입 도메인별</button> -->
                        <button type="button" class="btn <?php echo ($this->input->get('datetype') === 'week') ? 'btn-success' : 'btn-default'; ?> btn-sm" onclick="fdate_submit('week');">요일별</button>
                        <button type="button" class="btn <?php echo ($this->input->get('datetype') === 'h') ? 'btn-success' : 'btn-default'; ?> btn-sm" onclick="fdate_submit('h');">시간별</button>
                        <button type="button" class="btn <?php echo ($this->input->get('datetype') !== 'h' && $this->input->get('datetype') !== 'y' && $this->input->get('datetype') !== 'm' && $this->input->get('datetype') !== 'week' && $this->input->get('datetype') !== 'domain') ? 'btn-success' : 'btn-default'; ?> btn-sm" onclick="fdate_submit('d');">일별</button>
                        <button type="button" class="btn <?php echo ($this->input->get('datetype') === 'm') ? 'btn-success' : 'btn-default'; ?> btn-sm" onclick="fdate_submit('m');">월별</button>
                        <button type="button" class="btn <?php echo ($this->input->get('datetype') === 'y') ? 'btn-success' : 'btn-default'; ?> btn-sm" onclick="fdate_submit('y');">년별</button>
                    </div>
                </div>
           
            </div>
            <div id="chart_div"></div>
            <div class="table-responsive">
                <div class="pull-right form-group">
                    <label for="allchkall" class="checkbox-inline">
                        <input type="checkbox" name="allchkall" id="allchkall" value="1" checked="checked" /> 전체 선택
                    </label>
                    <label for="withoutzero" class="checkbox-inline">
                        <input type="checkbox" name="withoutzero" id="withoutzero" value="1" /> 클릭수가 0 인 데이터 제외
                    </label>
                    <label for="orderdesc" class="checkbox-inline">
                        <input type="checkbox" name="orderdesc" id="orderdesc" value="1"/> 역순으로보기
                    </label>
                </div>
           
            
                <div class="box-search">
                    <div class="row">
                        <div class="col-md-11">
                            <div class="form-horizontal">
                            <?php
                            if (element('search_list', element('get_member_group_post_list', $view))){
                                $checkbox_content=""; 
                                $i=0;
                                
                                foreach(element('search_list', element('get_member_group_post_list', $view)) as $key => $value){
                                    
                                    $checked="";
                                    foreach($value as $key_ =>$value_){
                                        if(empty($this->input->get('post_id_')) || in_array($key_,$this->input->get('post_id_'))) $checked='checked';
                                    }
                                    $checkbox_content.= '<div class="form-group">';
                                    $checkbox_content.= '<label for="'.$key.'" class="col-sm-2 control-label checkbox-inline" style="color:#ec5956;text-align:center;font-weight:bold;"><input type="checkbox"  data-chkkey="chk'.$key.'" name="chkall" id="'.$key.'" '.$checked.'/>'.html_escape(element($key,element('member_group_name_list', element('get_member_group_post_list', $view)))).'</label>';
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
            <?php
            $progress_color=array();
            if (element('list',element('link', $view))) {
                echo '<div class="form-group">';                
                foreach (element('list',element('link', $view)) as $result) {
                    $progress_color[element('pln_id',$result)] = array_pop($color_array);

                    echo '<label for="allchkall" class="checkbox-inline"><span style="width:20px;height:15px;background-color:'.$progress_color[element('pln_id',$result)].';display:inline-block;"></span> '.html_escape(element('cit_summary',$result)).'</label>';

                }
                echo '</div>';
            }

            ?>



            <table class="table table-hover table-striped table-bordered">
                <colgroup>
                    <col class="col-md-1">
                    <col class="col-md-1">
                    <col class="col-md-1">
                    <col class="col-md-1">
                    <col class="col-md-1">
                    <col class="col-md-7">
                </colgroup>
                <thead>
                    <tr>
                        <th><?php echo $this->input->get('datetype', null, 'd')==='domain'? '도메인':'일시'; ?></th>
                        <th>미디어 view count</th>
                        <th>미디어 click count</th>
                        <th>클릭률</th>
                        <th>점유율</th>
                        <th>그래프</th>
                    </tr>
                </thead>
                <tbody class="graphlist">
                <?php
                if (element('list', $view)) {
                    foreach (element('list', $view) as $key => $result) {

                ?>
                    <tr class="<?php echo ( ! element('count', $result)) ? 'zerodata' : ''; ?>">
                        <td <?php if($this->input->get('datetype')==='d' || ($this->input->get('datetype') !== 'h' && $this->input->get('datetype') !== 'y' && $this->input->get('datetype') !== 'm' && $this->input->get('datetype') !== 'week' && $this->input->get('datetype') !== 'domain')) echo 'onclick="fdate_submit(\'h\',\''.$key.'\');" style="cursor:pointer" '; ?>><?php 
                            if($this->input->get('datetype') === 'week'){
                                echo element($key, element('week_korean', $view)); 
                            } elseif($this->input->get('datetype') === 'h') {
                                echo $key.' 시';
                            } else {
                                echo $key;
                            }
                            ?>
                        </td>
                        
                        <td><?php echo number_format(element('count', $result, 0)); ?></td>
                        <td><?php echo number_format(element('hit_count', $result, 0)); ?></td>
                        <td><?php if(!empty(element('count', $result, 0))) echo round((element('hit_count', $result, 0)/element('count', $result, 0)*100),2); ?>%</td>
                        <td><?php echo element('s_rate', $result, 0); ?>%</td>
                        <td>
                           
                            <div class="progress">

                                
                                <?php 
                                if(element('pln_cnt',$result)){
                                    foreach(element('pln_cnt',$result) as $key_ => $value_){ 

                                     
                                ?>
                                
                                <div class="progress-bar progress-bar-striped" role="progressbar" aria-valuenow="<?php echo ($value_ /element('pln_cnt_sum',$result) * element('s_rate', $result, 0)) ?>" aria-valuemin="0" aria-valuemax="<?php echo element('max_value', $view, 0); ?>" style="width: <?php echo ($value_ /element('pln_cnt_sum',$result) * element('s_rate', $result, 0)) ?>%;background-color:<?php echo !empty($progress_color[$key_]) ? $progress_color[$key_] : '' ?>;">
                                    <span class=""><?php echo $value_ ?></span>
                                </div>
                                <?php 
                                    }
                                } else { 
                                ?>
                                    <div class="progress">
                                        <div class="progress-bar progress-bar-warning progress-bar-striped" role="progressbar" aria-valuenow="<?php echo element('s_rate', $result, 0); ?>" aria-valuemin="0" aria-valuemax="<?php echo element('max_value', $view, 0); ?>" style="width: <?php echo element('s_rate', $result, 0); ?>%">
                                            <span class="sr-only"><?php echo element('s_rate', $result, 0); ?>%</span>
                                        </div>
                                    </div>

                                <?php 
                                }
                                ?>
                            </div>
                        </td>
                    </tr>
                <?php
                    }
                }
                ?>
                </tbody>
                <?php
                if (element('list', $view)) {
                ?>
                    <tfoot>
                        <tr class="warning">
                            <td>전체</td>
                            <td><?php echo number_format(element('sum_count', $view, 0)); ?></td>
                            <td><?php echo number_format(element('hit_sum_count', $view, 0)); ?></td>
                            
                            <td><?php if(!empty(element('sum_count', $view, 0))) echo round((element('hit_sum_count', $view, 0)/element('sum_count', $view, 0)*100),2); ?>%</td>
                            <td>100%</td>
                            <td>
                           
                            <div class="progress">

                                
                                <?php 
                                if(element('hit_sum_count_sub',$view)){
                                    foreach(element('hit_sum_count_sub',$view) as $key_ => $value_){ 

                                ?>
                                
                                <div class="progress-bar progress-bar-striped" role="progressbar" aria-valuenow="<?php echo ($value_ /element('hit_sum_count',$view) * 100) ?>" aria-valuemin="0" aria-valuemax="<?php echo element('max_value', $view, 0); ?>" style="width: <?php echo ($value_ /element('hit_sum_count',$view) * 100) ?>%;background-color:<?php echo !empty($progress_color[$key_]) ? $progress_color[$key_] : '' ?>;">
                                    <span class=""><?php echo $value_ ?></span>
                                </div>
                                <?php 
                                    }
                                }
                                ?>
                            </div>
                        </td>
                        </tr>
                    </tfoot>
                <?php
                }
                ?>
            </table>
        </div>
        <div class="box-info">
            <div class="btn-group pull-right" role="group" aria-label="...">
                <button type="button" class="btn btn-outline btn-success btn-sm" id="export_to_excel"><i class="fa fa-file-excel-o"></i> 엑셀 다운로드</button>
            </div>            
        </div>
    </div>
</div>

<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">

// Load the Visualization API and the corechart package.
google.charts.load('current', {'packages':['corechart']});

// Set a callback to run when the Google Visualization API is loaded.
google.charts.setOnLoadCallback(drawChart);

// Callback that creates and populates a data table,
// instantiates the pie chart, passes in the data and
// draws it.
function drawChart() {

    var data = new google.visualization.DataTable();

    <?php 
    if($this->input->get('datetype') === 'domain'){
     ?>
        data.addColumn('string', '도메인');    
        data.addColumn('number', '비율');
        data.addRows([
            <?php
           
            $sum = 0;
            if (element('list', $view)) {
                 $i=0;
                foreach (element('list', $view) as $key => $result) {
                $i++;
                $sum += element('count', $result, 0);
                if ($i > 8) break;
            ?>
               
            ['<?php echo html_escape(element('key', $result));?>',<?php echo element('count', $result, 0); ?>],
            <?php
                }
            }

            
            if (element('sum_count', $view) && $sum && $sum < element('sum_count', $view)) {
            ?>
                ['기타',<?php echo element('sum_count', $view, 0) - $sum; ?>],
            <?php
            }
            
            ?>
        ]);

       
        var chart = new google.visualization.PieChart(document.getElementById('chart_div'));
     
     
     <?php } else {?>

        data.addColumn('string', '기간');    
        
        <?php 
        if (element('list',element('link', $view))) {
            foreach (element('list',element('link', $view)) as $result) {
            $cit_summary = array();
            $cit_summary = explode("\n",element('cit_summary',$result));     
         ?>
        data.addColumn('number', '<?php echo empty($cit_summary[1]) ? html_escape(element('cit_summary',$result)): html_escape($cit_summary[1])?> click');        
            <?php } ?>
        <?php } else { ?>
            data.addColumn('number', '미디어 click count');
        <?php } ?>
        data.addColumn('number', '미디어 view');
        data.addRows([
            <?php
            if (element('list', $view)) {
                foreach (element('list', $view) as $key => $result) {
            ?>
               
                    ['<?php if($this->input->get('datetype') === 'week') echo element($key, element('week_korean', $view)); 
                        else echo $key;
                    ?>',
                    <?php 
                    
                    if(element('list',element('link', $view))) {
                        foreach (element('list',element('link', $view)) as $key_ => $result_) {
                            echo element(element('pln_id',$result_),element('pln_cnt',$result)) ? element(element('pln_id',$result_),element('pln_cnt',$result)) : 0;
                            echo ',';
                        }
                        echo element('count', $result, 0).',';
                    } else { 

                        echo element('hit_count', $result, 0).',';
                        echo element('count', $result, 0).',';
                    }                    
                    echo '],';
                }
            }
            ?>
        ]);

        
        
        var chart = new google.visualization.ComboChart(document.getElementById('chart_div'));
        

    <?php } ?>

    chart.draw(data, {
        width: '100%', height: '400',
        legendTextStyle: {fontName: 'gulim', fontSize: '12'},
        tooltipTextStyle: {color: '#006679', fontName: 'dotum', fontSize: '12'},
        hAxis: {textStyle: {color: '#959595', fontName: 'dotum', fontSize: '12'}},
        vAxis: {textStyle: {color: '#959595', fontName: 'dotum', fontSize: '12'}, gridlineColor: '#e1e1e1', baselineColor: '#e1e1e1', textPosition: 'out'},
        series: {   
            <?php 
            $i=0;
            foreach($progress_color as  $value){
                echo $i .": { color: '".$value."' },";
                $i++;
            }

             ?>
            
          
                   <?php echo element('list',element('link', $view)) ? count(element('list',element('link', $view))) : 1; ?>:{targetAxisIndex:1,type: 'line'},
                  },
        seriesType: 'bars',
        lineWidth: 3,
        pointSize: 5,
        isStacked: true,
        vAxes: {
            // Adds titles to each axis.
            
            0: {title: '미디어 click count'},
            1: {title: '미디어 view count'}
          }
    });


    google.visualization.events.addListener(chart, 'select', selectHandler);

    function selectHandler() {

          var datetime = data.getValue(chart.getSelection()[0].row, 0);
        <?php if($this->input->get('datetype') !== 'y' && $this->input->get('datetype') !== 'm' && $this->input->get('datetype') !== 'h'){?>           
            fdate_submit('h',datetime);
        <?php } elseif($this->input->get('datetype') === 'h'){?>
            //fdate_submit('h',datetime);
        <?php } ?>

        
        // var selection = chart.getSelection();
        // var message = '';
        // for (var i = 0; i < selection.length; i++) {
        // var item = selection[i];
        // if (item.row != null && item.column != null) {
        // var str = data.getFormattedValue(item.row, item.column);
        // var category = data
        // .getValue(chart.getSelection()[0].row, 0)
        // var type
        // if (item.column == 1) {
        // type = "sale";
        // } else if(item.column == 2){
        // type = "Expense";
        // }else{
        // type = "Profit";
        // }
        // message += '{row:' + item.row + ',column:' + item.column
        // + '} = ' + str + '  The Category is:' + category
        // + ' it belongs to : ' + type + '\n';
        // } else if (item.row != null) {
        // var str = data.getFormattedValue(item.row, 0);
        // message += '{row:' + item.row
        // + ', column:none}; value (col 0) = ' + str
        // + '  The Category is:' + category + '\n';
        // } else if (item.column != null) {
        // var str = data.getFormattedValue(0, item.column);
        // message += '{row:none, column:' + item.column
        // + '}; value (row 0) = ' + str
        // + '  The Category is:' + category + '\n';
        // }
        // }
        // if (message == '') {
        // message = 'nothing';
        // }
        // alert('You selected ' + message);

    }
}



$(document).on('change', '#withoutzero', function(){
    if (this.checked) {
        $('.zerodata').hide();
    } else {
        $('.zerodata').show();
    }
})

//$('#withoutzero').click();
$(document).on('change', '#orderdesc', function(){
    var $body = $('tbody.graphlist');
    var list = $body.children('tr');
    $body.html(list.get().reverse());
})
$(document).on('click', '#export_to_excel', function(){
    exporturl = '<?php echo site_url(uri_string() . '/excel' . '?' . $this->input->server('QUERY_STRING', null, '')); ?>';
    if ($('#withoutzero:checked').length)
    {
        exporturl += '&withoutzero=1';
    }
    if ($('#orderdesc:checked').length)
    {
        exporturl += '&orderby=desc';
    }
    document.location.href = exporturl;
})




function fdate_submit(datetype,datetime)
{
    var f = document.flist;
    f.datetype.value = datetype;
    if(datetime) f.datetime.value = datetime;
    else f.datetime.value = '';
    f.submit();
}

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