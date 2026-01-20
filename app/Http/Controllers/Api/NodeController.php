<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\NodeResource;
use App\Models\Node;
use App\Services\NodeService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class NodeController extends Controller
{
	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct(
        private NodeService $nodeService
    )
	{
	}

    /**
     * Store a new node.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'parent_id' => [ 'nullable', 'integer', 'exists:nodes,id'],
        ]);

        $node = $this->nodeService->create($data['parent_id'] ?? null);

        return (new NodeResource($node))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

	/**
	 * Get all root nodes.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function roots()
    {
        return NodeResource::collection(
            $this->nodeService->getRoots()
        );
    }

	/**
	 * Get the children of a node.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \App\Models\Node  $node
	 * @return \Illuminate\Http\Response
	 */
	public function children(Request $request, Node $node)
    {
        $depth = $request->query('depth');

        return NodeResource::collection(
             $this->nodeService->getChildren(
                $node,
                $depth !== null ? (int) $depth : null
             )
        );
    }

	/**
	 * Delete a node.
	 *
	 * @param  \App\Models\Node  $node
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(Node $node)
    {
       $this->nodeService->delete($node);

        return response()->noContent();
    }
}
