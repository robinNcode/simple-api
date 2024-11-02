<?php namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\EmployeeDepartments;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\HTTP\ResponseInterface;
use ReflectionException;

class EmployeeDepartmentController extends BaseController
{
    use ResponseTrait;
    private EmployeeDepartments $employeeDepartments;

    public function __construct()
    {
        $this->employeeDepartments = new EmployeeDepartments();
    }

    /**
     * Display a listing of the resource ...
     * @return ResponseInterface
     * [GET] /employee/departments
     */
    public function index(): ResponseInterface
    {
        $departments = $this->employeeDepartments->findAll();
        return $this->respond(['departments' => $departments]);
    }

    /**
     * Create a new department record in the database ...
     * @return ResponseInterface
     * @throws ReflectionException
     * [POST] /employee/departments
     */
    public function create(): ResponseInterface
    {
        $departmentInfo = [
            'name' => $this->request->getPost('name'),
            'code' => $this->request->getPost('code'),
            'mnemonic' => $this->request->getPost('mnemonic'),
        ];

        if($this->employeeDepartments->insert($departmentInfo)){
            return $this->respond(['message' => 'Department created successfully']);
        }

        return $this->fail('Failed to create department');
    }

    /**
     * Display the specified department record ...
     * @param $id
     * @return ResponseInterface
     * [GET] /employee/departments/{id}
     */
    public function show($id = null): ResponseInterface
    {
        $department = $this->employeeDepartments->find($id);
        if($department){
            return $this->respond(['department' => $department]);
        }

        return $this->failNotFound('Department not found!');
    }

    /**
     * Update a department record in the database ...
     * @throws ReflectionException
     * [PUT] /employee/departments/{id}
     */
    public function update($id = null): ResponseInterface
    {
        $departmentInfo = [
            'name' => $this->request->getPost('name'),
            'code' => $this->request->getPost('code'),
            'mnemonic' => $this->request->getPost('mnemonic'),
        ];

        if($this->employeeDepartments->update($id, $departmentInfo)){
            return $this->respond(['message' => 'Department updated successfully!']);
        }

        return $this->fail('Failed to update department!');
    }

    /**
     * Delete a department record from the database ...
     * @param $id
     * @return ResponseInterface
     * [DELETE] /employee/departments/{id}
     */
    public function delete($id = null): ResponseInterface
    {
        if($this->employeeDepartments->delete($id)){
            return $this->respond(['message' => 'Department deleted successfully!']);
        }

        return $this->fail('Failed to delete department!');
    }
}
