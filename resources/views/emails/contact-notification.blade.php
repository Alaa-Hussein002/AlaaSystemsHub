<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 20px; direction: rtl; text-align: right; }
        .container { max-width: 600px; margin: 0 auto; background-color: white; border-radius: 12px; box-shadow: 0 10px 40px rgba(0,0,0,0.2); overflow: hidden; }
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 40px 20px; color: white; text-align: center; }
        .header h1 { font-size: 28px; margin-bottom: 10px; font-weight: 700; }
        .header p { font-size: 14px; opacity: 0.9; }
        .content { padding: 40px 30px; }
        .greeting { font-size: 16px; color: #333; margin-bottom: 30px; line-height: 1.6; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px; }
        .info-item { background-color: #f8f9fa; padding: 15px; border-radius: 8px; border-right: 4px solid #667eea; }
        .info-label { font-size: 12px; color: #999; text-transform: uppercase; margin-bottom: 5px; font-weight: 600; }
        .info-value { font-size: 15px; color: #333; font-weight: 600; word-break: break-all; }
        .badges { display: flex; gap: 10px; margin-bottom: 30px; flex-wrap: wrap; }
        .badge { padding: 8px 16px; border-radius: 20px; font-size: 12px; font-weight: 600; display: inline-block; }
        .badge-category { background-color: #e3f2fd; color: #1976d2; }
        .badge-priority { color: white; font-weight: 700; }
        .message-section { background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); padding: 20px; border-radius: 8px; margin-bottom: 30px; border-left: 5px solid #667eea; }
        .message-section h3 { color: #333; margin-bottom: 15px; font-size: 14px; text-transform: uppercase; font-weight: 700; }
        .message-content { background-color: white; padding: 20px; border-radius: 6px; color: #555; line-height: 1.8; font-size: 15px; min-height: 100px; white-space: pre-wrap; word-wrap: break-word; }
        .footer-info { background-color: #f5f5f5; padding: 15px; border-radius: 8px; margin-bottom: 30px; font-size: 12px; color: #666; border-right: 3px solid #ddd; }
        .footer-info p { margin: 8px 0; }
        .actions { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 30px; }
        .action-btn { padding: 12px; border: none; border-radius: 6px; font-size: 13px; font-weight: 600; text-decoration: none; display: inline-block; text-align: center; cursor: pointer; color: white; }
        .action-btn-email { background-color: #667eea; }
        .action-btn-whatsapp { background-color: #25D366; }
        .footer { background-color: #f8f9fa; padding: 25px; border-top: 1px solid #eee; text-align: center; font-size: 12px; color: #999; }
        .footer p { margin: 5px 0; }
        @media only screen and (max-width: 600px) {
            .info-grid { grid-template-columns: 1fr; }
            .actions { grid-template-columns: 1fr; }
            .content { padding: 20px 15px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📨 رسالة جديدة</h1>
            <p>من نموذج التواصل الخاص بموقعك</p>
        </div>

        <div class="content">
            <div class="greeting">
                مرحباً! 👋<br>
                لقد استقبلت رسالة جديدة من خلال نموذج التواصل. إليك التفاصيل:
            </div>

            <div class="badges">
                <span class="badge badge-category">📁 {{ $message['category'] ?? 'أخرى' }}</span>
                <span class="badge badge-priority" style="background-color: #0088ff;">⚡ {{ $message['priority'] ?? 'عادية' }}</span>
            </div>

            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">👤 الاسم</div>
                    <div class="info-value">{{ $message['name'] ?? '' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">📧 البريد الإلكتروني</div>
                    <div class="info-value">
                        <a href="mailto:{{ $message['email'] ?? '' }}" style="color: #667eea; text-decoration: none;">
                            {{ $message['email'] ?? '' }}
                        </a>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">📱 رقم الهاتف</div>
                    <div class="info-value">
                        <a href="tel:{{ $message['phone'] ?? '' }}" style="color: #667eea; text-decoration: none;">
                            {{ $message['phone'] ?? '' }}
                        </a>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">📌 الموضوع</div>
                    <div class="info-value">{{ $message['subject'] ?? '' }}</div>
                </div>
            </div>

            <div class="message-section">
                <h3>💬 محتوى الرسالة</h3>
                <div class="message-content">{{ $message['message'] ?? '' }}</div>
            </div>

            <div class="footer-info">
                <p><strong>⏰ التاريخ والوقت:</strong> {{ $message['created_at'] ?? '' }}</p>
                <p><strong>🌐 عنوان IP:</strong> {{ $message['ip_address'] ?? '' }}</p>
                <p><strong>🔐 معرف الرسالة:</strong> #{{ $message['id'] ?? '' }}</p>
            </div>

            <div class="actions">
                <a href="mailto:{{ $message['email'] ?? '' }}?subject=رد على: {{ $message['subject'] ?? '' }}" class="action-btn action-btn-email">
                    ✉️ الرد عبر البريد
                </a>
                <a href="https://wa.me/{{ str_replace(['+', ' ', '-'], '', $message['phone'] ?? '') }}?text=مرحباً" class="action-btn action-btn-whatsapp" target="_blank">
                    💬 الرد عبر واتساب
                </a>
            </div>
        </div>

        <div class="footer">
            <p>✉️ تم استقبال هذه الرسالة من خلال نموذج التواصل</p>
            <p style="margin-top: 10px; opacity: 0.7;">© 2026 جميع الحقوق محفوظة | Alaa Hussein</p>
        </div>
    </div>
</body>
</html>