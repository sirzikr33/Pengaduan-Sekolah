<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\ChatSession;
use App\Models\Kategori;
use App\Models\Pengaduan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ChatController extends Controller
{
    /**
     * Show the chat page for siswa.
     */
    public function index()
    {
        $user = Auth::user();
        $kategoris = Kategori::orderBy('nama_kategori')->get();

        // Find or create an active chat session
        $session = ChatSession::where('user_id', $user->id)
            ->whereIn('status', ['chatbot', 'queued', 'active'])
            ->latest()
            ->first();

        return view('siswa.chat.index', compact('kategoris', 'session'));
    }

    /**
     * Show list of resolved (history) chat sessions for the logged-in siswa.
     */
    public function history()
    {
        $user = Auth::user();

        $histories = ChatSession::where('user_id', $user->id)
            ->where('status', 'resolved')
            ->with(['pengaduan.kategori', 'messages' => function ($q) {
                $q->orderBy('created_at')->limit(1);
            }])
            ->withCount('messages')
            ->latest('resolved_at')
            ->paginate(10);

        return view('siswa.chat.history', compact('histories'));
    }

    /**
     * Show a single resolved chat session in read-only mode.
     */
    public function showHistory(ChatSession $chatSession)
    {
        $user = Auth::user();

        // Hanya bisa lihat history milik sendiri
        if ($chatSession->user_id !== $user->id) {
            abort(403, 'Sesi chat ini bukan milik Anda.');
        }

        $chatSession->load(['pengaduan.kategori', 'messages', 'admin']);

        return view('siswa.chat.history_show', compact('chatSession'));
    }

    /**
     * Start a new chat session.
     */
    public function startSession(Request $request)
    {
        $user = Auth::user();

        // Close any existing open sessions
        ChatSession::where('user_id', $user->id)
            ->whereIn('status', ['chatbot', 'queued'])
            ->update(['status' => 'resolved', 'resolved_at' => now()]);

        $session = ChatSession::create([
            'user_id' => $user->id,
            'status'  => 'chatbot',
        ]);

        // Send bot welcome message
        $siswa = $user->siswa;
        ChatMessage::create([
            'chat_session_id' => $session->id,
            'sender_type'     => 'bot',
            'message'         => "Halo {$siswa->nama}! 👋\nSelamat datang di Layanan Pengaduan Sekolah.\n\nSaya akan membantu kamu membuat laporan pengaduan. Silakan pilih kategori masalah di bawah ini:",
        ]);

        $kategoris = Kategori::orderBy('nama_kategori')->get();
        $options = $kategoris->map(fn($k) => ['id' => $k->id, 'label' => $k->nama_kategori])->toArray();

        ChatMessage::create([
            'chat_session_id' => $session->id,
            'sender_type'     => 'bot',
            'message'         => '__SELECT_KATEGORI__',
            'metadata'        => ['type' => 'options', 'step' => 'kategori', 'options' => $options],
        ]);

        return response()->json([
            'success'    => true,
            'session_id' => $session->id,
            'messages'   => $session->messages()->orderBy('created_at')->get(),
        ]);
    }

    /**
     * Send a message in the chat (user side).
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'session_id' => 'required|exists:chat_sessions,id',
            'message'    => 'nullable|string|max:2000',
            'step'       => 'nullable|string',
            'value'      => 'nullable|string',
        ]);

        $user = Auth::user();
        $session = ChatSession::where('id', $request->session_id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        // If session is active (admin chat), just save the message
        if ($session->isActive()) {
            ChatMessage::create([
                'chat_session_id' => $session->id,
                'sender_type'     => 'user',
                'sender_id'       => $user->id,
                'message'         => $request->message,
            ]);

            return response()->json(['success' => true]);
        }

        // Bot conversation flow
        $step = $request->step;
        $value = $request->value ?? $request->message;

        // Save user message
        $userMsg = $request->message ?? $value;
        if ($userMsg) {
            ChatMessage::create([
                'chat_session_id' => $session->id,
                'sender_type'     => 'user',
                'sender_id'       => $user->id,
                'message'         => $userMsg,
            ]);
        }

        // Process bot response based on step
        $botMessages = $this->processBotStep($session, $step, $value);

        return response()->json([
            'success'  => true,
            'messages' => $botMessages,
        ]);
    }

    /**
     * Upload a photo attachment in chat.
     */
    public function uploadPhoto(Request $request)
    {
        $request->validate([
            'session_id' => 'required|exists:chat_sessions,id',
            'photo'      => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $user = Auth::user();
        $session = ChatSession::where('id', $request->session_id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        // Hapus foto lama di storage jika sudah ada (replace)
        $oldPath = $session->photo_path;
        if ($oldPath && Storage::disk('public')->exists($oldPath)) {
            Storage::disk('public')->delete($oldPath);
        }

        $path = $request->file('photo')->store('chat_photos', 'public');

        // Simpan path foto ke session (database), bukan cache
        $session->update(['photo_path' => $path]);

        $msg = ChatMessage::create([
            'chat_session_id' => $session->id,
            'sender_type'     => 'user',
            'sender_id'       => $user->id,
            'message'         => '📷 Foto dikirim',
            'attachment'      => $path,
            'attachment_type'  => 'image',
        ]);

        // If we're in the photo step, respond
        $lastBotMsg = $session->messages()
            ->where('sender_type', 'bot')
            ->latest()
            ->first();

        $botMessages = [];
        if ($lastBotMsg && $lastBotMsg->metadata && ($lastBotMsg->metadata['step'] ?? '') === 'foto') {
            $botMessages = $this->processBotStep($session, 'foto_received', $path);
        }

        return response()->json([
            'success'      => true,
            'message'      => $msg,
            'bot_messages' => $botMessages,
        ]);
    }

    /**
     * Get messages for polling.
     */
    public function getMessages(Request $request)
    {
        $request->validate([
            'session_id' => 'required|exists:chat_sessions,id',
            'after'      => 'nullable|string',
        ]);

        $user = Auth::user();
        $session = ChatSession::where('id', $request->session_id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $query = $session->messages()->orderBy('created_at');

        if ($request->after) {
            $query->where('created_at', '>', $request->after);
        }

        return response()->json([
            'success'  => true,
            'messages' => $query->get(),
            'status'   => $session->status,
        ]);
    }

    /**
     * Request escalation to admin.
     */
    public function escalate(Request $request)
    {
        $request->validate([
            'session_id' => 'required|exists:chat_sessions,id',
        ]);

        $user = Auth::user();
        $session = ChatSession::where('id', $request->session_id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $queuePos = ChatSession::where('status', 'queued')->count() + 1;

        $session->update([
            'status'         => 'queued',
            'queue_position' => $queuePos,
            'queued_at'      => now(),
        ]);

        ChatMessage::create([
            'chat_session_id' => $session->id,
            'sender_type'     => 'bot',
            'message'         => "🔄 Permintaan kamu untuk berbicara dengan admin sudah masuk antrean.\n\n📍 Posisi antrean: #{$queuePos}\n\nMohon tunggu, admin akan segera merespons. Kamu akan mendapat notifikasi saat admin menerima chat kamu.",
        ]);

        return response()->json([
            'success'  => true,
            'position' => $queuePos,
        ]);
    }

    // ════════════════════════════════════════════════════════
    // Bot Step Processing
    // ════════════════════════════════════════════════════════

    private function processBotStep(ChatSession $session, ?string $step, ?string $value): array
    {
        $messages = [];

        switch ($step) {
            case 'kategori':
                $kategori = Kategori::find($value);
                if (!$kategori) {
                    $messages[] = $this->botMsg($session, "❌ Kategori tidak valid. Silakan pilih dari opsi yang tersedia.");
                    break;
                }
                // Simpan ke database (payload_data), bukan cache
                $session->setPayload('kategori', $value);
                $session->setPayload('kategori_nama', $kategori->nama_kategori);

                $messages[] = $this->botMsg($session, "✅ Kategori: {$kategori->nama_kategori}\n\nBaik, sekarang tuliskan judul/nama pengaduan kamu.\nContoh: \"Kursi kelas X rusak\" atau \"AC Lab tidak dingin\"");
                $messages[count($messages) - 1]->metadata = ['step' => 'nama'];
                $messages[count($messages) - 1]->save();
                break;

            case 'nama':
                if (empty($value) || strlen($value) < 5) {
                    $messages[] = $this->botMsg($session, "❌ Judul terlalu pendek. Minimal 5 karakter ya. Coba lagi:");
                    $messages[count($messages) - 1]->metadata = ['step' => 'nama'];
                    $messages[count($messages) - 1]->save();
                    break;
                }
                $session->setPayload('nama', $value);

                $messages[] = $this->botMsg($session, "📝 Judul: \"{$value}\"\n\nSekarang jelaskan detail permasalahannya. Tulis sejelas mungkin ya:");
                $messages[count($messages) - 1]->metadata = ['step' => 'deskripsi'];
                $messages[count($messages) - 1]->save();
                break;

            case 'deskripsi':
                if (empty($value) || strlen($value) < 10) {
                    $messages[] = $this->botMsg($session, "❌ Deskripsi terlalu pendek. Minimal 10 karakter. Coba jelaskan lebih detail:");
                    $messages[count($messages) - 1]->metadata = ['step' => 'deskripsi'];
                    $messages[count($messages) - 1]->save();
                    break;
                }
                $session->setPayload('deskripsi', $value);

                $messages[] = $this->botMsg($session, "📍 Sekarang, di mana lokasi kejadiannya?\nContoh: \"Lab Komputer\", \"Ruang Kelas X RPL 1\", \"Kantin\"");
                $messages[count($messages) - 1]->metadata = ['step' => 'lokasi'];
                $messages[count($messages) - 1]->save();
                break;

            case 'lokasi':
                if (empty($value)) {
                    $messages[] = $this->botMsg($session, "❌ Lokasi tidak boleh kosong. Tulis lokasi kejadian:");
                    $messages[count($messages) - 1]->metadata = ['step' => 'lokasi'];
                    $messages[count($messages) - 1]->save();
                    break;
                }
                $session->setPayload('lokasi', $value);

                $messages[] = $this->botMsg($session, "⚠️ Seberapa parah kondisi masalahnya?", ['type' => 'options', 'step' => 'kondisi', 'options' => [
                    ['id' => 'ringan', 'label' => '🟢 Ringan'],
                    ['id' => 'sedang', 'label' => '🟡 Sedang'],
                    ['id' => 'berat',  'label' => '🔴 Berat'],
                ]]);
                break;

            case 'kondisi':
                if (!in_array($value, ['ringan', 'sedang', 'berat'])) {
                    $messages[] = $this->botMsg($session, "❌ Pilihan tidak valid. Pilih salah satu: Ringan, Sedang, atau Berat.");
                    break;
                }
                $session->setPayload('kondisi', $value);

                $messages[] = $this->botMsg($session, "📷 Apakah kamu ingin melampirkan foto bukti?", ['type' => 'options', 'step' => 'ask_foto', 'options' => [
                    ['id' => 'ya',    'label' => '📷 Ya, lampirkan foto'],
                    ['id' => 'tidak', 'label' => '⏭️ Tidak, lanjut saja'],
                ]]);
                break;

            case 'ask_foto':
                if ($value === 'ya') {
                    $messages[] = $this->botMsg($session, "📷 Silakan kirim foto bukti kamu. Kamu bisa menggunakan tombol kamera 📸 atau upload file gambar.", ['step' => 'foto']);
                } else {
                    // Siswa tidak mau foto — pastikan photo_path bersih
                    $session->update(['photo_path' => null]);
                    $messages = array_merge($messages, $this->showConfirmation($session));
                }
                break;

            case 'foto_received':
                // value = path foto yang baru saja diupload
                // photo_path di session sudah diupdate di uploadPhoto()
                $messages[] = $this->botMsg($session, "✅ Foto berhasil diterima!");
                $messages = array_merge($messages, $this->showConfirmation($session));
                break;

            case 'konfirmasi':
                if ($value === 'kirim') {
                    $messages = array_merge($messages, $this->submitPengaduan($session));
                } elseif ($value === 'batal') {
                    // FIX #2: Hapus file foto fisik dari storage sebelum clear payload
                    $fotoPath = $session->photo_path;
                    if ($fotoPath && Storage::disk('public')->exists($fotoPath)) {
                        Storage::disk('public')->delete($fotoPath);
                    }
                    $session->clearPayload();
                    $session->update(['status' => 'resolved', 'resolved_at' => now()]);
                    $messages[] = $this->botMsg($session, "❌ Pengaduan dibatalkan.\n\nJika kamu ingin membuat pengaduan baru, silakan mulai chat baru.");
                } else {
                    $messages[] = $this->botMsg($session, "Pilih \"Kirim Pengaduan\" atau \"Batalkan\".");
                }
                break;

            case 'selesai':
                if ($value === 'baru') {
                    $session->update(['status' => 'resolved', 'resolved_at' => now()]);
                    return []; // Frontend should redirect to start new session
                } elseif ($value === 'admin') {
                    // Escalate to admin
                    $queuePos = ChatSession::where('status', 'queued')->count() + 1;
                    $session->update([
                        'status'         => 'queued',
                        'queue_position' => $queuePos,
                        'queued_at'      => now(),
                    ]);
                    $messages[] = $this->botMsg($session, "🔄 Kamu sudah masuk antrean untuk berbicara dengan admin.\n\n📍 Posisi antrean: #{$queuePos}\n\nMohon tunggu ya, admin akan segera merespons.");
                }
                break;

            default:
                // Fallback — check if there's an active step from last bot message
                $lastBot = $session->messages()
                    ->where('sender_type', 'bot')
                    ->whereNotNull('metadata')
                    ->latest()
                    ->first();

                if ($lastBot && isset($lastBot->metadata['step'])) {
                    return $this->processBotStep($session, $lastBot->metadata['step'], $value);
                }

                $messages[] = $this->botMsg($session, "Maaf, saya tidak mengerti. Gunakan tombol pilihan yang tersedia atau ketik pesan sesuai instruksi.");
                break;
        }

        return $messages;
    }

    private function showConfirmation(ChatSession $session): array
    {
        // Baca dari payload_data di database (bukan cache)
        $nama      = $session->getPayload('nama', '-');
        $kategori  = $session->getPayload('kategori_nama', '-');
        $deskripsi = $session->getPayload('deskripsi', '-');
        $lokasi    = $session->getPayload('lokasi', '-');
        $kondisi   = $session->getPayload('kondisi', '-');
        $foto      = $session->photo_path; // disimpan di kolom khusus

        $summary  = "📋 **Ringkasan Pengaduan**\n\n";
        $summary .= "📌 Judul: {$nama}\n";
        $summary .= "📂 Kategori: {$kategori}\n";
        $summary .= "📝 Deskripsi: {$deskripsi}\n";
        $summary .= "📍 Lokasi: {$lokasi}\n";
        $summary .= "⚠️ Kondisi: " . ucfirst($kondisi) . "\n";
        $summary .= "📷 Foto: " . ($foto ? 'Ada' : 'Tidak ada') . "\n";
        $summary .= "\nApakah data di atas sudah benar?";

        $messages = [];
        $messages[] = $this->botMsg($session, $summary, ['type' => 'options', 'step' => 'konfirmasi', 'options' => [
            ['id' => 'kirim', 'label' => '✅ Kirim Pengaduan'],
            ['id' => 'batal', 'label' => '❌ Batalkan'],
        ]]);

        return $messages;
    }

    private function submitPengaduan(ChatSession $session): array
    {
        $user  = Auth::user();
        $siswa = $user->siswa;

        // Baca dari payload_data di database
        $fotoPath = $session->photo_path;
        $fotoName = $fotoPath ? basename($fotoPath) : 'no-image.jpg';

        // Jika foto ada di chat_photos, salin ke folder pengaduan
        if ($fotoPath && Storage::disk('public')->exists($fotoPath)) {
            $newPath = 'pengaduan/' . basename($fotoPath);
            Storage::disk('public')->copy($fotoPath, $newPath);
            // Hapus file asli di chat_photos setelah disalin
            Storage::disk('public')->delete($fotoPath);
            $fotoName = basename($fotoPath);
        }

        $pengaduan = Pengaduan::create([
            'siswa_id'          => $siswa->id,
            'kategori_id'       => $session->getPayload('kategori'),
            'nama_pengaduan'    => $session->getPayload('nama'),
            'deskripsi'         => $session->getPayload('deskripsi'),
            'lokasi'            => $session->getPayload('lokasi'),
            'foto_pengaduan'    => $fotoName,
            'status'            => 'pending',
            'kondisi_pengaduan' => $session->getPayload('kondisi'),
            'tanggal_pengaduan' => now()->toDateString(),
        ]);

        $session->update(['pengaduan_id' => $pengaduan->id]);

        // Bersihkan payload (foto sudah dipindahkan, data draf sudah dipakai)
        $session->clearPayload();

        $messages = [];
        $messages[] = $this->botMsg($session, "🎉 **Pengaduan berhasil dikirim!**\n\n📋 ID: #{$pengaduan->id}\n📌 Status: Pending\n\nTerima kasih sudah melapor. Admin akan segera meninjau pengaduan kamu.\n\nApa yang ingin kamu lakukan selanjutnya?", [
            'type' => 'options', 'step' => 'selesai', 'options' => [
                ['id' => 'baru',  'label' => '📝 Buat Pengaduan Baru'],
                ['id' => 'admin', 'label' => '💬 Bicara dengan Admin'],
            ]
        ]);

        return $messages;
    }

    private function botMsg(ChatSession $session, string $message, ?array $metadata = null): ChatMessage
    {
        return ChatMessage::create([
            'chat_session_id' => $session->id,
            'sender_type'     => 'bot',
            'message'         => $message,
            'metadata'        => $metadata,
        ]);
    }

    private function clearCacheForSession(ChatSession $session): void
    {
        // Metode ini sekarang wrap clearPayload() untuk backward compatibility
        $session->clearPayload();
    }
}
