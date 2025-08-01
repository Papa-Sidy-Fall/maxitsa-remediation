<?php ob_start(); ?>

<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div class="bg-white rounded-xl shadow-lg p-8">
            <div class="text-center mb-8">
                <div class="mb-4">
                    <div class="bg-orange-500 text-white px-6 py-4 rounded-3xl shadow-lg inline-block">
                        <div class="text-2xl font-bold">Max It</div>
                        <div class="text-xl font-bold">SA</div>
                    </div>
                </div>
                <p class="text-gray-600">Connectez-vous à votre compte</p>
            </div>

            <form action="/login" method="POST" class="space-y-6">
                <?= csrf_field() ?>
                
                <div>
                    <label for="telephone" class="block text-sm font-medium text-gray-700 mb-2">
                        Numéro de téléphone
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-phone text-gray-400"></i>
                        </div>
                        <input type="tel" 
                               class="block w-full pl-10 pr-3 py-2 border <?= errors('telephone') ? 'border-red-300 text-red-900 placeholder-red-300' : 'border-gray-300' ?> rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500" 
                               id="telephone" 
                               name="telephone" 
                               value="<?= old('telephone') ?>"
                               placeholder="77 123 45 67">
                    </div>
                    <?php if (errors('telephone')): ?>
                        <p class="mt-1 text-sm text-red-600"><?= errors('telephone')[0] ?></p>
                    <?php endif; ?>
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        Mot de passe
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400"></i>
                        </div>
                        <input type="password" 
                               class="block w-full pl-10 pr-3 py-2 border <?= errors('password') ? 'border-red-300 text-red-900 placeholder-red-300' : 'border-gray-300' ?> rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500" 
                               id="password" 
                               name="password">
                    </div>
                    <?php if (errors('password')): ?>
                        <p class="mt-1 text-sm text-red-600"><?= errors('password')[0] ?></p>
                    <?php endif; ?>
                </div>

                <button type="submit" class="w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-orange-500 hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-colors">
                    <i class="fas fa-sign-in-alt mr-2"></i>
                    Se connecter
                </button>
            </form>

            <div class="text-center mt-6">
                <p class="text-gray-600">
                    Pas encore de compte ? 
                    <a href="/register" class="font-medium text-orange-500 hover:text-orange-600 transition-colors">
                        Créer un compte
                    </a>
                </p>
            </div>
        </div>
    </div>
</div>

<?php 
$content = ob_get_clean();
$title = 'Connexion - Max It SA';
include __DIR__ . '/../layout.php';
?>
