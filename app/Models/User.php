<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laratrust\Contracts\LaratrustUser;
use Laratrust\Traits\HasRolesAndPermissions;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements LaratrustUser
{
    use HasApiTokens, HasFactory, Notifiable, softDeletes, HasRolesAndPermissions;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'nid',
        'gender',
        'birth_date',
        'password',
        'category_id',
        'grade',
        'serial',
        'is_success',
        'start_final_round',
        'grade2',
        'serial2',
        'final_serial',
        'degree',
        'school_id',
        'phone',
        'country',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function userQuestionAnswers()
    {
        return $this->hasMany(UserQuestionAnswers::class);
    }

    public function getGradeAttribute($value)
    {
        return !is_null($value) ? round($value, 2) : null;
    }

    public function setGradeAttribute($value)
    {
        $this->attributes['grade'] = round($value, 2);
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }
}
