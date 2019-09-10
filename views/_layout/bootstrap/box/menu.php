<div class="sidebar_latest">
    
        <ul class="nav navbar-default">
            <?php
            $menuhtml = '';
            if (element('menu', $layout)) {
                $menu = element('menu', $layout);
                if (element(0, $menu)) {
                    foreach (element(0, $menu) as $mkey => $mval) {
                        
                        if(element('men_id',$mval) !== element(0,element('active',$menu))) 
                            continue;
                        

                        
                        $mlink = element('men_link', $mval) ? element('men_link', $mval) : 'javascript:;';
                        $menuhtml .= '<div class="headline"><h3>'.html_escape(element('men_name', $mval)).'</h3></div>';
                        
                        if(element(element('men_id', $mval), $menu))
                            foreach (element(element('men_id', $mval), $menu) as $skey => $sval) {
                                $active='';
                                
                                if(element('men_id',$sval) === element(1,element('active',$menu))) {
                                    $active='active';
                                }
                                $slink = element('men_link', $sval) ? element('men_link', $sval) : 'javascript:;';
                                $menuhtml .= '<li class="'.$active.'"><a href="' . $slink . '" ' . element('men_custom', $sval);
                                $menuhtml .= ' title="' . html_escape(element('men_name', $sval)) . '">' . html_escape(element('men_name', $sval)) . '</a></li>';
                            }
                        

                        
                    }
                }
            }
            echo $menuhtml;
            ?>
        </ul>
    
</div>
    