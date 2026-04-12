<?php

namespace Database\Seeders;

use App\Models\Kategori;
use App\Models\Pengaduan;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // ─── Admin ───────────────────────────────────────────
        User::create([
            'name'     => 'Administrator',
            'email'    => 'admin@sekolah.sch.id',
            'password' => Hash::make('admin123'),
        ]);

        // ─── Kategori Pengaduan ───────────────────────────────
        $kategoris = [
            ['nama_kategori' => 'Fasilitas Kelas'],
            ['nama_kategori' => 'Kebersihan Sekolah'],
            ['nama_kategori' => 'Keamanan Sekolah'],
            ['nama_kategori' => 'Sarana Olahraga'],
            ['nama_kategori' => 'Peralatan Lab'],
        ];

        foreach ($kategoris as $kat) {
            Kategori::create($kat);
        }

        $allKategori = Kategori::all();

        // ─── Siswa ────────────────────────────────────────────
        $siswas = [
            ['nisn' => '0123456789', 'nama' => 'Ahmad Fauzan',     'kelas' => 'X RPL 1'],
            ['nisn' => '0123456790', 'nama' => 'Siti Rahmawati',   'kelas' => 'X RPL 2'],
            ['nisn' => '0123456791', 'nama' => 'Budi Santoso',     'kelas' => 'XI TKJ 1'],
            ['nisn' => '0123456792', 'nama' => 'Dewi Lestari',     'kelas' => 'XI TKJ 2'],
            ['nisn' => '0123456793', 'nama' => 'Rizky Pratama',    'kelas' => 'XII MM 1'],
        ];

        foreach ($siswas as $sw) {
            Siswa::create($sw);
        }

        $allSiswa = Siswa::all();

        // ─── Pengaduan ────────────────────────────────────────
        $pengaduans = [
            [
                'nama_barang'      => 'Kursi Rusak',
                'foto_barang'      => 'kursi_rusak.jpg',
                'status'           => 'pending',
                'tanggal_pengaduan'=> '2026-04-01',
            ],
            [
                'nama_barang'      => 'Proyektor Tidak Menyala',
                'foto_barang'      => 'proyektor.jpg',
                'status'           => 'proses',
                'tanggal_pengaduan'=> '2026-04-03',
            ],
            [
                'nama_barang'      => 'Kaca Jendela Pecah',
                'foto_barang'      => 'kaca_pecah.jpg',
                'status'           => 'selesai',
                'tanggal_pengaduan'=> '2026-03-28',
            ],
            [
                'nama_barang'      => 'Bola Voli Kempes',
                'foto_barang'      => 'bola_voli.jpg',
                'status'           => 'pending',
                'tanggal_pengaduan'=> '2026-04-05',
            ],
            [
                'nama_barang'      => 'Komputer Lab Error',
                'foto_barang'      => 'komputer_lab.jpg',
                'status'           => 'proses',
                'tanggal_pengaduan'=> '2026-04-07',
            ],
        ];

        foreach ($pengaduans as $index => $pg) {
            Pengaduan::create(array_merge($pg, [
                'siswa_id'    => $allSiswa[$index % $allSiswa->count()]->id,
                'kategori_id' => $allKategori[$index % $allKategori->count()]->id,
            ]));
        }

        $this->command->info('✅ Seeder selesai!');
        $this->command->info('   Email : admin@sekolah.sch.id');
        $this->command->info('   Pass  : admin123');
    }
}
