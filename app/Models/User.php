<?php
namespace App\Models;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;
class User extends Authenticatable
{
    use HasApiTokens, Notifiable, SoftDeletes;
    protected $fillable = [
        'username',
        'email',
        'password',
        'birthday',
        'created_by',
        'deleted_by',
    ];
    protected $hidden = [
        'password',
        'remember_token',
    ];
    protected $dates = ['deleted_at'];
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'users_and_roles')
            ->withPivot('created_at', 'created_by', 'deleted_at', 'deleted_by')
            ->withTimestamps();
    }
    
    
    public function hasPermission($permissionCode)
    {
        if (!$this->roles || $this->roles->isEmpty()) 
        {
            return false;
        }
        $permissions = $this->roles->flatMap(function ($role)
        {
            return $role->permissions ?? collect();
        })->pluck('code');
        return $permissions->contains($permissionCode);
    }
}
