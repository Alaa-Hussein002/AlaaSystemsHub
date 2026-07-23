<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Media;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Cloudinary\Cloudinary;

class MediaController extends Controller
{
    use ApiResponse;

    // protected $cloudinary;
    // public function __construct()
    // {
    //     // ✅ إعداد Cloudinary يدوياً
    //     $this->cloudinary = new Cloudinary([
    //         'cloud' => [
    //             'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
    //             'api_key'    => env('CLOUDINARY_API_KEY'),
    //             'api_secret' => env('CLOUDINARY_API_SECRET'),
    //         ],
    //         'url' => [
    //             'secure' => true
    //         ]
    //     ]);
    // }
    public function index(Request $request)
    {
        $query = Media::orderBy('created_at', 'desc');
        
        if ($request->has('folder')) {
            $query->where('folder', $request->folder);
        }
        
        if ($request->has('type')) {
            $query->images();
        }

        $media = $query->get();
        return $this->success($media);
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file'     => 'required|file|max:10240',
            'folder'   => 'nullable|string|max:50',
            'alt_text' => 'nullable|string|max:255',
        ]);

        try {
            $file = $request->file('file');
            $folder = Str::slug($request->get('folder', 'general'));
            
            // ✅ توليد اسم فريد للملف
            // $fileName = Str::random(40) . '.' . $file->getClientOriginalExtension();
            // ✅ رفع إلى Cloudinary
            // $uploadedFile = $file->storeOnCloudinary("media/{$folder}");
            
            // ✅ الحصول على البيانات
            // $fileUrl = $uploadedFile->getSecurePath(); // رابط HTTPS
            // $publicId = $uploadedFile->getPublicId(); // معرّف الملف
            // $filePath = $uploadedFile->getPath(); // المسار

            // ✅ حفظ الملف
            // $filePath = $file->storeAs("media/{$folder}", $fileName, 'public');
            
            // ✅ توليد الرابط الكامل
            // $fileUrl = Storage::url($filePath);

            // $uploadedFile = $this->cloudinary->uploadApi()->upload(
            //     $file->getRealPath(),
            //     [
            //         'folder' => "media/{$folder}",
            //         'resource_type' => 'auto', // صورة/فيديو/ملف تلقائياً
            //         'public_id' => Str::random(40), // اسم فريد
            //     ]
            // );
            
            // ✅ إعداد Cloudinary
            $cloudinary = new Cloudinary([
                'cloud' => [
                    'cloud_name' => config('cloudinary.cloud_name', env('CLOUDINARY_CLOUD_NAME')),
                    'api_key'    => config('cloudinary.api_key', env('CLOUDINARY_API_KEY')),
                    'api_secret' => config('cloudinary.api_secret', env('CLOUDINARY_API_SECRET')),
                ],
                'url' => [
                    'secure' => true
                ]
            ]);
            
            // ✅ رفع الملف
            $uploadedFile = $cloudinary->uploadApi()->upload(
                $file->getRealPath(),
                [
                    'folder'        => "media/{$folder}",
                    'resource_type' => 'auto',
                    'public_id'     => Str::random(40),
                ]
            );

            // ✅ إنشاء السجل
            $media = Media::create([
                'original_name'   => $file->getClientOriginalName(),
                'file_name'       => $uploadedFile['public_id'], // استخدام معرّف Cloudinary كاسم الملف
                'file_path'       => $uploadedFile['public_id'], // المسار
                'file_url'        => $uploadedFile['secure_url'], // ✅ رابط كامل
                'mime_type'       => $file->getMimeType(),
                'file_size'       => $uploadedFile['bytes'],
                'file_size_human' => $this->humanFileSize($uploadedFile['bytes']),
                'alt_text'        => $request->alt_text,
                'folder'          => $folder,
                'disk'            => 'cloudinary',
                'uploaded_by'     => Auth::id(),
            ]);

            ActivityLog::log('create', 'media', "رفع ملف: {$file->getClientOriginalName()}");

            return $this->created($media, 'تم رفع الملف بنجاح');

        } catch (\Exception $e) {
            Log::error('Media upload error: ' . $e->getMessage());
            Log::error('Cloudinary config: ' . json_encode([
                'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
                'api_key' => env('CLOUDINARY_API_KEY'),
                'has_secret' => !empty(env('CLOUDINARY_API_SECRET')),
            ]));
            return $this->error('فشل رفع الملف: ' . $e->getMessage(), 500);
        }
    }

    public function destroy(string $id)
    {
        try {
            $media = Media::find($id);
            
            if (!$media) {
                return $this->notFound('الملف غير موجود');
            }

            // حذف الملف الفعلي
            if (Storage::disk('public')->exists($media->file_path)) {
                Storage::disk('public')->delete($media->file_path);
            }

            $originalName = $media->original_name;
            $media->delete();

            ActivityLog::log('delete', 'media', "حذف ملف: {$originalName}");

            return $this->success(null, 'تم حذف الملف بنجاح');

        } catch (\Exception $e) {
            Log::error('Media delete error: ' . $e->getMessage());
            return $this->error('فشل حذف الملف', 500);
        }
    }

    /**
     * عرض الملف مباشرة
     */
    public function show(string $id)
    {
        $media = Media::find($id);
        
        if (!$media) {
            return $this->notFound('الملف غير موجود');
        }

        $path = Storage::disk('public')->path($media->file_path);

        if (!file_exists($path)) {
            return $this->notFound('الملف غير موجود على الخادم');
        }

        return response()->file($path);
    }

    private function humanFileSize($bytes): string
    {
        if ($bytes == 0) return '0 B';
        
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $factor = floor((strlen((string)$bytes) - 1) / 3);
        
        return sprintf("%.1f %s", $bytes / pow(1024, $factor), $units[$factor] ?? 'TB');
    }
    // private function humanFileSize($bytes, $decimals = 2)
    // {
    //     $size = ['B', 'KB', 'MB', 'GB', 'TB'];
    //     $factor = floor((strlen($bytes) - 1) / 3);
    //     return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . ' ' . @$size[$factor];
    // }
}