<?php

namespace Metadrop\Composer\Comments;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Composer\Command\BaseCommand;

/**
 * The "comments" command class.
 *
 * Displays the comments associated to packages found inthe composer.json file.
 *
 * @internal
 */
class CommentsCommand extends BaseCommand {

  /**
   * @var CommentManager Comment manager to access to the package comments.
   */
  protected CommentsManager $commentsManager;


  /**
   * {@inheritdoc}
   */
  protected function configure(): void {

    $this->setName('comments')
      ->addArgument('package', InputArgument::OPTIONAL, 'Package to display comments from. If not provided all comments are displayed.')
      ->setDescription('Display custom comments on packages')
      ->setHelp(
        <<<EOT
The <info>comments</info> command display any comments found in the
composer.json file.

<info>php composer.phar comments</info>

If you wanmt to display the comment of a single package add the pacakge name
to the command:

<info>php composer.phar comments vendor/package-nmame</info>

Comments are useful to add information about a package such as what a package
is used for or why a package has a certain version. They are used to provide
information that is not obvious in the composer.json itself.

EOT
      );
  }

  /**
   * {@inheritdoc}
   */
  protected function execute(InputInterface $input, OutputInterface $output): int {

    $this->commentsManager = new CommentsManager($this->requireComposer());
    $this->commentsManager->intialize();

    $package_name = $input->getArgument('package');

    $output = [];

    if ($package_name) {
      $output = $this->packageCommentOutput($package_name);
    } else {
      $output = $this->allCommentsOutput();
    }

    $this->getIO()->write($output);

    return 0;
  }

  /**
   * Builds the output with all comments defined in the composer.json.
   *
   * @return array
   *   Array of strings with the desired ouput.
   */
  protected function allCommentsOutput(): array {

    $output = [];

    if ($this->commentsManager->commentCount()) {
      array_push($output, "<info>Comments found in composer.json: </info>");
      foreach ($this->commentsManager->getComments() as $name => $comment) {
        array_push($output, "  <comment>- " . $name . ":</comment> " . $comment);
      }
    } else {
      array_push($output, "<info>\nThere are no comments in the composer.json file to display\n</info>\n");
    }

    return $output;
  }

  /**
   * Builds the output with the comment for a package.
   *
   * @param string $name
   *   Name of the package to get the comments from.
   *
   * @return array
   *   Array of strings with the desired ouput.
   */
  protected function packageCommentOutput(string $name): array {

    if (!$this->commentsManager->hasComment($name)) {
      throw new \InvalidArgumentException(sprintf('There are no comments in the composer.json file for the package %s', $name));
    }

    return [
      sprintf('<info>Comment for package %s:</info> %s', $name, $this->commentsManager->getComment($name))
    ];
  }
}
