<?php

declare(strict_types=1);

namespace De\SWebhosting\TYPO3Surf\Tests\Unit;

use De\SWebhosting\TYPO3Surf\HardlinkReleaseTask;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use TYPO3\Surf\Domain\Model\Application;
use TYPO3\Surf\Domain\Model\Deployment;
use TYPO3\Surf\Domain\Model\Node;
use TYPO3\Surf\Domain\Service\ShellCommandService;

final class HardLinkReleaseTaskTest extends TestCase
{
    private HardlinkReleaseTask $hardlinkReleaseTask;

    protected function setUp(): void
    {
        $shellCommandServiceMock = $this->createMock(ShellCommandService::class);

        $this->hardlinkReleaseTask = new HardlinkReleaseTask();
        $this->hardlinkReleaseTask->setShellCommandService($shellCommandServiceMock);
    }

    public function testExecute(): void
    {
        $nodeMock = $this->createNodeMock();
        $applicationMock = $this->createMock(Application::class);
        $deploymentMock = $this->getDeploymentMock();
        $this->hardlinkReleaseTask->execute(
            $nodeMock,
            $applicationMock,
            $deploymentMock
        );
    }

    public function testRollback(): void
    {
        $nodeMock = $this->createNodeMock();
        $applicationMock = $this->createMock(Application::class);
        $deploymentMock = $this->getDeploymentMock();
        $this->hardlinkReleaseTask->rollback(
            $nodeMock,
            $applicationMock,
            $deploymentMock
        );
    }

    public function testSimulate(): void
    {
        $nodeMock = $this->createNodeMock();
        $applicationMock = $this->createMock(Application::class);
        $deploymentMock = $this->getDeploymentMock();
        $this->hardlinkReleaseTask->simulate(
            $nodeMock,
            $applicationMock,
            $deploymentMock
        );
    }

    private function createNodeMock(): MockObject&Node
    {
        $nodeMock = $this->createMock(Node::class);
        $nodeMock->method('getReleasesPath')->willReturn('/my/cool/release');
        $nodeMock->method('getName')->willReturn('my-test-node');
        return $nodeMock;
    }

    private function getDeploymentMock(): Deployment&MockObject
    {
        $loggerMock = $this->createMock(LoggerInterface::class);

        $deploymentMock = $this->createMock(Deployment::class);
        $deploymentMock->method('isDryRun')->willReturn(true);
        $deploymentMock->method('getLogger')->willReturn($loggerMock);
        $deploymentMock->method('getReleaseIdentifier')->willReturn('the-release-id');
        return $deploymentMock;
    }
}
