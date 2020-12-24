<?php

namespace Commando\Parsers;

use Commando\Parsers\Parser;
use Commando\Models\Settings;

class ConsoleParser extends Parser
{
	private ?string $command = null;
	private array $args = [];
	private array $options = [];
	private ?string $error = null;


	public function __construct(Settings $settings)
	{
		parent::__construct($settings);
	}

	public function parseTokens(array $tokens = []): void
	{
		$this->emptyAttrs();

		try {
			if (empty($tokens) || empty($tokens[0]))
				throw new \Exception('You must specify command');

			if (!$this->settings->commandExists($tokens[0])) {
				$msg = sprintf("Command '%s' does not exist", $tokens[0]);
				throw new \Exception($msg);
			}
			
			$this->command = array_shift($tokens);
			$count = $this->countCommandArgs($this->command);

			while ($count > 0) {
				if (empty($tokens) || empty($tokens[0])) {
					$msg = sprintf("Command '%s' needs %d more argument", 
						$this->command, $count);
					throw new \Exception($msg);
				}

				if (str_starts_with($tokens[0], '-'))
					throw new \Exception("Command arguments cannot start with '-'");

				$this->args[] = array_shift($tokens);
				$count--;
			}

			while (!empty($tokens)) {
				if (!str_starts_with($tokens[0], '-'))
					throw new \Exception("All options must start with '-'");

				if (!$this->settings->commandOptionExists(
					$this->command, $tokens[0])) {
					$msg = sprintf("Option '%s' is not available for command '%s'", 
						$tokens[0], $this->command
					);
					throw new \Exception($msg);
				}
				
				$option = array_shift($tokens);
				$this->options[$option] = [];
				$argsCount = $this->countCommandOptionArgs(
					$this->command, $option
				);

				while ($argsCount > 0) {
					if (empty($tokens)) {
						$msg = sprintf("Option '%s' needs %d more arg", 
							$option, $argsCount
						);
						throw new \Exception($msg);
					}

					$this->options[$option][] = array_shift($tokens);
					$argsCount--;
				}
			}
		}
		catch (\Exception $e) {
			$this->error = $e->getMessage();
		}
	}

	public function countCommandArgs(string $command): int
	{
		return count($this->settings->getCommandArgs($command));
	}

	private function countCommandOptionArgs(string $command, string $option): int
	{
		return count($this->settings->getCommandOptionArgs($command, $option));
	}

	private function getTokensFromString(string $string): array
	{
		$tokens = explode(' ', $string);
		$tokens = array_filter($tokens, fn(string $s) => !empty($s));

		return $tokens;
	}

	private function emptyAttrs(): void
	{
		$this->command = null;
		$this->options = [];
		$this->args = [];
		$this->error = null;
	}

	public function getCommand(): ?string
	{
		return $this->command;
	}

	public function getCommandClass(): ?string
	{
		return (is_string($this->command)) 
			? $this->settings->getCommandClass($this->command)
			: null;
	}

	public function getArgs(): array
	{
		return $this->args;
	}

	public function getOptions(): array
	{
		return $this->options;
	}

	public function getError(): ?string
	{
		return $this->error;
	}
}