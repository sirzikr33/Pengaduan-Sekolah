<?php

namespace Database\Seeders;

use App\Models\Kategori;
use App\Models\Pengaduan;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ─── Admin ───────────────────────────────────────────
        User::create([
            'name'     => 'Administrator',
            'email'    => 'admin@sekolah.sch.id',
            'password' => Hash::make('admin123'),
            'role'     => 'admin',
            'siswa_id' => null,
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

        // ─── Siswa + User siswa ───────────────────────────────
        $siswaDatas = [
            ['nisn' => '0123456789', 'nama' => 'Zikra Malik',   'kelas' => 'X RPL 1'],
            ['nisn' => '0123456790', 'nama' => 'Siti Rahmawati', 'kelas' => 'X RPL 2'],
            ['nisn' => '0123456791', 'nama' => 'Budi Santoso',   'kelas' => 'XI TKJ 1'],
            ['nisn' => '0123456792', 'nama' => 'Dewi Lestari',   'kelas' => 'XI TKJ 2'],
            ['nisn' => '0123456793', 'nama' => 'Rizky Pratama',  'kelas' => 'XII MM 1'],
        ];

        foreach ($siswaDatas as $sw) {
            $siswa = Siswa::create($sw);

            // Buat email dari nama: "Ahmad Fauzan" → "ahmad.fauzan@siswa.sch.id"
            $email = strtolower(str_replace(' ', '.', $sw['nama'])) . '@siswa.sch.id';

            User::create([
                'name'     => $sw['nama'],
                'email'    => $email,
                'password' => Hash::make('siswa123'),
                'role'     => 'siswa',
                'siswa_id' => $siswa->id,
            ]);
        }

        $allSiswa = Siswa::all();

        // ─── Pengaduan ────────────────────────────────────────
        $pengaduans = [
            [
                'nama_pengaduan'    => 'Kursi Rusak',
                'foto_pengaduan'    => 'no-image.jpg',
                'status'            => 'pending',
                'deskripsi'         => 'Kursi di kelas 101 rusak dan tidak dapat digunakan.',
                'lokasi'            => 'Kelas 101',
                'kondisi_pengaduan' => 'sedang',
                'tanggal_pengaduan' => '2026-04-01',
            ],
            [
                'nama_pengaduan'    => 'Proyektor Tidak Menyala',
                'foto_pengaduan'    => 'no-image.jpg',
                'status'            => 'proses',
                'deskripsi'         => 'Proyektor di ruang guru tidak menyala.',
                'lokasi'            => 'Ruang Guru',
                'kondisi_pengaduan' => 'berat',
                'tanggal_pengaduan' => '2026-04-03',
            ],
            [
                'nama_pengaduan'    => 'Kaca Jendela Pecah',
                'foto_pengaduan'    => 'no-image.jpg',
                'status'            => 'selesai',
                'deskripsi'         => 'Kaca jendela di kelas 202 pecah akibat angin kencang.',
                'lokasi'            => 'Kelas 202',
                'kondisi_pengaduan' => 'berat',
                'tanggal_pengaduan' => '2026-03-28',
            ],
            [
                'nama_pengaduan'    => 'Bola Voli Kempes',
                'foto_pengaduan'    => 'no-image.jpg',
                'status'            => 'pending',
                'deskripsi'         => 'Bola voli di lapangan olahraga kempes dan tidak bisa dipakai.',
                'lokasi'            => 'Lapangan Olahraga',
                'kondisi_pengaduan' => 'ringan',
                'tanggal_pengaduan' => '2026-04-05',
            ],
            [
                'nama_pengaduan'    => 'Komputer Lab Error',
                'foto_pengaduan'    => 'no-image.jpg',
                'status'            => 'proses',
                'deskripsi'         => 'Salah satu komputer di laboratorium komputer sering error saat digunakan.',
                'lokasi'            => 'Laboratorium Komputer',
                'kondisi_pengaduan' => 'sedang',
                'tanggal_pengaduan' => '2026-04-07',
            ],
        ];

        foreach ($pengaduans as $index => $pg) {
            Pengaduan::create(array_merge($pg, [
                'siswa_id'    => $allSiswa[$index % $allSiswa->count()]->id,
                'kategori_id' => $allKategori[$index % $allKategori->count()]->id,
            ]));
        }

        $this->command->info('');
        $this->command->info('✅ Seeder selesai!');
        $this->command->info('');
        $this->command->info('  👤 ADMIN');
        $this->command->info('     Email : admin@sekolah.sch.id');
        $this->command->info('     Pass  : admin123');
        $this->command->info('');
        $this->command->info('  🎓 SISWA (semua password: siswa123)');
        $this->command->info('     zikra.malik@siswa.sch.id');
        $this->command->info('     siti.rahmawati@siswa.sch.id');
        $this->command->info('     budi.santoso@siswa.sch.id');
        $this->command->info('     dewi.lestari@siswa.sch.id');
        $this->command->info('     rizky.pratama@siswa.sch.id');
        $this->command->info('');
    }
}
