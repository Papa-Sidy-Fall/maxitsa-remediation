<?php

namespace App\Maxitsa\Entity;

use DateTime;
use ReflectionClass;
use AbstractEntity;

class User extends \AbstractEntity
{
    private string $nom;
    private string $prenom;
    private string $telephone;
    private string $carte_identite;
    private string $adresse;
    private string $photo_recto;
    private string $photo_verso;
    private string $password;

    // Getters
    public function getNom(): string
    {
        return $this->nom;
    }

    public function getPrenom(): string
    {
        return $this->prenom;
    }

    public function getTelephone(): string
    {
        return $this->telephone;
    }

    public function getCarteIdentite(): string
    {
        return $this->carte_identite;
    }

    public function getAdresse(): string
    {
        return $this->adresse;
    }

    public function getPhotoRecto(): string
    {
        return $this->photo_recto;
    }

    public function getPhotoVerso(): string
    {
        return $this->photo_verso;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getFullName(): string
    {
        return $this->prenom . ' ' . $this->nom;
    }

    // Setters
    public function setNom(string $nom): void
    {
        $this->nom = $nom;
    }

    public function setPrenom(string $prenom): void
    {
        $this->prenom = $prenom;
    }

    public function setTelephone(string $telephone): void
    {
        $this->telephone = $telephone;
    }

    public function setCarteIdentite(string $carteIdentite): void
    {
        $this->carte_identite = $carteIdentite;
    }

    public function setAdresse(string $adresse): void
    {
        $this->adresse = $adresse;
    }

    public function setPhotoRecto(string $photoRecto): void
    {
        $this->photo_recto = $photoRecto;
    }

    public function setPhotoVerso(string $photoVerso): void
    {
        $this->photo_verso = $photoVerso;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function verifyPassword(string $password): bool
    {
        return password_verify($password, $this->password);
    }
}
