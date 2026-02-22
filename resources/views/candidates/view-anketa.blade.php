<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        html, body {
            height: 100%;
            width: 100%;
            overflow: hidden;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }
        .container {
            display: flex;
            flex-direction: column;
            height: 100vh;
        }
        .header {
            background: linear-gradient(135deg, #234088 0%, #1a3066 100%);
            color: white;
            padding: 16px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        }
        .header h1 {
            font-size: 1.25rem;
            font-weight: 600;
        }
        .header-actions {
            display: flex;
            gap: 12px;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s;
            border: none;
            cursor: pointer;
        }
        .btn-download {
            background: #10b981;
            color: white;
        }
        .btn-download:hover {
            background: #059669;
        }
        .btn-back {
            background: rgba(255,255,255,0.15);
            color: white;
        }
        .btn-back:hover {
            background: rgba(255,255,255,0.25);
        }
        .pdf-container {
            flex: 1;
            width: 100%;
            background: #f3f4f6;
        }
        .pdf-container iframe {
            width: 100%;
            height: 100%;
            border: none;
        }
        @media (max-width: 640px) {
            .header {
                padding: 12px 16px;
                flex-direction: column;
                gap: 12px;
            }
            .header h1 {
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ $title }}</h1>
            <div class="header-actions">
                <a href="{{ $pdfUrl }}" download class="btn btn-download">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z"/>
                    </svg>
                    Скачать PDF
                </a>
                <a href="{{ route('candidate.report', $candidate) }}" class="btn btn-back">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/>
                    </svg>
                    Назад
                </a>
            </div>
        </div>
        <div class="pdf-container">
            <iframe src="{{ $pdfUrl }}" title="{{ $title }}"></iframe>
        </div>
    </div>
</body>
</html>
