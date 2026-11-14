<?php

namespace Celios\Core\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use BezhanSalleh\FilamentShield\Traits\HasPanelShield;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Passkeys\Contracts\PasskeyUser;
use Laravel\Passkeys\PasskeyAuthenticatable;
use Laravel\Sanctum\HasApiTokens;
use Modules\Payment\Traits\HasPayments;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser, HasMedia, PasskeyUser
{
    use HasApiTokens, HasFactory, HasPanelShield, HasPayments, HasRoles, InteractsWithMedia, LogsActivity, Notifiable, PasskeyAuthenticatable;

    public function socialAccounts(): HasMany
    {
        return $this->hasMany(SocialAccount::class);
    }

    protected static function newFactory()
    {
        return UserFactory::new();
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
        $hasRole = $this->hasAnyRole(['super_admin', 'super-admin', 'admin', 'panel_user'])
            || $this->roles()->where('name', '!=', 'user')->exists();

        if (! $hasRole && Schema::hasTable('model_has_roles')) {
            $hasRole = DB::table('model_has_roles')
                ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
                ->where('model_has_roles.model_id', $this->getKey())
                ->whereIn('model_has_roles.model_type', ['App\\Models\\User', static::class])
                ->where('roles.name', '!=', 'user')
                ->exists();
        }

        return $hasRole && ($this->status === true || $this->getRawOriginal('status') === 'active');
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
