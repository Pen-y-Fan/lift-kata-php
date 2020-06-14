<?php

declare(strict_types=1);

namespace Tests;

use ApprovalTests\Approvals;
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
}
