<?php

namespace App\Services;

use App\Models\Node;
use Illuminate\Validation\ValidationException;
use NumberFormatter;

class NodeService
{
   
    /**
     * Create a new node with the given parent ID.
     * 
     * If no parent ID is given, the node will be a root node.
     * 
     * The title of the node will be automatically generated based on its ID.
     * 
     * @param int|null $parentId
     * @return Node
     */
    public function create(?int $parentId = null): Node
    {
        $node = Node::create([
            'parent_id' => $parentId,
            'title' => '', // It will be updated later to ensure the title is correct.
        ]);

        // Generate the title with the correct ID from DDBB.
        $formatter = new NumberFormatter('en', NumberFormatter::SPELLOUT);
        $node->update([
            'title' => $formatter->format($node->id),
        ]);

        return $node;
    }

    /**
     * Get all root nodes.
     * 
     * @return Collection
     */
    public function getRoots()
    {
        return Node::whereNull('parent_id')->get();
    }

    /**
     * Get the children of a node.
     * 
     * @param Node $node
     * @param int|null $depth
     * @return Collection
     */
    public function getChildren(Node $node, ?int $depth = null)
    {
        if ($depth === null) {
            return $node->children;
        }

        if ($depth < 1) {
            throw ValidationException::withMessages([
                'depth' => 'Depth must be a positive integer',
            ]);
        }

        $this->loadRecursive($node, $depth);

        return $node->children;
    }

    /**
     * Delete a node.
     * 
     * @param Node $node
     * @return void
     */
    public function delete(Node $node): void
    {
        if ($node->children()->exists()) {
            throw ValidationException::withMessages([
                'node' => 'Cannot delete a node with children',
            ]);
        }

        $node->delete();
    }

    /**
     * Loads all children of a node recursively, up to a given depth.
     * 
     * @param Node $node
     * @param int $depth
     * @return void
     */
    private function loadRecursive(Node $node, int $depth): void
    {
        if ($depth <= 0) {
            return;
        }

        if (!$node->relationLoaded('children')) {
            $node->load('children');
        }

        foreach ($node->children as $child) {
            $this->loadRecursive($child, $depth - 1);
        }
    }
}
