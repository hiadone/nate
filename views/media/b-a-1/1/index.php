<?php  

    $config = array(
            'skin' => 'bootstrap',
            'brd_key' => element('brd_key', $board),
            'post_id' => element('post_id', $post),
            
        );
        if($type==='iframe')
           echo $this->board->media_iframe($config);
        elseif($type==='script')
           echo $this->board->media_script($config);
?>

