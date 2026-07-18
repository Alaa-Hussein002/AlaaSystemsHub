<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>رمز التحقق</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 40px 20px;
            direction: rtl;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 40px 30px;
            text-align: center;
        }
        .header h1 {
            color: white;
            font-size: 28px;
            margin-bottom: 10px;
        }
        .header p {
            color: rgba(255,255,255,0.9);
            font-size: 16px;
        }
        .content {
            padding: 40px 30px;
            text-align: center;
        }
        .greeting {
            font-size: 20px;
            color: #333;
            margin-bottom: 20px;
        }
        .message {
            font-size: 16px;
            color: #666;
            line-height: 1.6;
            margin-bottom: 30px;
        }
        .otp-box {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 15px;
            padding: 30px;
            margin: 30px 0;
        }
        .otp-code {
            font-size: 48px;
            font-weight: bold;
            color: white;
            letter-spacing: 10px;
            font-family: 'Courier New', monospace;
        }
        .expiry {
            background: #fff3cd;
            border-right: 4px solid #ffc107;
            padding: 15px 20px;
            margin: 20px 0;
            border-radius: 8px;
            text-align: right;
        }
        .expiry-text {
            color: #856404;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .warning {
            background: #f8d7da;
            border-right: 4px solid #dc3545;
            padding: 15px 20px;
            margin: 20px 0;
            border-radius: 8px;
            text-align: right;
        }
        .warning-text {
            color: #721c24;
            font-size: 14px;
        }
        .footer {
            background: #f8f9fa;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #dee2e6;
        }
        .footer p {
            color: #6c757d;
            font-size: 14px;
            margin: 5px 0;
        }
        .footer a {
            color: #667eea;
            text-decoration: none;
        }
        .icon {
            width: 24px;
            height: 24px;
            display: inline-block;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔐 رمز إعادة تعيين كلمة المرور</h1>
            <p>Alaa Hussein - نظام إدارة متكامل</p>
        </div>

        <div class="content">
            <p class="greeting">مرحباً {{ $userName }} 👋</p>
            
            <p class="message">
                تلقينا طلباً لإعادة تعيين كلمة المرور لحسابك.<br>
                استخدم الرمز التالي لإكمال عملية إعادة التعيين:
            </p>

            <div class="otp-box">
                <div class="otp-code">{{ $otp }}</div>
            </div>

            <div class="expiry">
                <p class="expiry-text">
                    ⏰ <strong>تنبيه:</strong> هذا الرمز صالح لمدة <strong>{{ $expiryMinutes }} دقائق</strong> فقط
                </p>
            </div>

            <div class="warning">
                <p class="warning-text">
                    ⚠️ <strong>تحذير أمني:</strong><br>
                    إذا لم تطلب إعادة تعيين كلمة المرور، يرجى تجاهل هذه الرسالة وعدم مشاركة هذا الرمز مع أي شخص.
                </p>
            </div>

            <p class="message" style="font-size: 14px; color: #999; margin-top: 30px;">
                لحماية حسابك، لا تشارك هذا الرمز مع أي شخص، حتى موظفي الدعم الفني.
            </p>
        </div>

        <div class="footer">
            <p><strong>Alaa Hussein</strong></p>
            <p>نظام إدارة متكامل للمشاريع والمتجر الإلكتروني</p>
            <p style="margin-top: 15px;">
                <a href="mailto:ala.hussein002@gmail.com">ala.hussein002@gmail.com</a>
            </p>
            <p style="color: #999; font-size: 12px; margin-top: 15px;">
                © {{ date('Y') }} Alaa Hussein. جميع الحقوق محفوظة.
            </p>
        </div>
    </div>
</body>
</html>