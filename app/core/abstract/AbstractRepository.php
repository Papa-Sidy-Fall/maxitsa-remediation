<?php



abstract class AbstractRepository
{
    protected Database $db;
    protected string $table;
    protected string $entityClass;

    public function __construct(Database $database)
    {
        $this->db = $database;
    }

    public function findAll(): array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table}");
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return array_map([$this, 'hydrate'], $results);
    }

    public function findById(int $id): ?object
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result ? $this->hydrate($result) : null;
    }

    public function create(AbstractEntity $entity): bool
    {
        $data = $entity->toArray();
        unset($data['id'], $data['createdAt'], $data['updatedAt']);
        
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        
        $sql = "INSERT INTO {$this->table} ({$columns}, created_at, updated_at) 
                VALUES ({$placeholders}, NOW(), NOW()) RETURNING id";
        
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute($data);
        
        if ($result) {
            $insertedId = $stmt->fetchColumn();
            $entity->setId((int)$insertedId);
        }
        
        return $result;
    }

    public function update(AbstractEntity $entity): bool
    {
        $data = $entity->toArray();
        unset($data['id'], $data['createdAt'], $data['updatedAt']);
        
        $setParts = [];
        foreach (array_keys($data) as $column) {
            $setParts[] = "{$column} = :{$column}";
        }
        $setClause = implode(', ', $setParts);
        
        $sql = "UPDATE {$this->table} SET {$setClause}, updated_at = NOW() WHERE id = :id";
        $data['id'] = $entity->getId();
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    protected function hydrate(array $data): object
    {
        $entity = new $this->entityClass();
        
        foreach ($data as $key => $value) {
            $setter = 'set' . ucfirst($key);
            
            if (method_exists($entity, $setter)) {
                if (in_array($key, ['createdAt', 'updatedAt', 'created_at', 'updated_at']) && $value) {
                    $value = new DateTime($value);
                }
                $entity->$setter($value);
            }
        }
        
        return $entity;
    }
}
