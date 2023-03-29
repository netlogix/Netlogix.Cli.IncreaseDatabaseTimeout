# Netlogix.Cli.IncreaseDatabaseTimeout

This package adds an aspect for the CommandController that allows to configure the database timeout for each command individually.

## Installation
```bash
composer require netlogix/cli-increasedatabasetimeout
```

## Usage
You can define timeouts per Flow command:
```yaml
Netlogix:
  Cli:
    IncreaseDatabaseTimeout:
      timeouts:
        'resource:publish': 3600
```
