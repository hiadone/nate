<div class="modal-header">
    <h4 class="modal-title">상품 리스트</h4>
</div>
<div class="modal-body">
    <div class="box">
        <div class="box-table">
            <?php
            echo show_alert_message($this->session->flashdata('message'), '<div class="alert alert-auto-close alert-dismissible alert-info">', '</div>');
            ?>
            <div class="">전체 : <?php echo element('total_rows', element('data', $view), 0); ?>건</div>
            <div class="col-md-12">
                <div class=" searchbox">
                    <form class="navbar-form navbar-right pull-right" action="<?php echo current_full_url(); ?>" onSubmit="return postSearch(this);">
                        <input type="hidden" name="findex" value="<?php echo html_escape($this->input->get('findex')); ?>" />
                        <input type="hidden" name="category_id" value="<?php echo html_escape($this->input->get('category_id')); ?>" />
                        <div class="form-group">
                            <select class="form-control pull-left px100" name="sfield">
                               
                                <option value="cit_name" <?php echo ($this->input->get('sfield') === 'cit_name') ? ' selected="selected" ' : ''; ?>>상품명</option>
                               
                            </select>
                            <input type="text" class="form-control px150" placeholder="Search" name="skeyword" value="<?php echo html_escape($this->input->get('skeyword')); ?>" />
                            <button class="btn btn-primary btn-sm" type="submit"><i class="fa fa-search"></i></button>
                        </div>
                    </form>
                </div>
            </div>
            <script type="text/javascript">
            //<![CDATA[
            function postSearch(f) {
                var skeyword = f.skeyword.value.replace(/(^\s*)|(\s*$)/g,'');
                if (skeyword.length < 2) {
                    alert('2글자 이상으로 검색해 주세요');
                    f.skeyword.focus();
                    return false;
                }
                return true;
            }
            
            $('.btn-point-info').popover({
                template: '<div class="popover" role="tooltip"><div class="arrow"></div><div class="popover-title"></div><div class="popover-content"></div></div>',
                html : true
            });
            //]]>
            </script>
            <div class="">
                <table class="table table-hover table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>순번</th>
                            <th>카테고리</th>
                            <th>이미지</th>
                            <th><a href="<?php echo element('cit_name', element('sort', $view)); ?>">상품명</a></th>
                            <th><a href="<?php echo element('cit_summary', element('sort', $view)); ?>">상품요약</a></th>
                            <th><a href="<?php echo element('cit_price', element('sort', $view)); ?>">판매가격</a></th>
                            <th>action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    if (element('list', element('data', $view))) {
                        foreach (element('list', element('data', $view)) as $result) {
                    ?>
                        <tr>
                            <td><?php echo element('num', $result); ?></td>
                            <td style="width:130px;">
                                <?php foreach (element('category', $result) as $cv) { echo '<label class="label label-info">' . html_escape(element('cca_value', $cv)) . '</label> ';} ?>
                                <?php if (element('cit_type1', $result)) { ?><label class="label label-danger">추천</label> <?php } ?>
                                <?php if (element('cit_type2', $result)) { ?><label class="label label-warning">인기</label> <?php } ?>
                                <?php if (element('cit_type3', $result)) { ?><label class="label label-default">신상품</label> <?php } ?>
                                <?php if (element('cit_type4', $result)) { ?><label class="label label-primary">할인</label> <?php } ?>
                            </td>
                            <td>
                                <?php if (element('cit_file_1', $result)) {?>
                                    <a href="<?php echo goto_url(html_escape(element('cit_shopping_url', $result))); ?>" target="_blank">
                                        <img src="<?php echo thumb_url('cmallitem', element('cit_file_1', $result), 80); ?>" alt="<?php echo html_escape(element('cit_name', $result)); ?>" title="<?php echo html_escape(element('cit_name', $result)); ?>" class="thumbnail mg0" style="width:80px;" />
                                    </a>
                                <?php } ?>
                            </td>
                            <td><?php echo html_escape(element('cit_name', $result)); ?></td>
                            <td><?php echo html_escape(element('cit_summary', $result)); ?></td>
                            <td><?php echo html_escape(element('cit_price', $result)); ?></td>
                            <td><button class="btn btn-default btn-xs cit_shopping_url" data-cit_shopping_url="<?php echo element('cit_shopping_url', $result); ?>" onClick="add();">선택</button></td>
                        </tr>
                    <?php
                        }
                    }
                    if ( ! element('list', element('data', $view))) {
                    ?>
                        <tr>
                            <td colspan="14" class="nopost">자료가 없습니다</td>
                        </tr>
                    <?php
                    }
                    ?>
                    </tbody>
                </table>
            </div>
            <div class="box-info">
                <?php echo element('paging', $view); ?>
            </div>
            
        </div>
        
    </div>
</div>
<script type="text/javascript">
//<![CDATA[


$(document).on('click', '.cit_shopping_url', function() {
    opener.document.getElementById('<?php echo urldecode(element('element_id', $view)); ?>').value = $(this).data('cit_shopping_url');    
});

//]]>
</script>
