<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تحت الإنشاء</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }

        /* خلفية متحركة */
        .animated-bg {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, rgba(255,255,255,0.1), rgba(255,255,255,0.05));
            background-size: 200% 200%;
            animation: gradientShift 6s ease infinite;
        }

        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* عناصر ديكورية متحركة */
        .floating-elements {
            position: absolute;
            width: 100%;
            height: 100%;
            overflow: hidden;
        }

        .floating-element {
            position: absolute;
            width: 20px;
            height: 20px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: float 8s infinite linear;
        }

        .floating-element:nth-child(1) {
            left: 10%;
            animation-delay: 0s;
            animation-duration: 6s;
        }

        .floating-element:nth-child(2) {
            left: 20%;
            animation-delay: 1s;
            animation-duration: 8s;
        }

        .floating-element:nth-child(3) {
            left: 30%;
            animation-delay: 2s;
            animation-duration: 7s;
        }

        .floating-element:nth-child(4) {
            left: 70%;
            animation-delay: 3s;
            animation-duration: 9s;
        }

        .floating-element:nth-child(5) {
            left: 80%;
            animation-delay: 4s;
            animation-duration: 6s;
        }

        @keyframes float {
            0% {
                transform: translateY(100vh) rotate(0deg);
                opacity: 0;
            }
            10% {
                opacity: 1;
            }
            90% {
                opacity: 1;
            }
            100% {
                transform: translateY(-100px) rotate(360deg);
                opacity: 0;
            }
        }

        .container {
            text-align: center;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            padding: 60px 40px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
            backdrop-filter: blur(10px);
            max-width: 600px;
            width: 90%;
            position: relative;
            z-index: 10;
            animation: slideUp 1s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .logo {
            max-width: 100%;
            /*width: 120px;*/
            /*height: 120px;*/
            margin: auto;
            /*border-radius: 15px;*/
            /*box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);*/
            /*animation: logoFloat 3s ease-in-out infinite;*/
            /*object-fit: contain;*/
            /*background: white;*/
            /*padding: 10px;*/
        }

        @keyframes logoFloat {
            0%, 100% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(-10px);
            }
        }

        h1 {
            color: #333;
            font-size: 2.5em;
            margin-bottom: 20px;
            font-weight: 700;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
        }

        .subtitle {
            color: #666;
            font-size: 1.2em;
            margin-bottom: 30px;
            line-height: 1.6;
        }

        .progress-container {
            width: 100%;
            height: 8px;
            background: #e0e0e0;
            border-radius: 4px;
            margin: 30px 0;
            overflow: hidden;
        }

        .progress-bar {
            height: 100%;
            background: linear-gradient(90deg, #667eea, #764ba2);
            border-radius: 4px;
            animation: progress 3s ease-out infinite;
        }

        @keyframes progress {
            0% { width: 0%; }
            50% { width: 75%; }
            100% { width: 0%; }
        }

        .contact-info {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }

        .contact-info p {
            color: #888;
            font-size: 0.9em;
            margin: 5px 0;
        }

        .social-links {
            margin-top: 20px;
        }

        .social-links a {
            display: inline-block;
            width: 40px;
            height: 40px;
            margin: 0 10px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 50%;
            color: white;
            text-decoration: none;
            line-height: 40px;
            transition: transform 0.3s ease;
        }

        .social-links a:hover {
            transform: scale(1.1);
        }

        @media (max-width: 768px) {
            .container {
                padding: 40px 20px;
                margin: 20px;
            }

            h1 {
                font-size: 2em;
            }

            .subtitle {
                font-size: 1em;
            }

            .logo {
                /*width: 100px;*/
                /*height: 100px;*/
            }
        }
    </style>
</head>
<body>
<div class="animated-bg"></div>

<div class="floating-elements">
    <div class="floating-element"></div>
    <div class="floating-element"></div>
    <div class="floating-element"></div>
    <div class="floating-element"></div>
    <div class="floating-element"></div>
</div>

<div class="container">
    <img src="https://a-plan.agency/uploadphotos/uploads/2025-03-27-232858-67e5df3a5afb7.png" alt="شعار الموقع" class="logo">

{{--    <h1>تحت الإنشاء</h1>--}}

{{--    <p class="subtitle">--}}
{{--        نحن نعمل بجد لتقديم تجربة رائعة لك<br>--}}
{{--        سيكون موقعنا جاهزاً قريباً جداً--}}
{{--    </p>--}}

{{--    <div class="progress-container">--}}
{{--        <div class="progress-bar"></div>--}}
{{--    </div>--}}

{{--    <div class="contact-info">--}}
{{--        <p><strong>للتواصل معنا:</strong></p>--}}
{{--        <p>📧 info@example.com</p>--}}
{{--        <p>📱 +20 XXX XXX XXXX</p>--}}
{{--    </div>--}}

{{--    <div class="social-links">--}}
{{--        <a href="#" title="فيسبوك">📘</a>--}}
{{--        <a href="#" title="تويتر">🐦</a>--}}
{{--        <a href="#" title="إنستغرام">📷</a>--}}
{{--        <a href="#" title="لينكد إن">💼</a>--}}
{{--    </div>--}}
</div>

<script>
    // إضافة تأثيرات تفاعلية
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.querySelector('.container');

        // تأثير hover على الحاوية
        container.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.02)';
            this.style.transition = 'transform 0.3s ease';
        });

        container.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
        });

        // تأثير النقر على اللوجو
        const logo = document.querySelector('.logo');
        logo.addEventListener('click', function() {
            this.style.animation = 'none';
            setTimeout(() => {
                this.style.animation = 'logoFloat 3s ease-in-out infinite';
            }, 100);
        });
    });
</script>
</body>
</html>
