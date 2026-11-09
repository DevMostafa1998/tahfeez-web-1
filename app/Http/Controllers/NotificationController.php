<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Google\Auth\Credentials\ServiceAccountCredentials;
use Google\Auth\HttpHandler\HttpHandlerFactory;
use Illuminate\Support\Facades\Http;
use App\Models\User;
use App\Models\Center;
use App\Notifications\SystemUpdateNotification;
use Illuminate\Support\Facades\Notification;

class NotificationController extends Controller
{
    // عرض الصفحة مع جلب بيانات المركز الحالي
    public function index()
    {
        $selectedCenterId = session('selected_center_id');
        $currentCenter = null;

        if ($selectedCenterId) {
            $currentCenter = Center::find($selectedCenterId);
        }

        return view('notifications.index', compact('currentCenter'));
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

        // 1. جلب رقم المركز المحدد حالياً من الـ Session
        $selectedCenterId = session('selected_center_id');

        try {
            // أولاً: إرسال إشعار للموقع (فلترة المستخدمين حسب المركز)
            if ($target == 'web' || $target == 'all') {
                $usersQuery = User::query();

                // إذا تم تحديد مركز من السيشن، نرسل للمحفظين/المستخدمين التابعين لهذا المركز فقط
                if ($selectedCenterId) {
                    $usersQuery->where('center_id', $selectedCenterId);
                }

                $users = $usersQuery->get();

                if ($users->isNotEmpty()) {
                    Notification::send($users, new SystemUpdateNotification($request->title, $request->body));
                    $statusMessage .= "تم إرسال إشعار الموقع بنجاح (" . $users->count() . " مستخدم). ";
                } else {
                    $statusMessage .= "لا يوجد مستخدمون مرتبطون بالمركز المحدد لإرسال إشعار الموقع. ";
                }
            }

            // ثانياً: إرسال إشعار للجوال
            if ($target == 'mobile' || $target == 'all') {
                $projectId = 'thfeez-app';

                // حل مشكلة SSL لجلب التوكن من السيرفر المحلي
                $httpClient = new \GuzzleHttp\Client(['verify' => false]);
                $handler = HttpHandlerFactory::build($httpClient);

                $credentials = new ServiceAccountCredentials(
                    'https://www.googleapis.com/auth/cloud-platform',
                    storage_path('app/firebase_credentials.json')
                );

                $token = $credentials->fetchAuthToken($handler);
                $accessToken = $token['access_token'];

                $url = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";

                // تحديد Topic الجوال بناءً على المركز المحدد
                // إذا كان هناك مركز محدد يرسل لـ topic_center_ID، وإلا يرسل للكل "all"
                $fcmTopic = $selectedCenterId ? "center_" . $selectedCenterId : "all";

                $data = [
                    "message" => [
                        "topic" => $fcmTopic,
                        "notification" => [
                            "title" => $request->title,
                            "body"  => $request->body,
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
                            "click_url" => $request->input('click_url', 'default'),
                            "center_id" => (string)$selectedCenterId,
                        ]
                    ]
                ];

                // إرسال الطلب الفعلي لجوجل
                $response = Http::withoutVerifying()->withToken($accessToken)->post($url, $data);

                if ($response->successful()) {
                    $statusMessage .= "تم إرسال إشعار الجوال بنجاح (الموضوع: {$fcmTopic}).";
                } else {
                    throw new \Exception("فشل إرسال الجوال: " . $response->body());
                }
            }

            return back()->with('success', $statusMessage);

        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

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
