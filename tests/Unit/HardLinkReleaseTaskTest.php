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

/**
 * @covers \De\SWebhosting\TYPO3Surf\HardlinkReleaseTask
 */
final class HardLinkReleaseTaskTest extends TestCase
{
    private HardlinkReleaseTask $hardlinkReleaseTask;

    /**
     * @var LoggerInterface&MockObject
     */
    private LoggerInterface $logger;

    /**
     * @var MockObject&ShellCommandService
     */
    private ShellCommandService $shellCommandService;

    protected function setUp(): void
    {
        $this->shellCommandService = $this->createMock(ShellCommandService::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        $this->hardlinkReleaseTask = new HardlinkReleaseTask();
        $this->hardlinkReleaseTask->setShellCommandService($this->shellCommandService);
    }

    public function testExecute(): void
    {
        $nodeMock = $this->createNodeMock();
        $applicationMock = $this->createMock(Application::class);
        $deploymentMock = $this->getDeploymentMock(false);

        $this->expectReleaseCommandCall($nodeMock, $deploymentMock);

        $this->logger->expects(self::once())
            ->method('notice')
            ->with('<success>Node "my-test-node" is live!</success>');

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
        $deploymentMock = $this->getDeploymentMock(false);

        $expectedCommand = [
            'cd \'/my/cool/release\'',
            'rm -rf ./current',
            'if [ -e ./previous ]; then mv ./previous ./current; fi',
        ];
        $this->shellCommandService->expects(self::once())
            ->method('execute')
            ->with(
                $expectedCommand,
                $nodeMock,
                $deploymentMock
            );

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
        $deploymentMock = $this->getDeploymentMock(true);

        $this->expectReleaseCommandCall($nodeMock, $deploymentMock);

        $this->logger->expects(self::once())
            ->method('notice')
            ->with('<success>Node "my-test-node" would be live!</success>');

        $this->hardlinkReleaseTask->simulate(
            $nodeMock,
            $applicationMock,
            $deploymentMock
        );
    }

    /**
     * @return MockObject&Node
     */
    private function createNodeMock(): MockObject
    {
        $nodeMock = $this->createMock(Node::class);
        $nodeMock->method('getReleasesPath')->willReturn('/my/cool/release');
        $nodeMock->method('getName')->willReturn('my-test-node');
        return $nodeMock;
    }

    /**
     * @param MockObject&Node $nodeMock
     * @param Deployment&MockObject $deploymentMock
     */
    private function expectReleaseCommandCall(Node $nodeMock, Deployment $deploymentMock): void
    {
        $expectedCommand = [
            'cd \'/my/cool/release\'',
            'rm -rf ./next',
            'cp -al \'./the-release-id\' ./next',
            'rm -rf ./previous',
            'if [ -e ./current ]; then mv ./current ./previous; fi',
            'mv ./next ./current',
        ];

        $this->shellCommandService->expects(self::once())
            ->method('executeOrSimulate')
            ->with(
                $expectedCommand,
                $nodeMock,
                $deploymentMock
            );
    }

    /**
     * @return Deployment&MockObject
     */
    private function getDeploymentMock(bool $isDryRun): MockObject
    {
        $deploymentMock = $this->createMock(Deployment::class);
        $deploymentMock->method('isDryRun')->willReturn($isDryRun);
        $deploymentMock->method('getLogger')->willReturn($this->logger);
        $deploymentMock->method('getReleaseIdentifier')->willReturn('the-release-id');
        return $deploymentMock;
    }
}
