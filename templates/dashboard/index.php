<?php ob_start(); ?>

<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Tableau de bord</h1>
    <div class="text-gray-600">
        Bienvenue, <?= htmlspecialchars($user->getFullName()) ?>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div class="bg-orange-600 rounded-lg shadow-md">
        <div class="p-6">
            <div class="flex justify-between items-center">
                <div>
                    <h6 class="text-orange-100 text-sm font-medium">Solde du compte principal</h6>
                    <h3 class="text-white text-2xl font-bold">
                        <?= $account ? $account->getSoldeFormatted() : '0 FCFA' ?>
                    </h3>
                </div>
                <div>
                    <i class="fas fa-wallet text-3xl text-white opacity-75"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="bg-green-600 rounded-lg shadow-md">
        <div class="p-6">
            <div class="flex justify-between items-center">
                <div>
                    <h6 class="text-green-100 text-sm font-medium">Transactions du mois</h6>
                    <h3 class="text-white text-2xl font-bold"><?= count($transactions) ?></h3>
                </div>
                <div>
                    <i class="fas fa-exchange-alt text-3xl text-white opacity-75"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="bg-orange-500 rounded-lg shadow-md">
        <div class="p-6">
            <div class="flex justify-between items-center">
                <div>
                    <h6 class="text-orange-100 text-sm font-medium">Numéro principal</h6>
                    <h5 class="text-white text-xl font-bold">
                        <?= $account ? htmlspecialchars($account->getTelephone()) : 'N/A' ?>
                    </h5>
                </div>
                <div>
                    <i class="fas fa-phone text-3xl text-white opacity-75"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2">
        <div class="bg-white rounded-lg shadow-md">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h5 class="text-lg font-semibold text-gray-900">Dernières transactions</h5>
                <a href="/dashboard/transactions" class="inline-flex items-center px-3 py-1.5 border border-orange-600 text-orange-600 text-sm font-medium rounded-md hover:bg-orange-50 transition-colors">
                    Voir tout
                </a>
            </div>
            <div class="p-6">
                <?php if (empty($transactions)): ?>
                    <div class="text-center py-8">
                        <i class="fas fa-inbox text-5xl text-gray-400 mb-4"></i>
                        <p class="text-gray-600 mb-4">Aucune transaction récente</p>
                        <a href="/transaction/deposit" class="inline-flex items-center px-4 py-2 bg-orange-600 text-white text-sm font-medium rounded-md hover:bg-orange-700 transition-colors">
                            Faire une transaction
                        </a>
                    </div>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Montant</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($transactions as $transaction): ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <i class="fas <?= $transaction->isTransfer() ? 'fa-exchange-alt' : 'fa-credit-card' ?> mr-3 text-orange-600"></i>
                                                <div>
                                                    <div class="font-medium text-gray-900"><?= htmlspecialchars($transaction->getType()) ?></div>
                                                    <?php if ($transaction->getSousType()): ?>
                                                        <div class="text-sm text-gray-500"><?= htmlspecialchars($transaction->getSousType()) ?></div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="font-medium text-gray-900"><?= $transaction->getMontantFormatted() ?></div>
                                            <?php if ($transaction->getFrais() > 0): ?>
                                                <div class="text-sm text-gray-500">Frais: <?= $transaction->getFraisFormatted() ?></div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full <?= $transaction->getStatusBadgeClass() ?>">
                                                <?= htmlspecialchars($transaction->getStatus()) ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900"><?= $transaction->getCreatedAt()->format('d/m/Y') ?></div>
                                            <div class="text-sm text-gray-500"><?= $transaction->getCreatedAt()->format('H:i') ?></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <?php if ($transaction->canBeCancelled() && $transaction->isDeposit()): ?>
                                                <form action="/transaction/<?= $transaction->getId() ?>/cancel" method="POST" class="inline">
                                                    <?= csrf_field() ?>
                                                    <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-red-600 text-red-600 text-sm font-medium rounded-md hover:bg-red-50 transition-colors" 
                                                            onclick="return confirm('Êtes-vous sûr de vouloir annuler cette transaction ?')">
                                                        Annuler
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="lg:col-span-1">
        <div class="bg-white rounded-lg shadow-md">
            <div class="px-6 py-4 border-b border-gray-200">
                <h5 class="text-lg font-semibold text-gray-900">Actions rapides</h5>
            </div>
            <div class="p-6">
                <div class="space-y-3">
                    <a href="/transaction/deposit" class="w-full inline-flex items-center justify-center px-4 py-2 bg-orange-600 text-white text-sm font-medium rounded-md hover:bg-orange-700 transition-colors">
                        <i class="fas fa-arrow-up mr-2"></i>
                        Faire un dépôt
                    </a>
                    <a href="/transaction/payment" class="w-full inline-flex items-center justify-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700 transition-colors">
                        <i class="fas fa-credit-card mr-2"></i>
                        Effectuer un paiement
                    </a>
                    <a href="/account/create" class="w-full inline-flex items-center justify-center px-4 py-2 bg-orange-500 text-white text-sm font-medium rounded-md hover:bg-orange-600 transition-colors">
                        <i class="fas fa-plus mr-2"></i>
                        Créer un compte secondaire
                    </a>
                    <a href="/dashboard/accounts" class="w-full inline-flex items-center justify-center px-4 py-2 border border-orange-600 text-orange-600 text-sm font-medium rounded-md hover:bg-orange-50 transition-colors">
                        <i class="fas fa-wallet mr-2"></i>
                        Gérer mes comptes
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
$content = ob_get_clean();
$title = 'Tableau de bord - Max It SA';
include __DIR__ . '/../layout.php';
?>
