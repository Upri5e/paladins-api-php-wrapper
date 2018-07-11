<?php
    require 'paladins-api.php';

    $name = new HiRezAPI();
    $name->devkey('1004');
    $name->devAuthID('23DF3C7E9BD14D84BF892AD206B6755C');

    /*
        pc - xbox - ps5
    */
    $name->platform('pc'); 
    
    /*
        en          =>       English
        ger         =>       German
        fr          =>       French
        ch          =>       Chinese
        esp         =>       Spanish
        esp-latin   =>       Spanich (Latin America)
        pt          =>       Portuguese
        ru          =>       Russian
        pl          =>       Polish
        tr          =>       Turkish
    */
    echo $name->language('fr');
    
    $name->CreateSession();    //CreateSession