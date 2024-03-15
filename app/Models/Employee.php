<?php

namespace App\Models;

use App\Models\Scopes\selectedHotelResource;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Shops\Entities\Hotel;

class Employee extends Model
{
    use Sluggable, HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'emp_id', 'slug', 'department_id', 'designation', 'salary', 'commission', 'mobile_number', 'birth_date', 'gender', 'blood_group', 'religion', 'appointment_date', 'joining_date', 'address', 'image_path', 'hotel_id', 'status', 'user_id',
    ];

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name',
            ],
        ];
    }

    protected $appends = ['calculated_salary'];

    /**
     * Get the present salary.
     *
     * @return string
     */
    public function getCalculatedSalaryAttribute()
    {
        return $this->totalSalary();
    }

    /**
     * Return employee total salary.
     */
    public function totalSalary()
    {
        if (! empty($this->salaryIncrements)) {
            return $this->salary + $this->salaryIncrements->where('status', 1)->sum('increment_amount');
        }

        return $this->salary;
    }

    public function hotels()
    {
        return $this->user ? $this->user->hotels : null;
    }

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    /**
     * Return employee increments.
     */
    public function salaryIncrements()
    {
        return $this->hasMany(SalaryIncrement::class, 'empolyee_id');
    }

    /**
     * Return employee department.
     */
    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    /**
     * Get the user .
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new SelectedHotelResource);
    }
}
