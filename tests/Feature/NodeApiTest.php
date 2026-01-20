<?php

namespace Tests\Feature;

use App\Models\Node;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use NumberFormatter;

class NodeApiTest extends TestCase
{
    use RefreshDatabase;

    
    /**
     * Setup the test environment.
     *
     * This method seeds the database with some example nodes.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\NodeSeeder::class);
    }

    public function test_it_creates_a_root_node(): void
    {
        $response = $this->postJson('/api/nodes');

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'parent',
                    'title',
                    'created_at',
                ],
            ]);

        $this->assertDatabaseCount('nodes', 6);
    }

    public function test_it_creates_a_child_node(): void
    {
        $parent = Node::first();

        $response = $this->postJson('/api/nodes', [
            'parent_id' => $parent->id,
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.parent', $parent->id);
    }

    public function test_it_lists_root_nodes(): void
    {
        $response = $this->getJson('/api/nodes/roots');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    public function test_it_lists_direct_children(): void
    {
        $node = Node::whereNotNull('parent_id')->first()->parent;

        $response = $this->getJson("/api/nodes/{$node->id}/children");

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }

    public function test_it_lists_children_with_depth(): void
    {
        $root = Node::whereNull('parent_id')->first();

        $response = $this->getJson("/api/nodes/{$root->id}/children?depth=2");

        $response->assertStatus(200)
            ->assertJsonCount(4, 'data');
    }

    public function test_it_translates_title_based_on_language_header(): void
    {
        $response = $this->getJson('/api/nodes/roots', ['Accept-Language' => 'es']);

        $node = $response->json('data.0');

        $formatter = new NumberFormatter('es', NumberFormatter::SPELLOUT);
        $expectedTitle = $formatter->format($node['id']);

        $this->assertEquals($expectedTitle, $node['title']);
    }

    public function test_it_converts_created_at_to_requested_timezone(): void
    {
        $response = $this->getJson('/api/nodes/roots', [
            'Time-Zone' => 'America/Bogota',
        ]);

        $this->assertNotEmpty(
            $response->json('data.0.created_at')
        );
    }

    public function test_it_does_not_delete_node_with_children(): void
    {
        $node = Node::whereNull('parent_id')->first();

        $response = $this->deleteJson("/api/nodes/{$node->id}");

        $response->assertStatus(422);
    }

    public function test_it_deletes_leaf_node(): void
    {
        $node = Node::doesntHave('children')->first();

        $response = $this->deleteJson("/api/nodes/{$node->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('nodes', [
            'id' => $node->id,
        ]);
    }
}
