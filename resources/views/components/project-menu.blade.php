@props([
    'workflowid',
    'active' => 'project', // default menu aktif
])

@php
    $menus = [
        [
            'key' => 'project',
            'label' => 'Project',
            'icon' => 'fas fa-project-diagram',
            'route' => route('project_list.detail', $workflowid),
        ],
        [
            'key' => 'sik',
            'label' => 'S.I.K',
            'icon' => 'fas fa-file-alt',
            'route' => '#',
        ],
        [
            'key' => 'itp',
            'label' => 'I.T.P',
            'icon' => 'fas fa-calendar-check',
            'route' => '#',
        ],
    ];
@endphp

<div class="project-menu">
    @foreach ($menus as $menu)
        <a href="{{ $menu['route'] }}" class="menu-tile {{ $active === $menu['key'] ? 'active' : '' }}">
            <i class="{{ $menu['icon'] }}"></i>
            <span>{{ $menu['label'] }}</span>
        </a>
    @endforeach
</div>
