<?php ob_start(); ?>

<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-semibold text-gray-900 mb-0">Mes comptes</h1>
    <a href="/account/create" class="bg-orange-500 hover:bg-orange-600 text-white font-medium py-2 px-4 rounded-lg inline-flex items-center transition-colors">
        <i class="fas fa-plus mr-2"></i>Nouveau compte
    </a>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php foreach ($accounts as $account): ?>
        <div class="bg-white rounded-lg shadow-md h-full <?= $account->getIsPrincipal() ? 'border-2 border-orange-500' : 'border border-gray-200' ?>">
            <div class="p-6">
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <h5 class="text-lg font-semibold text-gray-900 mb-2">
                            <?= htmlspecialchars($account->getTelephone()) ?>
                        </h5>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $account->getIsPrincipal() ? 'bg-orange-100 text-orange-800' : 'bg-gray-100 text-gray-800' ?>">
                            <?= $account->getAccountType() ?>
                        </span>
                    </div>
                    <i class="fas fa-wallet text-2xl text-gray-400"></i>
                </div>
                
                <div class="mb-6">
                    <h4 class="text-2xl font-bold text-green-600 mb-1"><?= $account->getSoldeFormatted() ?></h4>
                    <small class="text-sm text-gray-500">Solde disponible</small>
                </div>
                
                <?php if (!$account->getIsPrincipal()): ?>
                    <form action="/account/<?= $account->getId() ?>/make-principal" method="POST" class="inline">
                        <?= csrf_field() ?>
                        <button type="submit" class="w-full bg-white border border-orange-500 text-orange-500 hover:bg-orange-50 font-medium py-2 px-4 rounded-lg inline-flex items-center justify-center transition-colors"
                                onclick="return confirm('Faire de ce compte votre compte principal ?')">
                            <i class="fas fa-star mr-2"></i>
                            Définir comme principal
                        </button>
                    </form>
                <?php endif; ?>
            </div>
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 rounded-b-lg">
                <small class="text-sm text-gray-500">
                    <i class="fas fa-calendar mr-2"></i>
                    Créé le <?= $account->getCreatedAt()->format('d/m/Y') ?>
                </small>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<?php if (empty($accounts)): ?>
    <div class="text-center py-12">
        <i class="fas fa-wallet text-6xl text-gray-400 mb-6"></i>
        <h4 class="text-xl font-semibold text-gray-900 mb-2">Aucun compte trouvé</h4>
        <p class="text-gray-500 mb-6">Créez votre premier compte pour commencer</p>
        <a href="/account/create" class="bg-orange-500 hover:bg-orange-600 text-white font-medium py-2 px-4 rounded-lg inline-flex items-center transition-colors">
            <i class="fas fa-plus mr-2"></i>Créer un compte
        </a>
    </div>
<?php endif; ?>

<?php 
$content = ob_get_clean();
$title = 'Mes comptes - Max It SA';
include __DIR__ . '/../layout.php';
?>
