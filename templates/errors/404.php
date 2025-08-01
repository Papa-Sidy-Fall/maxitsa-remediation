<?php ob_start(); ?>

<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-gray-50 to-gray-100 px-4">
    <div class="max-w-md w-full bg-white rounded-xl shadow-2xl p-8 text-center">
        <!-- Icon -->
        <div class="mb-6">
            <div class="w-24 h-24 mx-auto bg-yellow-100 rounded-full flex items-center justify-center">
                <i class="fas fa-exclamation-triangle text-4xl text-yellow-500"></i>
            </div>
        </div>
        
        <!-- Error Code -->
        <h1 class="text-6xl font-bold text-gray-800 mb-2">404</h1>
        
        <!-- Error Title -->
        <h2 class="text-2xl font-semibold text-gray-700 mb-4">Page non trouvée</h2>
        
        <!-- Error Description -->
        <p class="text-gray-600 mb-8 leading-relaxed">
            Désolé, la page que vous recherchez n'existe pas ou a été déplacée.
        </p>
        
        <!-- Action Buttons -->
        <div class="space-y-3">
            <a href="/dashboard" 
               class="w-full inline-flex items-center justify-center px-6 py-3 bg-orange-600 text-white font-medium rounded-lg hover:bg-orange-700 transition-colors duration-200 shadow-lg hover:shadow-xl">
                <i class="fas fa-home mr-2"></i>
                Retour à l'accueil
            </a>
            <button onclick="history.back()" 
                    class="w-full inline-flex items-center justify-center px-6 py-3 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200 transition-colors duration-200">
                <i class="fas fa-arrow-left mr-2"></i>
                Page précédente
            </button>
        </div>
    </div>
</div>

<?php 
$content = ob_get_clean();
$title = 'Page non trouvée - Max It SA';
include __DIR__ . '/../layout.php';
?>
