<?php

namespace App\Controllers;

use App\Models\RoleModel;
use CodeIgniter\HTTP\Response;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;
use Codewrite\CoopAuth\ApiResponse;

class Roles extends ResourceController
{
   /**
     * Return an array of resource objects, themselves in array format.
     *
     * @return ResponseInterface
     */
    public function index()
    {
        $response = auth()->can('view', 'roles');
        if ($response->denied())
            return $response->responsed();

        $roleModel = new RoleModel();
        auth()->applyConditionsToModel($roleModel, 'roles');

        $params = $this->request->getVar(['columns', 'sort', 'page', 'pageSize']);
        $allowedColumns = [];

        $response = new ApiResponse($roleModel, $params, $allowedColumns);

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
        $response = auth()->can('view', 'roles', ['id' => $id]);
        if ($response->denied())
            return $response->responsed();

        $roleModel = new RoleModel();
        $roleModel->where('id', $id);

        $params = $this->request->getVar(['columns']);
        $allowedColumns = []; // all columns
        $response = new ApiResponse($roleModel, $params, $allowedColumns);

        return $response->getSingleResponse();
    }

    /**
     * Create a new resource object, from "posted" parameters.
     *
     * @return ResponseInterface
     */
    public function create()
    {
        $response = auth()->can('create', 'roles');

        if ($response->denied())
            return $response->responsed();

        $rules = [
            'name'  => 'required|max_length[100]',
            'owner' => 'permit_empty|is_not_unique[users.id]',
        ];

        // Validate input
        if (!$this->validate($rules)) {
            return $this->response->setJSON(
                [
                    'status'  => false,
                    'message' => 'Failed validating data',
                    'error'   => $this->validator->getErrors()
                ]
            )->setStatusCode(Response::HTTP_BAD_REQUEST);
        }

        $data = $this->validator->getValidated();

        // Save role to database using the RoleModel
        $roleModel = new RoleModel();

        if ($roleModel->save($data)) {
            return $this->respondCreated([
                'status'  => true,
                'message' => 'Role created successfully',
                'data'    => $roleModel->getInsertID()
            ]);
        } else {
            return $this->response->setJSON(
                [
                    'status'  => false,
                    'message' => 'Failed to create role',
                ]
            )->setStatusCode(Response::HTTP_OK);
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
        $response = auth()->can('update', 'roles', ['id' => $id]);
        if ($response->denied())
            return $response->responsed();

            $rules = [
                'name'  => 'max_length[100]',
                'owner' => 'permit_empty|is_not_unique[users.id]',
            ];

        // Validate input
        if (!$this->validate($rules)) {
            return $this->response->setJSON(
                [
                    'status'  => false,
                    'message' => 'Failed validating data',
                    'error'   => $this->validator->getErrors()
                ]
            )->setStatusCode(Response::HTTP_BAD_REQUEST);
        }

        $roleModel  = new RoleModel();
        $role       =  $roleModel->find($id);
        $data       = $this->validator->getValidated();

        if (count($data) === 0) {
            return $this->response->setJSON(
                [
                    'status'  => false,
                    'message' => 'No request data',
                    'error'   => []
                ]
            )->setStatusCode(Response::HTTP_BAD_REQUEST);
        }
        $role->fill($this->validator->getValidated());

        // Set the password using the entity's method to trigger automatic hashing
        $role->setPassword($this->request->getVar('password'));

        if ($roleModel->save($role)) {
            return $this->respondUpdated([
                'status'  => true,
                'message' => 'Role created successfully',
                'data'    => $role
            ]);
        } else {
            return $this->response->setJSON(
                [
                    'status'  => false,
                    'message' => 'Failed to create role',
                ]
            )->setStatusCode(Response::HTTP_OK);
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
        $response = auth()->can('delete', 'roles', ['id' => $id]);
        if ($response->denied())
            return $response->responsed();

        $roleModel = new RoleModel();
        if ($roleModel->delete($id)) {
            return $this->respondDeleted([
                'status'  => true,
                'message' => 'Role delete successfully',
                'data'    => null
            ]);
        } else {
            return $this->response->setJSON(
                [
                    'status'  => false,
                    'message' => 'Failed to delete role',
                ]
            )->setStatusCode(Response::HTTP_OK);
        }
    }
}
