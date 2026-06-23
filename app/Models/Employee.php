<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = [
        'name', 'phone', 'email', 'bank_name', 'account_number', 'account_name',
        'position', 'department', 'base_salary', 'status', 'join_date', 'notes',
        'created_by', 'updated_by',
    ];

    protected $casts = [
        'base_salary' => 'decimal:2',
        'join_date' => 'date',
        'status' => 'string',
    ];

    public function salaries()
    {
        return $this->hasMany(EmployeeSalary::class)->orderBy('period_year', 'desc')->orderBy('period_month', 'desc');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'aktif');
    }

    public function getLatestSalaryAttribute()
    {
        return $this->salaries()->latest('period_year')->latest('period_month')->first();
    }
}
