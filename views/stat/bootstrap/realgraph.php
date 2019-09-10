<?php
$this->managelayout->add_css(element('view_skin_url', $layout) . '/css/style.css'); 
$this->managelayout->add_css(base_url('assets/css/datepicker3.css'));
$this->managelayout->add_js(base_url('assets/js/bootstrap-datepicker.js'));
$this->managelayout->add_js(base_url('assets/js/bootstrap-datepicker.kr.js'));
 ?>
<div class="box">
    <div class="box-table">
        <div class="box-table-header">
            <ul class="nav nav-pills">
                    <li role="presentation" ><a href="<?php echo site_url($this->pagedir); ?>">캠페인 목록</a></li>
                    <!-- <li role="presentation" class="active"><a href="<?php echo site_url($this->pagedir . '/realgraph'); ?>">실시간 그래프</a></li> -->
                    <li role="presentation"><a href="<?php echo site_url($this->pagedir . '/reallist'); ?>">실시간 리스트</a></li>
                    <li role="presentation"><a href="<?php echo site_url($this->pagedir . '/graph'); ?>">기간별 그래프</a></li>
                    <?php if (element('is_admin', $view)) { ?>
                    <li role="presentation"><a href="<?php echo site_url($this->pagedir . '/cleanlog'); ?>">로그삭제</a></li>
                    <?php } ?>
                </ul>
        </div>
        <div class="box-table-header">
            <form class="form-inline" name="flist" action="<?php echo current_url(); ?>" method="get" >
                <input type="hidden" name="datetype" value="<?php echo html_escape($this->input->get('datetype')); ?>" />
                <input type="hidden" name="datetime" value="<?php echo html_escape($this->input->get('datetime')); ?>" />
                <input type="hidden" name="sfield" value="<?php echo html_escape($this->input->get('sfield')); ?>" />
                <input type="hidden" name="skeyword" value="<?php echo html_escape($this->input->get('skeyword')); ?>" />
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
                    
                    <div class="btn-group" role="group" aria-label="...">
                        <button type="button" class="btn btn-success btn-sm" >실시간 시간별보기</button>
                        
                    </div>
                </div>
           
        </div>
        <div id="chart_div"></div>
        <div class="table-responsive">
            <div class="pull-right form-group">
                <label for="allchkall" class="checkbox-inline">
                    <input type="checkbox" name="allchkall" id="allchkall" value="1" /> 전체 선택
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
                            if (element('postlist', $view)){
                                $checkbox_content=""; 
                                $i=0;
                                
                                foreach(element('postlist', $view) as $key => $value){

                                    $checked="";
                                    foreach($value as $value_){
                                        if(empty($this->input->get('post_id_')) || in_array(element('post_id',$value_),$this->input->get('post_id_'))) $checked='checked';
                                    }
                                    $checkbox_content.= '<div class="form-group">';
                                    $checkbox_content.= '<label for="'.$key.'" class="col-sm-2 control-label checkbox-inline" style="color:#ec5956;text-align:center;font-weight:bold;"><input type="checkbox"  data-chkkey="chk'.$key.'" name="chkall" id="'.$key.'" '.$checked.'/>'.html_escape(element($key,$brd_name)).'</label>';
                                    $checkbox_content.= '<div class="col-sm-10" style="text-align:left">';

                                    foreach($value as $value_){
                                    $checked="";
                                    if(empty($this->input->get('post_id_')) || in_array(element('post_id',$value_),$this->input->get('post_id_'))) $checked='checked';
                                    $checkbox_content .= '<label for="post_id_' . $i . '" class="checkbox-inline"><input type="checkbox" name="post_id_[]" id="post_id_' . $i . '" value="' . element('post_id',$value_) . '" class="chk'.$key.'" '.$checked.' /> ' . html_escape(element('post_title',$value_)) . ' </label> ';
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
            <table class="table table-hover table-striped table-bordered">
                <colgroup>
                    <col class="col-md-2">
                    <col class="col-md-1">
                    <col class="col-md-1">
                    <col class="col-md-1">
                    <col class="col-md-1">
                    <col class="col-md-6">
                </colgroup>
                <thead>
                    <tr>
                        <th>일시</th>
                        <th>노출수</th>
                        <th>클릭수</th>
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
                        <td><?php echo $key; ?><?php echo ($this->input->get('datetype') === 'h') ?' 시':'';?></td>
                        <td><?php echo element('count', $result, 0); ?></td>
                        <td><?php echo element('hit_count', $result, 0); ?></td>
                        <td><?php if(!empty(element('count', $result, 0))) echo round((element('hit_count', $result, 0)/element('count', $result, 0)*100),2); ?>%</td>
                        <td><?php echo element('s_rate', $result, 0); ?>%</td>
                        <td>
                            <div class="progress">
                                <div class="progress-bar progress-bar-warning progress-bar-striped" role="progressbar" aria-valuenow="<?php echo element('s_rate', $result, 0); ?>" aria-valuemin="0" aria-valuemax="<?php echo element('max_value', $view, 0); ?>" style="width: <?php echo element('s_rate', $result, 0); ?>%">
                                    <span class="sr-only"><?php echo element('s_rate', $result, 0); ?>%</span>
                                </div>
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
                            <td><?php echo element('sum_count', $view, 0); ?></td>
                            <td><?php echo element('hit_sum_count', $view, 0); ?></td>
                            <td><?php if(!empty(element('sum_count', $view, 0))) echo round((element('hit_sum_count', $view, 0)/element('sum_count', $view, 0)*100),2); ?>%</td>
                            <td></td>
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

    data.addColumn('string', '기간');
    data.addColumn('number', '노출 수');
    data.addColumn('number', '클릭 수');
    
    data.addRows([
        <?php
        if (element('list', $view)) {
            foreach (element('list', $view) as $key => $result) {

        ?>
        ['<?php echo $key; ?>',<?php echo element('count', $result, 0); ?>,<?php echo element('hit_count', $result, 0); ?>],
        <?php
            }
        }
        ?>
    ]);

    var chart = new google.visualization.ComboChart(document.getElementById('chart_div'));

    chart.draw(data, {
        width: '100%', height: '400',
        legendTextStyle: {fontName: 'gulim', fontSize: '12'},
        tooltipTextStyle: {color: '#006679', fontName: 'dotum', fontSize: '12'},
        hAxis: {textStyle: {color: '#959595', fontName: 'dotum', fontSize: '12'}},
        vAxis: {textStyle: {color: '#959595', fontName: 'dotum', fontSize: '12'}, gridlineColor: '#e1e1e1', baselineColor: '#e1e1e1', textPosition: 'out'},
        series: {0: {targetAxisIndex:0},
                   1:{targetAxisIndex:1,type: 'bars'},
                  },
        lineWidth: 3,
        pointSize: 5,
        vAxes: {
            // Adds titles to each axis.
            0: {title: '노출 수'},
            1: {title: '클릭 수'}
          }
    });


    

    
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
    exporturl = '<?php echo site_url($this->pagedir . '/graph/excel' . '?' . $this->input->server('QUERY_STRING', null, '')); ?>';
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
