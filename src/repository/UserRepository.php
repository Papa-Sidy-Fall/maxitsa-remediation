<?php

namespace App\Maxitsa\Repository;

require_once __DIR__ . '/../../app/core/abstract/AbstractRepository.php';
require_once __DIR__ . '/../entity/User.php';

use App\Maxitsa\Entity\User;
use PDO;
use DateTime;
use Exception;

class UserRepository extends \AbstractRepository
{
    protected string $table = 'users';
    protected string $entityClass = User::class;

    public function findByTelephone(string $telephone): ?User
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE telephone = :telephone");
        $stmt->execute(['telephone' => $telephone]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result ? $this->hydrate($result) : null;
    }

    public function findByCarteIdentite(string $carteIdentite): ?User
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE carte_identite = :carte_identite");
        $stmt->execute(['carte_identite' => $carteIdentite]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result ? $this->hydrate($result) : null;
    }

    public function telephoneExists(string $telephone): bool
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM {$this->table} WHERE telephone = :telephone");
        $stmt->execute(['telephone' => $telephone]);
        return $stmt->fetchColumn() > 0;
    }

    public function carteIdentiteExists(string $carteIdentite): bool
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM {$this->table} WHERE carte_identite = :carte_identite");
        $stmt->execute(['carte_identite' => $carteIdentite]);
        return $stmt->fetchColumn() > 0;
    }

    public function authenticateUser(string $telephone, string $password): ?User
    {
        $user = $this->findByTelephone($telephone);
        
        if ($user && $user->verifyPassword($password)) {
            return $user;
        }
        
        return null;
    }

    protected function hydrate(array $data): User
    {
        $user = new User();
        
        if (isset($data['id'])) $user->setId($data['id']);
        if (isset($data['nom'])) $user->setNom($data['nom']);
        if (isset($data['prenom'])) $user->setPrenom($data['prenom']);
        if (isset($data['telephone'])) $user->setTelephone($data['telephone']);
        if (isset($data['carte_identite'])) $user->setCarteIdentite($data['carte_identite']);
        if (isset($data['adresse'])) $user->setAdresse($data['adresse']);
        if (isset($data['photo_recto'])) $user->setPhotoRecto($data['photo_recto']);
        if (isset($data['photo_verso'])) $user->setPhotoVerso($data['photo_verso']);
        if (isset($data['password'])) $user->setPassword($data['password']);
        
        if (isset($data['created_at'])) {
            $user->setCreatedAt(new DateTime($data['created_at']));
        }
        if (isset($data['updated_at'])) {
            $user->setUpdatedAt(new DateTime($data['updated_at']));
        }
        
        return $user;
    }
}
