<?php

namespace App\Controllers;

use App\Entities\UserEntity;
use App\Models\UserModel;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;

class Registration extends ResourceController
{
    /**
     * Create a new user with email or phone, username, name, and password.
     *
     * @return ResponseInterface
     */
    public function create()
    {
        $rules = [
            'email'    => 'required|valid_email|is_unique[users.email]',
            'phone'    => 'permit_empty|numeric|min_length[10]|max_length[15]|is_unique[users.phone]',
            'username' => 'required|min_length[3]|max_length[20]|is_unique[users.username]',
            'name'     => 'required|min_length[3]|max_length[50]',
            'password' => 'required|min_length[6]',
        ];

        // Validate input
        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        // Create a new UserEntity instance and set the properties
        $user = new UserEntity();
        $user->fill([
            'id' => $user->generateUniqueId(),
            'email'    => $this->request->getVar('email'),
            'phone'    => $this->request->getVar('phone'),
            'username' => $this->request->getVar('username'),
            'name'     => $this->request->getVar('name'),
        ]);

        // Set the password using the entity's method to trigger automatic hashing
        $user->setPassword($this->request->getVar('password'));

        // Save user to database using the UserModel
        $userModel = new UserModel();

        if ($userModel->save($user)) {
            return $this->respondCreated([
                'status'  => 'success',
                'message' => 'User registered successfully',
                'data'    => $user
            ]);
        } else {
            return $this->fail('Failed to register user');
        }
    }
}
