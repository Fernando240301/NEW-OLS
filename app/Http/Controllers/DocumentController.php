<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Document;
use ZipArchive;

class DocumentController extends Controller
{
    // ===============================
    // INDEX / OPEN FOLDER
    // ===============================
    public function documents(Request $request, $workflowid, $folderId = null)
    {
        $app_workflow = DB::table('app_workflow')
            ->where('workflowid', $workflowid)
            ->first();

        if (!$app_workflow) {
            abort(404);
        }

        $workflowdata = json_decode($app_workflow->workflowdata, true);

        $search = $request->search;

        $documents = Document::where('workflowid', $workflowid)

            // Folder logic
            ->when(!$search, function ($query) use ($folderId) {
                if ($folderId) {
                    $query->where('parent_id', $folderId);
                } else {
                    $query->whereNull('parent_id');
                }
            })

            // 🔥 SEARCH LOGIC
            ->when($search, function ($query) use ($search, $workflowid) {
                $query->where('workflowid', $workflowid)
                    ->where('name', 'like', "%{$search}%");
            })



            ->orderBy('type', 'desc')
            ->orderBy('name')
            ->paginate(40)
            ->withQueryString();

        // ===============================
        // BUILD BREADCRUMB
        // ===============================
        $breadcrumbs = [];

        $currentFolder = null;

        if ($folderId) {
            $currentFolder = Document::find($folderId);

            while ($currentFolder) {
                $breadcrumbs[] = $currentFolder;

                $currentFolder = $currentFolder->parent_id
                    ? Document::find($currentFolder->parent_id)
                    : null;
            }

            $breadcrumbs = array_reverse($breadcrumbs);
        }


        return view('documents.index', compact(
            'workflowdata',
            'app_workflow',
            'documents',
            'folderId',
            'search',
            'breadcrumbs'
        ));
    }


    // ===============================
    // UPLOAD FILE (MULTIPLE)
    // ===============================
    public function upload(Request $request)
    {
        if (!$request->hasFile('files')) {
            return response()->json(['error' => 'No files received'], 400);
        }

        $request->validate([
            'workflow_id' => 'required',
            'files.*' => 'file'
        ]);

        $files = $request->file('files');
        $parentId = $request->parent_id ?: null;
        $username = Auth::check() ? Auth::user()->username : 'system';

        foreach ($files as $file) {

            if (!$file->isValid()) {
                continue;
            }

            // UNIQUE NAME (NO OVERWRITE)
            $uniqueName = Str::uuid() . '_' . $file->getClientOriginalName();

            $storedPath = $file->storeAs(
                'documents/' . $request->workflow_id,
                $uniqueName,
                'public'
            );

            Document::create([
                'workflowid'  => $request->workflow_id,
                'parent_id'   => $parentId,
                'name'        => $file->getClientOriginalName(),
                'type'        => 'file',
                'file_path'   => $storedPath,
                'mime_type'   => $file->getMimeType(),
                'uploaded_by' => $username,
            ]);
        }

        return response()->json(['success' => true]);
    }

    // ===============================
    // CREATE FOLDER
    // ===============================
    public function createFolder(Request $request)
    {
        $request->validate([
            'workflow_id' => 'required',
            'name' => 'required|string|max:255'
        ]);

        $username = Auth::check() ? Auth::user()->username : 'system';

        Document::create([
            'workflowid'  => $request->workflow_id,
            'parent_id'   => $request->parent_id ?: null,
            'name'        => $request->name,
            'type'        => 'folder',
            'uploaded_by' => $username,
        ]);

        return back()->with('success', 'Folder berhasil dibuat');
    }

    // ===============================
    // DELETE (FILE / FOLDER RECURSIVE)
    // ===============================
    public function destroy($id)
    {
        $doc = Document::findOrFail($id);

        DB::transaction(function () use ($doc) {
            $this->deleteRecursive($doc);
        });

        return response()->json([
            'success' => true
        ], 200);
    }



    // ===============================
    // RECURSIVE FUNCTION
    // ===============================
    private function deleteRecursive($document)
    {
        // Jika folder → hapus semua child dulu
        if ($document->type === 'folder') {

            $children = Document::where('parent_id', $document->id)->get();

            foreach ($children as $child) {
                $this->deleteRecursive($child);
            }
        }

        // Jika file → hapus file fisik
        if ($document->type === 'file' && $document->file_path) {
            Storage::disk('public')->delete($document->file_path);
        }

        $document->delete();
    }

    public function rename(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $doc = Document::findOrFail($id);

        $doc->name = $request->name;
        $doc->save();

        return response()->json([
            'success' => true,
            'name' => $doc->name
        ]);
    }

    public function download(Request $request)
    {
        dd($request->ids);
    }
}
