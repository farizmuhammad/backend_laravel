<?php

namespace App\Traits;

trait ApiResponse
{
    public function coreResponse($message, $data = null, $statusCode, $isSuccess = true)
    {
        if($isSuccess) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $data
            ], $statusCode);
        } else {
            return response()->json([
                'success' => false,
                'message' => $message,
            ], $statusCode);
        }
    }
    
    public function success($message, $data, $statusCode = 200)
    {
        return $this->coreResponse($message, $data, $statusCode);
    }
    
    public function error($message, $statusCode = 500)
    {
        return $this->coreResponse($message, null, $statusCode, false);
    }
}
