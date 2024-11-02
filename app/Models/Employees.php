<?php

namespace App\Models;

use CodeIgniter\Model;

class Employees extends Model
{
    protected $table            = 'employees';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;

    /**
     * Table    Create Table
     * employees    CREATE TABLE `employees` (
     * `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Employee identification number',
     * `branch_id` smallint(5) unsigned NOT NULL COMMENT 'Branch identification number',
     * `designation_id` smallint(5) unsigned NOT NULL COMMENT 'Designation identification number',
     * `name` varchar(200) NOT NULL COMMENT 'Employee’s name',
     * `code` varchar(50) DEFAULT NULL COMMENT 'Employee’s code',
     * `fathers_name` varchar(200) NOT NULL COMMENT 'Employee’s father name',
     * `mothers_name` varchar(200) NOT NULL COMMENT 'Employee’s mother name',
     * `spouse_name` varchar(200) DEFAULT NULL COMMENT 'Employee’s husband/wife name ',
     * `permanent_address` varchar(500) DEFAULT NULL COMMENT 'Employee’s permanent address',
     * `present_address` varchar(500) DEFAULT NULL COMMENT 'Employee’s present address',
     * `last_achieved_degree` int(11) DEFAULT NULL COMMENT 'Last educational qualification achieved',
     * `date_of_birth` date DEFAULT NULL COMMENT 'Birth date',
     * `date_of_joining` date DEFAULT NULL COMMENT 'Joining date',
     * `date_of_discontinue` date DEFAULT NULL COMMENT 'Resigned date',
     * `secuirity_money` decimal(10,2) DEFAULT 0.00 COMMENT 'Which money keep  to the organization at the joining time',
     * `starting_salary` decimal(10,2) DEFAULT 0.00 COMMENT 'The amount of beginning salary',
     * `current_salary` decimal(10,2) DEFAULT 0.00 COMMENT 'Running salary ',
     * `national_id` bigint(20) DEFAULT NULL COMMENT 'Nationality identification number',
     * `smart_id` bigint(20) DEFAULT NULL,
     * `blood_group` varchar(3) DEFAULT '0' COMMENT 'Blood Group',
     * `gender` varchar(1) DEFAULT NULL,
     * `mobile_no` varchar(30) DEFAULT NULL,
     * `email` varchar(100) DEFAULT NULL,
     * `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Present status of the employee active or inactive',
     * `employee_picture` varchar(200) DEFAULT NULL COMMENT 'Photo of the Employee',
     * `refence_info_1` varchar(500) DEFAULT NULL COMMENT 'Reference related information ',
     * `refence_info_2` varchar(500) DEFAULT NULL COMMENT 'Reference related information ',
     * `attached_documents` varchar(200) DEFAULT NULL COMMENT 'Attached document like CV',
     * `is_field_officer` tinyint(1) NOT NULL DEFAULT 0,
     * `is_manager` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'No=0; Yes=1',
     * `id_sequence_no` int(11) DEFAULT NULL,
     * `sync_date` date DEFAULT NULL COMMENT 'Sync date from HR',
     * `field_officer_pin` smallint(6) DEFAULT NULL,
     * `nominee_info` text DEFAULT NULL COMMENT 'Employee nominee info JSON data',
     */
    protected $allowedFields    = [
        'branch_id',
        'designation_id',
        'name',
        'code',
        'fathers_name',
        'mothers_name',
        'spouse_name',
        'permanent_address',
        'present_address',
        'last_achieved_degree',
        'date_of_birth',
        'date_of_joining',
        'date_of_discontinue',
        'secuirity_money',
        'starting_salary',
        'current_salary',
        'national_id',
        'smart_id',
        'blood_group',
        'refence_info_1',
        'refence_info_2',
        'attached_documents',
        'is_field_officer',
        'is_manager',
        'id_sequence_no',
        'nominee_info'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Get all employees with their departments
     * @param int|null $employeeId
     * @return array
     */
    public function employeeWithDepartments(int $employeeId = null): array
    {
        $query = $this->db->table('employees')
            ->select('
                employees.*,
                employee_designations.name as designation,
                employee_designations.code as designation_code,
                CONCAT(employee_designations.short_name, " - ", employee_designations.code) as short_name,
                employee_departments.name as department
            ')
            ->join('employee_designations', 'employee_designations.id = employees.designation_id', 'inner')
            ->join('employee_departments', 'employee_departments.id = employee_designations.department_id', 'right');

        if($employeeId){
            $query->where('id', $employeeId);
        }

        return $query->get()->getResultArray();
    }
}
