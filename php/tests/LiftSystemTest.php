<?php

declare(strict_types=1);

namespace Tests;

use ApprovalTests\Approvals;
use Lift\Call;
use Lift\Direction;
use Lift\Lift;
use Lift\LiftSystem;
use PHPUnit\Framework\TestCase;

class LiftSystemTest extends TestCase
{
    public function testLiftWithNoRequestsDoesNothing(): void
    {
        $liftA = new Lift('A', 0);
        $lifts = new LiftSystem([0, 1], [$liftA], []);

        $lifts->tick();

        Approvals::verifyString((new LiftSystemPrinter())->printWithDoors($lifts));
    }

    public function testLiftWithRequestsOnCurrentFloorOpensDoors(): void
    {
        $liftA = new Lift('A', 0, [0], false);
        $lifts = new LiftSystem([0, 1], [$liftA], []);

        $lifts->tick();

        Approvals::verifyString((new LiftSystemPrinter())->printWithDoors($lifts));
    }

    public function testLiftWithNoRequestsOnCurrentFloorWithOpensDoorsClosesDoors(): void
    {
        $liftA = new Lift('A', 0, [1], true);
        $lifts = new LiftSystem([0, 1], [$liftA], []);

        $lifts->tick();

        Approvals::verifyString((new LiftSystemPrinter())->printWithDoors($lifts));
    }

    public function testLiftWithRequestsOnCurrentFloorOpensDoorsNextTickClosesDoors(): void
    {
        $liftA = new Lift('A', 0, [0], false);
        $lifts = new LiftSystem([0, 1], [$liftA], []);

        $lifts->tick();
        $lifts->tick();

        Approvals::verifyString((new LiftSystemPrinter())->printWithDoors($lifts));
    }

    public function testLiftWithClosedDoorsCanMoveTowardsRequest(): void
    {
        $liftA = new Lift('A', 0, [1], false);
        $lifts = new LiftSystem([0, 1], [$liftA], []);

        $lifts->tick();

        Approvals::verifyString((new LiftSystemPrinter())->printWithDoors($lifts));
    }

    public function testLiftWithClosedDoorsCanMoveDownTowardsRequest(): void
    {
        $liftA = new Lift('A', 2, [0], false);
        $lifts = new LiftSystem([0, 1, 2], [$liftA], []);

        $lifts->tick(); // Move to floor 1
        $lifts->tick(); // Move to floor 0

        Approvals::verifyString((new LiftSystemPrinter())->printWithDoors($lifts));
    }

    public function testLiftCanMoveTowardsCall(): void
    {
        $liftA = new Lift('A', 0, [], false);
        $call = new Call(1, Direction::DOWN());
        $lifts = new LiftSystem([0, 1, 2], [$liftA], [$call]);

        $lifts->tick(); // Move up to floor 1 (Direction is UP)
        $lifts->tick(); // Stops and opens door as no more requests
        $lifts->tick(); // Doors closed,  Direction is Down :) -> Call needs to have a required floor too.

        Approvals::verifyString((new LiftSystemPrinter())->printWithDoors($lifts));
    }

    public function testSingleLiftSystem(): void
    {
        $liftA = new Lift('A', 4, [2, 4], false);
        $callA = new Call(1, Direction::DOWN());
        $callB = new Call(1, Direction::UP());
        $callC = new Call(0, Direction::UP());
        $lifts = new LiftSystem([0, 1, 2, 3, 4], [$liftA], [$callA, $callB, $callC]);

        $lifts->tick(); // Doors open, request floor 4 done
        $lifts->tick(); // Doors close
        $lifts->tick(); // Move Down to floor 3
        $lifts->tick(); // Move Down to floor 2
        $lifts->tick(); // Doors open, request floor 2 done
        $lifts->tick(); // Doors close
        $lifts->tick(); // Move Down to floor 1
        $lifts->tick(); // Door open, Call down done
        $lifts->tick(); // Door close
        $lifts->tick(); // Door open, call up done
        $lifts->tick(); // Doors closed,  Direction is still up :( -> unexpected - however system is not finished.
        $lifts->tick(); // Move Up to floor 2
        $lifts->tick(); // Move Up to floor 3
        $lifts->tick(); // Move Up to floor 4
        $lifts->tick(); // Move Stop at floor 4

        $lifts->tick(); // Move Up to floor 3
        $lifts->tick(); // Move Up to floor 2
        $lifts->tick(); // Move Up to floor 1
        $lifts->tick(); // Move Down to floor 0
        $lifts->tick(); // Door open, call up done
        $lifts->tick(); // Doors closed
        $lifts->tick(); // Lift Stop

        Approvals::verifyString((new LiftSystemPrinter())->printWithDoors($lifts));
    }
}
