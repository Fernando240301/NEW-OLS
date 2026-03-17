@props(['workflowid'])

@php
    $menus = [
        [
            'label' => 'Project',
            'icon' => 'fas fa-project-diagram',
            'route' => route('project_list.detail', $workflowid),
            'route_name' => 'project_list.detail',
        ],
        [
            'label' => 'S.I.K',
            'icon' => 'fas fa-file-alt',
            'route' => route('project_list.sik', $workflowid),
            'route_name' => 'project_list.sik',
        ],
        [
            'label' => 'Documents',
            'icon' => 'fas fa-folder',
            'route' => route('documents.index', $workflowid),
            'route_name' => 'documents.index',
        ],
        [
            'label' => 'Units',
            'icon' => 'fas fa-sitemap',
            'route' => route('units.index', $workflowid),
            'route_name' => 'units.index',
        ],
    ];
@endphp

<div class="project-menu-grid">

    @foreach ($menus as $menu)
        <a href="{{ $menu['route'] }}" class="menu-card {{ request()->routeIs($menu['route_name']) ? 'active' : '' }}">

            <div class="menu-icon">
                <i class="{{ $menu['icon'] }}"></i>
            </div>

            <div class="menu-title">
                {{ $menu['label'] }}
            </div>

        </a>
    @endforeach

</div>


@push('css')
    <style>
        .project-menu-grid {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
        }

        .menu-card {
            width: 120px;
            height: 90px;
            border: 1px solid #dcdcdc;
            border-radius: 6px;
            text-align: center;
            padding: 12px;
            background: #fff;
            color: #333;
            text-decoration: none;
            transition: 0.2s;
        }

        .menu-card:hover {
            background: #f4f6f9;
            border-color: #007bff;
        }

        .menu-card.active {
            border: 2px solid #007bff;
            background: #eef5ff;
        }

        .menu-icon {
            font-size: 22px;
            color: #007bff;
            margin-bottom: 5px;
        }

        .menu-title {
            font-size: 14px;
            font-weight: 500;
        }
    </style>
@endpush
