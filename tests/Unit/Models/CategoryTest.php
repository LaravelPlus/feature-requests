<?php

namespace LaravelPlus\FeatureRequests\Tests\Unit\Models;

use LaravelPlus\FeatureRequests\Models\Category;
use LaravelPlus\FeatureRequests\Models\FeatureRequest;
use LaravelPlus\FeatureRequests\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\Collection;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_category_has_many_feature_requests()
    {
        $category = Category::factory()->create();
        $featureRequests = FeatureRequest::factory()->count(3)->create(['category_id' => $category->id]);

        $this->assertInstanceOf(Collection::class, $category->featureRequests);
        $this->assertCount(3, $category->featureRequests);
    }

    public function test_category_uses_soft_deletes()
    {
        $category = Category::factory()->create();
        
        $this->assertNull($category->deleted_at);
        
        $category->delete();
        
        $this->assertSoftDeleted('feature_request_categories', ['id' => $category->id]);
    }

    public function test_category_has_slug()
    {
        $category = Category::factory()->create(['name' => 'User Interface']);
        
        $this->assertNotNull($category->slug);
        $this->assertEquals('user-interface', $category->slug);
    }

    public function test_category_has_description()
    {
        $category = Category::factory()->create(['description' => 'UI related features']);
        
        $this->assertEquals('UI related features', $category->description);
    }

    public function test_category_has_color()
    {
        $category = Category::factory()->create(['color' => '#FF5733']);
        
        $this->assertEquals('#FF5733', $category->color);
    }

    public function test_category_can_be_active()
    {
        $category = Category::factory()->create(['is_active' => true]);
        
        $this->assertTrue($category->is_active);
    }

    public function test_category_scope_active()
    {
        Category::factory()->create(['is_active' => true]);
        Category::factory()->create(['is_active' => false]);
        
        $activeCategories = Category::active()->get();
        
        $this->assertCount(1, $activeCategories);
        $this->assertTrue($activeCategories->first()->is_active);
    }

    public function test_category_scope_by_slug()
    {
        $category = Category::factory()->create(['slug' => 'test-category']);
        
        $foundCategory = Category::bySlug('test-category')->first();
        
        $this->assertNotNull($foundCategory);
        $this->assertEquals($category->id, $foundCategory->id);
    }

    public function test_category_feature_requests_count()
    {
        $category = Category::factory()->create();
        FeatureRequest::factory()->count(5)->create(['category_id' => $category->id]);
        
        $this->assertEquals(5, $category->featureRequests()->count());
    }

    public function test_category_with_feature_requests_scope()
    {
        $category = Category::factory()->create();
        FeatureRequest::factory()->count(3)->create(['category_id' => $category->id]);
        Category::factory()->create(); // Category without feature requests
        
        $categoriesWithRequests = Category::withFeatureRequests()->get();
        
        $this->assertCount(1, $categoriesWithRequests);
        $this->assertEquals($category->id, $categoriesWithRequests->first()->id);
    }
}
