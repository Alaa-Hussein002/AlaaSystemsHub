<?php
// app/Http/Controllers/Api/Admin/ProjectController.php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProjectResource;
use App\Models\ActivityLog;
use App\Models\Project;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProjectController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $query = Project::orderBy('sort_order', 'asc')->orderBy('created_at', 'desc');

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                // ✅ FIXED: MySQL JSON query syntax
                $q->where('title->ar', 'like', "%{$search}%")
                  ->orWhere('title->en', 'like', "%{$search}%");
            });
        }

        $projects = $query->get();
        return $this->success(ProjectResource::collection($projects), 'قائمة المشاريع');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'  => 'required|array',
            'slug'   => 'nullable|string|unique:projects,slug',
            'status' => 'required|in:completed,in_progress,planned',
        ]);

        $data = $request->all();
        
        // Generate slug if not provided
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['title']['en'] ?? $data['title']['ar']);
        }
        
        // Initialize counters
        $data['views_count'] = 0;
        $data['likes_count'] = 0;

        $project = Project::create($data);

        // ✅ FIXED: Use $project->id instead of $project->_id
        ActivityLog::log(
            'create', 
            'projects', 
            "أضاف مشروع: " . ($data['title']['ar'] ?? ''), 
            'project', 
            $project->id
        );

        return $this->created(new ProjectResource($project), 'تم إنشاء المشروع');
    }

    public function show(string $id)
    {
        $project = Project::find($id);
        if (!$project) {
            return $this->notFound('المشروع غير موجود');
        }

        $project->load('testimonials');
        return $this->success(new ProjectResource($project));
    }

    public function update(Request $request, string $id)
    {
        $project = Project::find($id);
        if (!$project) {
            return $this->notFound('المشروع غير موجود');
        }

        // ✅ FIXED: Validation with SQL primary key
        $request->validate([
            'slug' => 'nullable|string|unique:projects,slug,' . $id . ',id',
        ]);

        $project->update($request->all());

        ActivityLog::log(
            'update', 
            'projects', 
            "عدّل مشروع: " . ($project->title['ar'] ?? ''), 
            'project', 
            $id
        );

        return $this->success(new ProjectResource($project), 'تم تحديث المشروع');
    }

    public function destroy(string $id)
    {
        $project = Project::find($id);
        if (!$project) {
            return $this->notFound('المشروع غير موجود');
        }

        $title = $project->title['ar'] ?? '';
        $project->delete();

        ActivityLog::log('delete', 'projects', "حذف مشروع: {$title}", 'project', $id);

        return $this->success(null, 'تم حذف المشروع');
    }
}