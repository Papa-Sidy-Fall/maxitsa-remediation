<?php ob_start(); ?>

<div class="flex justify-center">
    <div class="w-full max-w-md lg:max-w-lg">
        <div class="bg-white rounded-lg shadow-md border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 rounded-t-lg">
                <h4 class="text-lg font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-plus-circle mr-2 text-blue-600"></i>
                    Créer un compte secondaire
                </h4>
            </div>
            <div class="p-6">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <div class="flex items-start">
                        <i class="fas fa-info-circle mr-2 text-blue-600 mt-1"></i>
                        <div>
                            <span class="font-semibold text-blue-800">Information :</span>
                            <span class="text-blue-700"> Pour créer un compte secondaire, seul le numéro de téléphone est requis. 
                            Si vous ajoutez un solde initial, il sera transféré depuis votre compte principal.</span>
                        </div>
                    </div>
                </div>

                <form action="/account/create" method="POST">
                    <?= csrf_field() ?>
                    
                    <div class="mb-4">
                        <label for="telephone" class="block text-sm font-medium text-gray-700 mb-2">Numéro de téléphone *</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-phone text-gray-400"></i>
                            </div>
                            <input type="tel" 
                                   class="w-full pl-10 pr-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors <?= errors('telephone') ? 'border-red-500 focus:ring-red-500 focus:border-red-500' : 'border-gray-300' ?>" 
                                   id="telephone" 
                                   name="telephone" 
                                   value="<?= old('telephone') ?>"
                                   placeholder="77 123 45 67">
                            <?php if (errors('telephone')): ?>
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <i class="fas fa-exclamation-triangle text-red-500"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        <?php if (errors('telephone')): ?>
                            <p class="mt-1 text-sm text-red-600">
                                <?= errors('telephone')[0] ?>
                            </p>
                        <?php endif; ?>
                        <p class="mt-1 text-sm text-gray-500">
                            Ce numéro ne doit pas déjà être utilisé par un autre compte.
                        </p>
                    </div>

                    <div class="mb-6">
                        <label for="solde" class="block text-sm font-medium text-gray-700 mb-2">Solde initial (optionnel)</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-coins text-gray-400"></i>
                            </div>
                            <input type="number" 
                                   class="w-full pl-10 pr-16 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors <?= errors('solde') ? 'border-red-500 focus:ring-red-500 focus:border-red-500' : 'border-gray-300' ?>" 
                                   id="solde" 
                                   name="solde" 
                                   value="<?= old('solde') ?>"
                                   placeholder="0"
                                   min="0"
                                   step="1">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 text-sm">FCFA</span>
                            </div>
                            <?php if (errors('solde')): ?>
                                <div class="absolute inset-y-0 right-16 pr-3 flex items-center pointer-events-none">
                                    <i class="fas fa-exclamation-triangle text-red-500"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        <?php if (errors('solde')): ?>
                            <p class="mt-1 text-sm text-red-600">
                                <?= errors('solde')[0] ?>
                            </p>
                        <?php endif; ?>
                        <p class="mt-1 text-sm text-gray-500">
                            Si vous spécifiez un montant, il sera transféré depuis votre compte principal.
                        </p>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-3">
                        <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200 flex items-center justify-center">
                            <i class="fas fa-save mr-2"></i>
                            Créer le compte
                        </button>
                        <a href="/dashboard/accounts" class="flex-1 border border-gray-300 hover:bg-gray-50 text-gray-700 font-medium py-2 px-4 rounded-lg transition-colors duration-200 flex items-center justify-center">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Retour
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php 
$content = ob_get_clean();
$title = 'Créer un compte - MAXITSA';
include __DIR__ . '/../layout.php';
?>
