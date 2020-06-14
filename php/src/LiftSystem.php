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
                $lift->openDoorsAndSatisfyAnyRequests();
                break;
            }

            // Only one request per tick.
            if ($lift->areDoorsOpen()) {
                $lift->closeDoors();
                break;
            }
            // a lift fulfills a _call_ when it moves to the correct floor, is about to go in the called direction, and opens the doors.
            if (
                $this->isDirectionOfTravelRequiredCall($lift)
                && !$lift->areDoorsOpen()
            ) {
                $lift->openDoorsAndSatisfyAnyRequests();
                $this->cancelCallForFloorInRequiredDirection($lift);
                break;
            }

            if ($this->hasNoMoreRequestsInDirectionAndFloorHasCall($lift)) {
                $lift->openDoorsAndSatisfyAnyRequests();
                $calls = $this->getCallsForFloor($lift->getFloor());
                foreach ($calls as $key => $call) {
                    $lift->setDirection($call->getDirection());
                    unset($this->calls[$key]);
                    break; // Only clear one call at a time.
                }
                break;
            }

            // a lift can only move between floors if the doors are closed.
            $this->moveLift($lift);
        }
    }

    private function isDirectionOfTravelRequiredCall(Lift $lift): bool
    {
        /** @var Call $call */
        foreach ($this->getCallsForFloor($lift->getFloor()) as $call) {
            if ($call->getDirection()->equals($lift->getDirection())) {
                return true;
            }
        }
        return false;
    }

    private function cancelCallForFloorInRequiredDirection(Lift $lift)
    {
        /**
         * @var int $key
         * @var Call $call
         */
        foreach ($this->calls as $key => $call) {
            if ($call->getDirection()->equals($lift->getDirection())) {
                unset($this->calls[$key]);
            }
        }
    }

    private function moveLift(Lift $lift)
    {
        // a lift can only move between floors if the doors are closed.
        if ($lift->areDoorsOpen()) {
            return;
        }

        // Stop at top floor
        if ($this->isTopFloor($lift) && $lift->goingUp()) {
            $lift->setDirection(Direction::STOP());
            return;
        }

        // Stop at bottom floor
        if ($this->isBottomFloor($lift) && $lift->goingDown()) {
            $lift->setDirection(Direction::STOP());
            return;
        }

        if ($this->hasNoRequestsOrCalls($lift)) {
            $lift->setDirection(Direction::STOP());
            return;
        }

        // Move in direction between floors
        foreach ($this->floors as $key => $floor) {
            if ($floor === $lift->getFloor()) {
                $this->moveLiftFloor($lift);
                return;
            }
        }
    }

    private function hasCalls(): bool
    {
        return count($this->calls) > 0;
    }

    private function moveLiftFloor(Lift $lift)
    {
        if ($this->isTopFloor($lift)) {
            $lift->setDirection(Direction::DOWN());
        }

        if ($this->isBottomFloor($lift)) {
            $lift->setDirection(Direction::UP());
        }

        // Already checked we are not on top or bottom floor
        if ($lift->isMoving()) {
            $this->moveInDirection($lift);
            return;
        }

        // Lift Request has priority over call, no Direction is set, so lets move towards the closest request and setDirection
        if ($lift->hasRequests()) {
            $lift->setDirectionOfClosestRequest();
            $this->moveInDirection($lift);
        }
    }

    private function isTopFloor($lift)
    {
        return $lift->getFloor() === $this->floors[count($this->floors)-1];
    }

    private function isBottomFloor($lift)
    {
        return $lift->getFloor() === $this->floors[0];
    }

    private function moveInDirection(Lift $lift)
    {
        if ($lift->getDirection()->equals(Direction::DOWN())) {
            $lift->setFloor($lift->getFloor() -1);
            return;
        }
        if ($lift->getDirection()->equals(Direction::UP())) {
            $lift->setFloor($lift->getFloor() +1);
            return;
        }
    }

    private function hasNoRequestsOrCalls($lift): bool
    {
        return !$lift->hasRequests() && !$this->hasCalls();
    }

    private function hasNoMoreRequestsInDirectionAndFloorHasCall(Lift $lift): bool
    {
        if ($lift->hasRequestsInDirection()) {
            return false;
        }
        return count($this->getCallsForFloor($lift->getFloor())) > 0;
    }
}
