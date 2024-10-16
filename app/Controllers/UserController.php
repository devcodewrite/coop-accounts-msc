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
    protected $modelName = UserModel::class;
    protected $allowedColumns = [
        'id',
        'type',
        'owner',
        'username',
        'name',
        'email',
        'phone',
        'email_verified',
        'phone_verified',
        'given_name',
        'family_name',
        'middle_name',
        'address',
        'gender',
        'picture',
        'status'
    ];
    /**
     * Return an array of resource objects, themselves in array format.
     *
     * @return ResponseInterface
     */
    public function index()
    {
        $params = $this->request->getVar(['columns', 'filters', 'sort', 'page', 'pageSize']);
        $response = new ApiResponse($this->model, $params, $this->allowedColumns);

        return $response->getCollectionResponse(true, ["owner"]);
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
        $params = $this->request->getVar(['columns']);
        $response = new ApiResponse($this->model, $params, $this->allowedColumns);
        return $response->getSingleResponse(true, ['owner']);
    }

    /**
     * Create a new resource object, from "posted" parameters.
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
            'social_id'         => 'permit_empty|string',
            'social_provider'   => 'permit_empty|string',
            'given_name'        => 'permit_empty|string',
            'family_name'       => 'permit_empty|string',
            'middle_name'       => 'permit_empty|string',
            'address'           => 'permit_empty|string',
            'gender'            => 'permit_empty|in_list[male,female,other]',
            'picture'           => 'permit_empty|string'
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
            'id' => $user->generateUniqueId(),
            'type' => 'sub_user',
            'creator' => auth()->user()->id,
            'owner' => auth()->user()->owner,
        ], $this->validator->getValidated());

        $user->fill($data);

        // Save user to database using the UserModel
        if ($this->model->save($user)) {
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
        $user = $this->model->find($id);
        if (!$user) {
            return $this->respond([
                'status'  => false,
                'message' => "User doesn't exist.",
            ], Response::HTTP_NOT_FOUND);
        }

        $response = auth()->can('update', 'users', ['owner'], [$user]);
        if ($response->denied())
            return $response->responsed();

        $rules = [
            'email'             => 'permit_empty|valid_email',
            'phone'             => 'permit_empty|numeric|min_length[10]|max_length[15]',
            'username'          => 'if_exist|min_length[3]|max_length[20]|is_unique[users.username,username,username]',
            'name'              => 'permit_empty|min_length[3]|max_length[50]',
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
        $data = $this->validator->getValidated();

        if (count($data) === 0) {
            return $this->respond([
                'status'  => false,
                'message' => 'Failed validating data',
                'error'   => $this->validator->getErrors()
            ], Response::HTTP_BAD_REQUEST);
        }
        $user->fill($this->validator->getValidated());

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

    /**
     * Delete the designated resource object from the model.
     *
     * @param int|string|null $id
     *
     * @return ResponseInterface
     */
    public function delete($id = null)
    {
        $user = $this->model->find($id);
        if (!$user) {
            return $this->respond([
                'status'  => false,
                'message' => "User doesn't exist.",
            ], Response::HTTP_NOT_FOUND);
        }
        $response = auth()->can('delete', 'users', ['owner'], [$user]);
        if ($response->denied())
            return $response->responsed();

        if ($this->model->delete($id)) {
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
