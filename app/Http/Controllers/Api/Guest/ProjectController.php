<?php
// app/Http/Controllers/Api/Guest/ProjectController.php

namespace App\Http\Controllers\Api\Guest;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProjectResource;
use Illuminate\Support\Facades\Auth;
use App\Models\AnalyticsEvent;
use App\Models\Project;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $query = Project::where('is_published', true)
            ->orderBy('sort_order', 'asc')
            ->orderBy('created_at', 'desc');

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by featured
        if ($request->filled('featured')) {
            $query->where('is_featured', true);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title->ar', 'like', "%{$search}%")
                  ->orWhere('title->en', 'like', "%{$search}%")
                  ->orWhere('short_description', 'like', "%{$search}%");
            });
        }

        $projects = $query->get();

        return $this->success(ProjectResource::collection($projects));
    }

    public function show(string $slug)
    {
        $project = Project::where('slug', $slug)
            ->where('is_published', true)
            ->first();

        if (!$project) {
            return $this->notFound('المشروع غير موجود');
        }

        // Increment views
        $project->increment('views_count');
        
        // Track analytics
        try {
            AnalyticsEvent::track([
                'event_type' => 'project_view',
                'event_category' => 'portfolio',
                'target_type' => 'project',
                'target_id' => $project->id,
                'target_title' => $project->title['ar'] ?? $project->title['en'] ?? 'Unknown',
                'visitor' => [
                    'user_id' => Auth::id(),
                ],
            ]);
        } catch (\Exception $e) {
            // Silent fail - analytics shouldn't break the app
        }

        return $this->success(new ProjectResource($project));
    }
}