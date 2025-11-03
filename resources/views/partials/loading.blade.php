<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loading...</title>
    @vite(['resources/css/app.css'])
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 0;
            overflow: hidden;
        }
        
        .loader-spinner {
            border: 4px solid #f3f4f6;
            border-top: 4px solid #3b82f6;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        .pulse {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: .5; }
        }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100">
    <div class="min-h-screen flex items-center justify-center fade-in">
        <div class="text-center px-4">
            <!-- Loader Spinner -->
            <div class="flex justify-center mb-6">
                <div class="loader-spinner"></div>
            </div>
            
            <!-- Icon -->
            <div class="mb-4">
                <svg class="w-16 h-16 mx-auto text-blue-600 pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
            
            <!-- Text -->
            <div class="bg-white rounded-lg shadow-lg p-6 max-w-md mx-auto">
                <h2 class="text-2xl font-bold text-gray-800 mb-2">Mohon Tunggu Sebentar</h2>
                <p class="text-gray-600" id="loadingText">Anda akan dialihkan ke halaman tujuan</p>
                
                <!-- Progress Bar -->
                <div class="mt-4">
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" 
                             role="progressbar" 
                             style="width: 100%">
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Tips -->
            <p class="text-sm text-gray-500 mt-4">
                <span class="pulse">●</span> Memproses permintaan Anda...
            </p>
        </div>
    </div>

    <script>
        // Ambil parameter dari URL
        const urlParams = new URLSearchParams(window.location.search);
        const destination = urlParams.get('to');
        const destinationName = urlParams.get('name');
        
        // Update text jika ada parameter
        if (destinationName) {
            document.getElementById('loadingText').textContent = 
                `Anda akan dialihkan ke ${destinationName}`;
        }
        
        // Redirect setelah 1.5 detik
        setTimeout(function() {
            if (destination) {
                window.location.href = destination;
            } else {
                window.location.href = '/dashboard'; // fallback
            }
        }, 1500);
    </script>
</body>
</html>