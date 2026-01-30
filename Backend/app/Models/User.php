<?php
namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Bug;
use App\Models\Fish;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'bio',
        'hemisphere',
        'island_name',
        'island_fruit',
        'money',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    public function fish()
    {
        return $this->belongsToMany(Fish::class)
            ->withPivot('donated_to_museum')
            ->withTimestamps();
    }

    public function bugs()
    {
        return $this->belongsToMany(Bug::class)
            ->withPivot('donated_to_museum')
            ->withTimestamps();
    }
    
    public function fossils()
    {
        return $this->belongsToMany(Fossil::class)
            ->withPivot('donated_to_museum')
            ->withTimestamps();
    }
}
