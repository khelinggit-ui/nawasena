<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Validator;
use Illuminate\Notifications\Notifiable;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Hash;
use DB;
use Spatie\Permission\Traits\HasRoles;
use App\Constants\RoleConstants;
use App\Traits\HasDynamicRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, HasDynamicRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $table = 'users'; // Specify the table name if it's not pluralized

    protected $fillable = [
        'name', 'email', 'password', 'last_login', 'jabatan_id', 'spv_id', 'cabang_id', 'kantor_id', 'role_name', 'status'
    ];

    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class, 'jabatan_id', 'id');
    }

    public function spv()
    {
        return $this->belongsTo(User::class, 'spv_id', 'id');
    }

    public function cabang()
    {
        return $this->belongsTo(Cabang::class, 'cabang_id', 'id');
    }

    public function kantor()
    {
        return $this->belongsTo(Kantor::class, 'kantor_id', 'id');
    }

    /**
     * Check if user has role by ID
     */
    public function hasRoleById(int|array $roleIds): bool
    {
        $roleIds = is_array($roleIds) ? $roleIds : [$roleIds];
        return $this->roles()->whereIn('id', $roleIds)->exists();
    }

    /**
     * Check if user has any role ID from array
     */
    public function hasAnyRoleById(array $roleIds): bool
    {
        return $this->hasRoleById($roleIds);
    }

    /**
     * Get primary role ID
     */
    public function getRoleId(): ?int
    {
        return $this->roles()->first()?->id;
    }

    /**
     * Get all role IDs
     */
    public function getRoleIds(): array
    {
        return $this->roles()->pluck('id')->toArray();
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->hasRoleById(RoleConstants::ADMIN_ID);
    }

    /**
     * Check if user is marketing
     */
    public function isMarketing(): bool
    {
        return $this->hasRoleById(RoleConstants::MARKETING_ID);
    }

    /**
     * Check if user is SPV
     */
    public function isSpv(): bool
    {
        return $this->hasRoleById(RoleConstants::SPV_ID);
    }

    /**
     * Check if user is admin kredit
     */
    public function isAdminKredit(): bool
    {
        return $this->hasRoleById(RoleConstants::ADMINKREDIT_ID);
    }

    /**
     * Check if user is analis
     */
    public function isAnalis(): bool
    {
        return $this->hasRoleById(RoleConstants::ANALIS_ID);
    }

    /**
     * Check if user is risk
     */
    public function isRisk(): bool
    {
        return $this->hasRoleById(RoleConstants::RISK_ID);
    }

    /**
     * Check if user is mitra
     */
    public function isMitra(): bool
    {
        return $this->hasRoleById(RoleConstants::MITRA_ID);
    }


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /** generate id */
    protected static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            $latestUser = self::orderBy('user_id', 'desc')->first();
            $nextID = $latestUser ? intval(substr($latestUser->user_id, 3)) + 1 : 1;
            $model->user_id = 'KH-' . sprintf("%04d", $nextID);

            // Ensure the user_id is unique
            while (self::where('user_id', $model->user_id)->exists()) {
                $nextID++;
                $model->user_id = 'KH-' . sprintf("%04d", $nextID);
            }
        });
    }

    /** Insert New Users */
    public function saveNewuser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'      => 'required|string|max:255',
            'email'     => 'required|string|email|max:255|unique:users,email',
            'password'  => 'required|string|min:8|confirmed',
        ], [
            'email.unique' => 'This email is already registered. Please use another.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput()->with('error', 'Please fix the errors below.');
        }

        try {
            $todayDate = Carbon::now()->toDayDateTimeString();
            $save             = new User;
            $save->name       = $request->name;
            $save->avatar     = $request->image;
            $save->email      = $request->email;
            $save->join_date  = $todayDate;
            $save->role_name  = 'User';
            $save->status     = 'Active';
            $save->password   = Hash::make($request->password);
            $save->save();
            return redirect('login')->with('success', 'Account created successfully :)');
        } catch (\Exception $e) {
            \Log::error($e);
            return redirect()->back()->with('error', 'Failed to Create Account. Please try again.');
        }
    }
}