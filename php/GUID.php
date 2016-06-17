<?php   

    // this function (GUID()) found on: 
    // http://php.net/manual/en/function.com-create-guid.php 
    // 1/18/2016 in user contributed notes by 'Alix Axel'
    function GUID()
    {
        if (function_exists('com_create_guid') === true)
        {
            return trim(com_create_guid(), '{}');
        }

        return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', 
                       mt_rand(0, 65535), 
                       mt_rand(0, 65535), 
                       mt_rand(0, 65535), 
                       mt_rand(16384, 20479), 
                       mt_rand(32768, 49151), 
                       mt_rand(0, 65535), 
                       mt_rand(0, 65535), 
                       mt_rand(0, 65535));
    };
?>