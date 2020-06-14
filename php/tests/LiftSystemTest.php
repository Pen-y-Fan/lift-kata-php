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
        /** @var Lift $liftA */
        $liftA = new Lift('A', 0);

        /** @var LiftSystem $lifts */
        $lifts = new LiftSystem([0, 1], [$liftA], []);

        $lifts->tick();
        Approvals::verifyString((new LiftSystemPrinter())->printWithDoors($lifts));
    }
}
