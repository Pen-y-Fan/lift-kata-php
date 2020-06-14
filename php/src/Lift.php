<?php

declare(strict_types=1);

namespace Lift;

class Lift
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var int
     */
    private $floor;

    /**
     * @var array<int>
     */
    private $requests;

    /**
     * @var boolean
     */
    private $doorsOpen;
    /**
     * @var null|Direction
     */
    private $direction;

    public function __construct(string $id, int $floor, array $requests = [], bool $doorsOpen = false)
    {
        $this->id = $id;
        $this->floor = $floor;
        $this->requests = $requests;
        $this->doorsOpen = $doorsOpen;
        $this->direction = null;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getFloor(): int
    {
        return $this->floor;
    }

    public function hasRequestForFloor(int $floor): bool
    {
        return array_key_exists($floor, array_flip($this->requests));
    }

    public function areDoorsOpen(): bool
    {
        return $this->doorsOpen;
    }

    public function openDoorsAndSatisfyAnyRequests(): void
    {
        $this->doorsOpen = true;
        /** @var int $request */
        foreach ($this->requests as $k => $v) {
            if ($this->floor === $v) {
                unset($this->requests[$k]);
            }
        }
    }

    public function closeDoors(): void
    {
        $this->doorsOpen = false;
    }

    public function getDirection(): Direction
    {
        return $this->direction;
    }

    public function hasDirection(): bool
    {
        return $this->direction !== null;
    }

    public function setDirection($direction): void
    {
        $this->direction = $direction;
    }

    public function hasRequests(): bool
    {
        return count($this->requests) > 0;
    }

    public function setFloor(int $floor): void
    {
        $this->floor = $floor;
    }

    public function setDirectionOfClosestRequest()
    {
        $min = 999999999;
        $closestFloor = $this->getFloor();
        foreach ($this->requests as $request) {
            if (abs($this->floor-$request) < $min ) {
                $min = abs($this->floor-$request);
                $closestFloor = $this->floor;
            }
        }

        // No match found or there are no requests
        if ($closestFloor === $this->floor) {
            $this->direction = null;
            return;
        }

        if ($closestFloor < $this->floor) {
            $this->direction = Direction::DOWN();
            return;
        }

        $this->direction = Direction::UP();
    }
}
