<?php
if(!function_exists('res_data')){
   function response_data($data,$message='',$status=200){
    $message=$message??__('message');
    return response([
        'data'=>!empty($data)?$data:null,
        'message'=>$message,
        'status'=>$status
    ],$status);
    }
}