<?php ob_start(); ?>

<div class="flex justify-center min-h-screen bg-gray-50 py-8">
    <div class="w-full max-w-md lg:max-w-lg">
        <div class="bg-white shadow-xl rounded-lg border border-gray-200 overflow-hidden">
            <div class="bg-gradient-to-r from-green-600 to-green-700 px-6 py-4">
                <h4 class="text-xl font-semibold text-white flex items-center">
                    <i class="fas fa-credit-card mr-2"></i>
                    Effectuer un paiement
                </h4>
            </div>
            <div class="p-6">
                <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-info-circle text-blue-400"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-blue-700">
                                <span class="font-medium">Information :</span> Les paiements sont immédiatement débités de votre compte 
                                et ne peuvent pas être annulés.
                            </p>
                        </div>
                    </div>
                </div>

                <form action="/transaction/payment" method="POST" class="space-y-6">
                    <?= csrf_field() ?>
                    
                    <div>
                        <label for="account_id" class="block text-sm font-medium text-gray-700 mb-2">Compte à débiter *</label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 <?= errors('account_id') ? 'border-red-500' : '' ?>" 
                                id="account_id" 
                                name="account_id">
                            <option value="">Sélectionnez un compte</option>
                            <?php foreach ($accounts as $account): ?>
                                <option value="<?= $account->getId() ?>" <?= old('account_id') == $account->getId() ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($account->getTelephone()) ?> 
                                    (<?= $account->getAccountType() ?>) - 
                                    <?= $account->getSoldeFormatted() ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (errors('account_id')): ?>
                            <p class="mt-1 text-sm text-red-600">
                                <?= errors('account_id')[0] ?>
                            </p>
                        <?php endif; ?>
                    </div>

                    <div>
                        <label for="montant" class="block text-sm font-medium text-gray-700 mb-2">Montant *</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-coins text-gray-400"></i>
                            </div>
                            <input type="number" 
                                   class="w-full pl-10 pr-16 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 <?= errors('montant') ? 'border-red-500' : '' ?>" 
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
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description du paiement *</label>
                        <textarea class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 resize-none <?= errors('description') ? 'border-red-500' : '' ?>" 
                                  id="description" 
                                  name="description" 
                                  rows="3" 
                                  maxlength="255"
                                  placeholder="Ex: Facture électricité, Achat en ligne, Frais de scolarité..."><?= old('description') ?></textarea>
                        <?php if (errors('description')): ?>
                            <p class="mt-1 text-sm text-red-600">
                                <?= errors('description')[0] ?>
                            </p>
                        <?php endif; ?>
                        <p class="mt-1 text-sm text-gray-500">
                            Décrivez le motif de ce paiement (minimum 5 caractères, maximum 255).
                        </p>
                    </div>

                    <div class="bg-amber-50 border-l-4 border-amber-400 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-triangle text-amber-400"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-amber-700">
                                    <span class="font-medium">Attention :</span> Une fois confirmé, ce paiement sera immédiatement débité 
                                    de votre compte et ne pourra pas être annulé.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="flex space-x-3 pt-4">
                        <button type="submit" class="flex-1 bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-md transition duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2" 
                                onclick="return confirm('Êtes-vous sûr de vouloir effectuer ce paiement ?')">
                            <i class="fas fa-check mr-2"></i>
                            Confirmer le paiement
                        </button>
                        <a href="/dashboard" class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 font-medium rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition duration-200 ease-in-out">
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
$title = 'Effectuer un paiement - MAXITSA';
include __DIR__ . '/../layout.php';
?>
