<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelLow;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use PhpOffice\PhpWord\TemplateProcessor;
use Endroid\QrCode\Encoding\Encoding;
use App\Models\Prospect;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use App\Models\JenisPeralatan;
use App\Models\User;
use PhpOffice\PhpWord\Shared\Html;
use App\Models\SysUser;
use App\Models\Penawaran;
use PhpOffice\PhpWord\Element\TextRun;
use PhpOffice\PhpWord\Element\Text;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpWord\SimpleType\Jc;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;
use Illuminate\Http\Request;
class PenawaranController extends Controller
{
    public function index()
{
    $user = Auth::user();

    // 🔥 pakai username sebagai pembeda
    $isDeam = strtolower($user->username) === 'deam';

    $data = Penawaran::with(['jenis', 'client','SysUser','picMitUser','approver'])
        ->get()
        ->map(function ($item) use ($isDeam) {

            $wordPath = storage_path('app/public/' . $item->surat);

            $hasPlaceholder = false;

            if ($item->surat && file_exists($wordPath)) {
                $hasPlaceholder = $this->wordHasPlaceholder($wordPath, '${QR_TTD}');
            }

            // 🔥 FINAL LOGIC APPROVE
            $item->can_approve = $isDeam && $hasPlaceholder;

            return $item;
        });

    return view('penawaran.index', compact('data'));
}

    
    public function create()
    {   
        $noSurat = $this->generateNoSurat();
        $penawaran = Penawaran::all();
        return view('penawaran.create', compact('penawaran','noSurat'));
    }
     function generateNoSurat()
{
    $now = Carbon::now();
    $prefix = 'QT' . $now->format('ym'); // QT2602

    // cari no terakhir di bulan yg sama
    $last = Penawaran::where('nosurat', 'like', $prefix . '%')
        ->orderBy('nosurat', 'desc')
        ->first();

    $lastNumber = 0;

    if ($last) {
        $lastNumber = (int) substr($last->nosurat, 6, 4);
    }

    $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);

    return $prefix . $newNumber . '.MT';
}
public function store(Request $request)
{
    $request->validate([
        'nosurat' => 'required|unique:penawaran,nosurat',
        'surat' => 'required|mimes:doc,docx|max:5120',
    ]);

    $filePath = null;
    $judul = null;
    $namaClient = null;

    if ($request->hasFile('surat')) {
        $file = $request->file('surat');
        $filePath = $file->store('penawaran', 'public');

        $phpWord = IOFactory::load($file->getPathname());

        $judul = '';
        $ambilSubject = false;

        $ambilClient = false;
        $namaClientLines = [];

        foreach ($phpWord->getSections() as $section) {
            foreach ($section->getElements() as $element) {

                $text = '';

                if ($element instanceof Text) {
                    $text = trim($element->getText());
                }

                if ($element instanceof TextRun) {
                    foreach ($element->getElements() as $child) {
                        if ($child instanceof Text) {
                            $text .= $child->getText();
                        }
                    }
                    $text = trim($text);
                }

                if ($text === '') {
                    // Jika baris kosong dan sudah mulai ambil subject, hentikan pengambilan
                    if ($ambilSubject) {
                        $ambilSubject = false;
                    }
                    // Jika baris kosong dan sedang ambil client, anggap selesai ambil client
                    if ($ambilClient) {
                        $ambilClient = false;
                    }
                    continue;
                }

                // Ambil subject
                if (!$ambilSubject) {
                    if (stripos($text, 'perihal') !== false || stripos($text, 'subject') !== false) {
                        $ambilSubject = true;
                        if (strpos($text, ':') !== false) {
                            $afterColon = trim(preg_replace('/.(perihal|subject)\s:\s*/i', '', $text));
                            $judul .= $afterColon;
                        } else {
                            $judul .= $text;
                        }
                        continue;
                    }
                } else {
                    $judul .= ' ' . $text;
                }
                // Ambil nama client
                if (!$ambilClient) {
                    // Deteksi baris yang mengandung "kepada" atau "kepada yth"
                    if (preg_match('/^kepada\s*(yth)?[,.]?/i', $text)) {
                        $ambilClient = true;
                        // Bersihkan kata "Kepada Yth," dari baris ini, jika ada teks sesudahnya simpan, kalau tidak lanjut ambil baris berikutnya
                        $cleaned = preg_replace('/^kepada\s*(yth)?[,.]?\s*/i', '', $text);
                        if ($cleaned !== '') {
                            $namaClientLines[] = $cleaned;
                        }
                        continue;
                    }
                } else {
                    // Jika sudah mulai ambil client, cek apakah baris ini adalah "di tempat" atau baris kosong (di atas sudah dicek kosong)
                    if (preg_match('/^di tempat$/i', $text)) {
                        // Selesai ambil client
                        $ambilClient = false;
                        continue;
                    }
                    // Tambahkan baris client
                    $namaClientLines[] = $text;
                }
            }
        }

        $judul = trim($judul);
        $judul = substr($judul, 0, 255);
        $namaClient = implode(' ', $namaClientLines);
        $namaClient = trim($namaClient);
        $namaClient = substr($namaClient, 0, 255);
    }

    Penawaran::create([
        'nosurat' => $request->nosurat,
        'judul' => $judul,
        'namaclient' => $namaClient,
        'picmit' => Auth::id(),
        'tanggal' => now(),
        'surat' => $filePath,
    ]);

    return redirect()->route('penawaran.index')
        ->with('success', 'Data berhasil disimpan!');
}
public function upload($id)
{
    $penawaran = Penawaran::findOrFail($id);
    return view('penawaran.upload', compact('penawaran'));
}
public function uploadStore(Request $request, $id)
{
    $request->validate([
        'surat' => 'required|mimes:doc,docx|max:5120',
    ]);

    $penawaran = Penawaran::findOrFail($id);

    $path = $request->file('surat')
        ->store('penawaran', 'public');

    $penawaran->update([
        'surat'          => $path,
        'status'         => 'draft',   // 🔥 reset ke draft
        'revision_note'  => null,       // 🔥 bersihkan catatan revisi
        'approved_by'    => null,
        'barcode'        => null,
        'approved_word'  => null,
        'pdf'            => null,
    ]);

    return redirect()
        ->route('penawaran.index')
        ->with('success', 'Revisi berhasil diupload. Silakan approve ulang.');
    }


public function approve($id)
{
    $penawaran = Penawaran::findOrFail($id);
    $approvedBy = auth()->id();

    DB::beginTransaction();

    try {
        /* ===============================
         * 1. SIMPAN DATA UTAMA DULU (ANTI FAIL)
         * =============================== */
        $penawaran->hash = hash_hmac(
            'sha256',
            $penawaran->id . $penawaran->nosurat . now(),
            config('app.key')
        );

        $penawaran->status = 'approved';
        $penawaran->approved_by = $approvedBy;
        $penawaran->save();

        DB::commit();
    } catch (\Throwable $e) {
        DB::rollBack();
        return back()->with('error', 'Gagal approve: '.$e->getMessage());
    }

    /* ===============================
     * 2. QR CODE (TIDAK BIKIN GAGAL)
     * =============================== */
    try {
        $qrText = route('penawaran.verify', $penawaran->hash);

        $qrCode = new QrCode($qrText);
        $qrCode->setSize(200)->setMargin(10);

        $writer = new PngWriter();

        $barcodeDir = storage_path('app/public/barcode');
        if (!file_exists($barcodeDir)) mkdir($barcodeDir, 0777, true);

        $barcodeName = "penawaran_{$penawaran->id}.png";
        $barcodePath = $barcodeDir . '/' . $barcodeName;

        $writer->write($qrCode)->saveToFile($barcodePath);

        $penawaran->barcode = 'barcode/' . $barcodeName;
        $penawaran->save();

    } catch (\Throwable $e) {
        Log::error('QR ERROR: '.$e->getMessage());
    }

    /* ===============================
     * 3. WORD APPROVED (OPTIONAL)
     * =============================== */
    try {
    if (!empty($penawaran->surat)) {

        $wordPath = storage_path('app/public/' . $penawaran->surat);

        if (file_exists($wordPath)) {

            $template = new TemplateProcessor($wordPath);

            if ($penawaran->barcode) {
                $template->setImageValue('QR_TTD', [
                    'path'   => storage_path('app/public/' . $penawaran->barcode),
                    'width'  => 100,
                    'height' => 100,
                    'ratio'  => true,
                ]);
            }

            $approvedWordName = 'APPROVED_' . basename($penawaran->surat);
            $approvedWordDir = storage_path('app/public/penawaran');

            if (!file_exists($approvedWordDir)) {
                mkdir($approvedWordDir, 0777, true);
            }

            $approvedWordPath = $approvedWordDir . '/' . $approvedWordName;

            $template->saveAs($approvedWordPath);

            if (!file_exists($approvedWordPath)) {
                Log::error("FAILED SAVE DOCX: " . $approvedWordPath);
            }

            $penawaran->approved_word = 'penawaran/' . $approvedWordName;
            $penawaran->save();
        }
    }
        } catch (\Throwable $e) {
            Log::error('WORD ERROR: ' . $e->getMessage());
        }

    /* ===============================
     * 4. PDF GENERATE (OPTIONAL)
     * =============================== */
    /* ===============================
 * 4. PDF GENERATE (PAKAI LIBREOFFICE)
 * =============================== */
try {

    if (!empty($penawaran->approved_word)) {

        $docxPath = storage_path('app/public/' . $penawaran->approved_word);

        if (!file_exists($docxPath)) {
            throw new \Exception("DOCX tidak ditemukan");
        }

        $outputDir = storage_path('app/public/pdf');

        if (!file_exists($outputDir)) {
            mkdir($outputDir, 0777, true);
        }

        $soffice = '"C:\\Program Files\\LibreOffice\\program\\soffice.exe"';

        $input = escapeshellarg($docxPath);
        $output = escapeshellarg($outputDir);

        $command = "$soffice --headless --convert-to pdf:writer_pdf_Export --outdir $output $input 2>&1";

        exec($command, $out, $return);

        $pdfName = pathinfo($docxPath, PATHINFO_FILENAME) . '.pdf';
        $pdfFullPath = $outputDir . DIRECTORY_SEPARATOR . $pdfName;

        sleep(1);

        if (!file_exists($pdfFullPath)) {
            throw new \Exception("PDF gagal dibuat");
        }

        $penawaran->pdf = 'pdf/' . $pdfName;
        $penawaran->save();
    }

} catch (\Throwable $e) {
    Log::error('PDF ERROR: ' . $e->getMessage());
}

    return redirect()
        ->route('penawaran.index')
        ->with('success', 'Penawaran berhasil disetujui!');
}
private function wordHasPlaceholder(string $docxPath, string $placeholder): bool
{
    $zip = new \ZipArchive;

    if ($zip->open($docxPath) === true) {
        $xml = $zip->getFromName('word/document.xml');
        $zip->close();

        if ($xml === false) {
            return false;
        }

        return str_contains($xml, $placeholder);
    }

    return false;
}
public function revisi(Request $request, $id)
{
    $request->validate([
        'revision_note' => 'required|min:5',
    ]);

    $penawaran = Penawaran::findOrFail($id);

    // RESET APPROVAL
    $penawaran->update([
        'status'        => 'revision',
        'is_revision'   => true,
        'revision_note' => $request->revision_note,
        'barcode'       => null,
        'approved_by'   => null,
        'approved_word' => null,
        'pdf'           => null,
        'hash'          => null, 
    ]);

    return redirect()
        ->route('penawaran.index')
        ->with('success', 'Revisi diajukan. Silakan upload dokumen terbaru.');
}
public function edit($id)
{
    $penawaran = Penawaran::findOrFail($id);
    return view('penawaran.edit', compact('penawaran'));
}
public function update(Request $request, $id)
{
    $request->validate([
        'judul' => 'required',
        'surat' => 'nullable|mimes:doc,docx|max:5120',
    ]);

    $penawaran = Penawaran::findOrFail($id);

    $data = [
        'judul' => $request->judul,
    ];

    /* ===============================
     * 🔥 JIKA ADA FILE BARU
     * =============================== */
    if ($request->hasFile('surat')) {

        // hapus file lama
        if ($penawaran->surat && Storage::exists('public/' . $penawaran->surat)) {
            Storage::delete('public/' . $penawaran->surat);
        }

        // simpan file baru
        $path = $request->file('surat')->store('penawaran', 'public');

        $data['surat'] = $path;

        // 🔥 reset semua hasil approve
        $data['status'] = 'draft';
        $data['hash'] = null;
        $data['barcode'] = null;
        $data['approved_word'] = null;
        $data['pdf'] = null;
        $data['approved_by'] = null;
    }

    $penawaran->update($data);

    return redirect()->route('penawaran.index')
        ->with('success', 'Data berhasil diupdate');
}
public function verify($hash)
{
    $penawaran = Penawaran::where('hash', $hash)->first();

    if (!$penawaran) {
        abort(404, 'Dokumen tidak terdaftar');
    }

    if ($penawaran->status !== 'approved') {
        return view('penawaran.valid', [
            'status' => 'invalid',
            'message' => 'Dokumen belum disetujui'
        ]);
    }

    return view('penawaran.valid', [
        'status' => 'valid',
        'penawaran' => $penawaran
    ]);
}
public function viewPdf($id, $hash)
{
    $penawaran = Penawaran::findOrFail($id);

    if ($penawaran->hash !== $hash) {
        abort(403, 'Hash tidak valid');
    }

    if (!$penawaran->pdf) {
        abort(404, 'PDF belum ada');
    }

    $fullPath = storage_path('app/public/' . $penawaran->pdf);

    if (!file_exists($fullPath)) {
        Log::error("PDF NOT FOUND", [
            'db' => $penawaran->pdf,
            'path' => $fullPath
        ]);
        abort(404, 'File PDF tidak ditemukan di storage');
    }

    return response()->file($fullPath, [
        'Content-Type' => 'application/pdf',
        'Content-Disposition' => 'inline; filename="'.$penawaran->nosurat.'.pdf"',
    ]);
}

}