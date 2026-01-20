<?php

namespace Database\Seeders;

use App\Models\Node;
use Illuminate\Database\Seeder;
use NumberFormatter;

class NodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $formatter = new NumberFormatter('en', NumberFormatter::SPELLOUT);

        $rootNode = Node::create([
            'parent_id' => null,
            'title' => $formatter->format(1),
        ]);

        $child = Node::create([
            'parent_id' => $rootNode->id,
            'title' => $formatter->format(2),
        ]);

        Node::create([
            'parent_id' => $rootNode->id,
            'title' => $formatter->format(3),
        ]);

        Node::create([
            'parent_id' => $child->id,
            'title' => $formatter->format(4),
        ]);

        Node::create([
            'parent_id' => $child->id,
            'title' => $formatter->format(5),
        ]);
    }
}
