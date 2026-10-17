<?php

namespace Tests\Feature\Admin;

use App\Enums\RoleName;
use App\Livewire\Admin\Gallery;
use App\Livewire\Admin\News;
use App\Livewire\Admin\Stories;
use App\Models\Country;
use App\Models\GalleryItem;
use App\Models\NewsPost;
use App\Models\SuccessStory;
use App\Models\User;
use App\Support\ImageStore;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

/*
 * These tests build fake images with GD (UploadedFile::fake()->image()).
 * If a test errors with "imagecreatetruecolor", enable the PHP gd extension.
 */
class GalleryNewsStoriesAdminTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        $this->seed(PermissionSeeder::class);
        Storage::fake('public');
    }

    private function userWithRole(RoleName $role): User
    {
        $user = User::factory()->create(['is_active' => true]);
        $user->assignRole($role->value);

        return $user;
    }

    private function admin(): User
    {
        $user = $this->userWithRole(RoleName::Admin);
        $this->actingAs($user);

        return $user;
    }

    private function photoFile(string $name = 'photo.jpg'): UploadedFile
    {
        return UploadedFile::fake()->image($name, 800, 600);
    }

    // ---------- access ----------

    public function test_staff_cannot_open_gallery_news_or_stories(): void
    {
        $this->actingAs($this->userWithRole(RoleName::Staff));

        foreach (['gallery', 'news', 'stories'] as $module) {
            $this->get(route("admin.{$module}.index"))->assertForbidden();
            $this->get(route("admin.{$module}.create"))->assertForbidden();
        }
    }

    public function test_admin_can_open_the_lists(): void
    {
        GalleryItem::create(['title' => 'Office opening', 'image_path' => 'gallery/a.jpg']);
        NewsPost::create(['title' => 'New hours', 'slug' => 'new-hours', 'body' => 'Body text']);
        SuccessStory::create(['name' => 'Rahim Uddin', 'story' => 'I now work abroad happily.']);
        $this->admin();

        $this->get(route('admin.gallery.index'))->assertOk()->assertSee('Office opening');
        $this->get(route('admin.news.index'))->assertOk()->assertSee('New hours');
        $this->get(route('admin.stories.index'))->assertOk()->assertSee('Rahim Uddin');
    }

    // ---------- image helper ----------

    public function test_image_store_only_deletes_inside_its_own_folders(): void
    {
        Storage::disk('public')->put('gallery/ok.jpg', 'x');
        Storage::disk('public')->put('other/keep.jpg', 'x');

        ImageStore::delete('other/keep.jpg');
        ImageStore::delete('gallery/../other/keep.jpg');
        ImageStore::delete('');
        ImageStore::delete(null);

        Storage::disk('public')->assertExists('other/keep.jpg');

        ImageStore::delete('gallery/ok.jpg');
        Storage::disk('public')->assertMissing('gallery/ok.jpg');
    }

    // ---------- gallery ----------

    public function test_admin_can_add_several_photos_at_once(): void
    {
        $this->admin();

        Livewire::test(Gallery\Form::class)
            ->set('photos', [$this->photoFile('a.jpg'), $this->photoFile('b.png')])
            ->set('category', 'training')
            ->set('title', 'Welding class')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('admin.gallery.index'));

        $this->assertSame(2, GalleryItem::count());

        foreach (GalleryItem::all() as $item) {
            $this->assertSame('training', $item->category->value);
            $this->assertSame('Welding class', $item->title);
            $this->assertStringStartsWith('gallery/', $item->image_path);
            Storage::disk('public')->assertExists($item->image_path);
        }
    }

    public function test_gallery_needs_at_least_one_valid_image(): void
    {
        $this->admin();

        Livewire::test(Gallery\Form::class)
            ->call('save')
            ->assertHasErrors(['photos' => 'required']);

        Livewire::test(Gallery\Form::class)
            ->set('photos', [UploadedFile::fake()->create('notes.pdf', 10, 'application/pdf')])
            ->call('save')
            ->assertHasErrors('photos.0');

        Livewire::test(Gallery\Form::class)
            ->set('photos', [UploadedFile::fake()->create('logo.svg', 5, 'image/svg+xml')])
            ->call('save')
            ->assertHasErrors('photos.0');

        $this->assertSame(0, GalleryItem::count());
    }

    public function test_gallery_rejects_images_over_the_size_limit(): void
    {
        $this->admin();

        Livewire::test(Gallery\Form::class)
            ->set('photos', [UploadedFile::fake()->image('big.jpg')->size(5000)])
            ->call('save')
            ->assertHasErrors('photos.0');

        $this->assertSame(0, GalleryItem::count());
    }

    public function test_gallery_rejects_unknown_category(): void
    {
        $this->admin();

        Livewire::test(Gallery\Form::class)
            ->set('photos', [$this->photoFile()])
            ->set('category', 'weddings')
            ->call('save')
            ->assertHasErrors('category');
    }

    public function test_replacing_a_gallery_photo_removes_the_old_file(): void
    {
        Storage::disk('public')->put('gallery/old.jpg', 'old');
        $item = GalleryItem::create(['title' => 'Old', 'image_path' => 'gallery/old.jpg', 'is_active' => true]);
        $this->admin();

        Livewire::test(Gallery\Form::class, ['item' => $item])
            ->set('title', 'New title')
            ->set('photo', $this->photoFile('new.jpg'))
            ->call('save')
            ->assertHasNoErrors();

        $item->refresh();
        $this->assertSame('New title', $item->title);
        $this->assertNotSame('gallery/old.jpg', $item->image_path);
        Storage::disk('public')->assertMissing('gallery/old.jpg');
        Storage::disk('public')->assertExists($item->image_path);
    }

    public function test_editing_a_gallery_item_without_a_new_photo_keeps_the_file(): void
    {
        Storage::disk('public')->put('gallery/keep.jpg', 'x');
        $item = GalleryItem::create(['title' => 'Keep', 'image_path' => 'gallery/keep.jpg']);
        $this->admin();

        Livewire::test(Gallery\Form::class, ['item' => $item])
            ->set('caption', 'A caption')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame('gallery/keep.jpg', $item->fresh()->image_path);
        Storage::disk('public')->assertExists('gallery/keep.jpg');
    }

    public function test_deleting_a_gallery_item_removes_the_file(): void
    {
        Storage::disk('public')->put('gallery/gone.jpg', 'x');
        $item = GalleryItem::create(['image_path' => 'gallery/gone.jpg']);
        $this->admin();

        Livewire::test(Gallery\Index::class)
            ->call('confirmDelete', $item->id)
            ->call('delete');

        $this->assertModelMissing($item);
        Storage::disk('public')->assertMissing('gallery/gone.jpg');
    }

    public function test_hidden_gallery_photo_is_not_public(): void
    {
        $item = GalleryItem::create(['title' => 'Secret photo', 'image_path' => 'gallery/s.jpg', 'is_active' => true]);
        $this->admin();

        $this->get(route('gallery'))->assertSee('gallery/s.jpg');

        Livewire::test(Gallery\Index::class)->call('toggleActive', $item->id);

        $this->get(route('gallery'))->assertDontSee('gallery/s.jpg');
    }

    // ---------- news ----------

    public function test_draft_is_saved_without_a_date_and_stays_private(): void
    {
        $user = $this->admin();

        Livewire::test(News\Form::class)
            ->set('title', 'Office closed on Friday')
            ->set('body', 'Our office will be closed.')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('admin.news.index'));

        $post = NewsPost::firstWhere('title', 'Office closed on Friday');
        $this->assertSame('office-closed-on-friday', $post->slug);
        $this->assertFalse($post->is_published);
        $this->assertNull($post->published_at);
        $this->assertSame($user->id, $post->author_id);

        $this->get(route('news'))->assertDontSee('Office closed on Friday');
        $this->get(route('news.show', $post))->assertNotFound();
    }

    public function test_publishing_without_a_date_uses_now_and_goes_live(): void
    {
        $this->admin();

        Livewire::test(News\Form::class)
            ->set('title', 'New batch starts')
            ->set('body', 'A new training batch starts on Saturday.')
            ->set('is_published', true)
            ->call('save')
            ->assertHasNoErrors();

        $post = NewsPost::firstWhere('title', 'New batch starts');
        $this->assertNotNull($post->published_at);
        $this->assertTrue($post->published_at->lte(now()->addMinute()));

        $this->get(route('news'))->assertSee('New batch starts');
        $this->get(route('news.show', $post))->assertOk();
    }

    public function test_future_date_schedules_the_post(): void
    {
        $this->admin();

        Livewire::test(News\Form::class)
            ->set('title', 'Eid notice')
            ->set('body', 'Office closed for Eid holidays.')
            ->set('is_published', true)
            ->set('published_at', now()->addDays(3)->format('Y-m-d\TH:i'))
            ->call('save')
            ->assertHasNoErrors();

        $post = NewsPost::firstWhere('title', 'Eid notice');
        $this->assertTrue($post->published_at->isFuture());
        $this->get(route('news.show', $post))->assertNotFound();
    }

    public function test_news_slug_must_be_valid_and_unique(): void
    {
        NewsPost::create(['title' => 'Existing', 'slug' => 'existing', 'body' => 'Text here']);
        $this->admin();

        Livewire::test(News\Form::class)
            ->set('title', 'Another')
            ->set('body', 'Text here')
            ->set('slug', 'existing')
            ->call('save')
            ->assertHasErrors('slug');

        Livewire::test(News\Form::class)
            ->set('title', 'Another')
            ->set('body', 'Text here')
            ->set('slug', 'Bad Slug!')
            ->call('save')
            ->assertHasErrors('slug');
    }

    public function test_news_requires_title_and_body(): void
    {
        $this->admin();

        Livewire::test(News\Form::class)
            ->call('save')
            ->assertHasErrors(['title' => 'required', 'body' => 'required']);
    }

    public function test_news_cover_can_be_added_replaced_and_removed(): void
    {
        $post = NewsPost::create(['title' => 'With cover', 'slug' => 'with-cover', 'body' => 'Some text']);
        $this->admin();

        Livewire::test(News\Form::class, ['post' => $post])
            ->set('cover', $this->photoFile('c1.jpg'))
            ->call('save')
            ->assertHasNoErrors();

        $first = $post->fresh()->cover_image_path;
        $this->assertStringStartsWith('news/', $first);
        Storage::disk('public')->assertExists($first);

        Livewire::test(News\Form::class, ['post' => $post->fresh()])
            ->set('cover', $this->photoFile('c2.jpg'))
            ->call('save')
            ->assertHasNoErrors();

        $second = $post->fresh()->cover_image_path;
        $this->assertNotSame($first, $second);
        Storage::disk('public')->assertMissing($first);
        Storage::disk('public')->assertExists($second);

        Livewire::test(News\Form::class, ['post' => $post->fresh()])
            ->set('removeCover', true)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertNull($post->fresh()->cover_image_path);
        Storage::disk('public')->assertMissing($second);
    }

    public function test_news_toggle_published_sets_a_date_and_deleting_removes_cover(): void
    {
        Storage::disk('public')->put('news/cover.jpg', 'x');
        $post = NewsPost::create(['title' => 'Toggle me', 'slug' => 'toggle-me', 'body' => 'Text here', 'cover_image_path' => 'news/cover.jpg']);
        $this->admin();

        Livewire::test(News\Index::class)->call('togglePublished', $post->id);

        $post->refresh();
        $this->assertTrue($post->is_published);
        $this->assertNotNull($post->published_at);

        Livewire::test(News\Index::class)
            ->call('confirmDelete', $post->id)
            ->call('delete');

        $this->assertModelMissing($post);
        Storage::disk('public')->assertMissing('news/cover.jpg');
    }

    // ---------- success stories ----------

    public function test_admin_can_create_a_story_with_photo_and_country(): void
    {
        $country = Country::create(['name' => 'Malaysia', 'slug' => 'malaysia', 'is_active' => true, 'sort_order' => 1]);
        $this->admin();

        Livewire::test(Stories\Form::class)
            ->set('name', 'Karim Mia')
            ->set('trade', 'Electrician')
            ->set('country_id', (string) $country->id)
            ->set('story', 'I trained for eight weeks and now work in Malaysia.')
            ->set('photo', $this->photoFile('karim.jpg'))
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('admin.stories.index'));

        $story = SuccessStory::firstWhere('name', 'Karim Mia');
        $this->assertSame($country->id, $story->country_id);
        $this->assertTrue($story->is_published);
        $this->assertStringStartsWith('stories/', $story->photo_path);
        Storage::disk('public')->assertExists($story->photo_path);

        $this->get(route('success-stories'))->assertSee('Karim Mia');
    }

    public function test_story_validation(): void
    {
        $this->admin();

        Livewire::test(Stories\Form::class)
            ->call('save')
            ->assertHasErrors(['name' => 'required', 'story' => 'required']);

        Livewire::test(Stories\Form::class)
            ->set('name', 'Someone')
            ->set('story', 'A long enough story text.')
            ->set('country_id', '9999')
            ->call('save')
            ->assertHasErrors('country_id');

        Livewire::test(Stories\Form::class)
            ->set('name', 'Someone')
            ->set('story', 'A long enough story text.')
            ->set('photo', UploadedFile::fake()->create('cv.pdf', 10, 'application/pdf'))
            ->call('save')
            ->assertHasErrors('photo');
    }

    public function test_story_photo_can_be_removed_and_delete_cleans_up(): void
    {
        Storage::disk('public')->put('stories/p.jpg', 'x');
        $story = SuccessStory::create(['name' => 'Jamal', 'story' => 'A long enough story text.', 'photo_path' => 'stories/p.jpg']);
        $this->admin();

        Livewire::test(Stories\Form::class, ['record' => $story])
            ->set('removePhoto', true)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertNull($story->fresh()->photo_path);
        Storage::disk('public')->assertMissing('stories/p.jpg');

        Storage::disk('public')->put('stories/q.jpg', 'x');
        $story->update(['photo_path' => 'stories/q.jpg']);

        Livewire::test(Stories\Index::class)
            ->call('confirmDelete', $story->id)
            ->call('delete');

        $this->assertModelMissing($story);
        Storage::disk('public')->assertMissing('stories/q.jpg');
    }

    public function test_hidden_story_is_not_public(): void
    {
        $story = SuccessStory::create(['name' => 'Hidden Person', 'story' => 'A long enough story text.', 'is_published' => true]);
        $this->admin();

        $this->get(route('success-stories'))->assertSee('Hidden Person');

        Livewire::test(Stories\Index::class)->call('togglePublished', $story->id);

        $this->get(route('success-stories'))->assertDontSee('Hidden Person');
    }
}
