

document.write("<style type='text/css'>"+
"#foin_pageid:after{"+
    "content: '';"+
    "display: block;"+
    "clear: both;}"+
"#foin_pageid .layout{"+
    "float: left;"+
    "width: 32%;"+
    "height: 140px;"+
    "margin-right: 2%;"+
    "margin-bottom: 7px;}"+
"#foin_pageid .layout:nth-child(3n){"+
    "margin-right: 0;}"+
 "#foin_pageid .layout a img{"+
    "display: inline-block;"+
    "width: 100%;"+
    "height: 100px;"+
    "margin-bottom: 5px;}"+
 "#foin_pageid .layout .summary{"+
    "display: inline-block;"+
    "overflow: hidden;"+
    "width: 100%;"+
    "height: 30px;"+
    "line-height: 15px;"+
    "font-size: 13px;"+
    "text-align: center;}"+
"#foin_pageid .layout .summary a{"+
    "display: inline-block;"+
    "text-decoration: none;"+
    "color: #000;}"+
"</style>"+





"<div id='foin_pageid'>"+
    <?php
    if (element('link', $view)) {
        foreach (element('link', $view) as $result) {
    ?>
       "<div class='layout'>"+
        "<div class='thum'>"+
            "<a href='<?php echo element('media_click',$result); ?>' target='_blank'><img src='<?php echo element('aws_image_url',element('item',$result)) ?>' border='0'>"+
            "</a>"+
        "</div>"+
        "<div class='summary'><a href='<?php echo site_url(element('media_click',$result)); ?>'><?php echo element('cit_summary',element('item',$result)) ?></a></div>"+
    "</div>"+
    <?php
        }
    }
    ?>    
"</div>");



