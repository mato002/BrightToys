<?php

namespace Database\Seeders;

use App\Models\AdminRole;
use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // Contributions
            'contributions.create' => 'Create partner contributions',
            'contributions.approve' => 'Approve partner contributions',

            // Projects
            'projects.create' => 'Create investment projects',
            'projects.update' => 'Edit investment projects',
            'projects.activate' => 'Activate projects',

            // Financial records
            'financial.records.create' => 'Create financial records',
            'financial.records.approve' => 'Approve financial records',
            'financial.records.view' => 'View financial records',

            // Documents
            'documents.upload' => 'Upload documents',
            'documents.approve' => 'Approve documents',

            // Reports
            'reports.financial.view' => 'View financial reports',

            // Loans
            'loans.view' => 'View loans and loan schedules',
            'loans.create' => 'Create and update loans',
            'loans.repayments.create' => 'Record loan repayments',
            'loans.repayments.reconcile' => 'Reconcile/confirm loan repayments',
        ];

        $permissionModels = [];
        foreach ($permissions as $name => $description) {
            $permissionModels[$name] = Permission::firstOrCreate(
                ['name' => $name],
                [
                    'display_name' => ucwords(str_replace('.', ' ', $name)),
                    'description' => $description,
                ]
            );
        }

        // Attach sensible defaults to existing roles
        $roles = AdminRole::whereIn('name', ['super_admin', 'finance_admin', 'chairman', 'treasurer'])->get()->keyBy('name');

        if ($roles->has('super_admin')) {
            // Super admin gets everything
            $roles['super_admin']->permissions()->sync($permissionModels ? collect($permissionModels)->pluck('id') : []);
        }

        if ($roles->has('finance_admin')) {
            $roles['finance_admin']->permissions()->syncWithoutDetaching(
                collect([
                    'contributions.create',
                    'contributions.approve',
                    'projects.create',
                    'projects.update',
                    'financial.records.create',
                    'financial.records.approve',
                    'financial.records.view',
                    'documents.upload',
                    'documents.approve',
                    'reports.financial.view',
                    'loans.view',
                    'loans.create',
                    'loans.repayments.create',
                    'loans.repayments.reconcile',
                ])->map(fn ($name) => $permissionModels[$name]->id)
            );
        }

        if ($roles->has('chairman')) {
            $roles['chairman']->permissions()->syncWithoutDetaching(
                collect([
                    'contributions.approve',
                    'projects.create',
                    'projects.update',
                    'projects.activate',
                    'financial.records.create',
                    'financial.records.approve',
                    'financial.records.view',
                    'documents.upload',
                    'documents.approve',
                    'reports.financial.view',
                    'loans.view',
                    'loans.create',
                    'loans.repayments.create',
                    'loans.repayments.reconcile',
                ])->map(fn ($name) => $permissionModels[$name]->id)
            );
        }

        if ($roles->has('treasurer')) {
            $roles['treasurer']->permissions()->syncWithoutDetaching(
                collect([
                    'contributions.create',
                    'projects.update',
                    'financial.records.create',
                    'financial.records.view',
                    'documents.upload',
                ])->map(fn ($name) => $permissionModels[$name]->id)
            );
        }
    }
}

