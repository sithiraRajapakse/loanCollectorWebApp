<?php

namespace Database\Seeders;

use App\Enums\SchemeType;
use App\Enums\UserType;
use App\Models\Collector;
use App\Models\Customer;
use App\Models\Organization;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Scheme;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StartSeeder extends Seeder
{
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {
        $this->createDefaultUser();
        $this->createDummyCustomers();
        $this->createCollectors();
        $this->createDefaultLoanSchemes();
    }

    /**
     * Create the default administrator user
     */
    private function createDefaultUser()
    {
        // create and get default organization
        $org = $this->createDefaultOrganization();

        $administratorUser = new User;
        $administratorUser->name = 'Administrator';
        $administratorUser->email = 'admin@admin.com';
        $administratorUser->password = Hash::make('password');
        $administratorUser->user_type = UserType::ADMINISTRATOR;
        $administratorUser->organization_id = $org->id;
        $administratorUser->save();

        $administratorUser->collector()->create([
            'name' => 'Administrator',
            'address' => 'Head office',
            'telephone' => '-',
            'nic_no' => '-',
            'drivers_license_no' => '-',
        ]);

        // attach permissions
        $this->attachRolesAndPermissionToUser($administratorUser);

        // create default

        return $administratorUser;
    }

    /**
     * Create the default organization
     *
     * @return Organization
     */
    private function createDefaultOrganization()
    {
        $organization = new Organization();
        $organization->name = 'Default Organization';
        $organization->address = '';
        $organization->telephone = '';
        $organization->email = '';
        $organization->fax = '';
        $organization->save();

        return $organization;
    }

    private function attachRolesAndPermissionToUser($administratorUser)
    {
        $permissionNames = [
            'view-customers' => 'View Customers',
            'register-customers' => 'Register Customers',
            'update-customers' => 'Update Customers',
            'delete-customers' => 'Delete Customers',
            'edit-customer-documents' => 'Edit Customer Documents',
            'delete-customer-documents' => 'Delete Customer Documents',
            'view-collectors' => 'View Collectors',
            'update-collectors' => 'Update Collectors',
            'delete-collectors' => 'Delete Collectors',
            'edit-loan-schemes' => 'Edit Loan Schemes',
            'delete-loan-schemes' => 'Delete Loan Schemes',
            'register-loans' => 'Register Loans',
            'customize-installments' => 'Customize Loan Installments',
            'make-installment-payments' => 'Make Loan Installment Payments',
            'delete-installments' => 'Delete Loan Installments',
        ];

        // create role admin
        $admin = Role::create([
            'name' => 'admin',
            'display_name' => 'User Administrator', // optional
            'description' => 'User is allowed to manage and edit other users', // optional
        ]);
        // create role general user
        $generalUser = Role::create([
            'name' => 'general_user',
            'display_name' => 'General User', // optional
            'description' => 'User is allowed to manage and edit permitted data.', // optional
        ]);
        // create role collector
        $collector = Role::create([
            'name' => 'collector',
            'display_name' => 'Loan Collector', // optional
            'description' => 'User only allowed to use the collector app and a limited set of data.', // optional
        ]);

        $permissions = [];
        // create permissions
        foreach ($permissionNames as $permissionKey => $permissionName) {
            $permissions[] = Permission::create([
                'name' => $permissionKey,
                'display_name' => $permissionName, // optional
                'description' => '-', // optional
            ]);
        }

        // attach permissions to the admin role
        $admin->syncPermissions($permissions);

        // assign admin role to administrator
        $administratorUser->attachRole($admin);
    }

    /**
     * Create 50 customer records
     */
    private function createDummyCustomers()
    {
        Customer::factory()->count(50)->create();
    }

    /**
     * Loan collectors
     */
    public function createCollectors()
    {
        $org = $this->createDefaultOrganization();

        $collectorUser = new User();
        $collectorUser->name = 'Loan Collector One';
        $collectorUser->email = 'collector@admin.com';
        $collectorUser->password = Hash::make('password');
        $collectorUser->user_type = UserType::COLLECTOR;
        $collectorUser->organization_id = $org->id;
        $collectorUser->save();

        Collector::create([
            'user_id' => $collectorUser->id,
            'name' => 'Loan Collector One',
            'address' => '',
            'telephone' => '',
            'nic_no' => '',
            'drivers_license_no' => '',
        ]);
    }

    /**
     * Create the default loan schemes
     */
    public function createDefaultLoanSchemes()
    {
        Scheme::create([
            'title' => 'Daily Loan',
            'type' => SchemeType::DAILY,
            'interest_rate' => 10.00,
        ]);
        Scheme::create([
            'title' => 'Weekly Loan',
            'type' => SchemeType::WEEKLY,
            'interest_rate' => 10.00,
        ]);
        Scheme::create([
            'title' => 'Monthly Loan',
            'type' => SchemeType::MONTHLY,
            'interest_rate' => 13.00,
        ]);
        Scheme::create([
            'title' => 'Custom Loan',
            'type' => SchemeType::CUSTOM,
            'interest_rate' => 10.00,
        ]);
        Scheme::create([
            'title' => 'Bi-weekly Loan',
            'type' => SchemeType::BI_WEEKLY,
            'interest_rate' => 10.00,
        ]);
    }

}
