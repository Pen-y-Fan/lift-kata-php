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
}
