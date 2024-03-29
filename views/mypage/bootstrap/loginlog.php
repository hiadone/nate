<?php $this->managelayout->add_css(element('view_skin_url', $layout) . '/css/style.css'); ?>

<div class="mypage">
	<ul class="nav nav-tabs">
		<li><a href="<?php echo site_url('mypage'); ?>" title="마이페이지">마이페이지</a></li>

		<?php if ($this->cbconfig->item('use_point')) { ?>
			<li><a href="<?php echo site_url('mypage/point'); ?>" title="포인트">포인트</a></li>
		<?php } ?>
		<li class="active"><a href="<?php echo site_url('mypage/loginlog'); ?>" title="나의 로그인기록">로그인기록</a></li>
		<li><a href="<?php echo site_url('membermodify'); ?>" title="정보수정">정보수정</a></li>
		<li><a href="<?php echo site_url('membermodify/memberleave'); ?>" title="탈퇴하기">탈퇴하기</a></li>
	</ul>
	<div class="page-header">
		<h4>로그인 기록</h4>
	</div>
	<table class="table table-striped">
		<thead>
		<tr>
			<th>로그인여부</th>
			<th>IP</th>
			<th>OS</th>
			<th>Browser</th>
			<th>날짜</th>
		</tr>
		</thead>
		<tbody>
		<?php
		if (element('list', element('data', $view))) {
			foreach (element('list', element('data', $view)) as $result) {
		?>
			<tr>
				<td><?php echo element('mll_success', $result) === '1' ? "<span class=\"label label-success\">로그인성공</span>":"<span class=\"label label-danger\">로그인실패</span>"; ?></td>
				<td><?php echo html_escape(element('mll_ip', $result)); ?></td>
				<td><?php echo html_escape(element('os', $result)); ?></td>
				<td><?php echo html_escape(element('browsername', $result)); ?> <?php echo html_escape(element('browserversion', $result)); ?> <?php echo html_escape(element('engine', $result)); ?></td>
				<td><?php echo display_datetime(element('mll_datetime', $result), 'full'); ?></td>
			</tr>
		<?php
			}
		}
		if ( ! element('list', element('data', $view))) {
		?>
			<tr>
				<td colspan="5" class="nopost">로그인 기록이 없습니다</td>
			</tr>
		<?php
		}
		?>
		</tbody>
	</table>
	<nav><?php echo element('paging', $view); ?></nav>
</div>
