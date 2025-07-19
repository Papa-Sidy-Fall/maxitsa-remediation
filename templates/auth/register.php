<?php ob_start(); ?>

<div class="flex justify-center py-12">
    <div class="w-full max-w-2xl">
        <div class="bg-white rounded-lg shadow-lg">
            <div class="p-8">
                <div class="text-center mb-6">
                    <h2 class="text-2xl font-bold mb-3">
                        <i class="fas fa-money-bill-wave text-blue-600 mr-2"></i>
                        MAXITSA
                    </h2>
                    <p class="text-gray-600">Créer votre compte principal</p>
                </div>

                <form action="/register" method="POST" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    
                    <!-- Affichage des erreurs -->
                    <?php if ($errors = errors()): ?>
                        <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-600 rounded-md">
                            <ul class="list-disc list-inside">
                                <?php foreach ($errors as $field => $fieldErrors): ?>
                                    <?php if (is_array($fieldErrors)): ?>
                                        <?php foreach ($fieldErrors as $error): ?>
                                            <li><?= htmlspecialchars($error) ?></li>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <li><?= htmlspecialchars($fieldErrors) ?></li>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <div class="mb-4">
                                <label for="prenom" class="block text-sm font-medium text-gray-700 mb-1">Prénom *</label>
                                <input type="text" 
                                class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent <?= errors('prenom') ? 'border-red-500' : 'border-gray-300' ?>" 
                                id="prenom" 
                                name="prenom" 
                                value="<?= old('prenom') ?>">
                                <?php if (errors('prenom')): ?>
                                    <div class="text-red-600 text-sm mt-1">
                                        <?= errors('prenom')[0] ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div>
                            <div class="mb-4">
                                <label for="nom" class="block text-sm font-medium text-gray-700 mb-1">Nom *</label>
                                <input type="text" 
                                class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent <?= errors('nom') ? 'border-red-500' : 'border-gray-300' ?>" 
                                id="nom" 
                                name="nom" 
                                value="<?= old('nom') ?>">
                                <?php if (errors('nom')): ?>
                                    <div class="text-red-600 text-sm mt-1">
                                        <?= errors('nom')[0] ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="telephone" class="block text-sm font-medium text-gray-700 mb-1">Numéro de téléphone *</label>
                        <input type="tel" 
                               class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent <?= errors('telephone') ? 'border-red-500' : 'border-gray-300' ?>" 
                               id="telephone" 
                               name="telephone" 
                               value="<?= old('telephone') ?>"
                               placeholder="77 123 45 67">
                        <?php if (errors('telephone')): ?>
                            <div class="text-red-600 text-sm mt-1">
                                <?= errors('telephone')[0] ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-4">
                        <label for="carte_identite" class="block text-sm font-medium text-gray-700 mb-1">Numéro de carte d'identité *</label>
                        <input type="text" 
                               class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent <?= errors('carte_identite') ? 'border-red-500' : 'border-gray-300' ?>" 
                               id="carte_identite" 
                               name="carte_identite" 
                               value="<?= old('carte_identite') ?>">
                        <?php if (errors('carte_identite')): ?>
                            <div class="text-red-600 text-sm mt-1">
                                <?= errors('carte_identite')[0] ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-4">
                        <label for="adresse" class="block text-sm font-medium text-gray-700 mb-1">Adresse *</label>
                        <textarea class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent <?= errors('adresse') ? 'border-red-500' : 'border-gray-300' ?>" 
                                  id="adresse" 
                                  name="adresse" 
                                  rows="3"><?= old('adresse') ?></textarea>
                        <?php if (errors('adresse')): ?>
                            <div class="text-red-600 text-sm mt-1">
                                <?= errors('adresse')[0] ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <div class="mb-4">
                                <label for="photo_recto" class="block text-sm font-medium text-gray-700 mb-1">Photo recto de la carte d'identité *</label>
                                <input type="file" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                       id="photo_recto" 
                                       name="photo_recto" 
                                       accept="image/*">
                            </div>
                        </div>
                        
                        <div>
                            <div class="mb-4">
                                <label for="photo_verso" class="block text-sm font-medium text-gray-700 mb-1">Photo verso de la carte d'identité *</label>
                                <input type="file" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                       id="photo_verso" 
                                       name="photo_verso" 
                                       accept="image/*">
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <div class="mb-4">
                                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Mot de passe *</label>
                                <input type="password" 
                                       class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent <?= errors('password') ? 'border-red-500' : 'border-gray-300' ?>" 
                                       id="password" 
                                       name="password">
                                <?php if (errors('password')): ?>
                                    <div class="text-red-600 text-sm mt-1">
                                        <?= errors('password')[0] ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div>
                            <div class="mb-6">
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirmer le mot de passe *</label>
                                <input type="password" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                       id="password_confirmation" 
                                       name="password_confirmation">
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-md transition duration-200 mb-4">
                        <i class="fas fa-user-plus mr-2"></i>
                        Créer mon compte
                    </button>
                </form>

                <div class="text-center">
                    <p class="mb-0">
                        Déjà un compte ? 
                        <a href="/login" class="text-blue-600 hover:text-blue-800 no-underline">
                            Se connecter
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
$content = ob_get_clean();
$title = 'Inscription - MAXITSA';
include __DIR__ . '/../layout.php';
?>
