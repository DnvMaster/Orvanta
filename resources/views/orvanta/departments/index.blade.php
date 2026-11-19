@extends('orvanta.layouts.app')

@section('content')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-1">Структура подразделений</h1>
                <p class="text-muted mb-0">Иерархия отделов компании для разграничения области видимости (department)</p>
            </div>
            <a href="{{ route('departments.create') }}" class="btn btn-primary">+ Добавить отдел</a>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif

        <div class="card shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                    <tr>
                        <th>Название подразделения</th>
                        <th class="text-center">Сотрудников</th>
                        <th class="text-end">Действия</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($rootDepartments as $department)
                        @include('orvanta.departments.partials.row', ['department' => $department, 'level' => 0])
                    @empty
                        <tr>
                            <td colspan="3" class="text-center py-4 text-muted">Подразделения еще не созданы.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
