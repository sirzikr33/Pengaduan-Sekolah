<?php

namespace App\Exports;

use App\Models\Pengaduan;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PengaduanExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithTitle
{
    protected array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function title(): string
    {
        return 'Rekap Pengaduan';
    }

    public function query()
    {
        $query = Pengaduan::with(['siswa', 'kategori'])->latest();

        if (!empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        if (!empty($this->filters['kondisi'])) {
            $query->where('kondisi_pengaduan', $this->filters['kondisi']);
        }

        if (!empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('nama_pengaduan', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%")
                  ->orWhereHas('siswa', fn($s) => $s->where('nama', 'like', "%{$search}%"));
            });
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Pengaduan',
            'Siswa',
            'Kelas',
            'Kategori',
            'Deskripsi',
            'Lokasi',
            'Kondisi',
            'Status',
            'Tanggal Pengaduan',
        ];
    }

    public function map($pengaduan): array
    {
        static $no = 0;
        $no++;

        return [
            $no,
            $pengaduan->nama_pengaduan,
            $pengaduan->siswa->nama ?? '-',
            $pengaduan->siswa->kelas ?? '-',
            $pengaduan->kategori->nama_kategori ?? '-',
            $pengaduan->deskripsi,
            $pengaduan->lokasi,
            ucfirst($pengaduan->kondisi_pengaduan),
            ucfirst($pengaduan->status),
            $pengaduan->tanggal_pengaduan ? $pengaduan->tanggal_pengaduan->format('d/m/Y') : '-',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            // Header row bold + hijau muda
            1 => [
                'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill'      => ['fillType' => 'solid', 'startColor' => ['argb' => 'FF22A645']],
                'alignment' => ['horizontal' => 'center'],
            ],
        ];
    }
}
