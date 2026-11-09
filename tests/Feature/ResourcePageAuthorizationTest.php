<?php

namespace Tests\Feature;

use App\Filament\Resources\Users\UserResource;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Passes on filament/filament v5.11.0, fails on v5.11.2 (#20957):
 * the pages call authorize*() instead of the overridden can*() methods.
 */
class ResourcePageAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    /** Sanity check: the user can access the panel and the resource. */
    public function test_list_page_is_accessible(): void
    {
        $this->actingAs(User::factory()->create());

        $this->get(UserResource::getUrl('index'))->assertOk();
    }

    public function test_edit_page_respects_overridden_can_edit(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->assertFalse(UserResource::canEdit($user));

        $this->get(UserResource::getUrl('edit', ['record' => $user]))->assertForbidden();
    }

    public function test_view_page_respects_overridden_can_view(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->assertFalse(UserResource::canView($user));

        $this->get(UserResource::getUrl('view', ['record' => $user]))->assertForbidden();
    }

    public function test_create_page_respects_overridden_can_create(): void
    {
        $this->actingAs(User::factory()->create());

        $this->assertFalse(UserResource::canCreate());

        $this->get(UserResource::getUrl('create'))->assertForbidden();
    }
}
