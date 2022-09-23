<?php

namespace Netlogix\Cli\IncreaseDatabaseTimeout\Aspect;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Aop\JoinPointInterface;

/**
 * @Flow\Aspect
 */
class CommandControllerAspect
{
	/**
	 * @Flow\InjectConfiguration(path="timeouts")
	 * @var array
	 */
	protected $timeouts = [];

	/**
	 * @Flow\Inject
	 * @var EntityManagerInterface
	 */
	protected $entityManager;

	/**
	 * @Flow\Before("method(Neos\Flow\Cli\CommandController->processRequest())")
	 * @param JoinPointInterface $joinPoint
	 * @return void
	 * @throws \Doctrine\DBAL\Exception
	 */
	public function increaseDatabaseTimeout(JoinPointInterface $joinPoint)
	{
		$commandIdentifier = $joinPoint->getMethodArgument('request')
			->getCommand()
			->getCommandIdentifier();

		$matchCount = preg_match('/[^:]*:(?<commandName>.*)/', $commandIdentifier, $matches);

		if ($matchCount === 0 || !array_key_exists($matches['commandName'], $this->timeouts)) {
			return;
		}

		$timeout = $this->timeouts[$matches['commandName']];

		ini_set('default_socket_timeout', (string)$timeout);

		$connection = $this->entityManager->getConnection();
		if (!$connection || !$connection instanceof Connection) {
			throw new Exception('No Doctrine Connection found, cannot increase MySQL timeout');
		}

		$connection->executeStatement(sprintf('SET SESSION wait_timeout = %d;', $timeout));
	}
}
