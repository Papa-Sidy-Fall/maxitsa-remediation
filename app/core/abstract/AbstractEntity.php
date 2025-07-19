<?php



abstract class AbstractEntity
{
    protected int $id;
    protected DateTime $createdAt;
    protected DateTime $updatedAt;

    public function __construct()
    {
        $this->createdAt = new DateTime();
        $this->updatedAt = new DateTime();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function toArray(): array
    {
        $reflection = new ReflectionClass($this);
        $properties = $reflection->getProperties();
        $data = [];

        foreach ($properties as $property) {
            $property->setAccessible(true);
            
            // Ignorer les propriétés non initialisées
            if (!$property->isInitialized($this)) {
                continue;
            }
            
            $value = $property->getValue($this);
            
            if ($value instanceof DateTime) {
                $value = $value->format('Y-m-d H:i:s');
            }
            
            $data[$property->getName()] = $value;
        }

        return $data;
    }
}
