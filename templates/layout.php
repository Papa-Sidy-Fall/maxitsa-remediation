<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'MAXITSA' ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f0f9ff',
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca',
                            800: '#3730a3',
                            900: '#312e81',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50">
    <?php if (Session::isLoggedIn()): ?>
        <div class="flex min-h-screen bg-gray-50">
            <!-- Sidebar -->
            <div class="w-64 bg-gradient-to-br from-indigo-600 to-purple-700 shadow-lg">
                <div class="flex flex-col h-full p-4">
                    <a href="/dashboard" class="text-white text-xl font-bold mb-8 flex items-center">
                        <i class="fas fa-money-bill-wave mr-2"></i>MAXITSA
                    </a>
                    
                    <nav class="flex-1 space-y-2">
                        <a href="/dashboard" class="flex items-center px-4 py-3 text-white/80 hover:text-white hover:bg-white/10 rounded-lg transition-colors">
                            <i class="fas fa-tachometer-alt mr-3"></i>Tableau de bord
                        </a>
                        <a href="/dashboard/accounts" class="flex items-center px-4 py-3 text-white/80 hover:text-white hover:bg-white/10 rounded-lg transition-colors">
                            <i class="fas fa-wallet mr-3"></i>Mes comptes
                        </a>
                        <a href="/dashboard/transactions" class="flex items-center px-4 py-3 text-white/80 hover:text-white hover:bg-white/10 rounded-lg transition-colors">
                            <i class="fas fa-exchange-alt mr-3"></i>Transactions
                        </a>
                        <a href="/transaction/deposit" class="flex items-center px-4 py-3 text-white/80 hover:text-white hover:bg-white/10 rounded-lg transition-colors">
                            <i class="fas fa-arrow-up mr-3"></i>Faire un dépôt
                        </a>
                        <a href="/transaction/payment" class="flex items-center px-4 py-3 text-white/80 hover:text-white hover:bg-white/10 rounded-lg transition-colors">
                            <i class="fas fa-credit-card mr-3"></i>Paiement
                        </a>
                        <a href="/account/create" class="flex items-center px-4 py-3 text-white/80 hover:text-white hover:bg-white/10 rounded-lg transition-colors">
                            <i class="fas fa-plus mr-3"></i>Nouveau compte
                        </a>
                    </nav>
                    
                    <form action="/logout" method="POST" class="mt-auto">
                        <?= csrf_field() ?>
                        <button type="submit" class="w-full flex items-center justify-center px-4 py-3 border border-white/20 text-white hover:bg-white/10 rounded-lg transition-colors">
                            <i class="fas fa-sign-out-alt mr-2"></i>Déconnexion
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- Main content -->
            <div class="flex-1 p-6">
                <!-- Messages flash -->
                <?php if (Session::hasFlash('success')): ?>
                    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-4 flex items-center">
                        <i class="fas fa-check-circle mr-2"></i>
                        <?= Session::getFlash('success') ?>
                        <button onclick="this.parentElement.remove()" class="ml-auto text-green-500 hover:text-green-700">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                <?php endif; ?>
                
                <?php if (Session::hasFlash('error')): ?>
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4 flex items-center">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        <?= Session::getFlash('error') ?>
                        <button onclick="this.parentElement.remove()" class="ml-auto text-red-500 hover:text-red-700">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                <?php endif; ?>
                
                <?= $content ?>
            </div>
        </div>
    <?php else: ?>
        <div class="container mx-auto px-4">
            <!-- Messages flash pour les pages non connectées -->
            <?php if (Session::hasFlash('success')): ?>
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mt-4 mb-4 flex items-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    <?= Session::getFlash('success') ?>
                    <button onclick="this.parentElement.remove()" class="ml-auto text-green-500 hover:text-green-700">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            <?php endif; ?>
            
            <?php if (Session::hasFlash('error')): ?>
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mt-4 mb-4 flex items-center">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <?= Session::getFlash('error') ?>
                    <button onclick="this.parentElement.remove()" class="ml-auto text-red-500 hover:text-red-700">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            <?php endif; ?>
            
            <?= $content ?>
        </div>
    <?php endif; ?>
</body>
</html>
