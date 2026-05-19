<?php

namespace App\Jobs;

use App\Models\Coupons;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AssignCouponToUsersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Coupons $coupon,
        public array $userIds
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $count = count($this->userIds);

        Log::info("Starting background bulk coupon assignment for Coupon ID: {$this->coupon->id} to {$count} users.");

        // Process in chunks of 1000 for high-performance bulk inserts
        collect($this->userIds)->chunk(1000)->each(function ($chunk) {
            $data = [];
            $now = now();

            foreach ($chunk as $userId) {
                $data[] = [
                    'coupon_id' => $this->coupon->id,
                    'user_id' => $userId,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
            DB::table('coupon_user')->insertOrIgnore($data);
        });

        Log::info("Successfully finished bulk assignment for Coupon ID: {$this->coupon->id}.");
    }
}
