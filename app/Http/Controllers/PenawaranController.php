<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Storage;
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
use App\Models\JenisPeralatan;
use App\Models\User;
use App\Models\SysUser;
use App\Models\Penawaran;
use PhpOffice\PhpWord\Element\TextRun;
use PhpOffice\PhpWord\Element\Text;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpWord\SimpleType\Jc;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Http\Request;
class PenawaranController extends Controller
{
    public function index()
{
    $data = Penawaran::with(['jenis', 'client','SysUser','picMitUser','approver'])
        ->get()
        ->map(function ($item) {

            $wordPath = storage_path('app/public/' . $item->surat);

            $item->can_approve = false;

            if ($item->surat && file_exists($wordPath)) {
                $item->can_approve = $this->wordHasPlaceholder($wordPath, '${QR_TTD}');
            }

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
        'nosurat'    => 'required|unique:penawaran,nosurat',
        'surat' => 'required|mimes:doc,docx|max:5120',

    ]);

    $userId = Auth::id();  // Ambil ID login
    if (!$userId) {
        return redirect()->route('login')->with('error', 'Anda harus login terlebih dahulu.');
    }
    $filePath = null;

    $judul = null;

if ($request->hasFile('surat')) {
    $file = $request->file('surat');
    $filePath = $file->store('penawaran', 'public');

    // BACA WORD
$judul = '';
$ambil = false;

$stopKeywords = [
    'kepada',
    'dengan hormat',
    'menindaklanjuti',
    'sehubungan',
];

$phpWord = IOFactory::load($file->getPathname());

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
            continue;
        }

        // ===== DETEKSI PERIHAL =====
        if (!$ambil && stripos($text, 'perihal') !== false) {
            $ambil = true;

            // Ambil teks setelah titik dua
            if (strpos($text, ':') !== false) {
                $afterColon = trim(preg_replace('/.*perihal\s*:\s*/i', '', $text));
                if ($afterColon !== '') {
                    $judul .= $afterColon;
                }
            }

            continue;
        }

        // ===== STOP JIKA MASUK BAGIAN BARU =====
        if ($ambil) {
            foreach ($stopKeywords as $stop) {
                if (stripos($text, $stop) !== false) {
                    break 3; // STOP TOTAL
                }
            }

            // STOP jika format bab
            if (preg_match('/^(I\.|1\.|A\.)/', $text)) {
                break 2;
            }

            // Tambah ke judul (multi-line)
            $judul .= ' ' . $text;
        }
    }

}

$namaClient = null;
$ambilClient = false;

$stopClient = [
    'di tempat',
    'dengan hormat',
    'perihal',
];

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

        if ($text === '') continue;

        // ===== DETEKSI KEPADA YTH =====
        if (!$ambilClient && stripos($text, 'kepada') !== false) {

            // Jika satu baris
            if (strpos($text, ':') !== false) {
                $after = trim(explode(':', $text, 2)[1]);
                if ($after !== '') {
                    $namaClient = $after;
                    break 2;
                }
            }

            $ambilClient = true;
            continue;
        }

        // ===== AMBIL BARIS SETELAHNYA =====
        if ($ambilClient) {

            // STOP jika masuk bagian lain
            foreach ($stopClient as $stop) {
                if (stripos($text, $stop) !== false) {
                    break 3;
                }
            }

            // Ambil baris pertama valid
            if (strlen($text) > 3 && preg_match('/[a-zA-Z]/', $text)) {
                $namaClient = $text;
                break 2;
            }
        }
    }
}


$judul = trim($judul);

}

     Penawaran::create([
    'nosurat'    => $request->nosurat,
    'judul'      => $judul, // ✅ PAKAI HASIL BACA WORD
    'namaclient' => $namaClient,
    'pic'        => $request->pic ?? null,
    'picmit'     => Auth::id(),
    'tanggal'    => now(),
    'status'     => $request->status ?? null,
    'harga'      => $request->harga ?? null,
    'surat'      => $filePath,
]);


    return redirect()->route('penawaran.index')->with('success', 'Data berhasil disimpan!');
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
    DB::beginTransaction();

    try {
        $penawaran = Penawaran::findOrFail($id);
        $approvedBy = auth()->id();

        /* ===============================
         * 1. GENERATE QR
         * =============================== */
        $qrText = json_encode([
            'penawaran_id' => $penawaran->id,
            'nosurat'      => $penawaran->nosurat,
            'approved_by'  => $approvedBy,
            'approved_at'  => now()->toDateTimeString(),
        ]);

        $qrCode = new QrCode($qrText);
        $qrCode->setSize(200)->setMargin(10);

        $writer = new PngWriter();
        $result = $writer->write($qrCode);

        $barcodePath = storage_path("app/public/barcode/penawaran_{$penawaran->id}.png");
        if (!file_exists(dirname($barcodePath))) {
            mkdir(dirname($barcodePath), 0777, true);
        }
        $result->saveToFile($barcodePath);

        /* ===============================
         * 2. LOAD WORD AS TEMPLATE
         * =============================== */
        $wordPath = storage_path('app/public/' . $penawaran->surat);
        if (!file_exists($wordPath)) {
            throw new \Exception('File Word tidak ditemukan');
        }

        if (!$this->wordHasPlaceholder($wordPath, '${QR_TTD}')) {
        throw new \Exception(
    'File Word belum memiliki area tanda tangan QR. ' .
    'Pastikan terdapat teks ${QR_TTD} di footer dokumen.'
);

        }

$template = new TemplateProcessor($wordPath);

        /* ===============================
         * 3. GANTI PLACEHOLDER QR
         * =============================== */
        $template->setImageValue('QR_TTD', [
            'path'   => $barcodePath,
            'width'  => 100,
            'height' => 100,
            'ratio'  => true,
        ]);

        /* ===============================
         * 4. SIMPAN WORD APPROVED
         * =============================== */
        $approvedWordName = 'APPROVED_' . basename($penawaran->surat);
        $approvedWordPath = storage_path('app/public/penawaran/' . $approvedWordName);

        if (!file_exists(dirname($approvedWordPath))) {
            mkdir(dirname($approvedWordPath), 0777, true);
        }

        $template->saveAs($approvedWordPath);

        /* ===============================
         * 5. WORD → PDF
         * =============================== */
        $phpWord = IOFactory::load($approvedWordPath);
        $htmlWriter = IOFactory::createWriter($phpWord, 'HTML');

        ob_start();
        $htmlWriter->save('php://output');
        $html = ob_get_clean();

        if (!Storage::exists('public/pdf')) {
            Storage::makeDirectory('public/pdf');
        }

        $pdfName = 'penawaran_' . $penawaran->id . '.pdf';

        $pdf = Pdf::loadHTML($html)->setOptions([
            'isRemoteEnabled' => true,
            'isHtml5ParserEnabled' => true,
        ]);

        Storage::put('public/pdf/' . $pdfName, $pdf->output());

        /* ===============================
         * 6. UPDATE DB
         * =============================== */
        $penawaran->update([
            'status'        => 'approved',
            'approved_by'   => $approvedBy,
            'barcode'       => 'barcode/penawaran_' . $penawaran->id . '.png',
            'pdf'           => 'pdf/' . $pdfName,
            'approved_word' => 'penawaran/' . $approvedWordName,
        ]);

        DB::commit();

        return redirect()
            ->route('penawaran.index')
            ->with('success', 'Approve berhasil, layout Word tetap rapi');

    } catch (\Throwable $e) {
        DB::rollBack();
        return back()->with('error', $e->getMessage());
    }
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
    ]);

    return redirect()
        ->route('penawaran.index')
        ->with('success', 'Revisi diajukan. Silakan upload dokumen terbaru.');
}


}

