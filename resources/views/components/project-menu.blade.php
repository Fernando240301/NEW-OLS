@props(['workflowid'])

@php
$menus = [
    [
        'label' => 'Project',
        'icon' => 'fas fa-project-diagram',
        'route' => route('project_list.detail',$workflowid),
        'route_name' => 'project_list.detail',
    ],
    [
        'label' => 'S.I.K',
        'icon' => 'fas fa-file-alt',
        'route' => route('project_list.sik',$workflowid),
        'route_name' => 'project_list.sik',
    ],
    [
        'label' => 'Documents',
        'icon' => 'fas fa-folder',
        'route' => route('documents.index',$workflowid),
        'route_name' => 'documents.index',
    ],
];
@endphp

<div class="project-menu-grid">

@foreach ($menus as $menu)

<a href="{{ $menu['route'] }}"
   class="menu-card {{ request()->routeIs($menu['route_name']) ? 'active' : '' }}">

    <div class="menu-icon">
        <i class="{{ $menu['icon'] }}"></i>
    </div>

    <div class="menu-title">
        {{ $menu['label'] }}
    </div>

</a>

@endforeach

</div>
