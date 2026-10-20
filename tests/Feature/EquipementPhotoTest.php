<?php

namespace Tests\Feature;

use App\Models\Equipement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class EquipementPhotoTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_an_equipment_without_a_photo(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('equipements.store'), $this->payload())
            ->assertRedirect(route('equipements.index'));

        $this->assertNull(Equipement::firstOrFail()->photo);
    }

    public function test_user_can_create_an_equipment_with_a_valid_photo(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('equipements.store'), $this->payload([
                'photo' => UploadedFile::fake()->image('climatiseur.jpg'),
            ]))
            ->assertRedirect(route('equipements.index'));

        $photo = Equipement::firstOrFail()->photo;
        $this->assertNotNull($photo);
        $this->assertStringStartsWith('equipements/', $photo);
        Storage::disk('public')->assertExists($photo);
    }

    public function test_user_cannot_upload_a_non_image_file_or_an_oversized_image(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();

        $this->actingAs($user)
            ->from(route('equipements.create'))
            ->post(route('equipements.store'), $this->payload([
                'photo' => UploadedFile::fake()->create('document.pdf', 200, 'application/pdf'),
            ]))
            ->assertRedirect(route('equipements.create'))
            ->assertSessionHasErrors('photo');

        $this->actingAs($user)
            ->from(route('equipements.create'))
            ->post(route('equipements.store'), $this->payload([
                'photo' => UploadedFile::fake()->image('grande-image.jpg')->size(4097),
            ]))
            ->assertRedirect(route('equipements.create'))
            ->assertSessionHasErrors('photo');
    }

    public function test_update_without_a_new_photo_keeps_the_existing_photo(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $photo = UploadedFile::fake()->image('ancienne.jpg')->store('equipements', 'public');
        $equipement = $user->equipements()->create($this->payload(['photo' => $photo]));

        $this->actingAs($user)
            ->put(route('equipements.update', $equipement), $this->payload(['nom' => 'Climatiseur modifié']))
            ->assertRedirect(route('equipements.show', $equipement));

        $this->assertSame($photo, $equipement->fresh()->photo);
        Storage::disk('public')->assertExists($photo);
    }

    public function test_update_with_a_new_photo_replaces_and_removes_the_old_photo(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $oldPhoto = UploadedFile::fake()->image('ancienne.jpg')->store('equipements', 'public');
        $equipement = $user->equipements()->create($this->payload(['photo' => $oldPhoto]));

        $this->actingAs($user)
            ->put(route('equipements.update', $equipement), $this->payload([
                'photo' => UploadedFile::fake()->image('nouvelle.png'),
            ]))
            ->assertRedirect(route('equipements.show', $equipement));

        $newPhoto = $equipement->fresh()->photo;
        $this->assertNotSame($oldPhoto, $newPhoto);
        Storage::disk('public')->assertMissing($oldPhoto);
        Storage::disk('public')->assertExists($newPhoto);
    }

    public function test_destroy_removes_the_equipment_photo(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $photo = UploadedFile::fake()->image('a-supprimer.jpg')->store('equipements', 'public');
        $equipement = $user->equipements()->create($this->payload(['photo' => $photo]));

        $this->actingAs($user)
            ->delete(route('equipements.destroy', $equipement))
            ->assertRedirect(route('equipements.index'));

        Storage::disk('public')->assertMissing($photo);
    }

    public function test_show_displays_the_uploaded_photo_or_the_existing_icon_fallback(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $photo = UploadedFile::fake()->image('climatiseur.jpg')->store('equipements', 'public');
        $withPhoto = $user->equipements()->create($this->payload(['photo' => $photo]));
        $withoutPhoto = $user->equipements()->create($this->payload([
            'nom' => 'Machine',
            'type' => 'Autre',
            'photo' => null,
        ]));

        $this->actingAs($user)
            ->get(route('equipements.show', $withPhoto))
            ->assertOk()
            ->assertSee('equipment-detail-photo')
            ->assertSee('storage/'.$photo);

        $this->actingAs($user)
            ->get(route('equipements.show', $withoutPhoto))
            ->assertOk()
            ->assertDontSee('equipment-detail-photo')
            ->assertSee('bi-cpu');
    }

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'nom' => 'Climatiseur salon',
            'type' => 'Climatisation',
            'marque' => 'LG',
            'description' => 'Équipement principal du scénario.',
            'sensible_chaleur' => true,
            'sensible_coupure' => true,
        ], $overrides);
    }
}
