<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\TagType;

class TagController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tags = Tag::all();
        return response()->json($tags);
    }

    /**
     * Display the specified resource.
     */
    public function show(Tag $tag)
    {
        return response()->json($tag);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tag $tag)
    {
        $tag->delete();

        return response()->json([
            'message' => 'Tag deleted successfully'
        ]);
    }

    /**
     * search tag type and verification if he's not null.
     */
    public function getTagsByType($type)
    {
        $tagType = TagType::where('type', $type)->first();
        if (!$tagType) {
            return response()->json(['message' => 'Type de tag introuvable'], 404);
        }

        $tags = $tagType->tags;
        return response()->json($tags);
    }


}

