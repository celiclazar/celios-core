<?php

declare(strict_types=1);

namespace Celios\Core\Filament\Resources\Roles\Pages;

use BezhanSalleh\FilamentShield\Facades\FilamentShield;
use BezhanSalleh\FilamentShield\Support\Utils;
use Celios\Core\Filament\Resources\Roles\RoleResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Override;

class EditRole extends EditRecord
{
    public Collection $permissions;

    protected static string $resource = RoleResource::class;

    protected function getActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    #[Override]
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->permissions = collect($data)
            ->filter(fn (mixed $permission, string $key): bool => ! in_array($key, ['name', 'guard_name', 'select_all', Utils::getTenantModelForeignKey()], true))
            ->values()
            ->flatten()
            ->unique();

        // Discard any newly submitted permissions that belong to disabled modules
        $activePermissions = FilamentShield::getEntitiesPermissions() ?? [];
        $this->permissions = $this->permissions->filter(fn (string $permission): bool => in_array($permission, $activePermissions, true));

        if (Utils::isTenancyEnabled() && Arr::has($data, Utils::getTenantModelForeignKey()) && filled($data[Utils::getTenantModelForeignKey()])) {
            return Arr::only($data, ['name', 'guard_name', Utils::getTenantModelForeignKey()]);
        }

        return Arr::only($data, ['name', 'guard_name']);
    }

    protected function afterSave(): void
    {
        $permissionModels = collect();
        $this->permissions->each(function (string $permission) use ($permissionModels): void {
            $permissionModels->push(Utils::getPermissionModel()::firstOrCreate([
                'name' => $permission,
                'guard_name' => $this->data['guard_name'],
            ]));
        });

        // Preserve permissions belonging to inactive modules so editing an enabled role does not strip them
        $currentPermissions = $this->record->permissions ?? collect();
        $activePermissions = FilamentShield::getEntitiesPermissions() ?? [];

        $preservedPermissions = $currentPermissions->filter(function ($permission) use ($activePermissions): bool {
            return ! in_array($permission->name, $activePermissions, true);
        });

        $finalPermissions = $permissionModels->merge($preservedPermissions)->unique('id');

        $this->record->syncPermissions($finalPermissions);
    }
}
