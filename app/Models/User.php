<?php

namespace App\Models;
use Filament\Models\Contracts\FilamentUser; // 1. Import the Contract
use Filament\Panel;                         // 2. Import the Panel class
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Queue\SerializesModels;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

#[Guarded(['email_verified_at'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail, FilamentUser
{
    public function canAccessPanel(Panel $panel): bool
    {
        // Now, only users assigned the 'admin' role via Spatie can access the panel
        return $this->hasRole(['admin', 'super admin']);
    }
    public function sendEmailVerificationNotification()
    {
        // This pushes the notification to the 'database' queue we set up
        $this->notify((new \App\Notifications\CustomVerifyEmail)->delay(now()->addSeconds(5)));
    }
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
    public function coupons()
    {
        return $this->belongsToMany(Coupons::class, 'coupon_user', 'user_id', 'coupon_id');
    }
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasApiTokens, HasRoles, Notifiable, SerializesModels;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
