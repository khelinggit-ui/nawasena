# Struktur Integrasi Evolution API di Laravel

Dokumen ini merancang arsitektur, struktur database, service, controller, dan alur integrasi Evolution API agar seluruh pengaturan (pengelolaan instance, ambil QR code, kirim pesan, dan webhook) dapat dikontrol secara terpusat langsung dari sistem Laravel Anda.

---

## 1. Arsitektur & Prinsip Utama
* **Multi-Instance Support:** Sistem Laravel menyimpan data setiap nomor WhatsApp ke dalam tabel database lokal (`whatsapp_instances`) sehingga Anda bisa mengelola banyak nomor/klien secara bersamaan.
* **Service Layer Pattern:** Seluruh komunikasi HTTP ke Evolution API diisolasi ke dalam satu kelas Service (`EvolutionApiService`) agar kode lebih rapi, terpusat, dan mudah di-maintenance.
* **Webhook Handler:** Menerima event balik secara real-time dari WhatsApp (seperti status koneksi terhubung/terputus atau pesan masuk) ke sistem Laravel.

---

## 2. Struktur Database (Migration)
Buat tabel untuk mencatat daftar perangkat/instance WhatsApp di database Laravel Anda:

```php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('whatsapp_instances', function (Blueprint $table) {
            $table->id();
            $table->string('instance_name')->unique(); // Nama unik instance (misal: toko_jakarta, cs_utama)
            $table->string('phone_number')->nullable(); // Nomor WhatsApp yang terhubung
            $table->enum('status', ['disconnected', 'connecting', 'connected'])->default('disconnected');
            $table->text('api_key')->nullable(); // API key spesifik instance (opsional)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_instances');
    }
};

3. Konfigurasi (.env & config/services.php)
Tambahkan variabel berikut pada file .env Laravel Anda:
EVOLUTION_API_URL=https://evo.nawasena.web.id
EVOLUTION_API_GLOBAL_KEY=NSB123456

Daftarkan kredensial tersebut di config/services.php:
'evolution' => [
    'url' => env('EVOLUTION_API_URL', 'https://evo.nawasena.web.id/'),
    'key' => env('EVOLUTION_API_GLOBAL_KEY'),
],

4. Service Layer (app/Services/EvolutionApiService.php)
Kelas ini bertugas menjembatani Laravel dengan endpoint HTTP Evolution API:

namespace App\Services;

use Illuminate\Support\Facades\Http;

class EvolutionApiService
{
    protected string $baseUrl;
    protected string $apiKey;

    public function __construct()
    {
        $this->baseUrl = config('services.evolution.url');
        $this->apiKey = config('services.evolution.key');
    }

    // 1. Membuat Instance Baru di Server Evolution
    public function createInstance(string $instanceName)
    {
        return Http::withHeaders(['apikey' => $this->apiKey])
            ->post("{$this->baseUrl}/instance/create", [
                'instanceName' => $instanceName,
                'integration' => 'WHATSAPP-BAILEYS',
            ]);
    }

    // 2. Mengambil QR Code / Koneksi Instance
    public function connectInstance(string $instanceName)
    {
        return Http::withHeaders(['apikey' => $this->apiKey])
            ->get("{$this->baseUrl}/instance/connect/{$instanceName}");
    }

    // 3. Mengecek Status Koneksi Terkini
    public function getConnectionState(string $instanceName)
    {
        return Http::withHeaders(['apikey' => $this->apiKey])
            ->get("{$this->baseUrl}/instance/connectionState/{$instanceName}");
    }

    // 4. Mengirim Pesan Teks
    public function sendText(string $instanceName, string $number, string $text)
    {
        return Http::withHeaders(['apikey' => $this->apiKey])
            ->post("{$this->baseUrl}/message/sendText/{$instanceName}", [
                'number' => $number,
                'text' => $text,
            ]);
    }
}

5. Controller Utama (app/Http/Controllers/WhatsAppController.php)
Controller untuk mengatur aksi dari sisi admin/pengguna sistem Laravel:

namespace App\Http\Controllers;

use App\Models\WhatsappInstance;
use App\Services\EvolutionApiService;
use Illuminate\Http\Request;

class WhatsAppController extends Controller
{
    protected EvolutionApiService $evolutionService;

    public function __construct(EvolutionApiService $evolutionService)
    {
        $this->evolutionService = $evolutionService;
    }

    // Menampilkan daftar instance WhatsApp yang terdaftar di sistem
    public function index()
    {
        $instances = WhatsappInstance::all();
        return view('whatsapp.index', compact('instances'));
    }

    // Mendaftarkan instance baru & menyimpannya ke DB lokal
    public function store(Request $request)
    {
        $request->validate([
            'instance_name' => 'required|alpha_dash|unique:whatsapp_instances,instance_name'
        ]);
        
        $name = $request->instance_name;

        // Buat instance di Evolution API
        $response = $this->evolutionService->createInstance($name);

        if ($response->successful() || $response->status() === 403) {
            WhatsappInstance::create(['instance_name' => $name]);
            return redirect()->route('whatsapp.show', $name);
        }

        return back()->with('error', 'Gagal membuat instance pada server Evolution API.');
    }

    // Menampilkan halaman QR Code untuk discan
    public function show(string $instanceName)
    {
        $instance = WhatsappInstance::where('instance_name', $instanceName)->firstOrFail();
        
        // Ambil base64 QR Code terbaru
        $response = $this->evolutionService->connectInstance($instanceName);
        $qrCode = null;

        if ($response->successful()) {
            $qrCode = $response->json('base64');
        }

        return view('whatsapp.connect', compact('instance', 'qrCode'));
    }
}

6. Webhook Controller (app/Http/Controllers/WebhookController.php)
Digunakan untuk menangkap perubahan status secara otomatis dari Evolution API (misalnya ketika HP berhasil melakukan scan QR):

namespace App\Http\Controllers;

use App\Models\WhatsappInstance;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    public function handle(Request $request)
    {
        $event = $request->input('event');
        $instanceName = $request->input('instance');

        // Event saat status koneksi berubah
        if ($event === 'connection.update') {
            $state = $request->input('data.state'); // 'open', 'connecting', 'close'
            
            $statusMap = [
                'open' => 'connected',
                'connecting' => 'connecting',
                'close' => 'disconnected',
            ];

            WhatsappInstance::where('instance_name', $instanceName)->update([
                'status' => $statusMap[$state] ?? 'disconnected'
            ]);
        }

        return response()->json(['status' => 'success']);
    }
}

7. Routing (routes/web.php & routes/api.php)
Di routes/web.php:

use App\Http\Controllers\WhatsAppController;

Route::resource('whatsapp', WhatsAppController::class)->except(['edit', 'update', 'destroy']);