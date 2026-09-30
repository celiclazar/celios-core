<?php

namespace Celios\Core\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use BezhanSalleh\FilamentShield\Traits\HasPanelShield;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Modules\Payment\Traits\HasPayments;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser, HasMedia
{
    use HasApiTokens, HasFactory, HasRoles, HasPanelShield, InteractsWithMedia, LogsActivity, Notifiable, HasPayments;

    protected static function newFactory()
    {
        return \Database\Factories\UserFactory::new();
    }

    protected string $guard_name = 'web';

    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
        'status',
        'phone',
        'avatar',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        $allowedRoles = ['super_admin', 'super-admin', 'admin', 'panel_user'];

        return $this->hasAnyRole($allowedRoles) && ($this->status === true || $this->getRawOriginal('status') === 'active');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'email', 'status', 'username', 'phone'])
            ->logOnlyDirty()
            ->useLogName('User Management');
    }

    protected function status(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => $value === 'active',
            set: fn ($value) => $value ? 'active' : 'inactive',
        );
    }
}
