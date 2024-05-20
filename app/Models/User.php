<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
    ];

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
        'password' => 'hashed',
    ];

    protected $appends = [
        'created_from',
        'image',
    ];

    public function getImageAttribute()
    {
        return $this->image()
            ->get(['mediable_type', 'name'])
            ->map(function ($image) {
                $dir = explode('\\', $image->mediable_type)[2];
                unset ($image->mediable_type);
                return asset("storage/$dir") . '/' . $image->name;
            });
    }


    public function getCreatedFromAttribute()
    {
        return $this->created_at->diffForHumans();
    }


    // Relations
    public function image(): MorphOne
    {
        return $this->morphOne(Media::class, 'mediable');
    }


    // teacher relations and methods
    // ________________________________

    public function Categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'specializations');
    }

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class, 'teacher_id');
    }

    // ________________________________





    //student relations and methods
    // ________________________________
    public function questions(): BelongsToMany
    {
        return $this->belongsToMany(Question::class, 'answers');
    }

    public function choices(): BelongsToMany
    {
        return $this->belongsToMany(Choice::class, 'answers', relatedPivotKey: 'chosen_choice_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class);
    }

    public function coursesEnrollments(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'enrollments')
            ->withPivot([
                'is_favorite',
                'is_active'
            ])
            ->where('is_active', true);
    }

    public function favoriteCourses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'enrollments')
            ->withPivot([
                'is_favorite',
                'is_active'
            ])
            ->where('is_favorite', true);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function isEnrolledInCourse($course)
    {
        return $this->coursesEnrollments->contains($course);
    }

    // ________________________________


}
