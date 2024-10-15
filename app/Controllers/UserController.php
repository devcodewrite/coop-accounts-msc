<?php

namespace App\Controllers;

use App\Entities\UserEntity;
use App\Models\UserModel;
use CodeIgniter\HTTP\Response;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;
use Codewrite\CoopAuth\ApiResponse;

class UserController extends ResourceController
{
    /**
     * Return an array of resource objects, themselves in array format.
     *
     * @return ResponseInterface
     */
    public function index()
    {
        $userModel = new UserModel();
        auth()->applyConditionsToModel($userModel, 'users', ['owner']);

        $params = $this->request->getVar(['columns', 'sort', 'page', 'pageSize']);
        $allowedColumns = [];

        $response = new ApiResponse($userModel, $params, $allowedColumns);

        return $response->getCollectionResponse();
    }

    /**
     * Return the properties of a resource object.
     *
     * @param int|string|null $id
     *
     * @return ResponseInterface
     */
    public function show($id = null)
    {
        $userModel = new UserModel();
        $userModel->where('id', $id);
        $user = $userModel->first();
        
        $response = auth()->can('view', 'users', ['owner' => $user->owner ?? '']);
        if ($response->denied())
            return $response->responsed();

        $params = $this->request->getVar(['columns']);
        $allowedColumns = []; // all columns
        $response = new ApiResponse($userModel, $params, $allowedColumns);

        return $response->getSingleResponse();
    }

    /**
     * Create a new resource object, from "posted" parameters.
     *
     * @return ResponseInterface
     */
    public function create()
    {
        $response = auth()->can('create', 'users');

        if ($response->denied())
            return $response->responsed();

        $rules = [
            'email'    => 'required|valid_email|is_unique[users.email]',
            'phone'    => 'permit_empty|numeric|min_length[10]|max_length[15]|is_unique[users.phone]',
            'username' => 'required|min_length[3]|max_length[20]|is_unique[users.username]',
            'name'     => 'required|min_length[3]|max_length[50]',
            'password' => 'required|min_length[6]',
            'social_id'         => 'permit_empty|string',
            'social_provider'   => 'permit_empty|string',
            'given_name'        => 'permit_empty|string',
            'family_name'       => 'permit_empty|string',
            'middle_name'       => 'permit_empty|string',
            'address'           => 'permit_empty|string',
            'gender'            => 'permit_empty|in_list[male,female,other]',
            'picture'           => 'permit_empty|string',
        ];

        // Validate input
        if (!$this->validate($rules)) {
            return $this->respond([
                'status'  => false,
                'message' => 'Failed validating data',
                'error'   => $this->validator->getErrors()
            ], Response::HTTP_BAD_REQUEST);
        }

        // Create a new UserEntity instance and set the properties
        $user = new UserEntity();
        $data = array_merge([
            'id' => $user->generateUniqueId()
        ], $this->validator->getValidated());

        $user->fill($data);

        // Set the password using the entity's method to trigger automatic hashing
        $user->setPassword($this->request->getVar('password'));

        // Save user to database using the UserModel
        $userModel = new UserModel();

        if ($userModel->save($user)) {
            return $this->respondCreated([
                'status'  => true,
                'message' => 'User created successfully',
                'data'    => $user
            ]);
        } else {
            return $this->respond([
                'status'  => false,
                'message' => 'Failed to create user',
            ], Response::HTTP_EXPECTATION_FAILED);
        }
    }

    /**
     * Add or update a model resource, from "posted" properties.
     *
     * @param int|string|null $id
     *
     * @return ResponseInterface
     */
    public function update($id = null)
    {
        $response = auth()->can('update', 'users', ['id' => $id]);
        if ($response->denied())
            return $response->responsed();

        $rules = [
            'email'             => 'permit_empty|valid_email',
            'phone'             => 'permit_empty|numeric|min_length[10]|max_length[15]',
            'username'          => 'permit_empty|min_length[3]|max_length[20]',
            'name'              => 'permit_empty|min_length[3]|max_length[50]',
            'password'          => 'permit_empty|min_length[6]',
            'social_id'         => 'permit_empty|string',
            'social_provider'   => 'permit_empty|string',
            'given_name'        => 'permit_empty|string',
            'family_name'       => 'permit_empty|string',
            'middle_name'       => 'permit_empty|string',
            'address'           => 'permit_empty|string',
            'gender'            => 'permit_empty|in_list[male,female,other]',
            'picture'           => 'permit_empty|string',
        ];

        // Validate input
        if (!$this->validate($rules)) {
            return $this->respond([
                'status'  => false,
                'message' => 'Failed validating data',
                'error'   => $this->validator->getErrors()
            ], Response::HTTP_BAD_REQUEST);
        }

        $userModel  = new UserModel();
        $user       =  $userModel->find($id);
        $data       = $this->validator->getValidated();

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

        if ($userModel->save($user)) {
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

    /**
     * Delete the designated resource object from the model.
     *
     * @param int|string|null $id
     *
     * @return ResponseInterface
     */
    public function delete($id = null)
    {
        $response = auth()->can('delete', 'users', ['id' => $id]);
        if ($response->denied())
            return $response->responsed();

        $userModel = new UserModel();
        if ($userModel->delete($id)) {
            return $this->respondDeleted([
                'status'  => true,
                'message' => 'User delete successfully',
                'data'    => null
            ]);
        } else {
            return $this->respond([
                'status'  => false,
                'message' => 'Failed to delete user',
            ], Response::HTTP_EXPECTATION_FAILED);
        }
    }
}
