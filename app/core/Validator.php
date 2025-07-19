<?php

class Validator
{
    private array $errors = [];
    private array $data = [];

    public function validate(array $data, array $rules): bool
    {
        $this->data = $data;
        $this->errors = [];

        foreach ($rules as $field => $fieldRules) {
            $this->validateField($field, $fieldRules);
        }

        return empty($this->errors);
    }

    private function validateField(string $field, string|array $rules): void
    {
        if (is_string($rules)) {
            $rules = explode('|', $rules);
        }

        foreach ($rules as $rule) {
            $this->applyRule($field, $rule);
        }
    }

    private function applyRule(string $field, string $rule): void
    {
        $params = [];
        if (strpos($rule, ':') !== false) {
            [$rule, $paramString] = explode(':', $rule, 2);
            $params = explode(',', $paramString);
        }

        $value = $this->data[$field] ?? null;

        switch ($rule) {
            case 'required':
                if (empty($value)) {
                    $this->addError($field, "Le champ $field est obligatoire.");
                }
                break;

            case 'string':
                if (!is_string($value) && $value !== null) {
                    $this->addError($field, "Le champ $field doit être une chaîne de caractères.");
                }
                break;

            case 'numeric':
                if (!is_numeric($value) && $value !== null) {
                    $this->addError($field, "Le champ $field doit être numérique.");
                }
                break;

            case 'email':
                if (!filter_var($value, FILTER_VALIDATE_EMAIL) && $value !== null) {
                    $this->addError($field, "Le champ $field doit être une adresse email valide.");
                }
                break;

            case 'min':
                $min = (int)$params[0];
                if (is_string($value) && strlen($value) < $min) {
                    $this->addError($field, "Le champ $field doit contenir au moins $min caractères.");
                } elseif (is_numeric($value) && $value < $min) {
                    $this->addError($field, "Le champ $field doit être supérieur ou égal à $min.");
                }
                break;

            case 'max':
                $max = (int)$params[0];
                if (is_string($value) && strlen($value) > $max) {
                    $this->addError($field, "Le champ $field ne doit pas dépasser $max caractères.");
                } elseif (is_numeric($value) && $value > $max) {
                    $this->addError($field, "Le champ $field doit être inférieur ou égal à $max.");
                }
                break;

            case 'phone':
                if (!preg_match('/^[0-9+\-\s()]+$/', $value) && $value !== null) {
                    $this->addError($field, "Le champ $field doit être un numéro de téléphone valide.");
                }
                break;

            case 'unique':
                // Implémentation simple pour vérifier l'unicité
                if (isset($params[0]) && $value !== null) {
                    $table = $params[0];
                    $column = $params[1] ?? $field;
                    $db = Database::getInstance();
                    $stmt = $db->prepare("SELECT COUNT(*) FROM $table WHERE $column = ?");
                    $stmt->execute([$value]);
                    if ($stmt->fetchColumn() > 0) {
                        $this->addError($field, "Cette valeur existe déjà.");
                    }
                }
                break;

            case 'confirmed':
                $confirmationField = $field . '_confirmation';
                if ($value !== ($this->data[$confirmationField] ?? null)) {
                    $this->addError($field, "La confirmation ne correspond pas.");
                }
                break;
        }
    }

    private function addError(string $field, string $message): void
    {
        if (!isset($this->errors[$field])) {
            $this->errors[$field] = [];
        }
        $this->errors[$field][] = $message;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getFirstError(string $field): ?string
    {
        return $this->errors[$field][0] ?? null;
    }

    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }
}
