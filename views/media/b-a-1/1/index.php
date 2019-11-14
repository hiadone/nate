<?php  

    $config = array(
            'skin' => 'bootstrap',
            'brd_key' => element('brd_key', $view),
            'post_id' => element('post_id', $view),
            'cache_minute' => 0,

        );
      
           echo $this->board->media_iframe($config);
      
?>

