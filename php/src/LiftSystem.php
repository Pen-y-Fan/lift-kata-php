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
        // TODO: implement this method
    }

    /*
     * The following features are not yet implemented:

- a lift fulfills a _request_ when it moves to the requested floor and opens the doors.
- a lift fulfills a _call_ when it moves to the correct floor, is about to go in the called direction, and opens the doors.
- a lift can only move between floors if the doors are closed.

Lifts do not respond immediately or do everything at once. To simplify handling time in this exercise, the provided LiftSystem class has a 'tick' method. Every time you call it, the lift system should simulate a unit of time passing, and update its state according to what changes occurred during that time period. Lifts can move between floors or open their doors for example.

To simplify things, Lifts only accept new calls and requests when they are on a floor. (Then we don't have to model what happens when they are between floors).

The starting code has a Lift class with basic attributes like a floor, requests and doors. Can you build on this code and create something that fulfills all the desired features? Consider Object-Oriented design principles. Can you make Lift and LiftSystem into a well-designed encapsulated objects?


     */

}
