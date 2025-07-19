<?php ob_start(); ?>

<div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Toutes les transactions</h1>
    <a href="/transaction/deposit" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200">
        <i class="fas fa-plus mr-2"></i>Nouvelle transaction
    </a>
</div>

<!-- Filtres -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
    <div class="p-6">
        <form method="GET" action="/dashboard/transactions" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="type" class="block text-sm font-medium text-gray-700 mb-2">Type</label>
                <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200" id="type" name="type">
                    <option value="">Tous les types</option>
                    <option value="Transfert" <?= $filters['type'] === 'Transfert' ? 'selected' : '' ?>>Transfert</option>
                    <option value="Paiement" <?= $filters['type'] === 'Paiement' ? 'selected' : '' ?>>Paiement</option>
                </select>
            </div>
            <div>
                <label for="date_from" class="block text-sm font-medium text-gray-700 mb-2">Du</label>
                <input type="date" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200" id="date_from" name="date_from" value="<?= $filters['date_from'] ?>">
            </div>
            <div>
                <label for="date_to" class="block text-sm font-medium text-gray-700 mb-2">Au</label>
                <input type="date" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200" id="date_to" name="date_to" value="<?= $filters['date_to'] ?>">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">&nbsp;</label>
                <div class="flex gap-2">
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200">
                        <i class="fas fa-search mr-1"></i>Filtrer
                    </button>
                    <a href="/dashboard/transactions" class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 hover:bg-gray-50 font-medium rounded-lg transition-colors duration-200">Réinitialiser</a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Liste des transactions -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200">
    <div class="px-6 py-4 border-b border-gray-200">
        <h5 class="text-lg font-semibold text-gray-900">
            Transactions (<?= $pagination['total'] ?> au total)
        </h5>
    </div>
    <div class="p-6">
        <?php if (empty($transactions)): ?>
            <div class="text-center py-8">
                <i class="fas fa-inbox text-4xl text-gray-400 mb-4"></i>
                <h5 class="text-lg font-medium text-gray-900 mb-2">Aucune transaction trouvée</h5>
                <p class="text-gray-500">Aucune transaction ne correspond à vos critères de recherche.</p>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="text-left py-3 px-4 font-medium text-gray-900">Date</th>
                            <th class="text-left py-3 px-4 font-medium text-gray-900">Type</th>
                            <th class="text-left py-3 px-4 font-medium text-gray-900">Description</th>
                            <th class="text-left py-3 px-4 font-medium text-gray-900">Montant</th>
                            <th class="text-left py-3 px-4 font-medium text-gray-900">Frais</th>
                            <th class="text-left py-3 px-4 font-medium text-gray-900">Statut</th>
                            <th class="text-left py-3 px-4 font-medium text-gray-900">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php foreach ($transactions as $transaction): ?>
                            <tr class="hover:bg-gray-50 transition-colors duration-150">
                                <td class="py-4 px-4">
                                    <div class="text-sm font-medium text-gray-900"><?= $transaction->getCreatedAt()->format('d/m/Y') ?></div>
                                    <div class="text-sm text-gray-500"><?= $transaction->getCreatedAt()->format('H:i') ?></div>
                                </td>
                                <td class="py-4 px-4">
                                    <div class="flex items-center">
                                        <i class="fas <?= $transaction->isTransfer() ? 'fa-exchange-alt' : 'fa-credit-card' ?> mr-3 text-blue-600"></i>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($transaction->getType()) ?></div>
                                            <?php if ($transaction->getSousType()): ?>
                                                <div class="text-sm text-gray-500"><?= htmlspecialchars($transaction->getSousType()) ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-4">
                                    <div class="text-sm text-gray-900"><?= htmlspecialchars($transaction->getDescription() ?: 'N/A') ?></div>
                                </td>
                                <td class="py-4 px-4">
                                    <div class="text-sm font-semibold <?= $transaction->isDeposit() ? 'text-green-600' : 'text-red-600' ?>">
                                        <?= $transaction->isDeposit() ? '+' : '-' ?><?= $transaction->getMontantFormatted() ?>
                                    </div>
                                </td>
                                <td class="py-4 px-4">
                                    <?php if ($transaction->getFrais() > 0): ?>
                                        <span class="text-sm text-yellow-600"><?= $transaction->getFraisFormatted() ?></span>
                                    <?php else: ?>
                                        <span class="text-sm text-gray-500">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-4 px-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $transaction->getStatusBadgeClass() ?>">
                                        <?= htmlspecialchars($transaction->getStatus()) ?>
                                    </span>
                                </td>
                                <td class="py-4 px-4">
                                    <?php if ($transaction->canBeCancelled() && $transaction->isDeposit()): ?>
                                        <form action="/transaction/<?= $transaction->getId() ?>/cancel" method="POST" class="inline">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-red-300 text-red-700 hover:bg-red-50 text-sm font-medium rounded-md transition-colors duration-200" 
                                                    onclick="return confirm('Êtes-vous sûr de vouloir annuler cette transaction ?')">
                                                <i class="fas fa-times mr-1"></i>Annuler
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <span class="text-sm text-gray-500">-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if ($pagination['total_pages'] > 1): ?>
                <nav aria-label="Navigation des transactions" class="flex justify-center mt-8">
                    <div class="flex items-center space-x-1">
                        <?php
                        $currentPage = $pagination['current_page'];
                        $totalPages = $pagination['total_pages'];
                        $queryParams = $_GET;
                        ?>
                        
                        <!-- Première page -->
                        <?php if ($currentPage > 1): ?>
                            <?php $queryParams['page'] = 1; ?>
                            <a class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50 hover:text-gray-700 transition-colors duration-200" href="?<?= http_build_query($queryParams) ?>">
                                <i class="fas fa-angle-double-left"></i>
                            </a>
                        <?php endif; ?>
                        
                        <!-- Page précédente -->
                        <?php if ($currentPage > 1): ?>
                            <?php $queryParams['page'] = $currentPage - 1; ?>
                            <a class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50 hover:text-gray-700 transition-colors duration-200" href="?<?= http_build_query($queryParams) ?>">
                                <i class="fas fa-angle-left"></i>
                            </a>
                        <?php endif; ?>
                        
                        <!-- Pages numérotées -->
                        <?php for ($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++): ?>
                            <?php $queryParams['page'] = $i; ?>
                            <a class="inline-flex items-center px-4 py-2 text-sm font-medium <?= $i === $currentPage ? 'text-white bg-blue-600 border-blue-600' : 'text-gray-500 bg-white border-gray-300 hover:bg-gray-50 hover:text-gray-700' ?> border rounded-md transition-colors duration-200" href="?<?= http_build_query($queryParams) ?>"><?= $i ?></a>
                        <?php endfor; ?>
                        
                        <!-- Page suivante -->
                        <?php if ($currentPage < $totalPages): ?>
                            <?php $queryParams['page'] = $currentPage + 1; ?>
                            <a class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50 hover:text-gray-700 transition-colors duration-200" href="?<?= http_build_query($queryParams) ?>">
                                <i class="fas fa-angle-right"></i>
                            </a>
                        <?php endif; ?>
                        
                        <!-- Dernière page -->
                        <?php if ($currentPage < $totalPages): ?>
                            <?php $queryParams['page'] = $totalPages; ?>
                            <a class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50 hover:text-gray-700 transition-colors duration-200" href="?<?= http_build_query($queryParams) ?>">
                                <i class="fas fa-angle-double-right"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </nav>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<?php 
$content = ob_get_clean();
$title = 'Transactions - MAXITSA';
include __DIR__ . '/../layout.php';
?>
