<?php

declare(strict_types=1);

namespace Lift;

class LiftSystem
{
    /**
     * @var array<int>
     */
    private $floors;

    /**
     * @var array<Call>
     */
    private $calls;

    /**
     * @var array<Lift>
     */
    private $lifts;

    public function __construct(array $floors, array $lifts, array $calls)
    {
        $this->floors = $floors;
        $this->lifts = $lifts;
        $this->calls = $calls;
    }

    public function getFloorsInDescendingOrder(): array
    {
        return array_reverse($this->floors);
    }

    public function getCallsForFloor(int $floor): array
    {
        return array_filter($this->calls, function ($call) use ($floor) {
            /** @var Call $call */
            return $call->getFloor() === $floor;
        });
    }

    public function getLifts(): array
    {
        return $this->lifts;
    }

    public function tick(): void
    {
        /** @var Lift $lift */
        foreach ($this->lifts as $lift) {
            // a lift fulfills a _request_ when it moves to the requested floor and opens the doors.
            // when lift has request for floor and lift is on requested floor - lift opens door
            if ($lift->hasRequestForFloor($lift->getFloor()) && !$lift->areDoorsOpen()) {
                $lift->openDoors();
                break;
            }

            // Only one request per tick.
            if ($lift->areDoorsOpen()) {
                $lift->closeDoors();
                break;
            }
            // a lift fulfills a _call_ when it moves to the correct floor, is about to go in the called direction, and opens the doors.

            // a lift can only move between floors if the doors are closed.


        }

    }
}
