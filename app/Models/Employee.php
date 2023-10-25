<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'employees';
    protected $primaryKey = 'employee_id';

    protected $fillable =
    [
        'employee_eid',
        'first_name',
        'last_name',
        'user_id',
        'address',
        'email',
        'birth_date',
        'join_date',
        'email',
        'pan',
        'aadhar'
    ];
    
    public function project()
    {
        return $this->hasOne(Projects::class, 'project_id', 'project_id');
    }
    public function salary()
    {
        return $this->hasMany(Salary::class, 'salary_id', 'salary_id');
    }
    public function timesheet()
    {
        return $this->hasMany(Timesheet::class, 'timesheeet_id', 'timesheeet_id');
    }
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id')->withTrashed();
    }
}
