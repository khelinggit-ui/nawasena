# Sistem Role Management - Dokumentasi

## ✅ Sistem Sekarang DINAMIS

Anda **TIDAK perlu lagi** mengubah `RoleConstants.php` ketika membuat role baru!

## 📌 Cara Membuat Role Baru

### Metode 1: Di Seeder (Waktu Setup)

```php
use App\Services\RoleManager;

// Create role baru
RoleManager::createOrGetRole('supervisor');

// Create role dan assign permissions
RoleManager::assignPermissionsToRole('supervisor', [
    'menu dashboard',
    'page data debitur',
    'view debitur',
    'manage payment'
]);
```

### Metode 2: Via Artisan Command

```bash
php artisan tinker

# Create role
>>> App\Services\RoleManager::createOrGetRole('supervisor')
>>> App\Services\RoleManager::assignPermissionsToRole('supervisor', ['menu dashboard', 'page data debitur'])
```

### Metode 3: Di Controller/Service

```php
use App\Services\RoleManager;

class RoleController extends Controller
{
    public function store(Request $request)
    {
        $role = RoleManager::assignPermissionsToRole(
            $request->role_name,
            $request->permissions
        );
        return response()->json($role);
    }
}
```

## 🎯 Cara Menggunakan Role dalam Kode

### Di Controller:

```php
use App\Services\RoleManager;

// Check role by name (BARU - lebih fleksibel)
if ($user->hasRoleByName('supervisor')) {
    // ...
}

// Get role ID by name (dinamis dari database)
$roleId = RoleManager::getRoleId('supervisor');

// Get all role IDs
$allRoles = RoleManager::getAllRoleIds();
// Output: ['admin' => 1, 'user' => 2, 'supervisor' => 9, ...]
```

### Di Blade Template:

```blade
@if(auth()->user()->hasRoleByName('supervisor'))
    <button>Akses Supervisor</button>
@endif

@can('manage payment')
    <button>Bayar Sekarang</button>
@endcan
```

### Di Model:

```php
// Check jika user adalah super admin
$user->isSuperAdmin();

// Get primary role
$roleName = $user->getPrimaryRoleName();
$roleId = $user->getPrimaryRoleId();

// Get semua role IDs
$roleIds = $user->getDynamicRoleIds(); // [1, 2]

// Get semua role names
$roleNames = $user->getDynamicRoleNames(); // ['admin', 'supervisor']
```

## 🔄 Migrasi dari RoleConstants Lama

Jika sudah ada kode yang menggunakan `RoleConstants::ADMIN_ID`, bisa tetap dipakai:

```php
// Lama (masih bisa dipakai):
$user->hasRoleById(RoleConstants::ADMIN_ID);

// Baru (lebih fleksibel, recommended):
$user->hasRoleByName('admin');

// Atau via RoleManager:
$roleId = RoleManager::getRoleId('admin');
$user->hasRoleById($roleId);
```

## 📊 Komponen Sistem

1. **RoleManager** (`App\Services\RoleManager`)
   - Service untuk CRUD operations pada roles
   - Caching untuk performa
   - Automatic permission creation

2. **HasDynamicRoles** (`App\Traits\HasDynamicRoles`)
   - Trait untuk User model
   - Method untuk check role by name
   - Helper methods untuk role management

3. **RoleConstants** (`App\Constants\RoleConstants`)
   - Tetap ada untuk backward compatibility
   - Optional untuk digunakan

## 🚀 Keuntungan Sistem Baru:

✅ **Tidak perlu edit code untuk tambah role baru**
✅ **Role ID auto-generated dari database**
✅ **Caching untuk performa**
✅ **Backward compatible dengan RoleConstants lama**
✅ **Flexible dan scalable**

## ⚠️ Catatan Penting:

- Jangan langsung edit tabel `roles`, gunakan RoleManager
- Cache otomatis di-clear saat ada perubahan role
- Pastikan permission sudah ada sebelum assign ke role

---

Sistem sekarang **FULLY DYNAMIC** dan siap scale! 🎉
