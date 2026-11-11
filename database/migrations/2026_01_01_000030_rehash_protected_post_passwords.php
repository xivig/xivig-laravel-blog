<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        // Rehash any plaintext post passwords existing in the system
        try {
            $posts = DB::table('posts')->whereNotNull('password')->where('password', '!=', '')->get();
            foreach ($posts as $post) {
                if (!str_starts_with($post->password, '$2y$') && !str_starts_with($post->password, '$2a$')) {
                    DB::table('posts')->where('id', $post->id)->update([
                        'password' => Hash::make($post->password),
                    ]);
                }
            }
        } catch (\Throwable $e) {}
    }

    public function down(): void {
        // Irreversible cryptographic hashing
    }
};
