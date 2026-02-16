@extends('adminlte::page')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/adminlte-custom.css') }}">
    <style>
        .action-menu {
            cursor: pointer;
            padding: 4px;
        }

        .action-menu:hover {
            background: #eee;
            border-radius: 6px;
        }

        #contextMenu {
            border-radius: 8px;
            animation: fadeInMenu 0.15s ease;
        }

        #contextMenu .dropdown-item:hover {
            background-color: #f1f3f5;
        }


        @keyframes fadeInMenu {
            from {
                opacity: 0;
                transform: scale(0.95);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }


        /* ========================= */
        /* SELECTION EFFECT */
        /* ========================= */

        .document-item.selected {
            background: #e8f0fe;
            border: 2px solid #4a90e2;
            outline: 2px solid #007bff;
            border-radius: 10px;
        }

        .grid-view .document-item.selected {
            box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.25);
        }

        .list-view .document-item.selected {
            background: #e8f0fe;
        }


        /* ========================= */
        /* DROP ZONE */
        /* ========================= */

        .drop-zone {
            min-height: 120px;
            transition: 0.2s;
        }

        .drop-zone.dragover {
            background-color: #f8f9fa;
            border: 2px dashed #007bff;
        }

        /* ========================= */
        /* GENERAL ITEM */
        /* ========================= */

        .document-item {
            transition: opacity 0.3s ease, transform 0.2s ease;
        }

        .document-item.fade-out {
            opacity: 0;
            transform: scale(0.9);
        }

        /* ===================================================== */
        /* ===================== GRID VIEW ===================== */
        /* ===================================================== */

        .grid-view {
            display: flex;
            flex-wrap: wrap;
            gap: 18px;
        }

        /* Card */
        .grid-view .document-item {
            width: 220px;
            background: #fff;
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        /* Hover */
        .grid-view .document-item:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.12);
        }

        /* Hide list content */
        .grid-view .list-content {
            display: none;
        }

        /* Preview area */
        .file-preview {
            height: 160px;
            background: #f4f6f9;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .file-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .file-preview i {
            font-size: 42px;
        }

        /* Bottom info */
        .file-info {
            padding: 10px 14px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .file-name {
            font-size: 14px;
            font-weight: 500;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 150px;
        }

        /* ===================================================== */
        /* ===================== LIST VIEW ===================== */
        /* ===================================================== */

        .list-view {
            display: block;
        }

        /* Remove card style */
        .list-view .document-item {
            display: block;
            background: transparent;
            box-shadow: none;
            border-radius: 0;
            border-bottom: 1px solid #eee;
        }

        /* Hide grid content */
        .list-view .grid-content {
            display: none;
        }

        /* Row layout */
        .list-view .list-content {
            display: grid;
            grid-template-columns: 3fr 1fr 1fr 1fr 40px;
            align-items: center;
            padding: 10px 15px;
        }

        /* Hover */
        .list-view .document-item:hover {
            background: #f5f5f5;
        }

        /* Header */
        .list-header {
            display: none;
            font-weight: 600;
            padding: 10px 15px;
            border-bottom: 2px solid #ddd;
        }

        .list-view .list-header {
            display: grid;
            grid-template-columns: 3fr 1fr 1fr 1fr 40px;
        }

        /* Name cell */
        .doc-name {
            display: flex;
            align-items: center;
            gap: 10px;
            overflow: hidden;
        }

        .doc-name a {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
    </style>
@endsection

@section('title', 'Documents')

@section('content_header')
    <h1 class="text-center text-primary">
        {{ $workflowdata['projectname'] }}
    </h1>
@endsection

@section('content')
    <hr>
    <x-project-menu :workflowid="$app_workflow->workflowid" active="project" />
    <hr>

    <div class="card">
        <div class="card-body">

            {{-- Alert --}}
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            {{-- Breadcrumb --}}
            @if (!empty($breadcrumbs))

                <nav aria-label="breadcrumb" class="mb-3">
                    <ol class="breadcrumb bg-white px-0">

                        {{-- ROOT --}}
                        <li class="breadcrumb-item">
                            <a href="{{ route('documents.index', $app_workflow->workflowid) }}">
                                Root
                            </a>
                        </li>

                        {{-- DYNAMIC FOLDERS --}}
                        @foreach ($breadcrumbs as $crumb)
                            @if ($loop->last)
                                <li class="breadcrumb-item active">
                                    {{ $crumb->name }}
                                </li>
                            @else
                                <li class="breadcrumb-item">
                                    <a href="{{ route('documents.index', [$app_workflow->workflowid, $crumb->id]) }}">
                                        {{ $crumb->name }}
                                    </a>
                                </li>
                            @endif
                        @endforeach

                    </ol>
                </nav>

            @endif


            {{-- Action Buttons --}}
            <div class="mb-3 d-flex justify-content-between align-items-center flex-wrap">

                {{-- LEFT SIDE: BUTTONS --}}
                <div class="d-flex align-items-center gap-2">

                    <button class="btn btn-success btn-sm" data-toggle="modal" data-target="#createFolderModal">
                        <i class="fas fa-folder-plus"></i> Buat Folder
                    </button>

                    <button type="button" onclick="document.getElementById('fileInput').click()"
                        class="btn btn-primary btn-sm">
                        <i class="fas fa-upload"></i> Upload File
                    </button>

                    {{-- VIEW TOGGLE --}}
                    <div class="btn-group ml-2">
                        <button id="gridViewBtn" class="btn btn-outline-secondary btn-sm active">
                            <i class="fas fa-th-large"></i>
                        </button>
                        <button id="listViewBtn" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-list"></i>
                        </button>
                    </div>

                </div>

                {{-- RIGHT SIDE: SEARCH --}}
                <form method="GET" action="" class="mb-0 mt-2 mt-md-0" style="width:350px;">
                    <div class="input-group">
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                            placeholder="Cari file atau folder...">
                        <div class="input-group-append">
                            <button class="btn btn-outline-primary">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </form>

            </div>


            {{-- Hidden Upload Form --}}
            <form id="uploadForm" action="{{ route('documents.upload') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="workflow_id" value="{{ $app_workflow->workflowid }}">
                @if ($folderId)
                    <input type="hidden" name="parent_id" value="{{ $folderId }}">
                @endif
                <input type="file" name="files[]" id="fileInput" multiple hidden>
            </form>

            <hr>

            {{-- Upload Progress --}}
            <div id="uploadProgressWrapper" style="display:none;" class="mb-3">
                <div class="progress">
                    <div id="uploadProgressBar" class="progress-bar progress-bar-striped progress-bar-animated"
                        role="progressbar" style="width: 0%">
                        0%
                    </div>
                </div>
            </div>

            <hr>

            {{-- File Grid (Drop Zone Here) --}}
            <div class="drop-zone grid-view" id="dropZone">

                <div class="list-header">
                    <div>Nama</div>
                    <div>Pemilik</div>
                    <div>Tanggal diubah</div>
                    <div>Ukuran</div>
                    <div></div>
                </div>

                @forelse($documents as $doc)

                    <div class="document-item" data-id="{{ $doc->id }}">

                        {{-- ================= GRID MODE ================= --}}
                        <div class="grid-content"
                            data-url="{{ $doc->type == 'folder'
                                ? route('documents.index', [$app_workflow->workflowid, $doc->id])
                                : asset('storage/' . $doc->file_path) }}"
                            data-type="{{ $doc->type }}">

                            <div class="file-preview">
                                @if ($doc->type == 'folder')
                                    <i class="fas fa-folder text-warning"></i>
                                @else
                                    @if (str_contains($doc->mime_type, 'image'))
                                        <img src="{{ asset('storage/' . $doc->file_path) }}">
                                    @elseif (str_contains($doc->mime_type, 'pdf'))
                                        <i class="fas fa-file-pdf text-danger"></i>
                                    @elseif (str_contains($doc->mime_type, 'word'))
                                        <i class="fas fa-file-word text-primary"></i>
                                    @else
                                        <i class="fas fa-file text-secondary"></i>
                                    @endif
                                @endif
                            </div>

                            <div class="file-info">
                                <div class="file-name">{{ $doc->name }}</div>

                                <div class="action-menu" data-id="{{ $doc->id }}">
                                    <i class="fas fa-ellipsis-v text-muted"></i>
                                </div>
                            </div>

                        </div>


                        {{-- ================= LIST MODE ================= --}}
                        <div class="list-content">

                            <div class="doc-name">
                                @if ($doc->type == 'folder')
                                    <i class="fas fa-folder text-warning"></i>
                                    <a href="{{ route('documents.index', [$app_workflow->workflowid, $doc->id]) }}">
                                        {{ $doc->name }}
                                    </a>
                                @else
                                    @if (str_contains($doc->mime_type, 'pdf'))
                                        <i class="fas fa-file-pdf text-danger"></i>
                                    @elseif(str_contains($doc->mime_type, 'image'))
                                        <i class="fas fa-file-image text-info"></i>
                                    @else
                                        <i class="fas fa-file text-primary"></i>
                                    @endif
                                    <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank">
                                        {{ $doc->name }}
                                    </a>
                                @endif
                            </div>

                            <div>{{ $doc->uploaded_by }}</div>

                            <div>{{ $doc->updated_at->format('d M Y') }}</div>

                            <div>
                                @if ($doc->type == 'file')
                                    {{ number_format(Storage::disk('public')->size($doc->file_path) / 1024 / 1024, 2) }} MB
                                @else
                                    —
                                @endif
                            </div>

                            <div>
                                <div class="action-menu" data-id="{{ $doc->id }}">
                                    <i class="fas fa-ellipsis-v text-muted"></i>
                                </div>
                            </div>

                        </div>

                    </div>

                @empty
                    <div class="text-center text-muted py-5">
                        Folder kosong
                    </div>
                @endforelse

            </div>

            <div class="mt-3">
                {{ $documents->links() }}
            </div>


        </div>
    </div>

    {{-- Modal Create Folder --}}
    <div class="modal fade" id="createFolderModal">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('documents.folder') }}">
                @csrf
                <input type="hidden" name="workflow_id" value="{{ $app_workflow->workflowid }}">
                @if ($folderId)
                    <input type="hidden" name="parent_id" value="{{ $folderId }}">
                @endif

                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Buat Folder Baru</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            &times;
                        </button>
                    </div>

                    <div class="modal-body">
                        <input type="text" name="name" class="form-control" placeholder="Nama Folder" required>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">
                            Simpan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Custom Context Menu --}}
    <div id="contextMenu"
        style="display:none; position:absolute; z-index:9999; background:#fff;
    border:1px solid #ddd; box-shadow:0 2px 8px rgba(0,0,0,0.1); min-width:160px;">

        <button id="renameItem" class="dropdown-item" style="cursor:pointer;">
            <i class="fas fa-edit"></i> Rename
        </button>

        <button id="detailItem" class="dropdown-item" style="cursor:pointer;">
            <i class="fas fa-info-circle"></i> Detail
        </button>

        <button id="downloadItem" class="dropdown-item">
            <i class="fas fa-download"></i> Download
        </button>

        <button id="copyLinkItem" class="dropdown-item">
            <i class="fas fa-link"></i> Copy Link
        </button>


        <div class="dropdown-divider"></div>

        <button id="deleteItem" class="dropdown-item text-danger" style="cursor:pointer;">
            <i class="fas fa-trash"></i> Hapus
        </button>
    </div>

    {{-- Modal Detail --}}
    <div class="modal fade" id="detailModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        &times;
                    </button>
                </div>
                <div class="modal-body" id="detailContent">
                </div>
            </div>
        </div>
    </div>



@endsection

@section('js')
    <script>
        const dropZone = document.getElementById('dropZone');
        const fileInput = document.getElementById('fileInput');

        function uploadFiles(files) {

            if (!files || files.length === 0) return;

            const progressWrapper = document.getElementById('uploadProgressWrapper');
            const progressBar = document.getElementById('uploadProgressBar');

            progressWrapper.style.display = 'block';
            progressBar.style.width = '0%';
            progressBar.innerText = '0%';

            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('workflow_id', '{{ $app_workflow->workflowid }}');

            @if ($folderId)
                formData.append('parent_id', '{{ $folderId }}');
            @endif

            Array.from(files).forEach(file => {
                formData.append('files[]', file);
            });

            const xhr = new XMLHttpRequest();

            xhr.open("POST", "{{ route('documents.upload') }}", true);

            // 🔥 PROGRESS EVENT
            xhr.upload.addEventListener("progress", function(e) {

                if (e.lengthComputable) {

                    let percent = Math.round((e.loaded / e.total) * 100);

                    progressBar.style.width = percent + "%";
                    progressBar.innerText = percent + "%";
                }
            });

            // SUCCESS
            xhr.onload = function() {

                if (xhr.status === 200) {

                    progressBar.classList.remove('progress-bar-animated');
                    progressBar.innerText = "Upload selesai ✔";

                    setTimeout(() => {
                        location.reload();
                    }, 800);

                } else {
                    alert("Upload gagal");
                    progressWrapper.style.display = 'none';
                }
            };

            // ERROR
            xhr.onerror = function() {
                alert("Upload error");
                progressWrapper.style.display = 'none';
            };

            xhr.send(formData);
        }


        // Manual upload
        fileInput.addEventListener('change', function() {
            uploadFiles(fileInput.files);
        });

        // Drag over
        dropZone.addEventListener('dragover', function(e) {
            e.preventDefault();
            dropZone.classList.add('dragover');
        });

        // Drag leave
        dropZone.addEventListener('dragleave', function() {
            dropZone.classList.remove('dragover');
        });

        // Drop file
        dropZone.addEventListener('drop', function(e) {
            e.preventDefault();
            dropZone.classList.remove('dragover');
            uploadFiles(e.dataTransfer.files);
        });

        const contextMenu = document.getElementById('contextMenu');
        let selectedId = null;
        let selectedItems = new Set();

        // =====================================
        // ADVANCED SELECTION SYSTEM (Explorer)
        // =====================================

        let lastSelectedIndex = null;
        const items = Array.from(document.querySelectorAll('.document-item'));

        function clearSelection() {
            items.forEach(el => el.classList.remove('selected'));
            selectedItems.clear();
            selectedId = null;
        }

        function selectItem(item) {
            item.classList.add('selected');
            selectedItems.add(item.dataset.id);
            selectedId = item.dataset.id;
        }

        function deselectItem(item) {
            item.classList.remove('selected');
            selectedItems.delete(item.dataset.id);
        }

        // ===============================
        // CLICK (CTRL + SHIFT SUPPORT)
        // ===============================

        items.forEach((item, index) => {

            item.addEventListener('click', function(e) {

                if (e.target.closest('.action-menu')) return;

                // SHIFT CLICK (range select)
                if (e.shiftKey && lastSelectedIndex !== null) {

                    clearSelection();

                    const start = Math.min(lastSelectedIndex, index);
                    const end = Math.max(lastSelectedIndex, index);

                    for (let i = start; i <= end; i++) {
                        selectItem(items[i]);
                    }

                }

                // CTRL CLICK (toggle)
                else if (e.ctrlKey) {

                    if (selectedItems.has(item.dataset.id)) {
                        deselectItem(item);
                    } else {
                        selectItem(item);
                    }

                    lastSelectedIndex = index;
                    return;
                }

                // NORMAL CLICK
                else {
                    clearSelection();
                    selectItem(item);
                    lastSelectedIndex = index;
                }

            });

        });

        // ===============================
        // CTRL + A (Select All)
        // ===============================

        document.addEventListener('keydown', function(e) {

            if (e.ctrlKey && e.key.toLowerCase() === 'a') {
                e.preventDefault();
                clearSelection();

                items.forEach(item => selectItem(item));
            }

        });

        // ===============================
        // CLICK EMPTY AREA = CLEAR
        // ===============================

        document.addEventListener('click', function(e) {

            if (!e.target.closest('.document-item')) {
                clearSelection();
            }

        });

        // ===============================
        // DRAG SELECTION BOX
        // ===============================

        let isDragging = false;
        let selectionBox = document.createElement('div');

        selectionBox.style.position = 'absolute';
        selectionBox.style.border = '1px dashed #4a90e2';
        selectionBox.style.background = 'rgba(74,144,226,0.15)';
        selectionBox.style.pointerEvents = 'none';
        selectionBox.style.display = 'none';
        selectionBox.style.zIndex = '9999';

        document.body.appendChild(selectionBox);

        let startX, startY;

        dropZone.addEventListener('mousedown', function(e) {

            if (e.target.closest('.document-item')) return;

            isDragging = true;
            clearSelection();

            startX = e.pageX;
            startY = e.pageY;

            selectionBox.style.left = startX + 'px';
            selectionBox.style.top = startY + 'px';
            selectionBox.style.width = '0px';
            selectionBox.style.height = '0px';
            selectionBox.style.display = 'block';
        });

        document.addEventListener('mousemove', function(e) {

            if (!isDragging) return;

            const currentX = e.pageX;
            const currentY = e.pageY;

            const width = currentX - startX;
            const height = currentY - startY;

            selectionBox.style.width = Math.abs(width) + 'px';
            selectionBox.style.height = Math.abs(height) + 'px';
            selectionBox.style.left = (width < 0 ? currentX : startX) + 'px';
            selectionBox.style.top = (height < 0 ? currentY : startY) + 'px';

            const boxRect = selectionBox.getBoundingClientRect();

            items.forEach(item => {

                const itemRect = item.getBoundingClientRect();

                const overlap =
                    boxRect.left < itemRect.right &&
                    boxRect.right > itemRect.left &&
                    boxRect.top < itemRect.bottom &&
                    boxRect.bottom > itemRect.top;

                if (overlap) {
                    selectItem(item);
                } else {
                    deselectItem(item);
                }

            });

        });

        document.addEventListener('mouseup', function() {

            if (!isDragging) return;

            isDragging = false;
            selectionBox.style.display = 'none';
        });

        // ===============================
        // ACTION MENU (TITIK 3)
        // ===============================

        document.querySelectorAll('.action-menu').forEach(menu => {

            menu.addEventListener('click', function(e) {

                e.stopPropagation();

                const id = this.dataset.id;
                const item = document.querySelector(`.document-item[data-id="${id}"]`);
                if (!item) return;

                // ✅ Jika item BELUM terseleksi → select hanya dia
                if (!selectedItems.has(id)) {
                    clearSelection();
                    selectItem(item);
                }

                // ✅ Jika item sudah terseleksi → biarkan multi selection tetap

                // Show menu dulu supaya bisa dihitung ukurannya
                contextMenu.style.display = 'block';

                const menuWidth = contextMenu.offsetWidth;
                const menuHeight = contextMenu.offsetHeight;

                const clickX = e.pageX;
                const clickY = e.pageY;

                const screenWidth = window.innerWidth + window.scrollX;
                const screenHeight = window.innerHeight + window.scrollY;


                // Default position
                let left = clickX;
                let top = clickY;

                // Jika terlalu kanan → geser ke kiri
                if (left + menuWidth > screenWidth) {
                    left = screenWidth - menuWidth - 10;
                }

                // Jika terlalu bawah → geser ke atas
                if (top + menuHeight > screenHeight + window.scrollY) {
                    top = clickY - menuHeight - 10;
                }

                contextMenu.style.left = left + 'px';
                contextMenu.style.top = top + 'px';

            });

        });

        // Klik di luar → hide menu
        document.addEventListener('click', function(e) {

            if (!e.target.closest('#contextMenu') &&
                !e.target.closest('.action-menu')) {

                contextMenu.style.display = 'none';
            }
        });


        // Delete action
        document.getElementById('deleteItem').addEventListener('click', function() {

            if (!selectedId) return;

            if (!confirm('Yakin ingin menghapus? Semua isi folder akan ikut terhapus.')) {
                return;
            }

            const itemElement = document.querySelector(`.document-item[data-id="${selectedId}"]`);
            if (!itemElement) return;

            itemElement.classList.add('fade-out');

            fetch(`/documents/${selectedId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (!data.success) throw new Error();

                    itemElement.remove();
                    contextMenu.style.display = 'none';
                })
                .catch(() => {
                    itemElement.classList.remove('fade-out');
                    contextMenu.style.display = 'none';
                });

        });

        // ===============================
        // GRID / LIST VIEW TOGGLE
        // ===============================
        const gridBtn = document.getElementById('gridViewBtn');
        const listBtn = document.getElementById('listViewBtn');
        const container = document.getElementById('dropZone');

        // default mode
        let savedView = localStorage.getItem('docView') || 'grid';

        if (savedView === 'list') {
            container.classList.remove('grid-view');
            container.classList.add('list-view');
            listBtn.classList.add('active');
            gridBtn.classList.remove('active');
        } else {
            container.classList.add('grid-view');
        }

        gridBtn.addEventListener('click', function() {
            container.classList.remove('list-view');
            container.classList.add('grid-view');
            gridBtn.classList.add('active');
            listBtn.classList.remove('active');
            localStorage.setItem('docView', 'grid');
        });

        listBtn.addEventListener('click', function() {
            container.classList.remove('grid-view');
            container.classList.add('list-view');
            listBtn.classList.add('active');
            gridBtn.classList.remove('active');
            localStorage.setItem('docView', 'list');
        });

        // ===============================
        // DELETE KEY = DELETE MULTI
        // ===============================

        document.addEventListener('keydown', function(e) {

            if (e.key === 'Delete' && selectedItems.size > 0) {

                e.preventDefault();

                if (!confirm(`Yakin ingin menghapus ${selectedItems.size} item?`)) return;

                selectedItems.forEach(id => {

                    const itemElement = document.querySelector(`.document-item[data-id="${id}"]`);
                    if (!itemElement) return;

                    itemElement.classList.add('fade-out');

                    fetch(`/documents/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            }
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                itemElement.remove();
                            }
                        });

                });

                selectedItems.clear();
                selectedId = null;
            }

        });



        // ===============================
        // DOUBLE CLICK FOR GRID ONLY
        // ===============================

        document.querySelectorAll('.grid-content').forEach(item => {

            item.addEventListener('dblclick', function(e) {

                // Jangan trigger kalau klik titik 3
                if (e.target.closest('.action-menu')) return;

                const url = this.dataset.url;
                const type = this.dataset.type;

                if (!url) return;

                if (type === 'folder') {
                    window.location.href = url;
                } else {

                    const extension = url.split('.').pop().toLowerCase();

                    // FILE OFFICE → OPEN PREVIEW
                    if (['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'].includes(extension)) {

                        const viewer = "https://view.officeapps.live.com/op/view.aspx?src=" +
                            encodeURIComponent(url);
                        window.open(viewer, '_blank');

                    } else {
                        // PDF / IMAGE / OTHERS
                        window.open(url, '_blank');
                    }
                }

            });

        });

        // ===============================
        // RENAME
        // ===============================

        document.getElementById('renameItem').addEventListener('click', function() {

            if (!selectedId) return;

            const item = document.querySelector(`.document-item[data-id="${selectedId}"]`);
            if (!item) return;

            const nameElement = item.querySelector('.file-name, .doc-name a');
            const currentName = nameElement.innerText;

            const newName = prompt("Rename file:", currentName);

            if (!newName || newName === currentName) return;

            fetch(`/documents/rename/${selectedId}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        name: newName
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (!data.success) throw new Error();

                    nameElement.innerText = data.name;
                    contextMenu.style.display = 'none';
                })
                .catch(() => alert('Rename gagal'));
        });

        // ===============================
        // F2 = RENAME (SINGLE ONLY)
        // ===============================

        document.addEventListener('keydown', function(e) {

            if (e.key === 'F2' && selectedItems.size === 1) {

                e.preventDefault();

                const item = document.querySelector(`.document-item[data-id="${selectedId}"]`);
                if (!item) return;

                const nameElement = item.querySelector('.file-name, .doc-name a');
                const currentName = nameElement.innerText;

                const newName = prompt("Rename file:", currentName);
                if (!newName || newName === currentName) return;

                fetch(`/documents/rename/${selectedId}`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            name: newName
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (!data.success) throw new Error();
                        nameElement.innerText = data.name;
                    })
                    .catch(() => alert('Rename gagal'));
            }

        });


        // ===============================
        // DETAIL
        // ===============================

        document.getElementById('detailItem').addEventListener('click', function() {

            if (!selectedId) return;

            const item = document.querySelector(`.document-item[data-id="${selectedId}"]`);
            if (!item) return;

            const name = item.querySelector('.file-name, .doc-name a').innerText;
            const owner = item.querySelector('.list-content div:nth-child(2)')?.innerText || '-';
            const date = item.querySelector('.list-content div:nth-child(3)')?.innerText || '-';
            const size = item.querySelector('.list-content div:nth-child(4)')?.innerText || '-';

            document.getElementById('detailContent').innerHTML = `
    <strong>Nama:</strong> ${name}<br>
    <strong>Pemilik:</strong> ${owner}<br>
    <strong>Tanggal diubah:</strong> ${date}<br>
    <strong>Ukuran:</strong> ${size}
`;

            $('#detailModal').modal('show');
            contextMenu.style.display = 'none';
        });

        document.getElementById('downloadItem').addEventListener('click', function(e) {

            e.preventDefault();

            if (selectedItems.size === 0) {
                alert('Tidak ada file yang dipilih');
                return;
            }

            const ids = Array.from(selectedItems);

            // 🔥 SINGLE FILE
            // 🔥 SINGLE FILE
            if (ids.length === 1) {

                const item = document.querySelector(`.document-item[data-id="${ids[0]}"]`);
                if (!item) return;

                const grid = item.querySelector('.grid-content');
                const type = grid?.dataset.type;

                if (type === 'folder') {
                    alert('Folder tidak bisa didownload langsung');
                    return;
                }

                const fileUrl = grid.dataset.url;

                const a = document.createElement('a');
                a.href = fileUrl;
                a.setAttribute('download', '');
                document.body.appendChild(a);
                a.click();
                a.remove();
            }


            // 🔥 MULTIPLE FILES → ZIP
            else {

                const query = ids.join(','); // ← INI YANG KURANG
                const baseUrl = "{{ url('/documents/download') }}";

                window.location.href = baseUrl + "?ids=" + query;
            }

            contextMenu.style.display = 'none';
        });

        // ===============================
        // COPY LINK
        // ===============================

        document.getElementById('copyLinkItem').addEventListener('click', function() {

            if (!selectedId) return;

            const item = document.querySelector(`.document-item[data-id="${selectedId}"]`);
            if (!item) return;

            const grid = item.querySelector('.grid-content');
            const type = grid?.dataset.type;
            let url = null;

            if (type === 'folder') {
                url = grid.dataset.url; // route folder
            } else {
                url = grid.dataset.url; // storage file
            }

            if (!url) return;

            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(url)
                    .then(() => showToast('Link berhasil disalin'))
                    .catch(() => fallbackCopy(url));
            } else {
                fallbackCopy(url);
            }

            function fallbackCopy(text) {
                const textarea = document.createElement("textarea");
                textarea.value = text;
                textarea.style.position = "fixed";
                textarea.style.left = "-9999px";
                document.body.appendChild(textarea);
                textarea.select();
                document.execCommand("copy");
                textarea.remove();
                showToast('Link berhasil disalin');
            }


            contextMenu.style.display = 'none';
        });

        function showToast(message) {

            const toast = document.createElement('div');
            toast.innerText = message;

            toast.style.position = 'fixed';
            toast.style.bottom = '20px';
            toast.style.right = '20px';
            toast.style.background = '#333';
            toast.style.color = '#fff';
            toast.style.padding = '10px 16px';
            toast.style.borderRadius = '6px';
            toast.style.fontSize = '14px';
            toast.style.zIndex = '9999';
            toast.style.opacity = '0';
            toast.style.transition = '0.3s';

            document.body.appendChild(toast);

            setTimeout(() => toast.style.opacity = '1', 50);
            setTimeout(() => {
                toast.style.opacity = '0';
                setTimeout(() => toast.remove(), 300);
            }, 2000);
        }
    </script>
@endsection
