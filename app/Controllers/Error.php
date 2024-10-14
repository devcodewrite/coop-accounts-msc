<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;

class Error extends ResourceController
{
    public function show404()
    {
        // Set the response code to 404
        return $this->respond([
            'error_code' => 404,
            'message'  => 'The resource you are looking for does not exist or has been moved.',
        ], 404);
    }
}
