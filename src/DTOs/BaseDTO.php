<?php

declare(strict_types=1);

namespace LaravelPlus\FeatureRequests\DTOs;

use LaravelPlus\FeatureRequests\Contracts\DTOs\DTOInterface;
use Illuminate\Support\Str;

abstract class BaseDTO implements DTOInterface
{
    /**
     * Convert the DTO to an array.
     */
    public function toArray(): array
    {
        $array = [];
        $reflection = new \ReflectionClass($this);
        
        foreach ($reflection->getProperties(\ReflectionProperty::IS_PUBLIC) as $property) {
            if ($property->isReadonly()) {
                $value = $property->getValue($this);
                
                // Handle nested DTOs
                if ($value instanceof DTOInterface) {
                    $array[$property->getName()] = $value->toArray();
                } elseif (is_array($value)) {
                    $array[$property->getName()] = $this->convertArrayToArray($value);
                } else {
                    $array[$property->getName()] = $value;
                }
            }
        }
        
        return $array;
    }

    /**
     * Get the DTO as JSON string.
     */
    public function toJson(int $options = 0): string
    {
        return json_encode($this->toArray(), $options);
    }

    /**
     * Check if the DTO has any data.
     */
    public function isEmpty(): bool
    {
        $array = $this->toArray();
        return empty(array_filter($array, fn($value) => $value !== null && $value !== ''));
    }

    /**
     * Get a unique identifier for caching purposes.
     */
    public function getCacheKey(): string
    {
        $className = Str::snake(class_basename(static::class));
        $data = $this->toArray();
        return $className . '_' . md5(serialize($data));
    }

    /**
     * Convert array values to array format recursively.
     */
    protected function convertArrayToArray(array $array): array
    {
        $result = [];
        
        foreach ($array as $key => $value) {
            if ($value instanceof DTOInterface) {
                $result[$key] = $value->toArray();
            } elseif (is_array($value)) {
                $result[$key] = $this->convertArrayToArray($value);
            } else {
                $result[$key] = $value;
            }
        }
        
        return $result;
    }

    /**
     * Get a property value by name.
     */
    public function get(string $property): mixed
    {
        if (property_exists($this, $property)) {
            return $this->$property;
        }
        
        throw new \InvalidArgumentException("Property '{$property}' does not exist on " . static::class);
    }

    /**
     * Check if a property exists and has a value.
     */
    public function has(string $property): bool
    {
        return property_exists($this, $property) && $this->$property !== null;
    }

    /**
     * Get all property names.
     */
    public function getPropertyNames(): array
    {
        $reflection = new \ReflectionClass($this);
        $properties = [];
        
        foreach ($reflection->getProperties(\ReflectionProperty::IS_PUBLIC) as $property) {
            if ($property->isReadonly()) {
                $properties[] = $property->getName();
            }
        }
        
        return $properties;
    }

    /**
     * Create a new instance with updated values.
     */
    public function with(array $updates): static
    {
        $data = $this->toArray();
        $data = array_merge($data, $updates);
        
        return static::fromArray($data);
    }
}
