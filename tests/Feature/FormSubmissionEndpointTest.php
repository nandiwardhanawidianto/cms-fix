<?php

namespace Tests\Feature;

use App\Models\FormSubmission;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class FormSubmissionEndpointTest extends TestCase
{
    private string $integrationToken;

    protected function setUp(): void
    {
        parent::setUp();

        // Test isolated from legacy CMS tables that lack full creation migrations.
        Schema::create('form_submissions', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 80)->unique();
            $table->string('customer_whatsapp', 30);
            $table->string('shopee_username', 120)->nullable();
            $table->string('display_order', 20)->default('female_first');
            $table->string('photo_package', 20)->nullable();
            $table->unsignedTinyInteger('photo_count')->default(0);
            $table->json('payload');
            $table->json('raw_form')->nullable();
            $table->string('status', 30)->default('pending_review');
            $table->unsignedBigInteger('published_slug_id')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });

        $this->integrationToken = str_repeat('k', 40);
        config(['services.form_undangan.integration_token' => $this->integrationToken]);
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('form_submissions');
        parent::tearDown();
    }

    private function submission(): array
    {
        return [
            'order_number' => '261101XYZABC',
            'customer_whatsapp' => '081234567890',
            'shopee_username' => 'pelanggan_test',
            'display_order' => 'female_first',
            'photo_package' => 'no_photo',
            'photo_count' => 0,
            'raw_form' => ['love_gift' => [['bank' => 'BCA', 'account_number' => '1234', 'account_name' => '']], 'turut_mengundang' => ['Bapak A']],
            'payload' => [
                'order_number' => '261101XYZABC',
                'theme' => null,
                'mempelai_1' => [],
                'mempelai_2' => [],
                'events' => [],
            ],
        ];
    }

    public function test_rejects_unauthenticated_requests(): void
    {
        $this->postJson('/api/form-submissions', $this->submission())
            ->assertStatus(401);

        $this->assertDatabaseCount('form_submissions', 0);
    }

    public function test_accepts_incomplete_data_without_publishing(): void
    {
        $this->withHeader('X-CMS-Integration-Token', $this->integrationToken)
            ->postJson('/api/form-submissions', $this->submission())
            ->assertStatus(202)
            ->assertJsonPath('status', 'pending_review');

        $this->assertDatabaseCount('form_submissions', 1);
        $this->assertSame('Bapak A', FormSubmission::firstOrFail()->raw_form['turut_mengundang'][0]);
        $this->assertDatabaseHas('form_submissions', [
            'order_number' => '261101XYZABC',
            'status' => 'pending_review',
            'published_slug_id' => null,
        ]);
    }

    public function test_same_shopee_order_updates_one_pending_submission(): void
    {
        $first = $this->submission();
        $second = $first;
        $second['shopee_username'] = 'nama_diperbaiki';

        $this->withHeader('X-CMS-Integration-Token', $this->integrationToken);
        $this->postJson('/api/form-submissions', $first)->assertStatus(202);
        $this->postJson('/api/form-submissions', $second)->assertStatus(202);

        $this->assertDatabaseCount('form_submissions', 1);
        $this->assertDatabaseHas('form_submissions', [
            'order_number' => '261101XYZABC',
            'shopee_username' => 'nama_diperbaiki',
        ]);
    }

    public function test_submission_under_admin_review_cannot_be_overwritten(): void
    {
        $data = $this->submission();
        $this->withHeader('X-CMS-Integration-Token', $this->integrationToken)
            ->postJson('/api/form-submissions', $data)->assertStatus(202);

        $saved = FormSubmission::firstOrFail();
        $payload = $saved->payload;
        $payload['mempelai_1']['short_name'] = 'Nama Admin';
        $saved->update(['status' => 'in_review', 'payload' => $payload]);

        $data['payload']['mempelai_1']['short_name'] = 'Nama Pelanggan';
        $this->postJson('/api/form-submissions', $data)
            ->assertStatus(409)->assertJsonPath('status', 'review_locked');

        $this->assertSame('Nama Admin', FormSubmission::firstOrFail()->payload['mempelai_1']['short_name']);
    }

    public function test_rejects_mismatched_order_numbers(): void
    {
        $data = $this->submission();
        $data['payload']['order_number'] = 'INVALID';

        $this->withHeader('X-CMS-Integration-Token', $this->integrationToken)
            ->postJson('/api/form-submissions', $data)
            ->assertStatus(422);

        $this->assertDatabaseCount('form_submissions', 0);
    }
}
