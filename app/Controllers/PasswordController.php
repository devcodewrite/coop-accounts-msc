<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\HTTP\Response;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;

class PasswordController extends ResourceController
{
    protected $modelName = UserModel::class;
    protected $allowedColumns = [
        'password'
    ];
    /**
     * Add or update a model resource, from "posted" properties.
     *
     * @param int|string|null $id
     *
     * @return ResponseInterface
     */
    public function update($id = null)
    {
        $user = $this->model->find($id);
        if (!$user) {
            return $this->respond([
                'status'  => false,
                'message' => "User doesn't exist.",
            ], Response::HTTP_NOT_FOUND);
        }

        $response = auth()->can('update', 'passwords', ['owner'], [$user]);
        if ($response->denied())
            return $response->responsed();

        $rules = [
            'password' => 'required|min_length[6]',
        ];

        // Validate input
        if (!$this->validate($rules)) {
            return $this->respond([
                'status'  => false,
                'message' => 'Failed validating data',
                'error'   => $this->validator->getErrors()
            ], Response::HTTP_BAD_REQUEST);
        }
        $data = $this->validator->getValidated();

        if (count($data) === 0) {
            return $this->respond([
                'status'  => false,
                'message' => 'Failed validating data',
                'error'   => $this->validator->getErrors()
            ], Response::HTTP_BAD_REQUEST);
        }
        $user->fill($this->validator->getValidated());

        // Set the password using the entity's method to trigger automatic hashing
        $user->setPassword($this->request->getVar('password'));

        if ($this->model->save($user)) {
            return $this->respondUpdated([
                'status'  => true,
                'message' => 'User updated successfully',
                'data'    => $user
            ]);
        } else {
            return $this->respondCreated([
                'status'  => false,
                'message' => 'Failed to create user',
            ]);
        }
    }
}
