<?php

namespace App\Http\Controllers\Api\Guest;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProjectResource;
use App\Models\AnalyticsEvent;
use App\Models\Project;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $query = Project::published()->ordered();

        // فلترة حسب التصنيف
        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        // فلترة المميزة فقط
        if ($request->boolean('featured')) {
            $query->featured();
        }

        // بحث
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title.ar', 'like', "%{$search}%")
                  ->orWhere('title.en', 'like', "%{$search}%")
                  ->orWhere('tags', 'all', [$search]);
            });
        }

        $projects = $query->get();

        return $this->success(
            ProjectResource::collection($projects),
            'قائمة المشاريع'
        );
    }

    public function show(string $slug)
    {
        $project = Project::published()
                          ->where('slug', $slug)
                          ->first();

        if (!$project) {
            return $this->notFound('المشروع غير موجود');
        }

        $project->incrementViews();
        $project->load('testimonials');

        // تسجيل الزيارة
        AnalyticsEvent::create([
            'event_type'     => 'page_view',
            'event_category' => 'portfolio',
            'target_type'    => 'project',
            'target_id'      => (string) $project->_id,
            'target_title'   => $project->title['ar'] ?? $project->title,
            'visitor'        => [
                'ip_hash'    => md5(request()->ip()),
                'session_id' => session()->getId(),
            ],
            'page_url' => request()->fullUrl(),
        ]);

        return $this->success(
            new ProjectResource($project),
            'تفاصيل المشروع'
        );
    }
}