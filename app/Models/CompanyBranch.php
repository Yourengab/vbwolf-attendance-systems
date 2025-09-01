<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CompanyBranch extends Model
{
    protected $fillable = [
        'name',
        'address',
    ];

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class, 'branch_id');
    }
}
