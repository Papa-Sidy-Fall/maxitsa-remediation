<?php ob_start(); ?>

<div class="flex justify-center min-h-screen bg-gray-50 py-8">
    <div class="w-full max-w-md lg:max-w-lg">
        <div class="bg-white shadow-xl rounded-lg border border-gray-200 overflow-hidden">
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
                <h4 class="text-xl font-semibold text-white flex items-center">
                    <i class="fas fa-arrow-up mr-2"></i>
                    Effectuer un dépôt
                </h4>
            </div>
            <div class="p-6">
                <div class="bg-amber-50 border-l-4 border-amber-400 p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-triangle text-amber-400"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-amber-700">
                                <span class="font-medium">Important :</span> Les transferts entre comptes principaux différents sont soumis 
                                à des frais de <?= env('TRANSFER_FEE_RATE', 0.08) * 100 ?>% du montant (plafonné à <?= formatCurrency(env('TRANSFER_FEE_MAX', 5000)) ?>).
                            </p>
                        </div>
                    </div>
                </div>

                <form action="/transaction/deposit" method="POST" class="space-y-6">
                    <?= csrf_field() ?>
                    
                    <div>
                        <label for="from_account" class="block text-sm font-medium text-gray-700 mb-2">Compte source *</label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 <?= errors('from_account') ? 'border-red-500' : '' ?>" 
                                id="from_account" 
                                name="from_account">
                            <option value="">Sélectionnez un compte</option>
                            <?php foreach ($accounts as $account): ?>
                                <option value="<?= $account->getId() ?>" <?= old('from_account') == $account->getId() ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($account->getTelephone()) ?> 
                                    (<?= $account->getAccountType() ?>) - 
                                    <?= $account->getSoldeFormatted() ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (errors('from_account')): ?>
                            <p class="mt-1 text-sm text-red-600">
                                <?= errors('from_account')[0] ?>
                            </p>
                        <?php endif; ?>
                    </div>

                    <div>
                        <label for="to_telephone" class="block text-sm font-medium text-gray-700 mb-2">Numéro de téléphone de destination *</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-mobile-alt text-gray-400"></i>
                            </div>
                            <input type="tel" 
                                   class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 <?= errors('to_telephone') ? 'border-red-500' : '' ?>" 
                                   id="to_telephone" 
                                   name="to_telephone" 
                                   value="<?= old('to_telephone') ?>"
                                   placeholder="77 123 45 67">
                        </div>
                        <?php if (errors('to_telephone')): ?>
                            <p class="mt-1 text-sm text-red-600">
                                <?= errors('to_telephone')[0] ?>
                            </p>
                        <?php endif; ?>
                        <p class="mt-1 text-sm text-gray-500">
                            Le numéro du compte qui recevra le dépôt.
                        </p>
                    </div>

                    <div>
                        <label for="montant" class="block text-sm font-medium text-gray-700 mb-2">Montant *</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-coins text-gray-400"></i>
                            </div>
                            <input type="number" 
                                   class="w-full pl-10 pr-16 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 <?= errors('montant') ? 'border-red-500' : '' ?>" 
                                   id="montant" 
                                   name="montant" 
                                   value="<?= old('montant') ?>"
                                   placeholder="0"
                                   min="100"
                                   step="1">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 text-sm">FCFA</span>
                            </div>
                        </div>
                        <?php if (errors('montant')): ?>
                            <p class="mt-1 text-sm text-red-600">
                                <?= errors('montant')[0] ?>
                            </p>
                        <?php endif; ?>
                        <p class="mt-1 text-sm text-gray-500">
                            Montant minimum : 100 FCFA
                        </p>
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description (optionnelle)</label>
                        <textarea class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none <?= errors('description') ? 'border-red-500' : '' ?>" 
                                  id="description" 
                                  name="description" 
                                  rows="3" 
                                  maxlength="255"
                                  placeholder="Description de la transaction..."><?= old('description') ?></textarea>
                        <?php if (errors('description')): ?>
                            <p class="mt-1 text-sm text-red-600">
                                <?= errors('description')[0] ?>
                            </p>
                        <?php endif; ?>
                        <p class="mt-1 text-sm text-gray-500">
                            Maximum 255 caractères.
                        </p>
                    </div>

                    <div class="flex space-x-3 pt-4">
                        <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            <i class="fas fa-paper-plane mr-2"></i>
                            Effectuer le dépôt
                        </button>
                        <a href="/dashboard" class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 font-medium rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-200 ease-in-out">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Retour
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Calculer les frais en temps réel
document.getElementById('montant').addEventListener('input', function() {
    const montant = parseFloat(this.value) || 0;
    const feeRate = <?= env('TRANSFER_FEE_RATE', 0.08) ?>;
    const feeMax = <?= env('TRANSFER_FEE_MAX', 5000) ?>;
    
    if (montant > 0) {
        const fees = Math.min(montant * feeRate, feeMax);
        const total = montant + fees;
        
        // Afficher une estimation des frais (à implémenter dans l'interface si souhaité)
        console.log(`Montant: ${montant}, Frais: ${fees}, Total: ${total}`);
    }
});
</script>

<?php 
$content = ob_get_clean();
$title = 'Effectuer un dépôt - MAXITSA';
include __DIR__ . '/../layout.php';
?>
