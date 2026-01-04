<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use App\Models\Role;
use App\Models\Docente;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // PK es id → NO TOCAR

    protected $fillable = [
        'name',
        'last_name',
        'email',
        'password',
        'phone',
        'image',
        'gender',
        'document_type',
        'document_number',
        'status',
    ];

    // ==============================
    // RELACIONES
    // ==============================

    // 🔗 User TIENE un Docente
    public function docente()
    {
        return $this->hasOne(Docente::class, 'user_id', 'id');
    }

    public function roles()
    {
        return $this->belongsToMany(
            Role::class,
            'user_role',
            'user_id',
            'role_id'
        );
    }

    public function hasRole($roleName)
    {
        return $this->roles()->where('name', $roleName)->exists();
    }
}