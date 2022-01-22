# Links
### create a link to a product:
$this->router->route('order-product', ['product' => $product->id])



# Migrations [console interaction only]
## Directory to save them:
- _db/Command/_
- _Console/Commands/_
## file where to define all Commands:
- _app/commands.php_ - must return an `array` of commands

## Commands:
- `php command.php help` - shows some info about how to use help
- `php command.php migrate` - runs the migrations that only have not been run/ are not preset in the migrations table
- `php command.php migrate --flesh` - delete all migrations

## Conventions
### Each command mast
- end with postfix Command
- inherited from `Symfony\Component\Console\Command\Command`
- must implement `execute()` method, see down below
- be described using `$this->setDescription()` and `$this->setHelp()` methods

## Possible methods in order of its execution
### optional methods for Command class:
1. `initialize()` method - is used to define vars;
2. `interact()` method - is used to check  options/arguments are missing and interactively ask the user for those values;
### required methods for Command class:
3. `execute()` - start the execution of a command (main login)

### list of sources:
- [more info here](https://symfony.com/doc/current/console.html)