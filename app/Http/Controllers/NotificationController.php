<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Google\Auth\Credentials\ServiceAccountCredentials;
use Google\Auth\HttpHandler\HttpHandlerFactory;
use Illuminate\Support\Facades\Http;
use App\Models\User;
use App\Notifications\SystemUpdateNotification;
use Illuminate\Support\Facades\Notification;

class NotificationController extends Controller
{
    // عرض الصفحة
    public function index()
    {
        return view('notifications.index');
    }
public function send(Request $request)
{
    $request->validate([
        'title'  => 'required|string|max:255',
        'body'   => 'required|string',
        'target' => 'required|in:mobile,web,all',
    ]);

    $statusMessage = "";
    $target = $request->target;

    try {
        // أولاً: إرسال إشعار للموقع
        if ($target == 'web' || $target == 'all') {
            $users = User::all();
            Notification::send($users, new SystemUpdateNotification($request->title, $request->body));
            $statusMessage .= "تم إرسال إشعار الموقع بنجاح. ";
        }

        // ثانياً: إرسال إشعار للجوال
        if ($target == 'mobile' || $target == 'all') {
            $projectId = 'thfeez-app';

            // حل مشكلة SSL لجلب التوكن من السيرفر المحلي
            $httpClient = new \GuzzleHttp\Client(['verify' => false]);
            $handler = \Google\Auth\HttpHandler\HttpHandlerFactory::build($httpClient);

            $credentials = new ServiceAccountCredentials(
                'https://www.googleapis.com/auth/cloud-platform',
                storage_path('app/firebase_credentials.json')
            );

            $token = $credentials->fetchAuthToken($handler);
            $accessToken = $token['access_token'];

            $url = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";

                    $data = [
                "message" => [
                    "topic" => "all",
                    "notification" => [
                        "title" => $request->title,
                        "body" => $request->body,
                    ],
                    "android" => [
                        "priority" => "high",
                        "notification" => [
                            "channel_id" => "high_importance_channel",
                            "sound" => "default",
                            "notification_priority" => "PRIORITY_MAX",
                        ]
                    ],
                    "data" => [
                        "click_action" => "FLUTTER_NOTIFICATION_CLICK",
                    ]
                ]
            ];

            // إرسال الطلب الفعلي لجوجل
            $response = Http::withoutVerifying()->withToken($accessToken)->post($url, $data);

            if ($response->successful()) {
                $statusMessage .= "تم إرسال إشعار الجوال بنجاح.";
            } else {
                throw new \Exception("فشل إرسال الجوال: " . $response->body());
            }
        }

        return back()->with('success', $statusMessage);

    } catch (\Exception $e) {
        return back()->with('error', 'حدث خطأ: ' . $e->getMessage());
    }
}

    // الدالة المعدلة لاخفاء الخط الأحمر نهائياً
    public function markAsRead($id)
    {
        $user = \Auth::user();

        $notification = $user->unreadNotifications()->where('id', $id)->firstOrFail();

        $notification->markAsRead();

        return back()->with('info', [
            'title' => $notification->data['title'] ?? 'بدون عنوان',
            'body'  => $notification->data['body'] ?? 'لا يوجد محتوى'
        ]);
    }
    public function markAllAsRead()
    {
        auth()->user()->unreadNotifications->markAsRead();
        return back()->with('success', 'تم تحديد جميع التنبيهات كمقروءة');
    }
}
