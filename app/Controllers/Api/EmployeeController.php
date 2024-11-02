<?php namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\Employees;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\HTTP\ResponseInterface;
use ReflectionException;

class EmployeeController extends BaseController
{
    use ResponseTrait;
    private Employees $employees;

    public function __construct()
    {
        $this->employees = new Employees();
    }

    /**
     * Display a listing of the resource ...
     * @return ResponseInterface
     * [GET] /employee
     */
    public function index(): ResponseInterface
    {
        return $this->respond($this->employees->employeeWithDepartments());
    }

    /**
     * Create a new employee record in the database ...
     * @return ResponseInterface
     * [POST] /employee
     * @throws ReflectionException
     */
    public function create(): ResponseInterface
    {
        $employeeInfo = [
            'branch_id' => $this->request->getPost('branch_id'),
            'designation_id' => $this->request->getPost('designation_id'),
            'name' => $this->request->getPost('name'),
            'code' => $this->request->getPost('code'),
            'fathers_name' => $this->request->getPost('fathers_name'),
            'mothers_name' => $this->request->getPost('mothers_name'),
            'spouse_name' => $this->request->getPost('spouse_name'),
            'permanent_address' => $this->request->getPost('permanent_address'),
            'present_address' => $this->request->getPost('present_address'),
            'last_achieved_degree' => $this->request->getPost('last_achieved_degree'),
            'date_of_birth' => $this->request->getPost('date_of_birth'),
            'date_of_joining' => $this->request->getPost('date_of_joining'),
            'date_of_discontinue' => $this->request->getPost('date_of_discontinue'),
            'secuirity_money' => $this->request->getPost('secuirity_money'),
            'starting_salary' => $this->request->getPost('starting_salary'),
            'current_salary' => $this->request->getPost('current_salary'),
            'national_id' => $this->request->getPost('national_id'),
            'smart_id' => $this->request->getPost('smart_id'),
            'blood_group' => $this->request->getPost('blood_group'),
            'refence_info_1' => $this->request->getPost('refence_info_1'),
            'refence_info_2' => $this->request->getPost('refence_info_2'),
            'attached_documents' => $this->request->getPost('attached_documents'),
            'is_field_officer' => $this->request->getPost('is_field_officer'),
            'is_manager' => $this->request->getPost('is_manager'),
            'id_sequence_no' => $this->request->getPost('id_sequence_no'),
            'nominee_info' => $this->request->getPost('nominee_info')
        ];

        if($this->employees->insert($employeeInfo)){
            return $this->respond(['message' => 'Employee created successfully']);
        }

        return $this->fail('Failed to create employee');
    }

    /**
     * Display the specified employee record ...
     * @param $id
     * @return ResponseInterface
     * [GET] /employee/{id}
     */
    public function show($id = null): ResponseInterface
    {
        $employee = $this->employees->employeeWithDepartments($id);
        if($employee){
            return $this->respond($employee);
        }
        return $this->failNotFound('Employee not found');
    }

    /**
     * Update the specified employee record in the database ...
     * @param $id
     * @return ResponseInterface
     * [POST] /employee/{id}
     * @throws ReflectionException
     */
    public function update($id = null): ResponseInterface
    {
        $employeeInfo = [
            'first_name' => $this->request->getPost('first_name'),
            'last_name' => $this->request->getPost('last_name'),
            'email' => $this->request->getPost('email'),
            'phone' => $this->request->getPost('phone'),
            'address' => $this->request->getPost('address'),
            'department_id' => $this->request->getPost('department_id'),
        ];

        if($this->employees->update($id, $employeeInfo)){
            return $this->respond(['message' => 'Employee updated successfully']);
        }

        return $this->fail('Failed to update employee');
    }

    /**
     * Remove the specified employee record from the database ...
     * @param $id
     * @return ResponseInterface
     * [POST] /employee/{id}
     */
    public function delete($id = null): ResponseInterface
    {
        if($this->employees->delete($id)){
            return $this->respond(['message' => 'Employee deleted successfully']);
        }

        return $this->fail('Failed to delete employee');
    }
}
