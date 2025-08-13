<div class="sidebar">
    <h5 class="px-3 text-white">Menu</h5>
    <a href="{{ route('departments.index')}}">Departments</a>
    <a href="{{ route('employees.index')}}">Employees</a>
</div>

<style>
    .sidebar {
        width: 220px;
        background: #343a40;
        color: #fff;
        padding-top: 20px;
        min-height: 100vh;
    }
    .sidebar a {
        color: #ddd;
        padding: 10px 15px;
        display: block;
        text-decoration: none;
    }
    .sidebar a:hover {
        background: #495057;
    }
</style>
