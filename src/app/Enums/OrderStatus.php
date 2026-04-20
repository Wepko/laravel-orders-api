<?php

namespace App\Enums;

enum OrderStatus: string
{
    case NEW = 'new';
    case CONFIRMED = 'confirmed';
    case PROCESSING = 'processing';
    case SHIPPED = 'shipped';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';

    /**
     * Получить все значения статусов
     *
     * @return array<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Карта разрешённых переходов из текущего статуса.
     */
    public function allowedTransitions(): array
    {
        return match ($this) {
            self::NEW        => [self::CONFIRMED, self::CANCELLED],
            self::CONFIRMED  => [self::PROCESSING, self::CANCELLED],
            self::PROCESSING => [self::SHIPPED, self::CANCELLED],
            self::SHIPPED    => [self::COMPLETED],
            self::COMPLETED  => [], // финальный статус
            self::CANCELLED  => [], // финальный статус
        };
    }

    /**
     * Проверить, разрешён ли переход в новый статус.
     */
    public function canTransitionTo(self $newStatus): bool
    {
        return in_array($newStatus, $this->allowedTransitions(), true);
    }

    /**
     * Проверить, является ли статус финальным (не может быть изменён).
     */
    public function isFinal(): bool
    {
        return empty($this->allowedTransitions());
    }

}