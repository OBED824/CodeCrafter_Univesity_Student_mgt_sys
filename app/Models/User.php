<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * RELATIONSHIP: hasOne(Student)
     *
     * If this account belongs to a student, this gives direct access to
     * their student profile ($user->student). The foreign key user_id
     * lives on the students table, hence hasOne from the user's side.
     */
    public function student(): HasOne
    {
        return $this->hasOne(Student::class);
    }

    /**
     * RELATIONSHIP: hasOne(Lecturer)
     *
     * Same idea for lecturer accounts ($user->lecturer).
     */
    public function lecturer(): HasOne
    {
        return $this->hasOne(Lecturer::class);
    }
}
