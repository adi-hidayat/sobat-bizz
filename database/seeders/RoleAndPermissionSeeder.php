<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Kemampuan dasar yang bisa dimiliki sebuah resource.
     *
     * @var list<string>
     */
    private const CRUD = ['view_any', 'view', 'create', 'update', 'delete'];

    /**
     * Daftar resource yang diatur permission-nya.
     *
     * @var list<string>
     */
    private const RESOURCES = [
        'lead',
        'customer',
        'supplier',
        'product',
        'product_category',
        'proposal',
        'quotation',
        'incoming_po',
        'purchase_order',
        'delivery_order',
        'invoice',
        'payment',
        'activity_log',
        'staff',
        'department',
        'job_title',
        'lead_source',
        'lead_rating',
        'tax',
        'region',
    ];

    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // Permission resource "user" sudah digantikan oleh "staff".
        Permission::query()
            ->whereIn('name', array_map(
                fn (string $ability): string => "{$ability}_user",
                self::CRUD,
            ))
            ->delete();

        foreach (self::RESOURCES as $resource) {
            foreach (self::CRUD as $ability) {
                Permission::findOrCreate("{$ability}_{$resource}", 'web');
            }
        }

        foreach ($this->roleMatrix() as $roleName => $grants) {
            $role = Role::findOrCreate($roleName, 'web');

            $permissions = collect($grants)
                ->flatMap(fn (array $abilities, string $resource): array => array_map(
                    fn (string $ability): string => "{$ability}_{$resource}",
                    $abilities,
                ))
                ->unique()
                ->values()
                ->all();

            $role->syncPermissions($permissions);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    /**
     * @return array<string, array<string, list<string>>>
     */
    private function roleMatrix(): array
    {
        $all = [];

        foreach (self::RESOURCES as $resource) {
            $all[$resource] = self::CRUD;
        }

        $readOnly = [];

        foreach (self::RESOURCES as $resource) {
            $readOnly[$resource] = ['view_any', 'view'];
        }

        return [
            'Administrator' => $all,

            'Sales Manager' => array_merge($readOnly, [
                'lead' => self::CRUD,
                'customer' => self::CRUD,
                'proposal' => self::CRUD,
                'quotation' => self::CRUD,
                'incoming_po' => self::CRUD,
                'delivery_order' => self::CRUD,
                'invoice' => ['view_any', 'view', 'create', 'update'],
            ]),

            'Sales Executive' => array_merge($readOnly, [
                'lead' => ['view_any', 'view', 'create', 'update'],
                'customer' => ['view_any', 'view', 'create', 'update'],
                'proposal' => ['view_any', 'view', 'create', 'update'],
                'quotation' => ['view_any', 'view', 'create', 'update'],
                'incoming_po' => ['view_any', 'view', 'create'],
                'delivery_order' => ['view_any', 'view'],
                'invoice' => ['view_any', 'view'],
            ]),

            'Purchasing' => array_merge($readOnly, [
                'supplier' => self::CRUD,
                'purchase_order' => self::CRUD,
                'product' => ['view_any', 'view', 'create', 'update'],
                'incoming_po' => ['view_any', 'view'],
            ]),

            'Warehouse' => array_merge($readOnly, [
                'delivery_order' => self::CRUD,
                'incoming_po' => ['view_any', 'view'],
                'product' => ['view_any', 'view'],
            ]),

            'Finance' => array_merge($readOnly, [
                'invoice' => self::CRUD,
                'payment' => self::CRUD,
                'customer' => ['view_any', 'view'],
                'incoming_po' => ['view_any', 'view'],
                'delivery_order' => ['view_any', 'view'],
                'quotation' => ['view_any', 'view'],
            ]),

            'Management' => $readOnly,
        ];
    }
}
