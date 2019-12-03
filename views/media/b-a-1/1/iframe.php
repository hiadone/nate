<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style type="text/css">
#foin_pageid:after{
    content: "";
    display: block;
    clear: both;
}
#foin_pageid {
    padding: 0 15px;
}
#foin_pageid .layout{
    float: left;
    width: 32%;
    height: 140px;
    margin-right: 2%;
    margin-bottom: 6px;
}
#foin_pageid .layout:nth-child(3n+1){
    margin-right: 0;
}
#foin_pageid .layout .thum {
    font-size: 0;
    text-align: center;
    background-color: #fff;
}
#foin_pageid .layout a img{
    display: inline-block;
    width: 100%;
    height: 100px;
    margin-bottom: 5px;
    object-fit: contain;
}
 #foin_pageid .layout .summary{
    display: inline-block;
    overflow: hidden;
    width: 100%;
    height: 30px;
    line-height: 15px;
    font-size: 13px;
    text-align: center;
}
#foin_pageid .layout .summary a{
    display: inline-block;
    text-decoration: none;
    color: #000;
    width: 100%;
}
</style>
<script>
function goLinkpageid(url)
{
     window.location.href=url;
}
</script>




<div id="foin_pageid">
    <div style="font-weight: bold;">TV 속 이 상품</div>
    <?php
    $i=0;
    if (element('link', $view)) {
        foreach (element('link', $view) as $result) {
        $cit_summary = array();
        $cit_summary = explode("\n",element('cit_summary',element('item',$result)));

    ?>
    <div class="layout">
        <div class="thum">
            <a href="<?php echo element('media_click',$result); ?>" target="_blank"><img src="<?php echo element('aws_image_url',element('item',$result)) ?>" border="0">
            </a>
        </div>
        <div class="summary">
            <a href="<?php echo element('media_click',$result); ?>" target="_blank">
                <?php if(count($cit_summary) > 1){ ?>
                    <span  style="overflow: hidden;white-space: nowrap;display:none;">
                    <<?php echo $cit_summary[0] ?>>
                    </span>
                    <div  style="overflow: hidden;white-space: nowrap;">
                    <<?php echo $cit_summary[0] ?>>
                    </div>
                    <span  style="overflow: hidden;white-space: nowrap;display:none;">
                    <?php echo $cit_summary[1] ?>
                    </span>
                    <div  style="overflow: hidden;white-space: nowrap;">
                    <?php echo $cit_summary[1] ?>
                    </div>    
                <?php } else {?>   
                    <span  style="overflow: hidden;white-space: nowrap;display:none;">
                    <?php echo $cit_summary[0] ?>
                    </span>                 
                    <div  style="overflow: hidden;white-space: nowrap;">
                    <?php echo $cit_summary[0] ?>
                    </div>    
                <?php }?>
                
            </a>
        </div>
    </div>
    <?php
        }
    }
    ?>    
</div>

<script type="text/javascript">
    $("div.summary").each(function(){
        var _span = 0 ;
        var _span2 = 0 ;
        for (var i = 1; i < 10; i++) {
            if($(this).width() < $(this).children().children('span').first().width()){                
                
                $(this).children().children('span').first().css('letter-spacing',-i);
            } else{

                _span = i-1;
                $(this).children().children('div').first().css('letter-spacing',-_span);
                break;
            }
        }
        

        

        for (var i = 1; i < 10; i++) {
            if($(this).width() < $(this).children().children('span:eq(1)').width()){                
                
                $(this).children().children('span:eq(1)').css('letter-spacing',-i);
            } else{

                _span2 = i-1;
                $(this).children().children('div:eq(1)').css('letter-spacing',_span2);
                break;
            }
        }

        
        
    });

</script>
