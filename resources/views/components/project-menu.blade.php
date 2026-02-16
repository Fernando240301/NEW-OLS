@props(['workflowid'])

@php
    $menus = [
        [
            'key' => 'project',
            'label' => 'Project',
            'icon' => 'fas fa-project-diagram',
            'route' => route('project_list.detail', $workflowid),
            'route_name' => 'project_list.detail',
        ],
        [
            'key' => 'sik',
            'label' => 'S.I.K',
            'icon' => 'fas fa-file-alt',
            'route' => route('project_list.sik', $workflowid),
            'route_name' => 'project_list.sik',
        ],
        [
            'key' => 'documents',
            'label' => 'Documents',
            'icon' => 'fas fa-file',
            'route' => route('documents.index', $workflowid),
            'route_name' => 'documents.index',
        ],
    ];
@endphp

<div class="project-menu">
    @foreach ($menus as $menu)
        <a href="{{ $menu['route'] }}" class="menu-tile {{ request()->routeIs($menu['route_name']) ? 'active' : '' }}">
            <i class="{{ $menu['icon'] }}"></i>
            <span>{{ $menu['label'] }}</span>
        </a>
    @endforeach
</div>
