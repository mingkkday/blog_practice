<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Http\Services\ArticleService;
use App\Http\Resources\ArticleResource;
use App\Http\Requests\ArticleStoreRequest;
use App\Http\Requests\ArticleUpdateRequest;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    private $service;
    public function __construct(ArticleService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $data = $this->service->getLatest();
        return ArticleResource::collection($data);
    }

    public function show(Article $article)
    {
        return new ArticleResource($article);
    }

    public function store(ArticleStoreRequest $request)
    {
        $formFields = $request->validated();
        if ($request->hasFile('photo')) {
            $formFields['photo'] = $request->file('photo')->store('photos', 'public');
        }
        $formFields['user_id'] = auth()->id();

        $this->service->create($formFields);
        return response()->json([], 204);
    }

    public function destroy(Article $article)
    {
        abort_if($article->user_id !== auth()->id(), 403, 'Unauthorized action.');
        $this->service->delete($article);
        return response()->json([], 204);
    }

    public function update(ArticleUpdateRequest $request, Article $article)
    {
        abort_if($article->user_id !== auth()->id(), 403, 'Unauthorized action.');

        $formFields = $request->validated();
        if ($request->hasFile('photo')) {
            $formFields['photo'] = $request->file('photo')->store('photos', 'public');
        }
        $formFields['id'] = $article->id;

        $this->service->update($formFields);
        return response()->json([], 204);
    }

    public function search(Request $request)
    {
        $body = json_decode($request->getContent());
        $searchId = $this->service->search($body->search);
        return response($searchId, 200);
    }

    public function searchResult(Request $request)
    {
        $searchId = $request->query('searchId');
        $result = $this->service->searchResult($searchId);

        if ($result === NULL) return response()->json(['error' => 'No such searchId or already responded, please retry search', 400]);
        if ($result === 'pending') return response()->json(['message' => 'pending', 204]);

        return response($result, 200);
    }
}
