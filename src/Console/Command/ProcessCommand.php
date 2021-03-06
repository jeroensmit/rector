<?php declare(strict_types=1);

namespace Rector\Console\Command;

use Rector\Application\ErrorAndDiffCollector;
use Rector\Application\RectorApplication;
use Rector\Autoloading\AdditionalAutoloader;
use Rector\CodingStyle\AfterRectorCodingStyle;
use Rector\Configuration\Configuration;
use Rector\Configuration\Option;
use Rector\Console\Output\ConsoleOutputFormatter;
use Rector\Console\Output\OutputFormatterCollector;
use Rector\Console\Shell;
use Rector\FileSystem\FilesFinder;
use Rector\Guard\RectorGuard;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\PackageBuilder\Console\Command\CommandNaming;

final class ProcessCommand extends AbstractCommand
{
    /**
     * @var FilesFinder
     */
    private $filesFinder;

    /**
     * @var AdditionalAutoloader
     */
    private $additionalAutoloader;

    /**
     * @var RectorGuard
     */
    private $rectorGuard;

    /**
     * @var ErrorAndDiffCollector
     */
    private $errorAndDiffCollector;

    /**
     * @var AfterRectorCodingStyle
     */
    private $afterRectorCodingStyle;

    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var RectorApplication
     */
    private $rectorApplication;

    /**
     * @var string[]
     */
    private $fileExtensions = [];

    /**
     * @var OutputFormatterCollector
     */
    private $outputFormatterCollector;

    /**
     * @param string[] $fileExtensions
     */
    public function __construct(
        FilesFinder $phpFilesFinder,
        AdditionalAutoloader $additionalAutoloader,
        RectorGuard $rectorGuard,
        ErrorAndDiffCollector $errorAndDiffCollector,
        AfterRectorCodingStyle $afterRectorCodingStyle,
        Configuration $configuration,
        RectorApplication $rectorApplication,
        OutputFormatterCollector $outputFormatterCollector,
        array $fileExtensions
    ) {
        $this->filesFinder = $phpFilesFinder;
        $this->additionalAutoloader = $additionalAutoloader;
        $this->rectorGuard = $rectorGuard;
        $this->errorAndDiffCollector = $errorAndDiffCollector;
        $this->afterRectorCodingStyle = $afterRectorCodingStyle;
        $this->configuration = $configuration;
        $this->rectorApplication = $rectorApplication;
        $this->fileExtensions = $fileExtensions;
        $this->outputFormatterCollector = $outputFormatterCollector;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
        $this->setDescription('Upgrade or refactor source code with provided rectors');
        $this->addArgument(
            Option::SOURCE,
            InputArgument::REQUIRED | InputArgument::IS_ARRAY,
            'Files or directories to be upgraded.'
        );
        $this->addOption(
            Option::OPTION_DRY_RUN,
            'n',
            InputOption::VALUE_NONE,
            'See diff of changes, do not save them to files.'
        );
        $this->addOption(
            Option::OPTION_AUTOLOAD_FILE,
            'a',
            InputOption::VALUE_REQUIRED,
            'File with extra autoload'
        );

        $this->addOption(
            Option::OPTION_WITH_STYLE,
            'w',
            InputOption::VALUE_NONE,
            'Apply basic coding style afterwards to make code look nicer'
        );

        $this->addOption(
            Option::HIDE_AUTOLOAD_ERRORS,
            'e',
            InputOption::VALUE_NONE,
            'Hide autoload errors for the moment.'
        );

        $this->addOption(Option::OPTION_RULE, 'r', InputOption::VALUE_REQUIRED, 'Run only this single rule.');

        $availableOutputFormatters = $this->outputFormatterCollector->getNames();
        $this->addOption(
            Option::OPTION_OUTPUT_FORMAT,
            'o',
            InputOption::VALUE_OPTIONAL,
            sprintf('Select output format: "%s".', implode('", "', $availableOutputFormatters)),
            ConsoleOutputFormatter::NAME
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->configuration->resolveFromInput($input);

        $this->rectorGuard->ensureSomeRectorsAreRegistered();

        $source = (array) $input->getArgument(Option::SOURCE);

        $phpFileInfos = $this->filesFinder->findInDirectoriesAndFiles($source, $this->fileExtensions);

        $this->additionalAutoloader->autoloadWithInputAndSource($input, $source);

        $this->rectorApplication->runOnFileInfos($phpFileInfos);

        // report diffs and errors
        $outputFormat = (string) $input->getOption(Option::OPTION_OUTPUT_FORMAT);
        $outputFormatter = $this->outputFormatterCollector->getByName($outputFormat);
        $outputFormatter->report($this->errorAndDiffCollector);

        // apply coding standard
        if ($this->configuration->isWithStyle()) {
            $this->afterRectorCodingStyle->apply($source);
        }

        // some errors were found → fail
        if ($this->errorAndDiffCollector->getErrors() !== []) {
            return Shell::CODE_ERROR;
        }

        // inverse error code for CI dry-run
        if ($this->configuration->isDryRun() && count($this->errorAndDiffCollector->getFileDiffs())) {
            return Shell::CODE_ERROR;
        }

        return Shell::CODE_SUCCESS;
    }
}
