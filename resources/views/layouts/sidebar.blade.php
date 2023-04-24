<!-- ========== Left Sidebar Start ========== -->
<div class="vertical-menu">

    <div data-simplebar class="h-100">

        <!--- Sidemenu -->
        <div id="sidebar-menu">
            <!-- Left Menu Start -->
            <ul class="metismenu list-unstyled" id="side-menu">
                <li>
                    <a href="{{ route('home') }}" class="waves-effect">
                        <i class="bx bx-home-circle"></i> <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('users.index') }}" class="waves-effect">
                        <i class="bx bx-group"></i> <span>Users &amp; Permissions</span>
                    </a>
                </li>

                <li class="menu-title">Customer Management</li>
                <li>
                    <a href="{{ route('customers') }}" class="waves-effect">
                        <i class="bx bx-user-circle"></i> <span>Customers List</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('customers.register') }}" class="waves-effect">
                        <i class="bx bx-user-plus"></i> <span>Register New Customers</span>
                    </a>
                </li>

                <li class="menu-title">Collector Management</li>
                <li>
                    <a href="{{ route('collectors') }}" class="waves-effect">
                        <i class="bx bx-user-pin"></i> <span>Collectors</span>
                    </a>
                </li>

                <li class="menu-title">Loan Management</li>
                <li>
                    <a href="{{ route('loans.schemes') }}" class="waves-effect">
                        <i class="bx bx-purchase-tag"></i> <span>Schemes</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('customer-loans.list') }}" class="waves-effect">
                        <i class="bx bxs-briefcase-alt-2"></i> <span>Customer Loans</span>
                    </a>
                </li>
                <li class="menu-title">Settings</li>
                <li>
                    <a href="{{ route('settings.holidays') }}" class="waves-effect">
                        <i class="bx bxs-calendar"></i> <span>Holiday Calendar</span>
                    </a>
                </li>
                <li class="menu-title">Reports</li>
                <li>
                    <a href="{{ route('reports.index') }}" class="waves-effect">
                        <i class="bx bxs-file-pdf"></i> <span>Reports</span>
                    </a>
                </li>
            </ul>
        </div>
        <!-- Sidebar -->
    </div>
</div>
<!-- Left Sidebar End -->
