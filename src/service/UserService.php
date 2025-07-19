<?php

namespace App\Maxitsa\Service;

require_once __DIR__ . '/../repository/UserRepository.php';
require_once __DIR__ . '/../repository/AccountRepository.php';
require_once __DIR__ . '/../entity/User.php';
require_once __DIR__ . '/../entity/Account.php';

use App\Maxitsa\Repository\UserRepository;
use App\Maxitsa\Repository\AccountRepository;
use App\Maxitsa\Entity\User;
use App\Maxitsa\Entity\Account;
use Exception;

class UserService
{
    private UserRepository $userRepository;
    private AccountRepository $accountRepository;

    public function __construct(UserRepository $userRepository, AccountRepository $accountRepository)
    {
        $this->userRepository = $userRepository;
        $this->accountRepository = $accountRepository;
    }

    public function createUser(array $userData, array $photoFiles): User
    {
        // Validation
        if ($this->userRepository->telephoneExists($userData['telephone'])) {
            throw new Exception("Ce numéro de téléphone est déjà utilisé.");
        }

        if ($this->userRepository->carteIdentiteExists($userData['carte_identite'])) {
            throw new Exception("Cette carte d'identité est déjà utilisée.");
        }

        // Upload des photos
        $fileUpload = \App::getDependency('fileUpload');
        
        $photoRectoResult = $fileUpload->upload($photoFiles['photo_recto'], 'users');
        if (!$photoRectoResult['success']) {
            throw new Exception("Erreur upload photo recto: " . $photoRectoResult['error']);
        }

        $photoVersoResult = $fileUpload->upload($photoFiles['photo_verso'], 'users');
        if (!$photoVersoResult['success']) {
            throw new Exception("Erreur upload photo verso: " . $photoVersoResult['error']);
        }

        try {
            // Créer l'utilisateur
            $user = new User();
            $user->setNom($userData['nom']);
            $user->setPrenom($userData['prenom']);
            $user->setTelephone($userData['telephone']);
            $user->setCarteIdentite($userData['carte_identite']);
            $user->setAdresse($userData['adresse']);
            $user->setPhotoRecto($photoRectoResult['path']);
            $user->setPhotoVerso($photoVersoResult['path']);
            $user->setPassword(password_hash($userData['password'], PASSWORD_DEFAULT));

            if (!$this->userRepository->create($user)) {
                throw new Exception("Erreur lors de la création de l'utilisateur.");
            }

            // Créer le compte principal
            $this->createPrincipalAccount($user->getId(), $userData['telephone']);

            return $user;
        } catch (Exception $e) {
            // Nettoyer les fichiers uploadés en cas d'erreur
            $fileUpload->delete($photoRectoResult['path']);
            $fileUpload->delete($photoVersoResult['path']);
            throw $e;
        }
    }

    public function authenticateUser(string $telephone, string $password): ?User
    {
        return $this->userRepository->authenticateUser($telephone, $password);
    }

    public function getUserById(int $id): ?User
    {
        return $this->userRepository->findById($id);
    }

    public function getUserByTelephone(string $telephone): ?User
    {
        return $this->userRepository->findByTelephone($telephone);
    }

    private function createPrincipalAccount(int $userId, string $telephone): Account
    {
        $account = new Account();
        $account->setUserId($userId);
        $account->setTelephone($telephone);
        $account->setSolde(0.0);
        $account->setIsPrincipal(true);

        if (!$this->accountRepository->create($account)) {
            throw new Exception("Erreur lors de la création du compte principal.");
        }

        return $account;
    }
}
